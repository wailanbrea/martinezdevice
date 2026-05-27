@props(['estado', 'size' => 'sm'])

@php
    $estadoNormalizado = mb_strtolower(trim((string) $estado));

    $clases = match ($estadoNormalizado) {
        'recibido' => 'bg-secondary',
        'en diagnóstico', 'en diagnostico' => 'bg-warning text-dark',
        'pendiente revisión admin', 'pendiente revision admin' => 'bg-dark',
        'esperando pieza' => 'bg-warning text-dark',
        'esperando aprobación', 'esperando aprobacion' => 'bg-info',
        'aprobado' => 'bg-success',
        'en proceso' => 'bg-info',
        'finalizado', 'listo' => 'bg-success',
        'sin reparación', 'sin reparacion' => 'bg-warning text-dark',
        'entregado' => 'bg-primary',
        'cancelado' => 'bg-danger',
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
