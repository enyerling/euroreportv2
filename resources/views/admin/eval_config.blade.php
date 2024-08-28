@extends('adminlte::page')
@section('title', 'Evaluacion Configuracion')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop


@section('content_header')
  
    
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                  <center>
                    <br>
                    <h4 class="box-title">{{ $hotel->name }}</h4>
                  </center>
                </div>
                <!-- /.box-header -->
                <form role="form" action="{{ route('admin.guardar_configuracion') }}" method="POST" id="configForm">
                    @csrf
                    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="sistemas"></label>
                            @foreach($sistemas as $sistema)
                                <div class="form-group">
                                <input type="hidden" name="sistemas[{{ $sistema->id }}][sistema_id]" value="{{ $sistema->id }}">
                                    <label>{{ $sistema->name }}:</label>
                                    <select class="form-control" name="sistemas[{{ $sistema->id }}][cantidad]">
                                        @for($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- /.box-body -->
                     <br>
                    <div class="box-footer">
                      <center>
                        <button type="submit" class="btn btn-primary" id="guardarConfigBtn">Guardar Configuración</button>
                      </center>
                    </div>
                    <br>
                </form>
            </div>
        </div>
    </div>
</div>

<!--Modal para configuracion-->
<div class="modal fade" id="configuracionModal" tabindex="-1" role="dialog" aria-labelledby="configuracionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 8px;">
        <div class="modal-header">
            <h5 class="modal-title" id="configuracionModalLabel">Configuración Guardada</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body text-center">
            <div class="mb-3" style="font-size: 40px; color: #28a745;">
                <i class="fas fa-check-circle"></i>
            </div>
                <p>La configuración de sistemas ha sido guardada con éxito.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="modalContinuarBtn">Continuar</button>
            </div>
        </div>
    </div>
</div>



@endsection

@section('js')
    <script> 
        document.getElementById('guardarConfigBtn').addEventListener('click', function(event) {
            event.preventDefault(); 

            var form = document.getElementById('configForm'); 
            var formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al guardar la configuración');
                }
                return response.json(); 
            })
            .then(data => {
                if (data.success) {
                    // Muestra el modal si la configuración se guardó con éxito
                    $('#configuracionModal').modal('show');
                    document.getElementById('modalContinuarBtn').addEventListener('click', function() {
                        window.location.href = "{{ route('admin.question_config', ['hotelId' => $hotel->id]) }}";
                    });
                } else {
                    throw new Error(data.message || 'Error al guardar la configuración');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar la configuración');
            });
        });

    </script>
@stop
