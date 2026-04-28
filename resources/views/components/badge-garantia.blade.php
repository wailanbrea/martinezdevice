@props(['diasRestantes' => null, 'estadoGarantia' => null, 'size' => 'sm'])

@php
    if ($diasRestantes === null && $estadoGarantia === null) {
        // Sin garantía
        $mostrar = false;
    } else {
        $mostrar = true;
        if ($estadoGarantia === null) {
            // Calcular estado si no se proporciona
            if ($diasRestantes < 0) {
                $estadoGarantia = 'vencida';
                $diasRestantes = abs($diasRestantes);
            } elseif ($diasRestantes <= 7) {
                $estadoGarantia = 'por_vencer';
            } else {
                $estadoGarantia = 'vigente';
            }
        }
        
        $claseColor = match($estadoGarantia) {
            'vencida' => 'bg-danger text-white',
            'por_vencer' => 'bg-warning text-dark',
            'vigente' => 'bg-success text-white',
            default => 'bg-secondary text-white'
        };
        
        $texto = match($estadoGarantia) {
            'vencida' => "Vencida hace {$diasRestantes} " . ($diasRestantes == 1 ? 'día' : 'días'),
            'por_vencer' => "Vence en {$diasRestantes} " . ($diasRestantes == 1 ? 'día' : 'días'),
            'vigente' => "{$diasRestantes} " . ($diasRestantes == 1 ? 'día' : 'días') . " restantes",
            default => ''
        };
    }
    
    $sizeClass = match($size) {
        'xs' => 'badge-xs',
        'sm' => 'badge-sm',
        'md' => 'badge-md',
        'lg' => 'badge-lg',
        default => 'badge-sm'
    };
@endphp

@if($mostrar)
    <span class="badge {{ $sizeClass }} {{ $claseColor }}">
        {{ $texto }}
    </span>
@endif

