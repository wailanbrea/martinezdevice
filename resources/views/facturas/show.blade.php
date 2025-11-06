@extends('layouts.app')

@section('title', 'Detalles de Factura - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Factura #{{ $factura->numero_factura ?? $factura->id }}</h1>
    <div class="page-actions">
        <a href="{{ route('facturas.imprimir', $factura->id) }}" class="btn btn-info" target="_blank">
            <i class="bi bi-printer"></i> Imprimir Factura
        </a>
        <a href="{{ route('facturas.edit', $factura->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Factura</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <label class="detail-label">Número de Factura</label>
                <div class="detail-value">
                    <strong>#{{ $factura->numero_factura ?? $factura->id }}</strong>
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Cliente</label>
                <div class="detail-value">
                    <a href="{{ route('clientes.show', $factura->cliente_id) }}">
                        {{ $factura->cliente->nombre ?? 'N/A' }}
                    </a>
                </div>
            </div>

            @if($factura->equipo)
            <div class="detail-item">
                <label class="detail-label">Equipo</label>
                <div class="detail-value">
                    <a href="{{ route('equipos.show', $factura->equipo_id) }}">
                        {{ $factura->equipo->tipo ?? 'N/A' }} - {{ $factura->equipo->marca ?? '' }} {{ $factura->equipo->modelo ?? '' }}
                    </a>
                </div>
            </div>
            @endif

            @if($factura->reparacion)
            <div class="detail-item">
                <label class="detail-label">Reparación</label>
                <div class="detail-value">
                    <a href="{{ route('reparaciones.show', $factura->reparacion_id) }}">
                        Reparación #{{ $factura->reparacion_id }}
                    </a>
                </div>
            </div>
            @endif

            <div class="detail-item">
                <label class="detail-label">Subtotal</label>
                <div class="detail-value">${{ number_format($factura->subtotal ?? 0, 2) }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Impuestos (ITBIS)</label>
                <div class="detail-value">${{ number_format($factura->impuestos ?? 0, 2) }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Total</label>
                <div class="detail-value">
                    <strong style="font-size: 1.25rem; color: var(--primary-color);">${{ number_format($factura->total ?? 0, 2) }}</strong>
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Forma de Pago</label>
                <div class="detail-value">
                    @php
                        $formasPago = [
                            'efectivo' => 'Efectivo',
                            'transferencia' => 'Transferencia',
                            'tarjeta' => 'Tarjeta',
                        ];
                    @endphp
                    {{ $formasPago[$factura->forma_pago] ?? ucfirst($factura->forma_pago ?? 'N/A') }}
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Fecha de Emisión</label>
                <div class="detail-value">
                    {{ $factura->fecha_emision ? \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y') : 'N/A' }}
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Fecha de Registro</label>
                <div class="detail-value">{{ $factura->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div class="form-actions mt-4">
            <a href="{{ route('facturas.imprimir', $factura->id) }}" class="btn btn-info" target="_blank">
                <i class="bi bi-printer"></i> Imprimir Factura
            </a>
            <a href="{{ route('facturas.edit', $factura->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar Factura
            </a>
            <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a la Lista
            </a>
        </div>
    </div>
</div>
@endsection

