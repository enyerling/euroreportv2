@extends('adminlte::page')

@section('title', 'Ver Evaluación')


@section('content_header')
@stop
@section('content')
<div class="container">
    <div class="col-12"><br>
        <h2 class="text-center mb-4">Detalles de la Evaluación</h2>
    </div>
    <div class="card">
        <div class="card-header">
            <button type="button" class="btn btn-primary" onclick="exportToPDF()">
                <i class="fas fa-file-pdf"></i> Exportar a PDF
            </button>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar...">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table id="evaluationsTable" class="table table-hover text-nowrap table-bordered table-sm">
                <thead class="thead-dark">
                <tr>
                    <th>Sistema</th>
                    <th>Pregunta</th>
                    <th>Respuesta</th>
                    <th>Fecha</th>
                    <th>Habitación</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($evaluations as $evaluation)
                <tr>
                    <td>{{ $evaluation->system->name }}</td>
                    <td>{{ $evaluation->question->name }}</td>
                    <td>{{ $evaluation->answer }}</td>
                    <td>{{ $evaluation->date }}</td>
                    <td>{{ $evaluation->room ?? 'N/A' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                
            </div>
            <div class="float-right">
                <button type="button" class="btn btn-dark" onclick="window.history.back();">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
            </div>
        </div>
    </div>
    
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>

<script>
    //Filtrado de busqueda de la tabla 
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('evaluationsTable');
        const tableBody = table.getElementsByTagName('tbody')[0];

        searchInput.addEventListener('input', function () {
            const filter = searchInput.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');

            Array.from(rows).forEach(row => {
                const cells = row.getElementsByTagName('td');
                let match = false;

                Array.from(cells).forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(filter)) {
                        match = true;
                    }
                });

                row.style.display = match ? '' : 'none';
            });
        });
    });

    //Funcion para exportar el contenido de la pagina en un documento pdf 
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const logoUrl = '{{ asset('vendor/adminlte/dist/img/logo_1.png') }}';

        const currentDate = new Date().toLocaleDateString();

        html2canvas(document.getElementById('evaluationsTable')).then(canvas => {
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgWidth = 190; // Ajusta el ancho de la imagen de la tabla
            const pageHeight = pdf.internal.pageSize.height;
            const pageWidth = pdf.internal.pageSize.width;
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            const bottomMargin = 40;

            let position = 65; // Posición inicial para la tabla

            pdf.setFontSize(12);
            pdf.setTextColor(40);

            // Agregar el logo
            pdf.addImage(logoUrl, 'PNG', 10, 10, 30, 30); // Ajusta posición y tamaño del logo

            // Encabezado
            pdf.setFontSize(10);
            pdf.text('Nombre del Hotel: {{ $hotelName }}', 50, 20);
            pdf.text('Gerente: {{ $managerName }}', 50, 30);
            pdf.text('Fecha de Expedición: ' + currentDate, 50, 40);

            // Título del contenido
            pdf.setFontSize(16);
            const title = 'Detalles de la Evaluación';
            const titleWidth = pdf.getTextWidth(title);
            pdf.text(title, (pageWidth - titleWidth) / 2, 55);

            // Agregar la primera parte de la tabla al PDF
            pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= (pageHeight - position);

            while (heightLeft > 0) {
                position = heightLeft - imgHeight- bottomMargin;
                pdf.addPage();
                pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            pdf.save('Detalles_de_la_evaluacion.pdf');
        });
    }
</script>
@endsection
