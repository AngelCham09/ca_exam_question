<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Services\ExamApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class InvestorController extends Controller
{
    protected $apiService;

    public function __construct(ExamApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        return view('investor.index', [
            'investors' => Investor::orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->get()
        ]);
    }

    public function create()
    {
        return view('investor.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:investors,email',
            'contact_number' => 'required|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            $investor = Investor::create($validated);

            $apiResponse = $this->apiService->createInvestor($validated);

            $investor->update([
                'api_id' => $apiResponse['id'],
                'api_created_at' => $apiResponse['created_at'] ?? null,
                'api_updated_at' => $apiResponse['updated_at'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('investors.index')->with('success', 'Investor created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Failed to create Investor", [
                'input'   => $validated,
                'message'     => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->withErrors(['api' => 'Failed to create investor: ' . $e->getMessage()]);
        }
    }

    public function edit(Investor $investor)
    {
        return view('investor.edit', compact('investor'));
    }

    public function update(Request $request, Investor $investor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('investors')->ignore($investor->id)
            ],
            'contact_number' => 'required|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            $investor->update($validated);

            if ($investor->api_id) {
                $apiResponse = $this->apiService->updateInvestor($investor, $validated);

                $investor->update([
                    'api_updated_at' => $apiResponse['updated_at'] ?? now(),
                ]);
            }

            DB::commit();

            return redirect()->route('investors.index')->with('success', 'Investor updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Failed to update investor", [
                'investor_id' => $investor->id,
                'api_id' => $investor->api_id,
                'message'     => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['api' => 'Failed to update investor: ' . $e->getMessage()]);
        }
    }
}
