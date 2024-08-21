@extends('adminlte::page')
@section('title', 'Editar Evaluacion')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('content')
<div class="container">
    <br>
    <center><h2>Editar Evaluación</h2></center>
    <form method="POST" action="{{ route('admin.evaluacion_actualizar', $recordEvaluation->id) }}" id="evaluationForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="evaluationId" value="{{ $recordEvaluation->id }}">
        <input type="hidden" name="hotel_id" value="{{ $hotelId }}">

        @foreach ($preguntasPorSistema as $index => $item)
            <div class="card mt-3">
                <div class="card-header">
                    <h1 class="card-title text-primary"><b>SISTEMA: {{ $item['system'] }}</b></h1>
                </div>
                <div class="card-body">
                    <input type="hidden" name="sistemas[{{ $index }}][system_id]" value="{{ $item['system_id'] }}">
                    <input type="hidden" name="sistemas[{{ $index }}][instance]" value="{{ $item['instance'] }}">
                    @if ($item['system_id'] == 12)
                        <div class="form-group">
                            <label for="numero_habitacion_{{ $index }}">Número de Habitación</label>
                            <input type="text" class="form-control" id="numero_habitacion_{{ $index }}" name="sistemas[{{ $index }}][numero_habitacion]" value="{{ old('sistemas.' . $index . '.numero_habitacion', $item['preguntas'][0]['room'] ?? '') }}">
                        </div>
                    @endif

                    @foreach ($item['preguntas'] as $preguntaIndex => $pregunta)
                        @for ($i = 1; $i <= $pregunta['cantidad']; $i++)
                            <div class="form-group">
                                @if($pregunta['accessorie_name'])
                                    <br>
                                    <h6><b>{{ $pregunta['accessorie_name'] }}</b></h6>
                                @endif
                                <label for="pregunta_{{ $index }}_{{ $pregunta['id'] }}_{{ $i }}">{{ $pregunta['name'] }} {{ $i }}</label>

                                <input type="hidden" name="sistemas[{{ $index }}][preguntas][{{ $pregunta['id'] }}][pregunta_id]" value="{{ $pregunta['id'] }}">
                                
                                @if ($pregunta['type'] === 'Cerrada')
                                    <select class="form-control" id="respuesta_{{ $index }}_{{ $pregunta['id'] }}_{{ $i }}" name="sistemas[{{ $index }}][preguntas][{{ $pregunta['id'] }}][respuesta]">
                                        <option value=" ">Por responder</option>
                                        <option value="si" {{ $pregunta['answer'] === 'si' ? 'selected' : '' }}>Sí</option>
                                        <option value="no" {{ $pregunta['answer'] === 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @if ($pregunta['answer'] === 'Fecha')
                                        <input type="date" class="form-control" id="fecha_{{ $index }}_{{ $pregunta['id'] }}_{{ $i }}" name="sistemas[{{ $index }}][preguntas][{{ $pregunta['id'] }}][respuesta_fecha]" value="{{ old('sistemas.'.$index.'.preguntas.'.$pregunta['id'].'.respuesta_fecha', $pregunta['respuesta_fecha'] ?? '') }}">
                                    @endif
                                @endif
                            </div>
                        @endfor
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary" id="guardarEvaluacionBtn">Actualizar Evaluación</button>
        </div>
        <br>
    </form>
</div>


<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Resultado de la Evaluación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3" id="resultIcon" style="font-size: 40px;">
                    <!-- Icono dinámico -->
                </div>
                <p id="resultMessage">Mensaje aquí</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <a href="{{ route('admin.evaluacioneshotel', ['hotelId' => $hotelId]) }}" class="btn btn-primary">Continuar</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#evaluationForm').submit(function(event) {
                event.preventDefault(); // Evita el envío estándar del formulario

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.faltan_preguntas) {
                            $('#resultIcon').css('color', '#ffc107'); // Amarillo para advertencia
                            $('#resultIcon').html('<i class="fas fa-exclamation-triangle"></i>');
                            $('#resultMessage').text('¡Atención! Recuerda que hay preguntas por responder.');
                        } else {
                            $('#resultIcon').css('color', '#28a745'); // Verde para éxito
                            $('#resultIcon').html('<i class="fas fa-check-circle"></i>');
                            $('#resultMessage').text('Evaluación editada con éxito.');
                        }

                        $('#resultModal').modal('show'); // Muestra el modal
                    },
                    error: function(xhr) {
                        console.error('Error en la solicitud:', xhr);
                    }
                });
            });
        });
    </script>

@stop
