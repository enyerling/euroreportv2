@extends('adminlte::page')
@section('title', 'Editar Evaluacion')

@section('content_header')
    <h3><center>Editar evaluación</center></h3>
@stop

@section('content')
<div class="container">
    <div id="autoSaveMessage" style="display: none; position: fixed; bottom: 20px; right: 20px; background-color: yellow; color: black; padding: 10px 20px; border-radius: 5px; z-index: 1000;">
        Formulario guardado automáticamente.
    </div>
    <form method="POST" action="{{ route('admin.evaluacion_actualizar', $recordEvaluation->id) }}" id="evaluationForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="evaluationId" value="{{ $recordEvaluation->id }}">
        <input type="hidden" name="hotel_id" value="{{ $hotelId }}">

        <div id="accordionEvaluation">
            @foreach ($preguntasPorSistema as $index => $item)
                <div class="card">
                    <div class="card-header" id="heading-{{ $index }}" style="padding: 0;">
                        <h5 class="mb-0">
                            <!-- Botón para expandir/contraer el acordeón -->
                            <button class="btn btn-link {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-toggle="collapse"  data-target="#collapse-{{ $index }}"  aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"  aria-controls="collapse-{{ $index }}"  style="width: 100%; text-align: left; padding: 10px; border: none; background: #f8f9fa; display: flex; justify-content: space-between; align-items: center;">
                                <strong> {{ $item['system'] }} </strong>
                                <span class="accordion-icon">
                                    <i class="fas fa-chevron-down"></i>
                                </span>
                            </button>
                        </h5>
                    </div>

                    <div id="collapse-{{ $index }}" class="collapse" aria-labelledby="heading-{{ $index }}" data-parent="#accordionEvaluation">
                        <div class="card-body">
                            <!-- Campos del sistema -->
                            <input type="hidden" name="sistemas[{{ $index }}][system_id]" value="{{ $item['system_id'] }}">
                            <input type="hidden" name="sistemas[{{ $index }}][instance]" value="{{ $item['instance'] }}">

                            <!-- Campo de número de habitación si es necesario -->
                            @if ($item['system_id'] == 12)
                                <div class="form-group">
                                    <label for="numero_habitacion_{{ $index }}">Número de Habitación</label>
                                    <input type="text" class="form-control" id="numero_habitacion_{{ $index }}" name="sistemas[{{ $index }}][numero_habitacion]" value="{{ old('sistemas.' . $index . '.numero_habitacion', $item['numero_habitacion'] ?? '') }}">
                                </div>
                            @endif

                            <!-- Preguntas del sistema -->
                            @foreach ($item['preguntas'] as $preguntaIndex => $pregunta)
                                @for ($i = 1; $i <= $pregunta['cantidad']; $i++)
                                    <div class="form-group mt-3">
                                        @if($pregunta['accessorie_name'])
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
                                            <br>
                                            @if ($pregunta['date'] === 'Fecha')
                                                <input type="date" class="form-control" id="fecha_{{ $index }}_{{ $pregunta['id'] }}_{{ $i }}" name="sistemas[{{ $index }}][preguntas][{{ $pregunta['id'] }}][respuesta_fecha]" value="{{ old('sistemas.'.$index.'.preguntas.'.$pregunta['id'].'.respuesta_fecha', $pregunta['respuesta_fecha'] ?? '') }}">
                                            @endif
                                        @endif
                                    </div>
                                @endfor
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Botón de guardar al final del formulario -->
        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary" >Actualizar Evaluación</button>
        </div>
        <br>
    </form>
</div>

<!--Modal para despues de editar mostrar resultado-->
<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Resultado de la Evaluación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="resultIcon" class="text-center mb-3"></div>
                <p id="resultMessage" class="text-center"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
        $(document).ready(function() {

            // Controla el modal después de la edición
            @if (session('status'))
            var status = "{{ session('status') }}";
            var message = "{{ session('message') }}";
            var hotelId = "{{ session('hotelId') }}";

            $('#resultIcon').html(status === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-triangle"></i>');
            $('#resultIcon').css('color', status === 'success' ? '#28a745' : '#ffc107');
            $('#resultMessage').text(message);
            $('#resultModal').modal('show');

            $('#resultModal').on('hidden.bs.modal', function (e) {
                var url = "{{ route('admin.evaluacioneshotel', ['hotelId' => ':hotelId']) }}";
                url = url.replace(':hotelId', hotelId);
                window.location.href = url; 
            });
            @endif
        });

        $(document).ready(function() {
            var previousData = $('#evaluationForm').serialize();
            function autoSaveForm() {
                var currentData = $('#evaluationForm').serialize();
                if (currentData !== previousData) {  // Solo enviar si hay cambios
                    previousData = currentData;

                    $.ajax({
                        url: $('#evaluationForm').attr('action'),
                        method: 'POST',
                        data: currentData,
                        success: function(response) {
                            $('#autoSaveMessage').text('Formulario guardado automáticamente.').fadeIn().delay(5000).fadeOut();
                            console.log('Formulario guardado automáticamente.');
                        },
                        error: function(xhr) {
                            $('#autoSaveMessage').text('Error al guardar el formulario.').fadeIn().delay(5000).fadeOut();
                            console.error('Error al guardar el formulario.');
                        }
                    });
                }
            }

            setInterval(autoSaveForm, 60000); // 1 minuto
        });

    </script>
@stop
