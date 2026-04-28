@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Configuración del Sistema'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-white mb-0">Configuración del Sistema</h2>
                <p class="text-white text-sm opacity-8">Configure los valores por defecto del sistema</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('sistema.configuracion.update') }}">
            @csrf
            @method('PUT')

            <!-- Configuración de Costos -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Costos por Defecto</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Costo de Diagnóstico (Reparaciones) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $configuracion->simbolo_moneda }}</span>
                                        <input type="number" 
                                               name="costo_diagnostico" 
                                               step="0.01" 
                                               min="0" 
                                               class="form-control @error('costo_diagnostico') is-invalid @enderror" 
                                               value="{{ old('costo_diagnostico', $configuracion->costo_diagnostico) }}" 
                                               required>
                                    </div>
                                    <small class="text-muted">Costo por defecto que se aplicará al crear una nueva reparación</small>
                                    @error('costo_diagnostico')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Costo de Diagnóstico (Mantenimientos) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $configuracion->simbolo_moneda }}</span>
                                        <input type="number" 
                                               name="costo_diagnostico_mantenimiento" 
                                               step="0.01" 
                                               min="0" 
                                               class="form-control @error('costo_diagnostico_mantenimiento') is-invalid @enderror" 
                                               value="{{ old('costo_diagnostico_mantenimiento', $configuracion->costo_diagnostico_mantenimiento) }}" 
                                               required>
                                    </div>
                                    <small class="text-muted">Costo por defecto para mantenimientos (generalmente 0.00)</small>
                                    @error('costo_diagnostico_mantenimiento')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de Moneda -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Configuración de Moneda</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Moneda *</label>
                                    <input type="text" 
                                           name="moneda" 
                                           class="form-control @error('moneda') is-invalid @enderror" 
                                           value="{{ old('moneda', $configuracion->moneda) }}" 
                                           required
                                           placeholder="Ej: DOP, USD, EUR">
                                    <small class="text-muted">Código de la moneda (ej: DOP, USD, EUR)</small>
                                    @error('moneda')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Símbolo de Moneda *</label>
                                    <input type="text" 
                                           name="simbolo_moneda" 
                                           class="form-control @error('simbolo_moneda') is-invalid @enderror" 
                                           value="{{ old('simbolo_moneda', $configuracion->simbolo_moneda) }}" 
                                           required
                                           placeholder="Ej: $, €, £">
                                    <small class="text-muted">Símbolo que se mostrará antes de los montos (ej: $, €, £)</small>
                                    @error('simbolo_moneda')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de Comisiones -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Configuración de Comisiones</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Porcentaje de Comisión para Técnicos (%) *</label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="porcentaje_comision" 
                                               step="0.01" 
                                               min="0" 
                                               max="100" 
                                               class="form-control @error('porcentaje_comision') is-invalid @enderror" 
                                               value="{{ old('porcentaje_comision', $configuracion->porcentaje_comision ?? 10.00) }}" 
                                               required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted">Porcentaje por defecto que recibirán los técnicos al completar trabajos (ej: 10.00 para 10%)</small>
                                    @error('porcentaje_comision')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de Impresión -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6><i class="fas fa-print me-2"></i>Configuración de Impresión</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="imprimir_etiqueta_auto" value="0">
                                        <input class="form-check-input" type="checkbox" name="imprimir_etiqueta_auto" id="imprimir_etiqueta_auto" value="1"
                                            {{ old('imprimir_etiqueta_auto', $configuracion->imprimir_etiqueta_auto ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="imprimir_etiqueta_auto">
                                            Imprimir etiqueta automáticamente al registrar un equipo
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">Al crear una reparación, se abrirá el diálogo de impresión para pegar la hoja de entrada del equipo.</small>
                                </div>
                                <div class="col-12">
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        En navegadores web no se puede detectar ni listar impresoras por seguridad. Al imprimir, se abrirá el diálogo del sistema donde podrás elegir tu impresora (funciona en web, escritorio y móvil).
                                    </p>
                                    <a href="{{ route('sistema.configuracion.probar-impresion') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-print me-1"></i>Probar impresión
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Secuencia de Facturas (NCF)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Código NCF (DGII)</label>
                                    <input type="text"
                                           name="ncf_codigo"
                                           class="form-control @error('ncf_codigo') is-invalid @enderror"
                                           value="{{ old('ncf_codigo', $configuracion->ncf_codigo) }}"
                                           placeholder="Ej: B01, B02, B14">
                                    <small class="text-muted">Prefijo para generar NCF de facturas. B01 consumo, B02 fiscal, B14 servicios. Vacío usa el de Configuración de Facturas.</small>
                                    @error('ncf_codigo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Configuración
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

