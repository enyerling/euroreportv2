@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Usuarios</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
            <i class="fas fa-plus"></i> Agregar usuario
        </button>
    </div>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert" id="success-alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
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
                            <td class="align-middle">{{ $user->hotel ? $user->hotel->name : 'N/A' }}</td>
                            <td>{{ $user->getRoleNames()->join(', ') }}</td>
                            <td class="text-center align-middle">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editUserModal-{{ $user->id }}" title="Editar usuario">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteUserModal-{{ $user->id }}" title="Eliminar usuario">
                                    <i class="fa fa-trash"></i>
                                </button>
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

<!-- Modal para agregar usuario-->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Agregar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Ingrese el nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Emai</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Ingrese el correo electrónico" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Ingrese la contraseña" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Rol</label>
                        <select name="role" id="role" class="form-control" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hotel_id">Hotel</label>
                        <select name="hotel_id" id="hotel_id" class="form-control" required>
                            <option value="0">N/A</option> 
                            @foreach($hotels as $hotel)
                                <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar usuario-->
@foreach($users as $user)
<div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel-{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel-{{ $user->id }}">Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" name="name" class="form-control" id="name-{{$user->id}}" value="{{ $user->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" id="email-{{$user->id}}" value="{{ $user->email }}" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Rol</label>
                        <select name="role" id="role-{{$user->id}}" class="form-control" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" @if($user->roles->first()->name == $role->name) selected @endif>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hotel_id">Hotel</label>
                        <select name="hotel_id" id="hotel_id-{{$user->id}}" class="form-control" required>
                            <option value="0" @if($user->hotel_id == 0) selected @endif>N/A</option>
                            @foreach($hotels as $hotel)
                                <option value="{{ $hotel->id }}" @if($user->hotel_id == $hotel->id) selected @endif>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal para eliminar usuario-->
@foreach($users as $user)
<div class="modal fade" id="deleteUserModal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel-{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel-{{ $user->id }}">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Está seguro de que desea eliminar al usuario <strong>{{ $user->name }}</strong>? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('js')
    <script>
        //Filtrado de busqueda de la tabla 
        document.addEventListener("DOMContentLoaded", function () {
                const searchInput = document.getElementById('searchInput');
                const tableBody = document.getElementById('tableBody');
                const rows = tableBody.getElementsByTagName('tr');

                searchInput.addEventListener('input', function () {
                    const filter = searchInput.value.toLowerCase();

                    Array.from(rows).forEach(row => {
                        const cells = row.getElementsByTagName('td');
                        let match = false;

                        Array.from(cells).forEach(cell => {
                            if (cell.textContent.toLowerCase().includes(filter)) {
                                match = true;
                            }
                        });

                        row.style.display = match ? '' : 'none';
                    });
                });
            });

             //Mostrar mensaje de exito por 43 segundos 
             document.addEventListener("DOMContentLoaded", function() {
            // Configura un temporizador de 3 segundos para ocultar la alerta
                setTimeout(function() {
                    var alert = document.getElementById('success-alert');
                    if (alert) {
                        alert.classList.remove('show'); 
                        alert.classList.add('fade');    
                        setTimeout(function() {
                            alert.remove(); 
                        }, 300); 
                    }
                }, 2000); 
            });
    </script>
        
@endsection
