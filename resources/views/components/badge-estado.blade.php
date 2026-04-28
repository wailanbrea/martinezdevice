@props(['estado', 'size' => 'sm'])

@php
    $clases = match ($estado) {
        'Recibido' => 'bg-secondary',
        'En Diagnóstico' => 'bg-warning',
        'Pendiente Revisión Admin' => 'bg-dark',
        'Esperando Pieza' => 'bg-orange',
        'Esperando Aprobación' => 'bg-info',
        'Aprobado' => 'bg-success',
        'En Proceso' => 'bg-info',
        'Finalizado' => 'bg-success',
        'Entregado' => 'bg-primary',
        'Cancelado' => 'bg-danger',
        default => 'bg-secondary',
    };

    $sizeClass = match ($size) {
        'xs' => 'badge-xs',
        'sm' => 'badge-sm',
        'md' => 'badge-md',
        'lg' => 'badge-lg',
        default => 'badge-sm',
    };
@endphp

<span class="badge {{ $sizeClass }} {{ $clases }}">
    {{ $estado }}
</span>
