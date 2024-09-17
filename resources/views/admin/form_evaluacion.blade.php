@extends('adminlte::page')
@section('title', 'Evaluacion')

@section('content_header')
    <h3><center>Evaluación</center></h3>
@stop

@section('content')
<div class="container">

    <form method="POST" action="{{ route('admin.guardar_evaluacion') }}" id="evaluationForm">
        @csrf
        <input type="hidden" name="hotel_id" value="{{ $hotelId }}">
        <input type="hidden" name="record_evaluation_id" id="recordEvaluationId" value="">
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
                            <input type="hidden" name="sistemas[{{ $index }}][system_id]" value="{{ $item['system_id'] }}">
                            <input type="hidden" name="sistemas[{{ $index }}][instance]" value="{{ $item['instance'] }}">

                            <!-- Campo de número de habitación si es necesario -->
                            @if ($item['system_id'] == 12)
                                <div class="form-group">
                                    <label for="numero_habitacion_{{ $index }}">Número de Habitación</label>
                                    <input type="text" class="form-control" id="numero_habitacion_{{ $index }}" name="sistemas[{{ $index }}][numero_habitacion]" value="{{ old('sistemas.' . $index . '.numero_habitacion') }}">
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
                                                <option value="si">Sí</option>
                                                <option value="no">No</option>
                                            </select>
                                            @if ($pregunta['answer'] === 'Fecha')
                                                <br>
                                                <input type="date" class="form-control" id="fecha_{{ $index }}_{{ $pregunta['id'] }}_{{ $i }}" name="sistemas[{{ $index }}][preguntas][{{ $pregunta['id'] }}][respuesta_fecha]" value="{{ old('sistemas.' . $index . '.preguntas.' . $pregunta['id'] . '.respuesta_fecha') }}">
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
            <button type="submit" class="btn btn-primary">Guardar Evaluación</button>
        </div>
    </form>
</div>

