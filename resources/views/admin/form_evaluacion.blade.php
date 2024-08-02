@extends('adminlte::page')
@section('title', 'Evaluacion')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop


@section('content_header')
  
    
@stop

@section('content')
<div class="container">
    <br>
    <center><h2>Evaluación</h2></center>
    @foreach ($preguntasPorSistema as $item)
        <div class="card mt-3">
            <div class="card-header">
                <h1 class="card-title text-primary"><b>SISTEMA: {{ $item['system'] }}</b></h1>
            </div>
            <div class="card-body">
                <form>
                    @if ($item['system_id'] == 12)
                        <div class="form-group">
                            <label for="numero_habitacion">Número de Habitación</label>
                            <input type="text" class="form-control" id="valor" name="numero_habitacion">
                        </div>
                    @endif
                    @foreach ($item['preguntas'] as $pregunta)
                        <div class="form-group">
                            <label for="pregunta_{{ $pregunta['id'] }}">{{ $pregunta['name'] }}</label>
                            @if ($pregunta['type'] === 'Cerrada' && $pregunta['answer'] === null)
                                <select class="form-control" id="answer" name="respuesta_{{ $pregunta['id'] }}">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            @elseif ($pregunta['type'] === 'Cerrada' && $pregunta['answer'] === 'Fecha')
                                <select class="form-control" id="answer" name="respuesta_{{ $pregunta['id'] }}">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                                <br>
                                <input type="date" class="form-control" id="fecha" name="respuesta_fecha_{{ $pregunta['id'] }}">
                            @endif
                        </div>
                    @endforeach
                    
                </form>
            </div>
        </div>
    @endforeach
</div>

@endsection

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
@stop
