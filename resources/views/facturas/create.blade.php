@extends('layouts.app')

@section('title', 'Nueva Factura - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Nueva Factura</h1>
    <div class="page-actions">
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>¡Error!</strong> Por favor corrige los siguientes errores:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Factura</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('facturas.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Cliente *</label>
                <select name="cliente_id" class="form-control" required id="clienteSelect">
                    <option value="">Seleccione un cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre }} - {{ $cliente->cedula_rnc }}
                        </option>
                    @endforeach
                </select>
                @error('cliente_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Equipo</label>
                <select name="equipo_id" class="form-control" id="equipoSelect">
                    <option value="">Seleccione un equipo (opcional)</option>
                    @foreach($equipos as $equipo)
                        <option value="{{ $equipo->id }}" data-cliente="{{ $equipo->cliente_id }}" {{ old('equipo_id') == $equipo->id ? 'selected' : '' }}>
                            {{ $equipo->tipo }} - {{ $equipo->marca }} {{ $equipo->modelo }}
                        </option>
                    @endforeach
                </select>
                @error('equipo_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Reparación</label>
                <select name="reparacion_id" class="form-control" id="reparacionSelect">
                    <option value="">Seleccione una reparación (opcional)</option>
                    @foreach($reparaciones as $reparacion)
                        <option value="{{ $reparacion->id }}" data-equipo="{{ $reparacion->equipo_id }}" data-total="{{ $reparacion->total ?? 0 }}" {{ old('reparacion_id') == $reparacion->id ? 'selected' : '' }}>
                            Reparación #{{ $reparacion->id }} - {{ $reparacion->equipo->tipo ?? 'N/A' }} (Total: ${{ number_format($reparacion->total ?? 0, 2) }})
                        </option>
                    @endforeach
                </select>
                @error('reparacion_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Subtotal *</label>
                    <input type="number" name="subtotal" id="subtotal" class="form-control" step="0.01" min="0" value="{{ old('subtotal') }}" required>
                    @error('subtotal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Impuestos (ITBIS 18%)</label>
                    <input type="number" name="impuestos" id="impuestos" class="form-control" step="0.01" min="0" value="{{ old('impuestos', 0) }}">
                    @error('impuestos')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Total *</label>
                <input type="number" name="total" id="total" class="form-control" step="0.01" min="0" value="{{ old('total') }}" required readonly style="background-color: #f8f9fa; font-weight: 600;">
                @error('total')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Forma de Pago *</label>
                <select name="forma_pago" class="form-control" required>
                    <option value="">Seleccione una forma de pago</option>
                    <option value="efectivo" {{ old('forma_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                    <option value="transferencia" {{ old('forma_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                    <option value="tarjeta" {{ old('forma_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                </select>
                @error('forma_pago')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Fecha de Emisión *</label>
                <input type="date" name="fecha_emision" class="form-control" value="{{ old('fecha_emision', date('Y-m-d')) }}" required>
                @error('fecha_emision')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('facturas.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Factura</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Calcular total automáticamente
    function calcularTotal() {
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const impuestos = parseFloat(document.getElementById('impuestos').value) || 0;
        const total = subtotal + impuestos;
        document.getElementById('total').value = total.toFixed(2);
    }

    document.getElementById('subtotal').addEventListener('input', calcularTotal);
    document.getElementById('impuestos').addEventListener('input', calcularTotal);

    // Calcular impuestos automáticamente (18% ITBIS)
    document.getElementById('subtotal').addEventListener('input', function() {
        const subtotal = parseFloat(this.value) || 0;
        const impuestos = subtotal * 0.18;
        document.getElementById('impuestos').value = impuestos.toFixed(2);
        calcularTotal();
    });

    // Cargar total desde reparación
    document.getElementById('reparacionSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const total = parseFloat(selectedOption.dataset.total) || 0;
            const impuestos = total * 0.18;
            const subtotal = total - impuestos;
            
            document.getElementById('subtotal').value = subtotal.toFixed(2);
            document.getElementById('impuestos').value = impuestos.toFixed(2);
            calcularTotal();
        }
    });

    // Filtrar equipos por cliente
    document.getElementById('clienteSelect').addEventListener('change', function() {
        const clienteId = this.value;
        const equipoSelect = document.getElementById('equipoSelect');
        
        Array.from(equipoSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                option.style.display = option.dataset.cliente == clienteId ? 'block' : 'none';
            }
        });
    });
</script>
@endpush
@endsection

