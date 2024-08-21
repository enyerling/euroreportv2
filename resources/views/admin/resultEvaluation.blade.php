@extends('adminlte::page')

@section('title', 'Puntaje Evaluación')

@section('content')
<div class="container">
    <br>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center mb-0">Resultados de la Evaluación</h2>
        
        @role('admin|subadmin')
        <a href="{{ route('admin.observations', ['record_evaluation_id' => $recordId]) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>Observaciones
        </a>
        @endrole
        
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

    <!-- Sección de Observaciones -->
    @if($observations->isNotEmpty())
    <div class="card card-dark mb-4">
        <div class="card-header">
            <h3 class="card-title">Observaciones</h3>
        </div>
        <div class="card-body">
            <ul class="list-group">
                @foreach($observations as $observation)
                    <li class="list-group-item">{{ $observation->answer }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <center>
    <button type="button" class="btn btn-primary" onclick="exportToPDF()">
        <i class="fas fa-file-pdf"></i> Exportar a PDF
    </button>
    </center>
</div>

<br>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
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
                            'rgb(13, 110, 253)','rgb(102, 16, 242)','rgb(111, 66, 193)','rgb(214, 51, 132)',
                            'rgb(220, 53, 69)','rgb(253, 126, 20)','rgb(255, 193, 7)','rgb(25, 135, 84)',
                            'rgb(32, 201, 151)','rgb(13, 202, 240)','rgb(244, 164, 96)','rgb(175, 238, 238)',
                            'rgb(102, 205, 170)','rgb(233, 150, 122)','rgb(240, 230, 140)','rgb(230, 230, 250)',
                            'rgb(106, 90, 205)','rgb(100, 149, 237)'
                        ],
                        borderColor: [
                            'rgb(13, 110, 253)','rgb(102, 16, 242)','rgb(111, 66, 193)','rgb(214, 51, 132)',
                            'rgb(220, 53, 69)','rgb(253, 126, 20)','rgb(255, 193, 7)','rgb(25, 135, 84)',
                            'rgb(32, 201, 151)','rgb(13, 202, 240)','rgb(244, 164, 96)','rgb(175, 238, 238)',
                            'rgb(102, 205, 170)','rgb(233, 150, 122)','rgb(240, 230, 140)','rgb(230, 230, 250)',
                            'rgb(106, 90, 205)','rgb(100, 149, 237)'
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

    // Variables de Blade pasadas al script
    
    const logoData = 'data:image/png;base64,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'; 
    const hotelName = "{{ $hotelName }}";
    const managerName = "Gerente: {{ $managerName }}";
    const issueDate = "Fecha de Expedición: {{ $issueDate }}";

    function exportToPDF() {
        html2canvas(document.querySelector(".container")).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jspdf.jsPDF('p', 'mm', 'a4');

            pdf.addImage(logoData, 'PNG', 10, 10, 30, 30);

            pdf.setFontSize(10);
            pdf.text(hotelName, 50, 15); 
            pdf.text(managerName, 50, 25); 
            pdf.text(issueDate, 50, 35); 

            const imgWidth = 210;
            const pageHeight = 297;
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;

            let position = 0;

            pdf.addImage(imgData, 'PNG', 0, 50, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            pdf.save('resultado_evaluacion.pdf');
        });
    }
</script>

@stop
