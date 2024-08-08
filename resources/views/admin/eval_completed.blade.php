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
                <form action="{{ route('evaluaciones_completas', ['hotelId' => $hotel->id]) }}" method="GET">
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-3">
                            <label for="status">Estado:</label>
                            <select name="status" id="status" class="form-control">
                                <option value="" {{ request('status') === 'todos' ? 'selected' : '' }}>Todos</option>
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
                    <i class="text-success fa fa-check-square fa-2x"></i>
                @else
                    <i class="text-warning fa fa-cog fa-spin fa-2x fa-fw"></i>
                    <span class="sr-only"></span>
                @endif
                <div class="card-body">
                    <h4>{{$evaluation->id}}</h4>
                    <h5 class="card-title">Fecha: {{ $evaluation->created_at->format('d-m-Y') }}</h5>
                </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> Ver detalles</button>
                    </div>
            </div>
        </div>
        @endforeach
    </div>
@stop

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
@stop