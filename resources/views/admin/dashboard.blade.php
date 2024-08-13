@extends('adminlte::page')
@section('title', 'Dashboard')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Hoteles</h1>
        <button class="btn btn-primary">Agregar hotel</button>
    </div>
@stop

@section('content')
@if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    <div class="row">
        @foreach($hotels as $hotel)
        <div class="col-md-3">
            <div class="card text-white bg-dark  mb-3" style="max-width: 18rem;">
            <small>Última Evaluación: {{ $hotelEvaluations[$hotel->id] }}</small>
                <div class="card-body">
                    <img src="{{asset($hotel->image)}}" class="card-img-top" alt="hotel">
                    <br><br>
                    <h5 class="card-title">{{ $hotel->name }}</h5>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.motrar_evaluacion', ['hotelId' => $hotel->id]) }}" class="btn btn-sm btn-primary" title="Realizar evaluación"><i class="fas fa-edit"></i></a>
                    <a href="{{ route('admin.evaluacioneshotel',['hotelId' => $hotel->id]) }}" class="btn btn-sm btn-secondary" title="Ver evaluaciones"><i class="fas fa-eye"></i></a>
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
