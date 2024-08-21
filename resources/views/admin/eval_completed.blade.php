@extends('adminlte::page')
@section('title', 'ListaEvaluaciones')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('content_header')
    <h1>{{ $hotel->name }}</h1>
@stop

@section('content')
    <center><h2>Evaluaciones</h2></center><br>
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-md-12">
                <form action="{{ route('admin.evaluacioneshotel', ['hotelId' => $hotel->id]) }}" method="GET">
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-3">
                            <label for="status">Estado:</label>
                            <select name="status" id="status" class="form-control">
                                <option value="" {{ request('status') === 'todos' ? 'selected' : '' }}>Todas</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Completadas</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Incompletas</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="fecha_inicio">Fecha Inicio:</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="fecha_fin">Fecha Fin:</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                        </div>

                        <div class="form-group col-md-3 text-right">
                            <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="row">
        @foreach($completedEvaluations as $evaluation)
        <div class="col-md-3 col-sm-4">
            <div class="card text-white bg-dark mb-3">
                @if ($evaluation->status === '1')
                    <div class="d-flex justify-content-between align-items-start">
                        <i class="text-success fa fa-check-square fa-2x"></i>
                        <a href="{{ route('enviar.resultados', ['evaluationId' => $evaluation->id]) }}" class="btn btn-sm btn-dark" title="Enviar correo">
                            <i class="fa fa-envelope "></i>
                        </a>
                    </div>
                @else
                    <div class="d-flex justify-content-between align-items-start">
                        <i class="text-warning fa fa-cog fa-spin fa-2x fa-fw"></i>
                        <span class="sr-only"></span>
                    </div>
                @endif
                <div class="card-body">
                    <h4>ID: {{$evaluation->id}}</h4>
                    <h5 class="card-title">Fecha: {{ $evaluation->created_at->format('d-m-Y') }}</h5>
                </div>
                    <div class="btn-group">
                        <a href="{{ route('admin.detalles_evaluacion', ['evaluationId' => $evaluation->id]) }}" class="btn btn-sm btn-secondary" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        @role('admin|subadmin')
                        <a href="{{ route('admin.evaluacion_editar', ['evaluationId' => $evaluation->id]) }}" class="btn btn-sm btn-primary" title="Editar evaluacion">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a class="btn btn-danger btn-delete btn-sm" href="#" data-id="{{ $evaluation->id }}" title="Eliminar evaluación">
                            <i class="fa fa-trash"></i> 
                        </a>
                        @endrole
                    </div>

            </div>
        </div>
        @endforeach
    </div>
<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar esta evaluacion? Esta acción eliminará todos los registros asociados.
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop


@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
       document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.btn-delete');
                
            deleteButtons.forEach(button => {
                   button.addEventListener('click', function () {
                    const evaluationId = this.getAttribute('data-id');
                    const form = document.getElementById('deleteForm');
                        
                        // Configura la URL de acción del formulario
                    form.action = `/delete/evaluations/${evaluationId}`; 
                        
                        // Muestra el modal
                    $('#deleteModal').modal('show');
                });
            });
        });
    </script>
@stop