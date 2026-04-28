@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Nuevo Cliente'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 mb-4">
                <h2 class="text-white mb-0">Registrar Nuevo Cliente</h2>
                <p class="text-white text-sm opacity-8">Complete los datos del cliente</p>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Información del Cliente</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nombre Completo *</label>
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                           placeholder="Ej: Juan Pérez López" value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cédula o RNC</label>
                                    <input type="text" name="cedula_rnc" class="form-control @error('cedula_rnc') is-invalid @enderror" 
                                           placeholder="Ej: 402-1234567-8" value="{{ old('cedula_rnc') }}">
                                    @error('cedula_rnc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono *</label>
                                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                                           placeholder="Ej: +1 (809) 555-0123" value="{{ old('telefono') }}" required>
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Correo Electrónico</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           placeholder="Ej: juan.perez@email.com" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Dirección</label>
                                    <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror" 
                                              rows="3" placeholder="Ej: Av. 27 de Febrero #123, Santiago">{{ old('direccion') }}</textarea>
                                    @error('direccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('clientes.index') }}" class="btn btn-light">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Cliente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @include('layouts.footers.auth.footer')
    </div>
@endsection
