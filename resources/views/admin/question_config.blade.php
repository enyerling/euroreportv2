@extends('adminlte::page')
@section('title', 'Preguntas Configuracion')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop


@section('content_header')
@stop

@section('content')
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Configuración de Preguntas</h4>
                </div>
                <div class="card-body">
                    <form role="form" action="{{ route('guardar_preguntas') }}" method="POST" id="configForm">
                        @csrf
                        <input type="hidden" name="hotel_id" value="{{ $hotelId }}">

                        @foreach($hotelSystems as $hotelSystem)
                            @if($hotelSystem->system)
                                <div class="card my-3">
                                    <div class="card-header">
                                        <h5><strong>{{ $hotelSystem->system->name }}:</strong></h5>
                                    </div>
                                    <div class="card-body">
                                        @if($hotelSystem->system->id === 12)
                                            @if(isset($questionsGrouped[$hotelSystem->system->id]))
                                                @foreach($questionsGrouped[$hotelSystem->system->id] as $accessorieId => $questions)
                                                    <br>
                                                    <h6><strong>{{ $accessories[$accessorieId] }}</strong></h6>
                                                    <ul class="list-group" id="accessorie_{{ $accessorieId }}">
                                                    <button type="button" class="btn btn-secondary btn-sm mb-2" onclick="selectAll('accessorie_{{ $accessorieId }}')">Seleccionar Todas</button>
                                                        @foreach($questions as $question)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="question_{{ $question->id }}" name="selected_questions[{{ $question->id }}][question_id]" value="{{ $question->id }}">
                                                                    <label class="form-check-label" for="question_{{ $question->id }}">{{ $question->name }}</label>
                                                                </div>
                                                                <input type="number" class="form-control" name="selected_questions[{{ $question->id }}][cantidad]" value="1" min="1" style="width: 80px;">
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endforeach
                                            @else
                                                <p>No questions available.</p>
                                            @endif
                                        @else
                                            <ul class="list-group" id="system_{{ $hotelSystem->system->id }}">
                                            <button type="button" class="btn btn-secondary btn-sm mb-2" onclick="selectAll('system_{{ $hotelSystem->system->id }}')"><i class="fa fa-check-square text-black"></i>  Seleccionar Todas</button>
                                                @foreach($questionsGrouped[$hotelSystem->system->id] as $question)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="question_{{ $question->id }}" name="selected_questions[{{ $question->id }}][question_id]" value="{{ $question->id }}">
                                                            <label class="form-check-label" for="question_{{ $question->id }}">{{ $question->name }}</label>
                                                        </div>
                                                        <input type="number" class="form-control" name="selected_questions[{{ $question->id }}][cantidad]" value="1" min="1" style="width: 80px;">
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <p>System data not available.</p>
                            @endif
                        @endforeach

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary" id="guardarConfigBtn">Guardar Configuración</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Modal para configuracion preguntas-->
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
                <p>La configuración de preguntas ha sido guardada con éxito.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="modalContinuarBtn">Continuar</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        function selectAll(listId) {
            var checkboxes = document.querySelectorAll('#' + listId + ' .form-check-input');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true; 
            });
        }
    </script>
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
                        window.location.href = "{{ route('admin.dashboard') }}";
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

