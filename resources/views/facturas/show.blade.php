@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Factura ' . $factura->numero_factura])
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Factura {{ $factura->numero_factura }}</h4>
                            <div class="d-flex gap-2">
                                @if(auth()->user()->hasRole('administrador'))
                                <a href="{{ route('facturas.edit', $factura) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-2"></i>Editar
                                </a>
                                @endif
                                <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-primary btn-sm" target="_blank">
                                    <i class="fas fa-download me-2"></i>Descargar PDF
                                </a>
                                @if($factura->cliente && $factura->cliente->telefono)
                                <a href="{{ route('facturas.whatsapp', $factura) }}" class="btn btn-success btn-sm" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i>Compartir por WhatsApp
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="mb-3">Información de la Empresa</h5>
                                @if(file_exists(public_path('img/logo-factura.png')))
                                <img src="{{ asset('img/logo-factura.png') }}" alt="Logo" class="mb-3" style="max-height: 80px;">
                                @elseif($configuracion->mostrar_logo && $configuracion->logo_path)
                                <img src="{{ Storage::url($configuracion->logo_path) }}" alt="Logo" class="mb-3" style="max-height: 80px;">
                                @endif
                                <p class="mb-1"><strong>{{ $configuracion->empresa_nombre }}</strong></p>
                                @if($configuracion->empresa_cedula_rnc)
                                <p class="mb-1">Cédula/RNC: {{ $configuracion->empresa_cedula_rnc }}</p>
                                @endif
                                @if($configuracion->empresa_direccion)
                                <p class="mb-1">{{ $configuracion->empresa_direccion }}</p>
                                @endif
                                @if($configuracion->empresa_telefono)
                                <p class="mb-1">Tel: {{ $configuracion->empresa_telefono }}</p>
                                @endif
                                @if($configuracion->empresa_email)
                                <p class="mb-1">Email: {{ $configuracion->empresa_email }}</p>
                                @endif
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h5 class="mb-3">Información del Cliente</h5>
                                <p class="mb-1"><strong>{{ $factura->cliente->nombre }}</strong></p>
                                @if($factura->cliente->cedula_rnc)
                                <p class="mb-1">Cédula/RNC: {{ $factura->cliente->cedula_rnc }}</p>
                                @endif
                                @if($factura->cliente->direccion)
                                <p class="mb-1">{{ $factura->cliente->direccion }}</p>
                                @endif
                                @if($factura->cliente->telefono)
                                <p class="mb-1">Tel: {{ $factura->cliente->telefono }}</p>
                                @endif
                                @if($factura->cliente->email)
                                <p class="mb-1">Email: {{ $factura->cliente->email }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>Fecha de Emisión:</strong> {{ $factura->fecha_emision->format('d/m/Y') }}</p>
                                @if($factura->aplicar_impuesto && $factura->ncf)
                                <p><strong>NCF:</strong> {{ $factura->ncf }}</p>
                                @endif
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p><strong>Forma de Pago:</strong> {{ ucfirst($factura->forma_pago) }}</p>
                            </div>
                        </div>

                        @if($configuracion->encabezado_factura)
                        <div class="alert alert-info mb-4">
                            {!! nl2br(e($configuracion->encabezado_factura)) !!}
                        </div>
                        @endif

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Descripción</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-end">Impuestos</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            @if($factura->reparacion)
                                                {{ $factura->reparacion->tipo_servicio === 'mantenimiento' ? 'Mantenimiento' : 'Reparación' }} - 
                                                {{ $factura->equipo->tipo ?? 'N/A' }} 
                                                {{ $factura->equipo->marca ?? '' }} 
                                                {{ $factura->equipo->modelo ?? '' }}
                                            @else
                                                Servicio
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $configuracion->simbolo_moneda }}{{ number_format($factura->subtotal, 2) }}</td>
                                        <td class="text-end">
                                            @if($factura->aplicar_impuesto)
                                                {{ $configuracion->simbolo_moneda }}{{ number_format($factura->impuestos, 2) }}
                                            @else
                                                {{ $configuracion->simbolo_moneda }}0.00
                                            @endif
                                        </td>
                                        <td class="text-end"><strong>{{ $configuracion->simbolo_moneda }}{{ number_format($factura->total, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th class="text-end">{{ $configuracion->simbolo_moneda }}{{ number_format($factura->total, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($configuracion->mostrar_terminos && $configuracion->terminos_condiciones)
                        <div class="mb-4">
                            <h5>Términos y Condiciones</h5>
                            <p class="text-muted small">{!! nl2br(e($configuracion->terminos_condiciones)) !!}</p>
                        </div>
                        @endif

                        @if($configuracion->pie_factura)
                        <div class="alert alert-secondary mb-0">
                            {!! nl2br(e($configuracion->pie_factura)) !!}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

