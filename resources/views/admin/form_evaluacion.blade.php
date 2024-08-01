@extends('adminlte::page')
@section('title', 'Evaluacion')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop


@section('content_header')
  
    
@stop

@section('content')
<div class="container">
    @foreach($hotelSystems as $hotelSystem)
        @for ($i = 0; $i < $hotelSystem->cantidad; $i++)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Sistema: {{ $hotelSystem->system->nombre }}</h5>
                    <!-- Aquí puedes mostrar más información sobre el sistema si es necesario -->
                </div>
            </div>
        @endfor
    @endforeach
    <h2>Preguntas Disponibles</h2>
    @foreach($preguntasDisponibles as $preguntaDisponible)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Pregunta: {{ $preguntaDisponible->name }}</h5>
                <p class="card-text">Sistema: {{ $preguntaDisponible->system->nombre }}</p>
                <!-- Puedes incluir un checkbox para seleccionar esta pregunta si es necesario -->
            </div>
        </div>
    @endforeach

    <form action="" method="POST">
        @csrf
        <input type="hidden" name="hotel_id" value="{{ $hotelId }}">
        <!-- Aquí puedes agregar campos adicionales según sea necesario para guardar la configuración -->

        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
    </form>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
@stop
