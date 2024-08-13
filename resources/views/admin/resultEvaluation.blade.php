@extends('adminlte::page')

@section('title', 'Puntaje Evaluación')

@section('content')
<div class="container">
    <br>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center mb-0">Resultados de la Evaluación</h2>
        <a href="{{ route('admin.observations', ['record_evaluation_id' => $recordId]) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>Observaciones
        </a>
    </div>

    <div class="alert alert-secondary text-center">
        <a>
        <h4>Puntaje Total: {{ number_format($totalScore, 2) }}  <a href="{{ route('admin.ver_evaluacion', ['recordId' => $recordId]) }}" class="btn btn-dark btn-sm float-right">
        <i class="fas fa-eye"></i> 
        </a></h4>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">Progreso por Sistema</h3>
                </div>
                <div class="card-body">
                    @foreach ($scoresBySystem as $systemId => $score)
                        <div class="progress-group mb-3">
                            <span class="progress-text">{{ $score['systemName'] }}</span>
                            <span class="float-right"><b>{{ $score['correctAnswers'] }}</b>/{{ $score['totalQuestions'] }}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar {{ $loop->index % 2 === 0 ? 'bg-primary' : 'bg-success' }}" style="width: {{ number_format(($score['correctAnswers'] / $score['totalQuestions']) * 100, 2) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">Distribución de Puntajes</h3>
                </div>
                <div class="card-body">
                    <canvas id="pieChart" style="height: 200px; max-height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">Detalles por Sistema</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped">
                        <thead>
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
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Datos para el gráfico de torta
        var pieLabels = @json(array_column($scoresBySystem, 'systemName'));
        var pieData = @json(array_column($scoresBySystem, 'score'));

        if (pieLabels.length > 0 && pieData.length > 0) {
            var ctxPie = document.getElementById('pieChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        label: 'Puntaje',
                        data: pieData,
                        backgroundColor: [
                            'rgb(13, 110, 253)',
                            'rgb(102, 16, 242)',
                            'rgb(111, 66, 193)',
                            'rgb(214, 51, 132)',
                            'rgb(220, 53, 69)',
                            'rgb(253, 126, 20)',
                            'rgb(255, 193, 7)',
                            'rgb(25, 135, 84)',
                            'rgb(32, 201, 151)',
                            'rgb(13, 202, 240)',
                            'rgb(244, 164, 96)',
                            'rgb(175, 238, 238)',
                            'rgb(102, 205, 170)',
                            'rgb(233, 150, 122)',
                            'rgb(240, 230, 140)',
                            'rgb(230, 230, 250)',
                            'rgb(106, 90, 205)',
                            'rgb(100, 149, 237)'
                        ],
                        borderColor: [
                            'rgb(13, 110, 253)',
                            'rgb(102, 16, 242)',
                            'rgb(111, 66, 193)',
                            'rgb(214, 51, 132)',
                            'rgb(220, 53, 69)',
                            'rgb(253, 126, 20)',
                            'rgb(255, 193, 7)',
                            'rgb(25, 135, 84)',
                            'rgb(32, 201, 151)',
                            'rgb(13, 202, 240)',
                            'rgb(244, 164, 96)',
                            'rgb(175, 238, 238)',
                            'rgb(102, 205, 170)',
                            'rgb(233, 150, 122)',
                            'rgb(240, 230, 140)',
                            'rgb(230, 230, 250)',
                            'rgb(106, 90, 205)',
                            'rgb(100, 149, 237)'
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
@stop
