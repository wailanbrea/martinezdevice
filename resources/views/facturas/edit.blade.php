@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Editar Factura ' . $factura->numero_factura])
    
    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Por favor, corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Editar Factura {{ $factura->numero_factura }}</h4>
                            <a href="{{ route('facturas.show', $factura) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('facturas.update', $factura) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="mb-3">Información de la Factura</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Número de Factura</label>
                                        <input type="text" class="form-control" value="{{ $factura->numero_factura }}" disabled>
                                        <small class="text-muted">El número de factura no se puede modificar</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Fecha de Emisión *</label>
                                        <input type="date" name="fecha_emision" class="form-control @error('fecha_emision') is-invalid @enderror" 
                                               value="{{ old('fecha_emision', $factura->fecha_emision->format('Y-m-d')) }}" required>
                                        @error('fecha_emision')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Forma de Pago *</label>
                                        <select name="forma_pago" class="form-select @error('forma_pago') is-invalid @enderror" required>
                                            <option value="efectivo" {{ old('forma_pago', $factura->forma_pago) == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                            <option value="transferencia" {{ old('forma_pago', $factura->forma_pago) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                            <option value="tarjeta" {{ old('forma_pago', $factura->forma_pago) == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                            <option value="cheque" {{ old('forma_pago', $factura->forma_pago) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        </select>
                                        @error('forma_pago')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3">Información del Cliente</h5>
                                    <p class="mb-1"><strong>{{ $factura->cliente->nombre }}</strong></p>
                                    @if($factura->cliente->cedula_rnc)
                                    <p class="mb-1">Cédula/RNC: {{ $factura->cliente->cedula_rnc }}</p>
                                    @endif
                                    @if($factura->cliente->telefono)
                                    <p class="mb-1">Tel: {{ $factura->cliente->telefono }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Detalles de la Factura</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th class="text-end">Subtotal</th>
                                                    @if($mostrarImpuestos)
                                                    <th class="text-end">Impuestos</th>
                                                    @endif
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        @if($factura->reparacion)
                                                            {{ $factura->reparacion->tipo_servicio === 'mantenimiento' ? 'Mantenimiento' : 'Reparación' }} - 
                                                            {{ $factura->equipo->tipo ?? 'N/A' }} 
                                                            {{ $factura->equipo->marca ?? '' }} 
                                                            {{ $factura->equipo->modelo ?? '' }}
                                                        @else
                                                            Servicio
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <input type="number" name="subtotal" step="0.01" min="0" 
                                                               class="form-control text-end @error('subtotal') is-invalid @enderror" 
                                                               value="{{ old('subtotal', $factura->subtotal) }}" 
                                                               id="subtotal" required>
                                                        @error('subtotal')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    @if($mostrarImpuestos)
                                                    <td class="text-end">
                                                        <input type="number" name="impuestos" step="0.01" min="0" 
                                                               class="form-control text-end @error('impuestos') is-invalid @enderror" 
                                                               value="{{ old('impuestos', $factura->impuestos) }}" 
                                                               id="impuestos" required>
                                                        @error('impuestos')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    @else
                                                    <input type="hidden" name="impuestos" id="impuestos" value="0">
                                                    @endif
                                                    <td class="text-end">
                                                        <strong id="total-display">{{ $configuracion->simbolo_moneda }}{{ number_format($factura->total, 2) }}</strong>
                                                        <input type="hidden" name="total" id="total" value="{{ old('total', $factura->total) }}">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    @if($mostrarImpuestos)
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="aplicar_impuesto" id="aplicar_impuesto" value="1" 
                                               {{ old('aplicar_impuesto', $factura->aplicar_impuesto) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="aplicar_impuesto">
                                            <strong>Aplicar Impuesto</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Marque esta opción si desea aplicar impuesto a esta factura.
                                        </small>
                                    </div>
                                    <div class="mb-3" id="ncf-container" style="display: {{ old('aplicar_impuesto', $factura->aplicar_impuesto) ? 'block' : 'none' }};">
                                        <label class="form-label">NCF (Número de Comprobante Fiscal)</label>
                                        <input type="text" name="ncf" class="form-control @error('ncf') is-invalid @enderror" 
                                               value="{{ old('ncf', $factura->ncf) }}" 
                                               placeholder="Ej: B01-00000001" maxlength="50">
                                        <small class="text-muted">El impuesto (ej. 18%) se aplica al marcar la casilla, con o sin NCF. Si NCF está vacío y tiene código configurado, se generará automáticamente.</small>
                                        @error('ncf')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('facturas.show', $factura) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subtotalInput = document.getElementById('subtotal');
            const impuestosInput = document.getElementById('impuestos');
            const totalInput = document.getElementById('total');
            const totalDisplay = document.getElementById('total-display');
            const aplicarImpuestoCheck = document.getElementById('aplicar_impuesto');
            const ncfContainer = document.getElementById('ncf-container');
            const configuracion = @json($configuracion);

            // Calcular total mostrando el estado real del checkbox de impuesto
            function calcularTotal() {
                const subtotal = parseFloat(subtotalInput.value) || 0;
                const impuestos = parseFloat(impuestosInput.value) || 0;
                const total = subtotal + impuestos;
                
                totalInput.value = total.toFixed(2);
                totalDisplay.textContent = configuracion.simbolo_moneda + total.toFixed(2);
            }

            // Porcentaje de impuesto: usar el configurado o 18% por defecto (el backend siempre recalcula al guardar)
            const porcentajeImpuesto = (configuracion.impuestos_activos && configuracion.impuesto_porcentaje != null && configuracion.impuesto_porcentaje !== '') 
                ? parseFloat(configuracion.impuesto_porcentaje) : 0;

            function sincronizarImpuestos() {
                const subtotal = parseFloat(subtotalInput.value) || 0;

                if (aplicarImpuestoCheck && aplicarImpuestoCheck.checked) {
                    const impuestos = (subtotal * porcentajeImpuesto) / 100;
                    impuestosInput.value = impuestos.toFixed(2);
                    impuestosInput.readOnly = true;
                } else {
                    impuestosInput.value = '0.00';
                    impuestosInput.readOnly = false;
                }

                calcularTotal();
            }

            subtotalInput.addEventListener('input', sincronizarImpuestos);
            impuestosInput.addEventListener('input', calcularTotal);

            // Mostrar/ocultar campo NCF según checkbox; calcular impuestos al activar (el impuesto se aplica con o sin NCF)
            if (aplicarImpuestoCheck) {
                aplicarImpuestoCheck.addEventListener('change', function() {
                    if (ncfContainer) {
                        ncfContainer.style.display = this.checked ? 'block' : 'none';
                    }

                    sincronizarImpuestos();
                });
            }

            sincronizarImpuestos();
        });
    </script>
    @endpush
@endsection
