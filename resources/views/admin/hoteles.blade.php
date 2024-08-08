@extends('adminlte::page')

@section('title', 'Hoteles')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endsection

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h2>Hoteles</h2>
        <button class="btn btn-primary">Agregar hotel</button>
    </div>
@endsection

@section('content')
<div class="container mt-2">
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="thead-dark">
                <tr class="text-center">
                    <th>NOMBRE</th>
                    <th>GERENTE</th>
                    <th>OPERACIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hotels as $hotel)
                <tr>
                    <td>{{ $hotel->name }}</td>
                    <td>{{ $hotel->manager }}</td>
                    <td class="text-center">
                        <a class="btn btn-info" href="{{ route('hotel_config', ['hotelId' => $hotel->id]) }}">
                            <i class="fa fa-cog"></i>
                        </a> 
                        <a class="btn btn-warning" href="">
                            <i class="fa fa-edit"></i> 
                        </a> 
                        <a class="btn btn-danger" href="#">
                        <i class="fa fa-trash"></i> 
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- Paginación --}}
    <div class="d-flex justify-content-center">
        {{ $hotels->links('vendor.adminlte.pagination') }}
    </div>
</div>


@endsection


@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
@endsection