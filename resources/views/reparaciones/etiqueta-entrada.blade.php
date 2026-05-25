@extends('layouts.print-etiqueta')

@section('content')
    @if(session('success'))
        <div class="no-print alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="no-print d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('reparaciones.show', $reparacion) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-file-alt me-1"></i>Ir a ficha
        </a>
        @php
            $urlWhatsApp = ($reparacion->equipo->cliente && trim($reparacion->equipo->cliente->telefono ?? ''))
                ? route('reparaciones.whatsapp-entrada', $reparacion) : null;
        @endphp
        @if($urlWhatsApp)
            <a href="{{ $urlWhatsApp }}" class="btn btn-success btn-sm" target="_blank">
                <i class="fab fa-whatsapp me-1"></i>WhatsApp
            </a>
        @endif
    </div>

    <div id="etiqueta-print" class="etiqueta-hoja card shadow">
        <div class="card-body p-5">
            @php
                $equipo = $reparacion->equipo;
                $cliente = $equipo->cliente ?? null;
                $config = \App\Models\FacturaConfiguracion::obtener();
                $empresa = $config->empresa_nombre ?? 'Martinez Devices';
                $tipoEquipo = $equipo->tipo . ($equipo->tipo_personalizado ? " ({$equipo->tipo_personalizado})" : '');
                $fechaIngreso = $reparacion->fecha_ingreso ? $reparacion->fecha_ingreso->format('d/m/Y') : '-';
                $fechaPrometida = $reparacion->fecha_prometida ? $reparacion->fecha_prometida->format('d/m/Y') : '-';
            @endphp
            <div class="text-center border-bottom pb-2 mb-3">
                <h5 class="mb-1 fw-bold">{{ $empresa }}</h5>
                <p class="text-muted mb-0" style="font-size: 1.1rem;">Hoja de entrada de equipo</p>
            </div>
            <table class="table table-borderless mb-0">
                <tr>
                    <td class="text-muted" style="width:35%">Código:</td>
                    <td class="fw-bold fs-5">{{ $reparacion->codigo_reparacion }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Teléfono:</td>
                    <td class="fw-bold fs-5">{{ $cliente->telefono ?? 'N/A' }}</td>
                </tr>
                <tr><td class="text-muted">Cliente:</td><td>{{ $cliente->nombre ?? 'N/A' }}</td></tr>
                <tr><td class="text-muted">Tipo:</td><td>{{ $tipoEquipo }}</td></tr>
                <tr><td class="text-muted">Marca / Modelo:</td><td>{{ $equipo->marca }} {{ $equipo->modelo }}</td></tr>
                @if($equipo->numero_serie)
                    <tr><td class="text-muted">Nº Serie:</td><td>{{ $equipo->numero_serie }}</td></tr>
                @endif
                <tr><td class="text-muted">Problema:</td><td>{{ Str::limit($equipo->descripcion_problema ?? '-', 80) }}</td></tr>
                <tr><td class="text-muted">Fecha ingreso:</td><td>{{ $fechaIngreso }}</td></tr>
                <tr><td class="text-muted">Fecha prometida:</td><td>{{ $fechaPrometida }}</td></tr>
                <tr><td class="text-muted">Tipo servicio:</td><td>{{ ucfirst($reparacion->tipo_servicio ?? 'reparación') }}</td></tr>
            </table>
            <div class="mt-3 pt-2 border-top text-center">
                @php $urlConsulta = route('public.consulta', ['codigo' => $equipo->codigo_unico]); @endphp
                <p class="text-muted mb-1" style="font-size: 1rem;">Consulta estado (escanea el QR):</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data={{ urlencode($urlConsulta) }}" alt="QR consulta estado" width="130" height="130" class="d-inline-block">
            </div>
        </div>
    </div>

    @if($autoImprimir ?? false)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() { window.print(); }, 500);
            });
        </script>
    @endif
@endsection
