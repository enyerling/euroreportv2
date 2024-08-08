@extends('adminlte::page')

@section('title', 'Puntaje Evaluacion')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endsection

@section('content')
<div class="container mt-4">
    <h2 class="text-center text-primary mb-4">Resultados de la Evaluación</h2>

    <div class="row mb-4">
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Sistema</th>
                        <th>Preguntas Totales</th>
                        <th>Respuestas Correctas</th>
                        <th>Puntaje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scoresBySystem as $systemId => $score)
                    <tr>
                        <td>{{ $score['systemName'] }}</td>
                        <td>{{ $score['totalQuestions'] }}</td>
                        <td>{{ $score['correctAnswers'] }}</td>
                        <td>{{ number_format($score['score'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="text-center text-success">Puntuación por Sistema</h3>
            <canvas id="barChart"></canvas>
        </div>
        <div class="col-md-6">
            <h3 class="text-center text-danger">Distribución de Puntajes</h3>
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Datos para el gráfico de barras
        var barLabels = @json(array_column($scoresBySystem, 'systemName'));
        var barData = @json(array_column($scoresBySystem, 'score'));

        if (barLabels && barData) {
            var ctxBar = document.getElementById('barChart').getContext('2d');
            var barChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [{
                        label: 'Puntaje',
                        data: barData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Datos para el gráfico de torta
        var pieLabels = @json(array_column($scoresBySystem, 'systemName'));
        var pieData = @json(array_column($scoresBySystem, 'score'));

        if (pieLabels && pieData) {
            var ctxPie = document.getElementById('pieChart').getContext('2d');
            var pieChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        label: 'Puntaje',
                        data: pieData,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.raw.toFixed(2);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
    
</script>
@endsection

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
@endsection