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
                <form role="form" action="{{ route('admin.guardar_configuracion') }}" method="POST">
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
                    <!-- /.box-footer -->
                </form>
                <script>
                    document.getElementById('guardarConfigBtn').addEventListener('click', function() {
                        var form = document.getElementById('configForm');
                        var formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            }
                            throw new Error('Error al guardar la configuración');
                        })
                        .then(data => {
                            // Redirigir a la página de mostrar preguntas
                            window.location.href = "{{ route('admin.question_config', ['hotelId' => $hotel->id]) }}";
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al guardar la configuración');
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
@stop
