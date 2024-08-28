@extends('adminlte::page')
@section('title', 'Home')

@section('css')
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Hoteles</h1>
    </div>
@stop

@section('content')
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
                    @role('admin|subadmin')
                    <a href="{{ route('admin.motrar_evaluacion', ['hotelId' => $hotel->id]) }}" class="btn btn-sm btn-primary" title="Realizar evaluación"><i class="fas fa-edit"></i></a>
                    @endrole
                    <a href="{{ route('admin.evaluacioneshotel',['hotelId' => $hotel->id]) }}" class="btn btn-sm btn-secondary" title="Ver evaluaciones"><i class="fas fa-eye"></i></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

@stop
