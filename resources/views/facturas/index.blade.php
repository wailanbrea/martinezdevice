@extends('layouts.app')

@section('title', 'Facturación - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Facturación</h1>
    <div class="page-actions">
        <a href="{{ route('facturas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Factura
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Facturas</h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Forma de Pago</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facturas as $factura)
                    <tr>
                        <td>{{ $factura->numero_factura }}</td>
                        <td>{{ $factura->cliente ? $factura->cliente->nombre : 'N/A' }}</td>
                        <td>${{ number_format($factura->total, 2) }}</td>
                        <td>{{ ucfirst($factura->forma_pago) }}</td>
                        <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('facturas.show', $factura->id) }}" class="btn btn-sm btn-icon" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('facturas.imprimir', $factura->id) }}" class="btn btn-sm btn-icon btn-info" title="Imprimir" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <a href="{{ route('facturas.edit', $factura->id) }}" class="btn btn-sm btn-icon btn-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay facturas registradas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