<!-- Modal de resultado de la evaluación -->
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
        // Mostrar modal al cargar la página si hay mensajes de estado
        $(document).ready(function() {
            var status = "{{ session('status') }}";
            var message = "{{ session('message') }}";
            var hotelId = "{{ session('hotelId') }}";

            $('#resultIcon').html(status === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-triangle"></i>');
            $('#resultIcon').css('color', status === 'success' ? '#28a745' : '#ffc107');
            $('#resultMessage').text(message);
            $('#resultModal').modal('show');

            // Redirige al usuario a la URL de evaluaciones del hotel cuando se cierre el modal
            $('#resultModal').on('hidden.bs.modal', function (e) {
                var url = "{{ route('admin.evaluacioneshotel', ['hotelId' => ':hotelId']) }}";
                url = url.replace(':hotelId', hotelId);
                window.location.href = url; 
            });
        });
    </script>
    @endif

<script>
    // Función para mostrar el modal con diferentes tipos de mensajes
     function showModal(type, message) {
            const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
            const resultIcon = document.getElementById('resultIcon');
            const resultMessage = document.getElementById('resultMessage');
            const closeButton = document.querySelector('#resultModal .btn-secondary');

            // Configura el ícono y el mensaje basado en el tipo
            if (type === 'success') {
                resultIcon.innerHTML = '<i class="fa fa-check-circle" style="color: green; font-size: 2em;"></i>';
                resultMessage.innerText = message;
            } else if (type === 'error') {
                resultIcon.innerHTML = '<i class="fa fa-exclamation-circle" style="color: red; font-size: 2em;"></i>';
                resultMessage.innerText = message;
            } else if (type === 'warning') {
                resultIcon.innerHTML = '<i class="fa fa-exclamation-triangle" style="color: orange; font-size: 2em;"></i>';
                resultMessage.innerText = message;
            }

            resultModal.show();
        }

        // Evento cuando el contenido del DOM se ha cargado
        document.addEventListener('DOMContentLoaded', () => {
            // Maneja el envío del formulario de evaluación
            document.getElementById('evaluationForm').addEventListener('submit', function (event) {
                event.preventDefault();
                const formData = new FormData(this);
                if (navigator.onLine) {
                    // Enviar formulario si hay conexión
                    this.submit();
                } else {
                    // Guardar en IndexedDB si está offline
                    saveFormOffline(formData);
                    showModal('warning', 'Estás offline. Los datos se guardaron localmente y se sincronizarán cuando vuelvas a estar en línea.');
                }
            });
                // Maneja el evento cuando se vuelve a estar en línea
                window.addEventListener('online', () => {
                    console.log('Conexión restaurada. Intentando sincronizar datos...');
                    syncOfflineData();
                });
        });

        // Función para guardar los datos del formulario en IndexedDB
        function saveFormOffline(formData) {
            let data = {
                _token: formData.get('_token'),
                hotel_id: formData.get('hotel_id'),
                sistemas: []
            };

            let sistemas = document.querySelectorAll('.tab-pane');
            sistemas.forEach((sistema, index) => {
                let systemData = {
                    system_id: formData.get(`sistemas[${index}][system_id]`),
                    instance: formData.get(`sistemas[${index}][instance]`),
                    numero_habitacion: formData.get(`sistemas[${index}][numero_habitacion]`) || null,
                    preguntas: {}
                };

                // Recolectar preguntas para este sistema
                let preguntas = sistema.querySelectorAll('[name^="sistemas[' + index + '][preguntas]"]');
                preguntas.forEach(pregunta => {
                    // Ajustar la expresión regular para extraer el ID de la pregunta
                    let match = pregunta.name.match(/sistemas\[\d+\]\[preguntas\]\[(\d+)\]/);
                    let preguntaId = match ? match[1] : null;

                    if (preguntaId) {
                        if (!systemData.preguntas[preguntaId]) {
                            systemData.preguntas[preguntaId] = {
                                pregunta_id: preguntaId,
                                respuesta: null,
                                respuesta_fecha: null
                            };
                        }

                        if (pregunta.type === 'select-one' || pregunta.type === 'text') {
                            systemData.preguntas[preguntaId].respuesta = pregunta.value || null;
                        } else if (pregunta.type === 'date') {
                            systemData.preguntas[preguntaId].respuesta_fecha = pregunta.value || null;
                        }
                    }
                });

                data.sistemas.push(systemData);
            });

            //// Abre la base de datos IndexedDB y guarda los datos
            openDatabase().then((db) => {
                const tx = db.transaction('pendingEvaluations', 'readwrite');
                const store = tx.objectStore('pendingEvaluations');
                store.put(data); 
                tx.oncomplete = () => {
                    console.log('Formulario guardado offline:', data);
                    showModal('warning', 'Datos guardados localmente. Serán sincronizados cuando se recupere la conexión.');
                };
                tx.onerror = (event) => {
                    console.error('Error al guardar el formulario:', event.target.error);
                    showModal('error', 'Error al guardar los datos localmente.');
                };
            });
        }
    
    // Función para sincronizar datos almacenados localmente con el servidor cuando se restaura la conexión
    function syncOfflineData() {
        openDatabase().then((db) => {
            const tx = db.transaction('pendingEvaluations', 'readonly');
            const store = tx.objectStore('pendingEvaluations');
            const request = store.getAll();

            request.onsuccess = (event) => {
                const evaluations = event.target.result;
                evaluations.forEach((evaluation) => {
                    fetch('/save/evaluation', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': evaluation._token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(evaluation)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Mostrar modal de éxito
                        showModal('success', data.message || 'Datos sincronizados con éxito.');

                        // Eliminar datos sincronizados de IndexedDB
                        const deleteTx = db.transaction('pendingEvaluations', 'readwrite');
                        const deleteStore = deleteTx.objectStore('pendingEvaluations');
                        deleteStore.delete(evaluation.id);
                        deleteTx.oncomplete = () => {
                            console.log('Datos eliminados de IndexedDB tras la sincronización.');
                        };
                        deleteTx.onerror = (event) => {
                            console.error('Error al eliminar los datos:', event.target.error);
                        };
                    })
                    .catch(error => {
                        // Mostrar modal de error
                        showModal('error', error.message || 'Error al sincronizar datos con el servidor.');
                    });
                });
            };

            request.onerror = (event) => {
                console.error('Error al obtener datos de IndexedDB:', event.target.error);
            };
        });
    }
        // Función para abrir y configurar la base de datos IndexedDB
        function openDatabase() {
            return new Promise((resolve, reject) => {
                const request = indexedDB.open('EvaluationsDB', 1);

                request.onupgradeneeded = (event) => {
                    const db = event.target.result;
                    if (!db.objectStoreNames.contains('pendingEvaluations')) {
                        db.createObjectStore('pendingEvaluations', { keyPath: 'id', autoIncrement: true });
                    }
                };

                request.onsuccess = (event) => {
                    resolve(event.target.result);
                };

                request.onerror = (event) => {
                    reject(event.target.error);
                };
            });
        }
    </script>
@stop