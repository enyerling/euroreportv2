@extends('adminlte::page')

@section('title', 'Perfil')

@section('content_header')
@stop

@section('content')
<div class="container">
    <br><center><h3>Perfil de usuario </h3></center><br>
    <div class="row">
        <!-- Perfil de Usuario -->
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información del Perfil</h3>
                </div>
                <div class="card-body text-center">
                    <!-- Nombre del Usuario -->
                    <h4>{{ Auth::user()->name }}</h4>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>

        <!-- Información del Usuario -->
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Detalles del Perfil</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update')}}" method="POST">
                        @csrf
                        @method('PATCH')

                        <!-- Nombre -->
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Correo Electrónico -->
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Contraseña Actual -->
                        <div class="form-group">
                            <label for="current_password">Contraseña Actual</label>
                            <div class="input-group">
                                <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePasswordVisibility('current_password')">
                                        <i id="current_password_icon" class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Nueva Contraseña -->
                        <div class="form-group">
                            <label for="password">Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePasswordVisibility('password')">
                                        <i id="password_icon" class="fas fa-eye"></i>
                                    </span>
                                </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Confirmar Nueva Contraseña -->
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePasswordVisibility('password_confirmation')">
                                        <i id="password_confirmation_icon" class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Botones de Acción -->
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            <a href="{{ route('profile') }}" class="btn btn-secondary">Volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 8px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Cambios Guardados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3" style="font-size: 40px; color: #28a745;">
                     <i class="fas fa-check-circle text-success"></i>
                    </div>
                    {{ session('status') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function togglePasswordVisibility(id) {
        var passwordField = document.getElementById(id);
        var icon = document.getElementById(id + '_icon');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
    @if (session('status'))
        <script>
            $(document).ready(function() {
                $('#successModal').modal('show');
            });
        </script>
    @endif

@stop