@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Editar Reparación'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 mb-4">
                <h2 class="text-white mb-0">Editar Reparación {{ $reparacion->codigo_reparacion }}</h2>
                <p class="text-white text-sm opacity-8">Actualice los detalles de la orden de trabajo</p>
            </div>
        </div>

        <form method="POST" action="{{ route('reparaciones.update', $reparacion) }}">
            @csrf
            @method('PUT')

            <!-- Detalles de la Reparación -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Detalles de la Reparación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Información del Cliente y Equipo (Solo lectura) -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $reparacion->equipo->cliente->nombre }}" disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Equipo</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $reparacion->equipo->marca }} {{ $reparacion->equipo->modelo }}" disabled>
                                </div>

                                <!-- Técnico Asignado -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Técnico Asignado</label>
                                    <select name="tecnico_id" class="form-select @error('tecnico_id') is-invalid @enderror">
                                        <option value="">Sin asignar</option>
                                        @foreach($tecnicos as $tecnico)
                                            <option value="{{ $tecnico->id }}" 
                                                {{ $reparacion->tecnico_id == $tecnico->id ? 'selected' : '' }}>
                                                {{ $tecnico->firstname }} {{ $tecnico->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tecnico_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-select @error('estado') is-invalid @enderror">
                                        <option value="Recibido" {{ $reparacion->estado == 'Recibido' ? 'selected' : '' }}>Recibido</option>
                                        <option value="En Diagnóstico" {{ $reparacion->estado == 'En Diagnóstico' ? 'selected' : '' }}>En Diagnóstico</option>
                                        <option value="Esperando Aprobación" {{ $reparacion->estado == 'Esperando Aprobación' ? 'selected' : '' }}>Esperando Aprobación</option>
                                        <option value="Aprobado" {{ $reparacion->estado == 'Aprobado' ? 'selected' : '' }}>Aprobado</option>
                                        <option value="Esperando Pieza" {{ $reparacion->estado == 'Esperando Pieza' ? 'selected' : '' }}>Esperando Pieza</option>
                                        <option value="En Proceso" {{ $reparacion->estado == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="Finalizado" {{ $reparacion->estado == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                                        <option value="Entregado" {{ $reparacion->estado == 'Entregado' ? 'selected' : '' }}>Entregado</option>
                                        <option value="Cancelado" {{ $reparacion->estado == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Comentario del cambio de estado -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">Comentario del cambio (opcional)</label>
                                    <input type="text" name="comentario" class="form-control" 
                                           placeholder="Describa el motivo del cambio de estado">
                                </div>

                                <!-- Fechas -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha Prometida</label>
                                    <input type="date" name="fecha_prometida" class="form-control" 
                                           value="{{ $reparacion->fecha_prometida?->format('Y-m-d') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha de Finalización</label>
                                    <input type="date" name="fecha_finalizacion" class="form-control" 
                                           value="{{ $reparacion->fecha_finalizacion?->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Costos -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Costos de la Reparación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Costo Diagnóstico ($)</label>
                                    <input type="number" name="costo_diagnostico" class="form-control" 
                                           step="0.01" min="0" value="{{ $reparacion->costo_diagnostico }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Costo Piezas ($)</label>
                                    <input type="number" name="costo_piezas" class="form-control" 
                                           step="0.01" min="0" value="{{ $reparacion->costo_piezas }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Costo Mano de Obra ($)</label>
                                    <input type="number" name="costo_mano_obra" class="form-control" 
                                           step="0.01" min="0" value="{{ $reparacion->costo_mano_obra }}">
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <strong>Total Estimado:</strong> ${{ number_format($reparacion->total_estimado, 2) }}
                                        <small class="d-block mt-1">Se recalculará automáticamente al guardar</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Garantía -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Información de Garantía</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="es_garantia" id="es_garantia_edit" value="1" {{ $reparacion->es_garantia ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="es_garantia_edit">
                                            Esta es una reparación en garantía
                                        </label>
                                    </div>
                                </div>

                                <div id="garantia_fields_edit" style="display: {{ $reparacion->es_garantia ? 'block' : 'none' }};">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Período de Garantía (días)</label>
                                            <input type="number" name="periodo_garantia_dias" id="periodo_garantia_dias_edit" 
                                                   class="form-control @error('periodo_garantia_dias') is-invalid @enderror" 
                                                   value="{{ $reparacion->periodo_garantia_dias }}" min="1" max="365" 
                                                   placeholder="Ej: 30, 60, 90">
                                            @error('periodo_garantia_dias')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Fecha de Vencimiento</label>
                                            <input type="date" name="fecha_vencimiento_garantia" 
                                                   class="form-control" 
                                                   value="{{ $reparacion->fecha_vencimiento_garantia?->format('Y-m-d') }}"
                                                   readonly>
                                            <small class="text-muted">Se calcula automáticamente al entregar</small>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Reparación Original</label>
                                            <select name="reparacion_original_id" id="reparacion_original_id_edit" 
                                                    class="form-select @error('reparacion_original_id') is-invalid @enderror">
                                                <option value="">Seleccione la reparación original...</option>
                                                @foreach($reparacionesOriginales ?? [] as $repOriginal)
                                                    <option value="{{ $repOriginal->id }}" 
                                                        {{ $reparacion->reparacion_original_id == $repOriginal->id ? 'selected' : '' }}>
                                                        {{ $repOriginal->codigo_reparacion }} - 
                                                        {{ $repOriginal->equipo->cliente->nombre ?? 'N/A' }} - 
                                                        {{ $repOriginal->fecha_finalizacion?->format('d/m/Y') ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('reparacion_original_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cotización -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Cotización para el Cliente</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Precio Cotizado ($)</label>
                                    <input type="number" name="precio_cotizado" id="precio_cotizado_edit" class="form-control @error('precio_cotizado') is-invalid @enderror" 
                                           step="0.01" min="0" value="{{ $reparacion->precio_cotizado }}">
                                    <small class="form-text text-muted">
                                        @if($reparacion->tipo_servicio === 'reparacion')
                                            Al ingresar un precio, el estado cambiará automáticamente a "Esperando Aprobación"
                                        @else
                                            Para mantenimientos, se genera factura inmediatamente
                                        @endif
                                    </small>
                                    @error('precio_cotizado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="aplicar_impuesto" id="aplicar_impuesto" value="1" 
                                               {{ old('aplicar_impuesto', $reparacion->factura ? $reparacion->factura->aplicar_impuesto : ($reparacion->aplicar_impuesto_cotizacion ?? true)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="aplicar_impuesto">
                                            <strong>Aplicar Impuesto</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Por defecto se aplica impuesto ({{ number_format($porcentajeImpuesto ?? 18, 0) }}%). Desmarque si no aplica.
                                        </small>
                                    </div>
                                    <div id="total-con-impuesto-edit" class="mt-2 small text-success fw-bold" style="display: none;">
                                        Total con impuesto: $<span id="total-impuesto-valor">0.00</span>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Descripción de la Cotización</label>
                                    <textarea name="descripcion_cotizacion" class="form-control @error('descripcion_cotizacion') is-invalid @enderror" 
                                              rows="5" placeholder="Describa qué tiene el equipo, qué piezas necesita (si aplica) y qué se le hará...">{{ $reparacion->descripcion_cotizacion }}</textarea>
                                    <small class="form-text text-muted">
                                        Esta descripción será visible para el cliente en la consulta pública. Incluya:
                                        <ul class="mb-0 mt-1">
                                            <li>Problema detectado en el equipo</li>
                                            <li>Piezas necesarias (si aplica, o indique "No requiere piezas")</li>
                                            <li>Trabajos a realizar</li>
                                        </ul>
                                    </small>
                                    @error('descripcion_cotizacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
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
                        <div class="card-body text-end">
                            <a href="{{ route('reparaciones.show', $reparacion) }}" class="btn btn-light">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @include('layouts.footers.auth.footer')
    </div>

    @push('js')
    <script>
        const porcentajeImpuestoEdit = {{ $porcentajeImpuesto ?? 18 }};
        function actualizarTotalConImpuesto() {
            const precioInput = document.getElementById('precio_cotizado_edit');
            const aplicarCheck = document.getElementById('aplicar_impuesto');
            const totalBlock = document.getElementById('total-con-impuesto-edit');
            const totalValor = document.getElementById('total-impuesto-valor');
            if (!precioInput || !totalBlock || !totalValor) return;
            const subtotal = parseFloat(precioInput.value) || 0;
            const aplicar = aplicarCheck ? aplicarCheck.checked : true;
            if (subtotal > 0 && aplicar) {
                const impuestos = (subtotal * porcentajeImpuestoEdit) / 100;
                totalValor.textContent = (subtotal + impuestos).toFixed(2);
                totalBlock.style.display = 'block';
            } else {
                totalBlock.style.display = 'none';
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            actualizarTotalConImpuesto();
            const precioInput = document.getElementById('precio_cotizado_edit');
            const aplicarCheck = document.getElementById('aplicar_impuesto');
            if (precioInput) precioInput.addEventListener('input', actualizarTotalConImpuesto);
            if (aplicarCheck) aplicarCheck.addEventListener('change', actualizarTotalConImpuesto);
        });
        // Mostrar/ocultar campos de garantía
        const esGarantiaCheckbox = document.getElementById('es_garantia_edit');
        const garantiaFields = document.getElementById('garantia_fields_edit');
        if (esGarantiaCheckbox && garantiaFields) {
            esGarantiaCheckbox.addEventListener('change', function() {
                garantiaFields.style.display = this.checked ? 'block' : 'none';
            });
        }
    </script>
    @endpush
@endsection
