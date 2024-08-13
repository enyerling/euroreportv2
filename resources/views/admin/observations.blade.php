@extends('adminlte::page')
@section('title', 'Observations')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop


@section('content_header')
  
    
@stop

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white text-center">
            <h4 class="mb-0">Observaciones Adicionales</h4>
        </div>
        <div class="card-body">
        <form method="POST" action="{{ route('admin.guardar_observations') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="record_evaluation_id" value="{{ $record_evaluation_id }}">

            <div class="form-group">
                <label for="observaciones" class="font-weight-bold">Observaciones Adicionales</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="4" placeholder="Ingrese cualquier observación adicional..."></textarea>
            </div>

            <div class="form-group">
                <label for="imagenes" class="font-weight-bold">Subir Imágenes o Tomar Foto</label>
                <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="imagenes" name="imagenes[]" accept="image/*" multiple>
                    <label class="custom-file-label" for="imagenes">Seleccionar archivos</label>
                </div>
                <small class="form-text text-muted">Puedes seleccionar múltiples imágenes o tomar una foto con tu cámara.</small>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">Tomar una Foto</label>
                <input type="file" accept="image/*" capture="environment" class="form-control-file" id="tomarFoto" name="imagenes[]">
            </div>

            <div class="row mt-3" id="preview"></div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>

        <script>
            document.getElementById('imagenes').addEventListener('change', function(event) {
                const preview = document.getElementById('preview');
                preview.innerHTML = ''; // Clear previous previews

                for (const file of event.target.files) {
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.classList.add('img-thumbnail');
                            img.style.width = '150px';
                            img.style.margin = '5px';
                            preview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });

            document.getElementById('tomarFoto').addEventListener('change', function(event) {
                const preview = document.getElementById('preview');
                preview.innerHTML = ''; // Clear previous previews

                if (event.target.files.length > 0) {
                    const file = event.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.classList.add('img-thumbnail');
                            img.style.width = '150px';
                            img.style.margin = '5px';
                            preview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });
        </script>

        </div>
    </div>
</div>

@endsection

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
@stop
