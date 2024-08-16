@extends('adminlte::page')

@section('title', 'Usuarios')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endsection

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h2>Usuarios</h2>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addHotelModal">
            <i class="fas fa-plus"></i> Agregar usuario
        </button>
    </div>
@endsection

@section('content')
<div class="container">
    <div class="card">
            <div class="card-tools">
                <br>
                <div class="input-group input-group-sm ml-auto" style="width: 250px;">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar...">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered mb-0">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Hotel</th>
                            <th>Rol</th>
                            <th>Operaciones</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach($users as $user)
                        <tr>
                            <td class="align-middle">{{ $user->name }}</td>
                            <td class="align-middle">{{ $user->email }}</td>
                            <td class="align-middle">{{ $user->hotel_id }}</td>
                            <td>{{ $user->getRoleNames()->join(', ') }}</td>
                            <td class="text-center align-middle">
                                <a class="btn btn-secondary btn-sm" href="" title="Configurar hotel">
                                    <i class="fa fa-cog"></i>
                                </a>
                                <button class="btn btn-primary btn-sm" onclick="" title="Editar usuario">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <a class="btn btn-danger btn-delete btn-sm" href="#" data-id="" title="Eliminar usuario">
                                    <i class="fa fa-trash"></i> 
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{$users->links('vendor.adminlte.pagination') }}
            </div>
        </div>
    </div>
</div>



@endsection

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
        
@endsection
