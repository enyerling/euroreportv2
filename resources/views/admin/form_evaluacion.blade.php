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
                        @for ($i = 1; $i <= $pregunta['cantidad']; $i++)
                            <div class="form-group">
                                <label for="pregunta_{{ $index }}_{{ $pregunta['id'] }}_{{ $i }}">{{ $pregunta['name'] }} {{ $i }}</label>
                                <input type="hidden" name="sistemas[{{ $index }}][preguntas][{{ $pregunta['id'] }}][pregunta_id]" value="{{ $pregunta['id'] }}">
                                
                                @if ($pregunta['type'] === 'Cerrada')
                                    @if ($pregunta['answer'] === null)
                                        <select class="form-control" id="respuesta_{{ $index }}_{{ $pregunta['id'] }}_{{ $i }}" name="sistemas[{{ $index }}][preguntas][{{ $pregunta['id'] }}][respuesta]">
                                            <option value=" ">Por responder</option>
                                            <option value="si">Sí</option>
                                            <option value="no">No</option>
                                        </select>
                                    @elseif ($pregunta['answer'] === 'Fecha')
                                        <select class="form-control" id="respuesta_{{ $index }}_{{ $pregunta['id'] }}_{{ $i }}" name="sistemas[{{ $index }}][preguntas][{{ $pregunta['id'] }}][respuesta]">
                                            <option value=" ">Por responder</option>
                                            <option value="si">Sí</option>
                                            <option value="no">No</option>
                                        </select>
                                        <br>
                                        <input type="date" class="form-control" id="fecha_{{ $index }}_{{ $pregunta['id'] }}_{{ $i }}" name="sistemas[{{ $index }}][preguntas][{{ $pregunta['id'] }}][respuesta_fecha]">
                                    @endif
                                @endif
                            </div>
                        @endfor
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
