<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Graph') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">

                    <div class="flex justify-between">
                        <p>{{ __('Equity Over Graph Line Chart') }}</p>

                        <x-button onclick="downloadSampleData()">
                            Download Sample Data
                        </x-button>
                    </div>

                    <div class="relative w-full h-[300px] sm:h-[400px] lg:h-[500px]">
                        <canvas id="equityChart"></canvas>
                    </div>

                </div>


                <div class="grid grid-cols-2 gap-4 p-6">
                    <div class="shadow-xl border p-4 rounded-lg">
                        <h1>Annual Return</h1>
                        <p class="text-2xl font-bold">{{ $metrics['annual_return'] }}</p>
                    </div>
                    <div class="shadow-xl border p-4 rounded-lg">
                        <h1>Sharpe Ratio</h1>
                        <p class="text-2xl font-bold">{{ $metrics['sharpe'] }}</p>
                    </div>
                    <div class="shadow-xl border p-4 rounded-lg">
                        <h1>Maximum Drawdown</h1>
                        <p class="text-2xl font-bold">{{ $metrics['max_dd'] }}</p>
                    </div>
                    <div class="shadow-xl border p-4 rounded-lg">
                        <h1>Calmar Ratio</h1>
                        <p class="text-2xl font-bold">{{ $metrics['calmar'] }}</p>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function downloadSampleData() {
            window.open('{{ asset('sample_data.csv') }}', "_blank")
        }

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('equityChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Equity',
                        data: {!! json_encode($equity) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: 10,
                                maxRotation: 45,
                                minRotation: 0,
                                font: {
                                    size: window.innerWidth < 640 ? 10 : 12
                                }
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Equity',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            beginAtZero: false,
                            suggestedMin: 1.0,
                            ticks: {
                                callback: (value) => value.toFixed(3),
                                font: {
                                    size: window.innerWidth < 640 ? 10 : 12
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
