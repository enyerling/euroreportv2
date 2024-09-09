@extends('adminlte::page')
@section('title', 'Editar Evaluacion')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('content')
    <div class="container">
        <br>
        <center><h2>Editar Evaluación</h2></center>
        <ul class="nav nav-pills mb-4" id="evaluationTabs" role="tablist">
            @foreach ($preguntasPorSistema as $index => $item)
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-{{ $index }}" data-toggle="tab" href="#section-{{ $index }}" role="tab" aria-controls="section-{{ $index }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                    {{ $item['system'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <form method="POST" action="{{ route('admin.evaluacion_actualizar', $recordEvaluation->id) }}" id="evaluationForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="evaluationId" value="{{ $recordEvaluation->id }}">
            <input type="hidden" name="hotel_id" value="{{ $hotelId }}">

            <div class="tab-content" id="evaluationTabsContent">
                @foreach ($preguntasPorSistema as $index => $item)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="section-{{ $index }}" role="tabpanel" aria-labelledby="tab-{{ $index }}">
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

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary" id="guardarEvaluacionBtn">Actualizar Evaluación</button>
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
    @if (session('status'))
    <script>
        $(document).ready(function() {
            // Obtiene los datos de sesión
            var status = "{{ session('status') }}";
            var message = "{{ session('message') }}";
            var hotelId = "{{ session('hotelId') }}";

            // Establece el ícono y el color del resultado basado en el estad
            $('#resultIcon').html(status === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-triangle"></i>');
            $('#resultIcon').css('color', status === 'success' ? '#28a745' : '#ffc107');
            $('#resultMessage').text(message);
            $('#resultModal').modal('show');

            // Evento que se dispara cuando el modal se oculta
            $('#resultModal').on('hidden.bs.modal', function (e) {
                var url = "{{ route('admin.evaluacioneshotel', ['hotelId' => ':hotelId']) }}";
                url = url.replace(':hotelId', hotelId);
                window.location.href = url; 
            });
        });

    </script>
    @endif

@stop
