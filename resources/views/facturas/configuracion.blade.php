@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Configuración de Facturas'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-white mb-0">Configuración de Facturas</h2>
                <p class="text-white text-sm opacity-8">Personalice el formato y la información de las facturas</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <strong>Por favor corrija los siguientes errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('facturas.configuracion.update') }}" enctype="multipart/form-data" data-skip-double-submit>
            @csrf

            <!-- Información de la Empresa -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Información de la Empresa</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre de la Empresa *</label>
                                    <input type="text" name="empresa_nombre" class="form-control @error('empresa_nombre') is-invalid @enderror" 
                                           value="{{ old('empresa_nombre', $configuracion->empresa_nombre) }}" required>
                                    @error('empresa_nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cédula/RNC</label>
                                    <input type="text" name="empresa_cedula_rnc" class="form-control @error('empresa_cedula_rnc') is-invalid @enderror" 
                                           value="{{ old('empresa_cedula_rnc', $configuracion->empresa_cedula_rnc) }}">
                                    @error('empresa_cedula_rnc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Dirección</label>
                                    <textarea name="empresa_direccion" class="form-control @error('empresa_direccion') is-invalid @enderror" rows="2">{{ old('empresa_direccion', $configuracion->empresa_direccion) }}</textarea>
                                    @error('empresa_direccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="empresa_telefono" class="form-control @error('empresa_telefono') is-invalid @enderror" 
                                           value="{{ old('empresa_telefono', $configuracion->empresa_telefono) }}">
                                    @error('empresa_telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="empresa_email" class="form-control @error('empresa_email') is-invalid @enderror" 
                                           value="{{ old('empresa_email', $configuracion->empresa_email) }}">
                                    @error('empresa_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Sitio Web</label>
                                    <input type="text" name="empresa_website" class="form-control @error('empresa_website') is-invalid @enderror" 
                                           value="{{ old('empresa_website', $configuracion->empresa_website) }}"
                                           placeholder="Ej: www.ejemplo.com o https://ejemplo.com">
                                    @error('empresa_website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Logo de la Empresa</label>
                                    @if($configuracion->logo_path)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($configuracion->logo_path) }}" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
                                        </div>
                                    @endif
                                    <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted">Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de Factura -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Configuración de Factura</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Porcentaje de Impuesto (%)</label>
                                    <input type="number" name="impuesto_porcentaje" step="0.01" min="0" max="100" 
                                           class="form-control @error('impuesto_porcentaje') is-invalid @enderror" 
                                           value="{{ old('impuesto_porcentaje', $configuracion->impuesto_porcentaje) }}">
                                    <small class="text-muted">Dejar vacío si no se aplica impuesto por defecto</small>
                                    @error('impuesto_porcentaje')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Código NCF (DGII)</label>
                                    <input type="text" name="ncf_codigo" class="form-control @error('ncf_codigo') is-invalid @enderror" 
                                           value="{{ old('ncf_codigo', $configuracion->ncf_codigo) }}" 
                                           placeholder="Ej: B01, B02, B14">
                                    <small class="text-muted">Código de la DGII para generar NCF (ej: B01 para factura de consumo, B02 para factura fiscal, etc.)</small>
                                    @error('ncf_codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Moneda *</label>
                                    <input type="text" name="moneda" class="form-control @error('moneda') is-invalid @enderror" 
                                           value="{{ old('moneda', $configuracion->moneda) }}" required>
                                    <small class="text-muted">Ej: DOP, USD, EUR</small>
                                    @error('moneda')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Símbolo de Moneda *</label>
                                    <input type="text" name="simbolo_moneda" class="form-control @error('simbolo_moneda') is-invalid @enderror" 
                                           value="{{ old('simbolo_moneda', $configuracion->simbolo_moneda) }}" required>
                                    <small class="text-muted">Ej: $, €, RD$</small>
                                    @error('simbolo_moneda')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Formato de Número de Factura *</label>
                                    <input type="text" name="formato_numero_factura" class="form-control @error('formato_numero_factura') is-invalid @enderror" 
                                           value="{{ old('formato_numero_factura', $configuracion->formato_numero_factura) }}" required>
                                    <small class="text-muted">Use {YEAR} para el año y {NUM} para el número secuencial. Ej: FAC-{YEAR}-{NUM}</small>
                                    @error('formato_numero_factura')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="mostrar_logo" id="mostrar_logo"
                                               {{ old('mostrar_logo', $configuracion->mostrar_logo) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="mostrar_logo">
                                            Mostrar Logo en Factura
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="mostrar_terminos" id="mostrar_terminos"
                                               {{ old('mostrar_terminos', $configuracion->mostrar_terminos) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="mostrar_terminos">
                                            Mostrar Términos y Condiciones
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Textos Personalizados -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Textos Personalizados</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Encabezado de Factura</label>
                                    <textarea name="encabezado_factura" class="form-control @error('encabezado_factura') is-invalid @enderror" rows="3">{{ old('encabezado_factura', $configuracion->encabezado_factura) }}</textarea>
                                    <small class="text-muted">Texto que aparecerá en la parte superior de la factura</small>
                                    @error('encabezado_factura')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Pie de Factura</label>
                                    <textarea name="pie_factura" class="form-control @error('pie_factura') is-invalid @enderror" rows="3">{{ old('pie_factura', $configuracion->pie_factura) }}</textarea>
                                    <small class="text-muted">Texto que aparecerá en la parte inferior de la factura</small>
                                    @error('pie_factura')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Términos y Condiciones</label>
                                    <textarea name="terminos_condiciones" class="form-control @error('terminos_condiciones') is-invalid @enderror" rows="5">{{ old('terminos_condiciones', $configuracion->terminos_condiciones) }}</textarea>
                                    <small class="text-muted">Términos y condiciones que aparecerán en la factura</small>
                                    @error('terminos_condiciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('dashboard') }}" class="btn btn-light">Cancelar</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Configuración
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @include('layouts.footers.auth.footer')
    </div>
@endsection

