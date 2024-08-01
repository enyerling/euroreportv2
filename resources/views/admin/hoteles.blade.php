@extends('adminlte::page')

@section('title', 'Hoteles')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endsection

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Hoteles</h1>
        <button class="btn btn-primary">Agregar hotel</button>
    </div>
@endsection

@section('content')
<div class="container">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Gerente</th>
                <th>Configurar evaluacion</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hotels as $hotel)
            <tr>
                <td>{{ $hotel->name }}</td>
                <td>{{ $hotel->manager }}</td>
                <td>
                <a class="btn btn-secondary"  href="{{ route('hotel_config', ['hotelId' => $hotel->id]) }}" > <i class="fas fa-cog"></i>Configuracion</a>  
                </td>
            </tr>
            @endforeach
        </tbody>
        
    </table>
    
</div>

@endsection


@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
@endsection