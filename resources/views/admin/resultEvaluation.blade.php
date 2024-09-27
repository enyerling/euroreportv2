@extends('adminlte::page')

@section('title', 'Puntaje Evaluación')

@section('content')
@if(session('success'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert" id="success-alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<div class="container">
    <br>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center mb-0">Resultados de la Evaluación</h2>
        
        @role('admin|subadmin')
        <div class="d-flex justify-content-start align-items-center mb-3">
            <a href="{{ route('admin.observations', ['record_evaluation_id' => $recordId]) }}" class="btn btn-primary mr-2">
                <i class="fas fa-plus"></i> Observaciones
            </a>
            <a class="btn btn-success" title="Enviar correo" onclick="exportToPDFAndSendEmail()">
                <i class="fa fa-envelope"></i>
            </a>
        </div>
        @endrole
        
    </div>

    <div class="alert alert-secondary text-center">
        <a>
        <h4>Puntaje Total: {{ $totalScore }}  <a href="{{ route('admin.ver_evaluacion', ['recordId' => $recordId]) }}" class="btn btn-dark btn-sm float-right" title="Ver mas detalles">
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
                                @php
                                    $correctAnswers = $score['correctAnswers'];
                                    $totalQuestions = $score['totalQuestions'];
                                    $percentage = ($correctAnswers / $totalQuestions) * 100;
                                    $progressColor = '';

                                    if ($percentage <= 35) {
                                        $progressColor = 'bg-danger';
                                    } elseif ($percentage > 35 && $percentage <= 70) {
                                        $progressColor = 'bg-warning'; 
                                    } else {
                                        $progressColor = 'bg-success'; 
                                    }
                                @endphp
                                <div class="progress-bar {{ $progressColor }}" style="width: {{ number_format($percentage, 2) }}%"></div>
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
        @foreach($observations as $observation)
            <div class="card card-dark mb-4">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Observaciones</h3>
                    <div class="ml-auto">
                        <form action="{{ route('admin.observations.destroy') }}" method="POST" style="margin-left: auto;">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="observation_id" value="{{ $observation->id }}">
                            @role('admin|subadmin')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fa fa-times"></i>
                                </button>
                            @endrole
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">{{ $observation->answer }}</li>
                    </ul>
                </div>
            </div>
        @endforeach
    @endif

    @if($images->isNotEmpty())
        <div class="card card-dark mb-4">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">Imagenes subidas de observaciones</h3>
            </div>
            <div class="card-body">
                <div class="row">
                @foreach($images as $image)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <form action="{{ route('admin.images.destroy') }}" method="POST" style="margin-left: auto;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="image_id" value="{{ $image->id }}">
                                    @role('admin|subadmin')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    @endrole
                            </form>
                            <img src="{{ asset('storage/' . $image->path) }}" class="card-img-top" alt="Imagen">
                        </div>
                    </div>
                    
                @endforeach
                </div>
            </div>
        </div>
    @endif

    <center>
    <button type="button" class="btn btn-primary" onclick="exportToPDF()">
        <i class="fas fa-file-pdf"></i> Exportar a PDF
    </button>
    </center>

    <!-- Modal -->
<div class="modal fade" id="emailSentModal" tabindex="-1" role="dialog" aria-labelledby="emailSentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="emailSentModalLabel">Correo Enviado</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        El PDF ha sido enviado correctamente por correo.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
</div>
<br>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
<script>
    //Grafico de torta 
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
    
    //Funcion oara exportar el contenido de la pagina en un pdf 
    async function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const logoUrl = '{{ asset('vendor/adminlte/dist/img/logo_1.png') }}';
        const currentDate = new Date().toLocaleDateString();

        // Crea una instancia de jsPDF
        const doc = new jsPDF();
        const pageWidth = doc.internal.pageSize.width;

        // Agregar logo
        doc.addImage(logoUrl, 'PNG', 10, 10, 30, 30); // Ajusta posición y tamaño del logo

        // Información del hotel
        doc.setFontSize(10);
        doc.text('Nombre del Hotel: {{ $hotelName }}', 50, 20);
        doc.text('Gerente: {{ $managerName }}', 50, 30);
        doc.text('Fecha de Expedición: ' + currentDate, 50, 40);

        // Agrega un título
        doc.setFontSize(20);
        const title = 'Resultados de la Evaluación';
        const titleWidth = doc.getTextWidth(title);
        doc.text(title, (pageWidth - titleWidth) / 2, 55); // Centrar título

        const score = {{ $totalScore }};
        const total = score.toFixed(2);
        // Agrega el puntaje total
        doc.setFontSize(20);
        doc.setFont('Arial', 'bold'); 
        doc.setTextColor(188,147,83);
        doc.text(`Puntaje Total: ${total}`, 14, 70); // Cambié la posición Y para más espacio

        doc.setFontSize(12);      // Aplicar tamaño normal
        doc.setFont('Helvetica', 'normal');   // Tipo de letra normal
        doc.setTextColor(0, 0, 0);  

        doc.setFontSize(16);
        const title2 = 'Detalles por Sistema';
        const title2Width = doc.getTextWidth(title2);
        doc.text(title2, (pageWidth - title2Width) / 2, 85);

        // Títulos de la tabla
        doc.setFontSize(12);
        const col = ["Sistema", "Preguntas Totales", "Respuestas Correctas", "Puntaje"];
        const rows = [];

        // Agregar datos a las filas
        @foreach ($scoresBySystem as $systemId => $score)
            rows.push(["{{ $score['systemName'] }}", "{{ $score['totalQuestions'] }}", "{{ $score['correctAnswers'] }}", "{{ number_format($score['score'], 2) }}"]);
        @endforeach

        // Configuración de la tabla
        doc.autoTable({
            head: [col],
            body: rows,
            startY: 90, // Y position to start the table
            theme: 'grid',
            headStyles: { fillColor: [45, 45, 45], textColor: [255, 255, 255] }// Color del encabezado
        });

        // Agregar Progreso por Sistema
        doc.addPage();
        doc.setFontSize(16);
        const title3 = 'Progreso por Sistema';
        const title3Width = doc.getTextWidth(title3);
        doc.text(title3, (pageWidth - title3Width) / 2, 20); // Centrar título

        doc.setFontSize(12);
        let yPosition = 30;

        // Definir variables para la barra de progreso
        const progressBarWidth = 150; // Ancho de la barra
        const progressBarHeight = 2;  // Alto de la barra

        @foreach ($scoresBySystem as $systemId => $score)
            @php
                $correctAnswers = $score['correctAnswers'];
                $totalQuestions = $score['totalQuestions'];
                $percentage = ($correctAnswers / $totalQuestions) * 100;
                // Definir el color basado en el porcentaje
                if ($percentage <= 35) {
                    $progressColor = 'red';
                } elseif ($percentage <= 70) {
                    $progressColor = 'yellow';
                } else {
                    $progressColor = 'green';
                }
            @endphp
            
            // Dibujar texto de progreso
            doc.setFontSize(10);
            doc.text(`{{ $score['systemName'] }}: {{ $score['correctAnswers'] }} / {{ $score['totalQuestions'] }}`, 14, yPosition);
            
            // Usar los valores de PHP en el JS
            doc.setFillColor("{{ $progressColor }}");
            doc.rect(14, yPosition + 2, ({{ $percentage }} / 100) * progressBarWidth, progressBarHeight, 'F'); // Dibujar la barra
            
            yPosition += 12; // Incrementar la posición Y para la siguiente barra
        @endforeach
        
        // Agregar leyenda visual al final de la página
        yPosition +=5; // Dar un poco de espacio antes de la leyenda

        doc.setFontSize(10);
        doc.setTextColor(0); // Negro para el texto

        // Dibujar la barra verde (progreso > 70%)
        doc.setFillColor(46, 182, 25); // Verde
        doc.rect(14, yPosition, 10, 5, 'F'); // Dibujar rectángulo verde
        doc.text('Progreso > 70%', 30, yPosition + 5); // Texto al lado de la barra

        // Dibujar la barra amarilla (36% - 70%)
        doc.setFillColor(255, 255, 0); // Amarillo
        doc.rect(14, yPosition + 10, 10, 5, 'F'); // Dibujar rectángulo amarillo
        doc.text('Progreso entre 36% y 70%', 30, yPosition + 15); // Texto al lado de la barra

        // Dibujar la barra roja (progreso < 35%)
        doc.setFillColor(255, 0, 0); // Rojo
        doc.rect(14, yPosition + 20, 10, 5, 'F'); // Dibujar rectángulo rojo
        doc.text('Progreso < 35%', 30, yPosition + 25); // Texto al lado de la barra

        doc.addPage();
        // Agregar Observaciones
        doc.setFontSize(16);
        const title4 = 'Observaciones';
        const title4Width = doc.getTextWidth(title4);
        doc.text(title4, (pageWidth - title4Width) / 2, 20);
        doc.setFontSize(12);

        yPosition = 30; // Posición inicial para el primer texto de observación

        @foreach($observations as $observation)
            // Agregar cada observación
            doc.text(`- {{ $observation->answer }}`, 14, yPosition);
            yPosition += 10; // Incrementar la posición Y para la siguiente observación
        @endforeach

        // Agregar un título para las imágenes
        doc.setFontSize(16);
        const imagesTitleY = yPosition + 10; // Posicionar título para imágenes
        const title5 = 'Imágenes de Observaciones';
        const title5Width = doc.getTextWidth(title5);
        doc.text(title5, (pageWidth - title5Width) / 2, imagesTitleY); // Título de imágenes
        let imageY = imagesTitleY + 10; // Posición inicial para las imágenes

        // Cargar todas las imágenes de manera asíncrona
        const imagePromises = @json($images).map(async (image) => {
            const imgSrc = "{{ asset('storage') }}" + "/" + image.path;  // Cambia esto según tu estructura
            const img = await loadImage(imgSrc);
            doc.addImage(img, 'JPEG', 14, imageY, 50, 50);
            imageY += 60; // Espacio entre imágenes
        });
        // Esperar a que todas las imágenes se carguen
        await Promise.all(imagePromises);
        // Guardar el PDF
        doc.save('Resultado_Evaluacion.pdf');
    }

    const recordId = @json($recordId); 
    async function exportToPDFAndSendEmail() {
        const { jsPDF } = window.jspdf;
        const logoUrl = '{{ asset('vendor/adminlte/dist/img/logo_1.png') }}';
        const currentDate = new Date().toLocaleDateString();

        // Crea una instancia de jsPDF
        const doc = new jsPDF();
        const pageWidth = doc.internal.pageSize.width;

        // Agregar logo
        doc.addImage(logoUrl, 'PNG', 10, 10, 30, 30);

        // Información del hotel
        doc.setFontSize(10);
        doc.text('Nombre del Hotel: {{ $hotelName }}', 50, 20);
        doc.text('Gerente: {{ $managerName }}', 50, 30);
        doc.text('Fecha de Expedición: ' + currentDate, 50, 40);

        // Agrega un título
        doc.setFontSize(20);
        const title = 'Resultados de la Evaluación';
        const titleWidth = doc.getTextWidth(title);
        doc.text(title, (pageWidth - titleWidth) / 2, 55);

        const score = {{ $totalScore }};
        const total = score.toFixed(2);
        doc.setFontSize(20);
        doc.setFont('Arial', 'bold'); 
        doc.setTextColor(188,147,83);
        doc.text(`Puntaje Total: ${total}`, 14, 70);

        doc.setFontSize(12);      
        doc.setFont('Helvetica', 'normal');   
        doc.setTextColor(0, 0, 0);

        // Detalles por Sistema
        doc.setFontSize(16);
        const title2 = 'Detalles por Sistema';
        const title2Width = doc.getTextWidth(title2);
        doc.text(title2, (pageWidth - title2Width) / 2, 85);

        // Títulos de la tabla
        doc.setFontSize(12);
        const col = ["Sistema", "Preguntas Totales", "Respuestas Correctas", "Puntaje"];
        const rows = [];

        // Agregar datos a las filas
        @foreach ($scoresBySystem as $systemId => $score)
            rows.push(["{{ $score['systemName'] }}", "{{ $score['totalQuestions'] }}", "{{ $score['correctAnswers'] }}", "{{ number_format($score['score'], 2) }}"]);
        @endforeach

        // Configuración de la tabla
        doc.autoTable({
            head: [col],
            body: rows,
            startY: 90,
            theme: 'grid',
            headStyles: { fillColor: [45, 45, 45], textColor: [255, 255, 255] }
        });

        // Agregar Progreso por Sistema
        doc.addPage();
        doc.setFontSize(16);
        const title3 = 'Progreso por Sistema';
        const title3Width = doc.getTextWidth(title3);
        doc.text(title3, (pageWidth - title3Width) / 2, 20);

        doc.setFontSize(12);
        let yPosition = 30;
        const progressBarWidth = 150;
        const progressBarHeight = 2;

        @foreach ($scoresBySystem as $systemId => $score)
            @php
                $correctAnswers = $score['correctAnswers'];
                $totalQuestions = $score['totalQuestions'];
                $percentage = ($correctAnswers / $totalQuestions) * 100;
                if ($percentage <= 35) {
                    $progressColor = 'red';
                } elseif ($percentage <= 70) {
                    $progressColor = 'yellow';
                } else {
                    $progressColor = 'green';
                }
            @endphp

            // Dibujar texto de progreso
            doc.setFontSize(10);
            doc.text(`{{ $score['systemName'] }}: {{ $score['correctAnswers'] }} / {{ $score['totalQuestions'] }}`, 14, yPosition);
            
            // Barra de progreso
            doc.setFillColor("{{ $progressColor }}");
            doc.rect(14, yPosition + 2, ({{ $percentage }} / 100) * progressBarWidth, progressBarHeight, 'F');
            yPosition += 12;
        @endforeach

         // Agregar leyenda visual al final de la página
         yPosition +=5; // Dar un poco de espacio antes de la leyenda

        doc.setFontSize(10);
        doc.setTextColor(0); // Negro para el texto

        // Dibujar la barra verde (progreso > 70%)
        doc.setFillColor(46, 182, 25); // Verde
        doc.rect(14, yPosition, 10, 5, 'F'); // Dibujar rectángulo verde
        doc.text('Progreso > 70%', 30, yPosition + 5); // Texto al lado de la barra

        // Dibujar la barra amarilla (36% - 70%)
        doc.setFillColor(255, 255, 0); // Amarillo
        doc.rect(14, yPosition + 10, 10, 5, 'F'); // Dibujar rectángulo amarillo
        doc.text('Progreso entre 36% y 70%', 30, yPosition + 15); // Texto al lado de la barra

        // Dibujar la barra roja (progreso < 35%)
        doc.setFillColor(255, 0, 0); // Rojo
        doc.rect(14, yPosition + 20, 10, 5, 'F'); // Dibujar rectángulo rojo
        doc.text('Progreso < 35%', 30, yPosition + 25); // Texto al lado de la barra

        // Agregar Observaciones
        doc.addPage();
        doc.setFontSize(16);
        const title4 = 'Observaciones';
        const title4Width = doc.getTextWidth(title4);
        doc.text(title4, (pageWidth - title4Width) / 2, 20);
        doc.setFontSize(12);

        yPosition = 30;
        @foreach($observations as $observation)
            doc.text(`- {{ $observation->answer }}`, 14, yPosition);
            yPosition += 10;
        @endforeach

        // Agregar Imágenes de Observaciones
        doc.setFontSize(16);
        const title5 = 'Imágenes de Observaciones';
        const title5Width = doc.getTextWidth(title5);
        doc.text(title5, (pageWidth - title5Width) / 2, yPosition + 10);

        let imageY = yPosition + 20;

        const imagePromises = @json($images).map(async (image) => {
            const imgSrc = "{{ asset('storage') }}" + "/" + image.path;
            const img = await loadImage(imgSrc);
            doc.addImage(img, 'JPEG', 14, imageY, 50, 50);
            imageY += 60;
        });

        await Promise.all(imagePromises);

        // Convertir PDF a Blob para enviar
        const pdfBlob = doc.output('blob');
        const formData = new FormData();
        formData.append('pdf', pdfBlob, 'Resultado_evaluacion.pdf');

        // Enviar el PDF al servidor
        fetch(`/sendresults/${recordId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#emailSentModal').modal('show');
            } else {
                alert('Error al enviar el PDF.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al enviar el PDF.');
        });
    }

     // Función para cargar imágenes
    function loadImage(url) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = () => reject(new Error('Error al cargar la imagen: ' + url));
            img.src = url;
        });
    }

    //Mostrar mensaje de exito por 3 segundos 
    document.addEventListener("DOMContentLoaded", function() {
    // Configura un temporizador de 3 segundos para ocultar la alerta
        setTimeout(function() {
                var alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show'); 
                alert.classList.add('fade');    
                setTimeout(function() {
                    alert.remove(); 
                }, 300); 
            }
        }, 2000); 
    });
    
</script>
@stop
