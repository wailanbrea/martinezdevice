@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detalles de la Entrada - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Detalles de la Entrada</h1>
    <div class="page-actions">
        <a href="{{ route('equipos.edit', $equipo->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Equipo</h3>
    </div>
    <div class="card-body">
        @if($equipo->foto)
        <div class="form-group" style="margin-bottom: 2rem;">
            <label class="detail-label">Foto del Equipo</label>
            <div style="text-align: center;">
                <img src="{{ Storage::url($equipo->foto) }}" alt="Foto del equipo" style="max-width: 100%; max-height: 400px; border-radius: 0.75rem; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
            </div>
        </div>
        @endif

        <div class="detail-grid">
            <div class="detail-item">
                <label class="detail-label">Código Único</label>
                <div class="detail-value">
                    <strong>{{ $equipo->codigo_unico }}</strong>
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Cliente</label>
                <div class="detail-value">
                    <a href="{{ route('clientes.show', $equipo->cliente_id) }}">
                        {{ $equipo->cliente->nombre ?? 'N/A' }}
                    </a>
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Tipo</label>
                <div class="detail-value">{{ $equipo->tipo }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Marca</label>
                <div class="detail-value">{{ $equipo->marca ?? 'N/A' }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Modelo</label>
                <div class="detail-value">{{ $equipo->modelo ?? 'N/A' }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Número de Serie</label>
                <div class="detail-value">{{ $equipo->numero_serie ?? 'N/A' }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Estado</label>
                <div class="detail-value">
                    @php
                        $badges = [
                            'recibido' => 'badge-info',
                            'diagnostico' => 'badge-primary',
                            'reparacion' => 'badge-warning',
                            'listo' => 'badge-success',
                            'entregado' => 'badge-secondary',
                            'garantia' => 'badge-info',
                        ];
                        $estadoLabels = [
                            'recibido' => 'Recibido',
                            'diagnostico' => 'En Diagnóstico',
                            'reparacion' => 'En Reparación',
                            'listo' => 'Listo',
                            'entregado' => 'Entregado',
                            'garantia' => 'En Garantía',
                        ];
                        $badgeClass = $badges[$equipo->estado] ?? 'badge-secondary';
                        $estadoLabel = $estadoLabels[$equipo->estado] ?? ucfirst($equipo->estado ?? 'N/A');
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $estadoLabel }}</span>
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Fecha de Registro</label>
                <div class="detail-value">{{ $equipo->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <div class="detail-item" style="grid-column: 1 / -1;">
                <label class="detail-label">Falla Reportada</label>
                <div class="detail-value" style="white-space: pre-wrap;">{{ $equipo->descripcion_falla }}</div>
            </div>
        </div>

        <div class="form-actions mt-4">
            <a href="{{ route('equipos.imprimir-qr', $equipo->id) }}" class="btn btn-info" target="_blank">
                <i class="bi bi-qr-code"></i> Imprimir QR
            </a>
            <a href="{{ route('equipos.edit', $equipo->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar Equipo
            </a>
            <a href="{{ route('equipos.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a la Lista
            </a>
        </div>
    </div>
</div>

@if($equipo->reparaciones->count() > 0)
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Historial de Reparaciones</h3>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Técnico</th>
                        <th>Estado</th>
                        <th>Costo Total</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipo->reparaciones as $reparacion)
                        <tr>
                            <td>{{ $reparacion->tecnico->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($reparacion->estado ?? 'N/A') }}</span>
                            </td>
                            <td>${{ number_format($reparacion->total ?? 0, 2) }}</td>
                            <td>{{ $reparacion->fecha_inicio ? \Carbon\Carbon::parse($reparacion->fecha_inicio)->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $reparacion->fecha_finalizacion ? \Carbon\Carbon::parse($reparacion->fecha_finalizacion)->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('reparaciones.show', $reparacion->id) }}" class="btn btn-sm btn-icon" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

