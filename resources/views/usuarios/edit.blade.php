@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Editar Usuario'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 mb-4">
                <h2 class="text-white mb-0">Editar Usuario</h2>
                <p class="text-white text-sm opacity-8">Actualice los datos del usuario</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Por favor corrija los siguientes errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Información del Usuario</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre de Usuario *</label>
                                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                           placeholder="Ej: juan.perez" value="{{ old('username', $usuario->username) }}" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           placeholder="Ej: juan.perez@martinezservice.com" value="{{ old('email', $usuario->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" name="firstname" class="form-control @error('firstname') is-invalid @enderror" 
                                           placeholder="Ej: Juan" value="{{ old('firstname', $usuario->firstname) }}" required>
                                    @error('firstname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" name="lastname" class="form-control @error('lastname') is-invalid @enderror" 
                                           placeholder="Ej: Pérez" value="{{ old('lastname', $usuario->lastname) }}">
                                    @error('lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contraseña</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="Dejar en blanco para no cambiar">
                                    <small class="text-muted">Deje en blanco si no desea cambiar la contraseña</small>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirmar Contraseña</label>
                                    <input type="password" name="password_confirmation" class="form-control" 
                                           placeholder="Repita la contraseña">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Roles *</label>
                                    <div class="form-check-group">
                                        @foreach($roles as $rol)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="roles[]" 
                                                       value="{{ $rol->id }}" id="rol_{{ $rol->id }}"
                                                       {{ (old('roles', $usuario->roles->pluck('id')->toArray()) && in_array($rol->id, old('roles', $usuario->roles->pluck('id')->toArray()))) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rol_{{ $rol->id }}">
                                                    <strong>{{ $rol->nombre }}</strong>
                                                    <small class="text-muted d-block">{{ $rol->descripcion }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('roles')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-light">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar Usuario
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @include('layouts.footers.auth.footer')
    </div>
@endsection
