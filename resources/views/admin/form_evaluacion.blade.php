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
    <form method="POST" action="{{ route('admin.guardar_evaluacion') }}">
        @csrf
        <input type="hidden" name="hotel_id" value="{{ $hotelId }}">

        @foreach ($preguntasPorSistema as $index => $item)
            <div class="card mt-3">
                <div class="card-header">
                    <h1 class="card-title text-primary"><b>SISTEMA: {{ $item['system'] }}</b></h1>
                </div>
                <div class="card-body">
                    <input type="hidden" name="sistemas[{{ $index }}][system_id]" value="{{ $item['system_id'] }}">

                    @if ($item['system_id'] == 12)
                        <div class="form-group">
                            <label for="numero_habitacion_{{ $index }}">Número de Habitación</label>
                            <input type="text" class="form-control" id="numero_habitacion_{{ $index }}" name="sistemas[{{ $index }}][numero_habitacion]">
                        </div>
                    @endif

                    @foreach ($item['preguntas'] as $preguntaIndex => $pregunta)
                        <div class="form-group">
                            <label for="pregunta_{{ $pregunta['id'] }}">{{ $pregunta['name'] }}</label>
                            <input type="hidden" name="sistemas[{{ $index }}][preguntas][{{ $preguntaIndex }}][question_id]" value="{{ $pregunta['id'] }}">
                            
                            @if ($pregunta['type'] === 'Cerrada' && $pregunta['answer'] === null)
                                <select class="form-control" id="respuesta_{{ $pregunta['id'] }}" name="sistemas[{{ $index }}][preguntas][{{ $preguntaIndex }}][respuesta]">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            @elseif ($pregunta['type'] === 'Cerrada' && $pregunta['answer'] === 'Fecha')
                                <select class="form-control" id="respuesta_{{ $pregunta['id'] }}" name="sistemas[{{ $index }}][preguntas][{{ $preguntaIndex }}][respuesta]">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                                <br>
                                <input type="date" class="form-control" id="fecha_{{ $pregunta['id'] }}" name="sistemas[{{ $index }}][preguntas][{{ $preguntaIndex }}][respuesta_fecha]">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Guardar Evaluación</button>
        </div>
    </form>
</div>

@endsection

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
@stop
