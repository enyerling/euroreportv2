@extends('adminlte::page')

@section('title', 'Puntaje Evaluación')

@section('content')
    @if(session('success'))
        <div id="successMessage" class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
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
                    <div style="position: relative; height: 400px; width: 100%;">
                        <canvas id="pieChart"></canvas>
                    </div>
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
                    maintainAspectRatio: false,
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
                },
                layout: {
                    padding: 20
                }
            });
        }
    });
    
    const hotelName = "{{ $hotelName }}";
    const managerName = "Gerente: {{ $managerName }}";
    const issueDate = "Fecha de Expedición: {{ $issueDate }}";

    function exportToPDF() {
        html2canvas(document.querySelector(".container")).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jspdf.jsPDF('p', 'mm', 'a4');

            const logoUrl = '{{ asset('vendor/adminlte/dist/img/logo_1.png') }}';
            const margin = 10; // Definir el tamaño del margen en puntos

            pdf.addImage(logoUrl, 'PNG', margin, margin, 30, 30);

            pdf.setFontSize(10);
            pdf.text(hotelName, 50, margin + 5); 
            pdf.text(managerName, 50, margin + 15); 
            pdf.text(issueDate, 50, margin + 25); 

            const imgWidth = 210 - 2 * margin; // Restar los márgenes de la anchura de la página
            const pageHeight = 297 - 4 * margin; // Restar los márgenes de la altura de la página
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;

            let position = 0;

            pdf.addImage(imgData, 'PNG', margin, margin + 40, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', margin, position + margin, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            pdf.save('Resultado_evaluacion.pdf');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Selecciona el mensaje de éxito
        const successMessage = document.getElementById('successMessage');
            
        // Si el mensaje existe, ocúltalo después de 5 segundos
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.opacity = '0'; // Desvanecer el mensaje
                setTimeout(() => {
                    successMessage.style.display = 'none'; // Ocultar el mensaje completamente
                }, 1000); // Tiempo de desvanecimiento
            }, 5000); // Tiempo para mostrar el mensaje
        }
    });
</script>


@stop
