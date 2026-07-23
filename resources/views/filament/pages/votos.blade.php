<x-filament::page>
    <h2 class="text-xl font-bold text-center">Votos por Candidato</h2>

    <div id="chart-wrapper" class="flex justify-center items-center bg-gray-100 p-4 rounded-lg">
        <div class="w-1/3">
            <ul id="legend" class="text-left text-lg font-semibold"></ul>
        </div>
        <div class="w-2/3">
            <canvas id="votosChart" style="max-width: 90vw; max-height: 400px;"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('votosChart').getContext('2d');
            const labels = @json($labels);
            const data = @json($data);
            const backgroundColors = ['#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0', '#9966ff', '#ff9f40'];

            const votosChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total de Votos',
                        data: data,
                        backgroundColor: backgroundColors,
                        borderColor: '#fff',
                        borderWidth: 2,
                        hoverOffset: 10,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        datalabels: {
                            color: '#fff',
                            font: {
                                size: 10,
                                weight: 'bold'
                            },
                            
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Generar leyenda personalizada
            const legendContainer = document.getElementById("legend");
            legendContainer.innerHTML = labels.map((label, index) => {
                return `<li class="flex items-center mb-2">
                            <span class="inline-block w-4 h-4 mr-2 rounded-full" style="background-color: ${backgroundColors[index]};"></span>
                            <span class="text-gray-700 font-medium">${label}: ${data[index]} votos</span>
                        </li>`;
            }).join('');
        });
    </script>
</x-filament::page>