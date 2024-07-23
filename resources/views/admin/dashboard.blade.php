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
@if($hotels->isEmpty())
    <p>No hay hoteles disponibles.</p>
@else
    <div class="row">
        @foreach($hotels as $hotel)
        <div class="col-md-3">
            <div class="card text-white bg-dark  mb-3" style="max-width: 18rem;">
                <div class="card-body">
                    <img src="{{asset($hotel->image)}}" class="card-img-top" alt="hotel">
                    <br>
                    <h5 class="card-title">{{ $hotel->name }}</h5>
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary"><i class="fas fa-star"></i></button>
                    <button class="btn btn-sm btn-danger"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@stop

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
@stop
