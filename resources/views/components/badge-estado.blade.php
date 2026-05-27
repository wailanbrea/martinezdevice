@props(['estado', 'size' => 'sm'])

@php
    $estadoNormalizado = mb_strtolower(trim((string) $estado));

    $palette = match ($estadoNormalizado) {
        'recibido' => ['background' => '#6c757d', 'color' => '#ffffff'],
        'en diagnóstico', 'en diagnostico' => ['background' => '#0d9488', 'color' => '#ffffff'],
        'pendiente revisión admin', 'pendiente revision admin' => ['background' => '#1f2937', 'color' => '#ffffff'],
        'esperando aprobación', 'esperando aprobacion' => ['background' => '#f59e0b', 'color' => '#1f2937'],
        'aprobado' => ['background' => '#15803d', 'color' => '#ffffff'],
        'esperando pieza' => ['background' => '#ea580c', 'color' => '#ffffff'],
        'en proceso' => ['background' => '#2563eb', 'color' => '#ffffff'],
        'finalizado', 'listo' => ['background' => '#059669', 'color' => '#ffffff'],
        'sin reparación', 'sin reparacion' => ['background' => '#7c3aed', 'color' => '#ffffff'],
        'entregado' => ['background' => '#db2777', 'color' => '#ffffff'],
        'cancelado' => ['background' => '#dc2626', 'color' => '#ffffff'],
        default => ['background' => '#64748b', 'color' => '#ffffff'],
    };

    $sizeClass = match ($size) {
        'xs' => 'badge-xs',
        'sm' => 'badge-sm',
        'md' => 'badge-md',
        'lg' => 'badge-lg',
        default => 'badge-sm',
    };
@endphp

<span
    class="badge {{ $sizeClass }}"
    style="background-color: {{ $palette['background'] }}; color: {{ $palette['color'] }};"
>
    {{ $estado }}
</span>
