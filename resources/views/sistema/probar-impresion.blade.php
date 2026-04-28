@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@push('css')
<style>
@media print {
    .no-print, .sidenav, .navbar, .navbar-main, #navbarBlur, .min-height-300, #pwa-install-btn { display: none !important; }
    body { background: #fff !important; }
    .etiqueta-hoja { box-shadow: none !important; border: 1px solid #333 !important; }
}
.etiqueta-hoja { max-width: 400px; margin: 0 auto; }
</style>
@endpush

@section('content')
    <div class="no-print">
        @include('layouts.navbars.auth.topnav', ['title' => 'Probar impresión'])
    </div>
    <div class="container-fluid py-4">
        <div class="no-print d-flex flex-wrap gap-2 mb-3">
            <button type="button" class="btn btn-primary" onclick="window.print();">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
            <a href="{{ route('sistema.configuracion') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a configuración
            </a>
        </div>

        <div id="etiqueta-print" class="etiqueta-hoja card shadow">
            <div class="card-body p-4">
                <div class="text-center border-bottom pb-2 mb-3">
                    <h5 class="mb-1 fw-bold">Martinez Devices</h5>
                    <p class="text-muted small mb-0">Hoja de entrada de equipo (prueba)</p>
                </div>
                <table class="table table-sm table-borderless mb-0 small">
                    <tr><td class="text-muted" style="width:35%">Código:</td><td class="fw-bold">REP-00001</td></tr>
                    <tr><td class="text-muted">Cliente:</td><td>Cliente de prueba</td></tr>
                    <tr><td class="text-muted">Tipo:</td><td>Laptop</td></tr>
                    <tr><td class="text-muted">Marca / Modelo:</td><td>Dell Inspiron 15</td></tr>
                    <tr><td class="text-muted">Nº Serie:</td><td>ABC123456</td></tr>
                    <tr><td class="text-muted">Problema:</td><td>No enciende, pantalla negra</td></tr>
                    <tr><td class="text-muted">Fecha ingreso:</td><td>{{ now()->format('d/m/Y') }}</td></tr>
                    <tr><td class="text-muted">Fecha prometida:</td><td>{{ now()->addDays(5)->format('d/m/Y') }}</td></tr>
                    <tr><td class="text-muted">Tipo servicio:</td><td>Reparación</td></tr>
                </table>
                <div class="mt-3 pt-2 border-top text-center">
                    <small class="text-muted">Si ves esta etiqueta correctamente, tu impresora está configurada.</small>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() { window.print(); }, 300);
        });
    </script>
@endsection
