@props(['reparacion'])

@php
    $diasRestantes = null;
    $estadoGarantia = null;
    
    if ($reparacion->fecha_vencimiento_garantia) {
        $diasRestantes = (int) \Carbon\Carbon::now()->diffInDays($reparacion->fecha_vencimiento_garantia, false);
        if ($diasRestantes < 0) {
            $estadoGarantia = 'vencida';
            $diasRestantes = abs($diasRestantes);
        } elseif ($diasRestantes <= 7) {
            $estadoGarantia = 'por_vencer';
        } else {
            $estadoGarantia = 'vigente';
        }
    }
@endphp

@if($reparacion->es_garantia)
    <div class="garantia-info">
        <span class="badge bg-warning text-dark garantia-badge">
            <i class="fas fa-shield-alt me-1"></i>Sí
        </span>
        @if($reparacion->fecha_vencimiento_garantia)
            <small class="garantia-texto {{ $estadoGarantia === 'vencida' ? 'text-danger' : ($estadoGarantia === 'por_vencer' ? 'text-warning' : 'text-success') }}">
                @if($estadoGarantia === 'vencida')
                    Vencida hace {{ $diasRestantes }} {{ $diasRestantes == 1 ? 'día' : 'días' }}
                @elseif($estadoGarantia === 'por_vencer')
                    Vence en {{ $diasRestantes }} {{ $diasRestantes == 1 ? 'día' : 'días' }}
                @else
                    {{ $diasRestantes }} {{ $diasRestantes == 1 ? 'día' : 'días' }} restantes
                @endif
            </small>
            <small class="text-xs text-secondary garantia-texto">{{ $reparacion->fecha_vencimiento_garantia->format('d/m/Y') }}</small>
        @endif
    </div>
@else
    <span class="text-muted">-</span>
@endif

