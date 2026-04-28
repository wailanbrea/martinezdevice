@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Editar Cliente'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 mb-4">
                <h2 class="text-white mb-0">Editar Cliente</h2>
                <p class="text-white text-sm opacity-8">Actualice los datos del cliente</p>
            </div>
        </div>

        <form method="POST" action="{{ route('clientes.update', $cliente) }}">
            @csrf
            @method('PUT')

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
                                           value="{{ old('nombre', $cliente->nombre) }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cédula o RNC</label>
                                    <input type="text" name="cedula_rnc" class="form-control @error('cedula_rnc') is-invalid @enderror" 
                                           value="{{ old('cedula_rnc', $cliente->cedula_rnc) }}">
                                    @error('cedula_rnc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono *</label>
                                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                                           value="{{ old('telefono', $cliente->telefono) }}" required>
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Correo Electrónico</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $cliente->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Dirección</label>
                                    <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror" 
                                              rows="3">{{ old('direccion', $cliente->direccion) }}</textarea>
                                    @error('direccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-light">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar Cliente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @include('layouts.footers.auth.footer')
    </div>
@endsection
