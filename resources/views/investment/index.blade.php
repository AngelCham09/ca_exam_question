<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Investment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fund
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($investments as $investment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($investment->start_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $investment->investor->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $investment->fund->name ?? 'N/A' }}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusValue = strtolower($investment->status ?? '');

                                                $colorClass = 'bg-gray-100 text-gray-800';

                                                if ($statusValue === 'active') {
                                                    $colorClass = 'bg-green-100 text-green-800';
                                                } elseif ($statusValue === 'pending') {
                                                    $colorClass = 'bg-yellow-100 text-yellow-800';
                                                } elseif ($statusValue === 'closed' || $statusValue === 'inactive') {
                                                    $colorClass = 'bg-red-100 text-red-800';
                                                }
                                            @endphp

                                            <span
                                                class="px-3 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                                                {{ strtoupper($investment->status ?? 'N/A') }}
                                            </span>
                                        </td>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                                            {{ number_format($investment->capital_amount, 2) }}
                                        </td>
                                    </tr>
                                 @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                            No investments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
