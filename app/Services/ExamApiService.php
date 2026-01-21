<?php

namespace App\Services;

use App\Models\Fund;
use App\Models\Investment;
use App\Models\Investor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExamApiService
{
    protected string $baseUrl;
    protected string $email;

    public function __construct()
    {
        $this->baseUrl = config('services.exam.base_url');
        $this->email = config('services.exam.email');
    }

    private function getValidToken(): string
    {
        return Cache::remember('exam_api_token', now()->addHours(23), function () {
            $response = Http::acceptJson()
                ->asJson()
                ->post("{$this->baseUrl}/generate-token", [
                    'email' => $this->email
                ]);
            return $response->json()['token'] ?? throw new \Exception("Could not generate API token.");
        });
    }

    private function request(string $method, string $endpoint, array $data = [])
    {
        return retry(2, function () use ($method, $endpoint, $data) {
            $response = Http::withToken($this->getValidToken())
                ->acceptJson()
                ->asJson()
                ->$method("{$this->baseUrl}/{$endpoint}", $data);

            if ($response->status() === 401) {
                Cache::forget('exam_api_token');
                throw new \Exception("Unauthorized. Refreshing token...");
            }

            if (!$response->successful()) {
                Log::error('API request failed', [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception(
                    $response->json('message') ?? "API request failed with status {$response->status()}"
                );
            }

            return $response->json()['data'] ?? [];
        }, 100);
    }

    public function syncAll(?callable $onStep = null): void
    {
        DB::transaction(function () use ($onStep) {

            if ($onStep) $onStep("Syncing Funds...");
            $this->syncFunds();

            if ($onStep) $onStep("Syncing Investors...");
            $this->syncInvestors();

            if ($onStep) $onStep("Syncing Investments...");
            $this->syncInvestments();
        });
    }

    private function syncFunds(): void
    {
        $data = $this->request('get', 'fund');
        foreach ($data as $row) {
            Fund::updateOrCreate(['api_id' => $row['id']], [
                'name' => $row['name'],
                'api_created_at'     => $row['created_at'],
                'api_updated_at'     => $row['updated_at'],
            ]);
        }
    }

    private function syncInvestors(): void
    {
        $data = $this->request('get', 'investor');
        foreach ($data as $row) {
            Investor::updateOrCreate(['api_id' => $row['id']], [
                'name' => $row['name'],
                'email' => $row['email'],
                'contact_number' => $row['contact_number'],
                'api_created_at'     => $row['created_at'],
                'api_updated_at'     => $row['updated_at'],
            ]);
        }
    }

    private function syncInvestments(): void
    {
        $data = $this->request('get', 'investments');
        $investorMap = Investor::pluck('id', 'api_id');
        $fundMap = Fund::pluck('id', 'api_id');

        foreach ($data as $row) {
            $localInvestorId = $investorMap[$row['investor']['id']] ?? null;
            $localFundId = $fundMap[$row['fund']['id']] ?? null;

            if ($localInvestorId && $localFundId) {
                Investment::updateOrCreate(['api_id' => $row['id']], [
                    'uid' => $row['uid'],
                    'investor_id' => $localInvestorId,
                    'fund_id' => $localFundId,
                    'capital_amount' => $row['capital_amount'],
                    'start_date' => $row['start_date'],
                    'status' => $row['status'],
                    'api_created_at'     => $row['created_at'],
                    'api_updated_at'     => $row['updated_at'],
                ]);
            }

        }
    }

    public function createInvestor(array $data): array
    {
        return $this->request('post', 'investor', $data);
    }

    public function updateInvestor(Investor $investor, array $data): array
    {
        return $this->request('put', "investor/{$investor->api_id}", $data);
    }
}
