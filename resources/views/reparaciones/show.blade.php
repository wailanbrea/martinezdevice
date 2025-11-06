@extends('layouts.app')

@section('title', 'Detalles de Reparación - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Detalles de Reparación</h1>
    <div class="page-actions">
        <a href="{{ route('reparaciones.edit', $reparacion->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('reparaciones.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Reparación</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <label class="detail-label">Equipo</label>
                <div class="detail-value">
                    <a href="{{ route('equipos.show', $reparacion->equipo_id) }}">
                        {{ $reparacion->equipo->tipo ?? 'N/A' }} - {{ $reparacion->equipo->marca ?? '' }} {{ $reparacion->equipo->modelo ?? '' }}
                    </a>
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Cliente</label>
                <div class="detail-value">
                    {{ $reparacion->equipo->cliente->nombre ?? 'N/A' }}
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Técnico</label>
                <div class="detail-value">{{ $reparacion->tecnico->name ?? 'N/A' }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Estado</label>
                <div class="detail-value">
                    @php
                        $estadoLabels = [
                            'pendiente' => 'Pendiente',
                            'en_proceso' => 'En Proceso',
                            'completada' => 'Completada',
                            'cancelada' => 'Cancelada',
                        ];
                        $badgeClasses = [
                            'pendiente' => 'badge-info',
                            'en_proceso' => 'badge-primary',
                            'completada' => 'badge-success',
                            'cancelada' => 'badge-danger',
                        ];
                        $estado = $reparacion->estado ?? 'pendiente';
                    @endphp
                    <span class="badge {{ $badgeClasses[$estado] ?? 'badge-secondary' }}">{{ $estadoLabels[$estado] ?? ucfirst($estado) }}</span>
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Costo Mano de Obra</label>
                <div class="detail-value">${{ number_format($reparacion->costo_mano_obra ?? 0, 2) }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Costo de Piezas</label>
                <div class="detail-value">${{ number_format($reparacion->costo_piezas ?? 0, 2) }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Total</label>
                <div class="detail-value">
                    <strong>${{ number_format($reparacion->total ?? 0, 2) }}</strong>
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Fecha de Inicio</label>
                <div class="detail-value">
                    {{ $reparacion->fecha_inicio ? \Carbon\Carbon::parse($reparacion->fecha_inicio)->format('d/m/Y') : 'N/A' }}
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Fecha de Finalización</label>
                <div class="detail-value">
                    {{ $reparacion->fecha_finalizacion ? \Carbon\Carbon::parse($reparacion->fecha_finalizacion)->format('d/m/Y') : 'N/A' }}
                </div>
            </div>

            @if($reparacion->diagnostico)
            <div class="detail-item" style="grid-column: 1 / -1;">
                <label class="detail-label">Diagnóstico</label>
                <div class="detail-value" style="white-space: pre-wrap;">{{ $reparacion->diagnostico }}</div>
            </div>
            @endif

            @if($reparacion->piezas_reemplazadas && is_array($reparacion->piezas_reemplazadas) && count($reparacion->piezas_reemplazadas) > 0)
            <div class="detail-item" style="grid-column: 1 / -1;">
                <label class="detail-label">Piezas Reemplazadas</label>
                <div class="detail-value">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach($reparacion->piezas_reemplazadas as $pieza)
                            <li>{{ $pieza }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            @if($reparacion->observaciones)
            <div class="detail-item" style="grid-column: 1 / -1;">
                <label class="detail-label">Observaciones</label>
                <div class="detail-value" style="white-space: pre-wrap;">{{ $reparacion->observaciones }}</div>
            </div>
            @endif
        </div>

        <div class="form-actions mt-4">
            <a href="{{ route('reparaciones.edit', $reparacion->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar Reparación
            </a>
            <a href="{{ route('reparaciones.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a la Lista
            </a>
        </div>
    </div>
</div>
@endsection

