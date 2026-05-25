<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px solid #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        .items th, .items td { border-bottom: 1px solid #000; padding: 4px; }
        .right { text-align: right; }
        .mt-20 { margin-top: 20px; }
        .mt-40 { margin-top: 40px; }
        .items th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- LOGO -->
    <div class="center">
        @if(file_exists(public_path('img/logo-factura.png')))
            <img src="{{ public_path('img/logo-factura.png') }}" width="120">
        @elseif($configuracion->mostrar_logo && $configuracion->logo_path && file_exists(public_path('storage/' . $configuracion->logo_path)))
            <img src="{{ public_path('storage/' . $configuracion->logo_path) }}" width="120">
        @endif
    </div>

    <!-- EMPRESA -->
    <!-- Nombre de empresa removido - solo se muestra el logo -->
    <div class="center">
        @if($configuracion->empresa_direccion)
            {{ $configuracion->empresa_direccion }}<br>
        @endif
        @if($configuracion->empresa_telefono)
            {{ $configuracion->empresa_telefono }}
        @endif
        @if($configuracion->empresa_website)
            - {{ $configuracion->empresa_website }}
        @endif
        @if($configuracion->empresa_telefono || $configuracion->empresa_website)
            <br>
        @endif
        @if($configuracion->empresa_cedula_rnc)
            RNC: {{ $configuracion->empresa_cedula_rnc }}
        @endif
    </div>

    <div class="line"></div>

    <!-- CLIENTE Y FACTURA -->
    <table>
        <tr>
            <td>
                <strong>Cliente:</strong> {{ $factura->cliente->nombre }}<br>
                @if($factura->cliente->cedula_rnc)
                    <strong>Cédula:</strong> {{ $factura->cliente->cedula_rnc }}<br>
                @endif
                @if($factura->cliente->telefono)
                    <strong>Teléfono:</strong> {{ $factura->cliente->telefono }}
                @endif
            </td>

            <td class="right">
                <strong>FACTURA</strong><br>
                <strong>No.:</strong> {{ $factura->numero_factura }}<br>
                @if($factura->aplicar_impuesto && $factura->ncf)
                    <strong>NCF:</strong> {{ $factura->ncf }}<br>
                @endif
                <strong>Fecha:</strong> {{ $factura->fecha_emision->format('d-m-Y h:i a') }}<br>
                @if($factura->reparacion && $factura->reparacion->fecha_prometida)
                    <strong>Vence:</strong> {{ $factura->reparacion->fecha_prometida->format('d-m-Y h:i a') }}
                @endif
            </td>
        </tr>
    </table>

    <div class="center bold mt-20" style="font-size:18px;">
        @if($factura->reparacion)
            {{ strtoupper($factura->reparacion->tipo_servicio === 'mantenimiento' ? 'MANTENIMIENTO' : 'REPARACIÓN') }}
            @if($factura->equipo)
                {{ strtoupper($factura->equipo->tipo ?? '') }}
            @endif
        @else
            SERVICIO
        @endif
    </div>

    <!-- TABLA ITEMS -->
    <table class="items mt-20">
        <thead>
            <tr class="bold">
                <th>CANTIDAD</th>
                <th>UM</th>
                <th>DESCRIPCIÓN</th>
                <th>PRECIO</th>
                @if($mostrarImpuestos)
                <th>IMPUESTO</th>
                @endif
                <th>IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Unidad(es)</td>
                <td>
                    @if($factura->reparacion)
                        {{ $factura->reparacion->descripcion_cotizacion ?: ($factura->reparacion->tipo_servicio === 'mantenimiento' ? 'MANTENIMIENTO' : ($factura->reparacion->estado === 'Sin Reparación' ? 'DIAGNOSTICO / REVISION' : 'REPARACIÓN')) }}
                        @if($factura->equipo)
                            - {{ $factura->equipo->tipo ?? 'N/A' }} 
                            {{ $factura->equipo->marca ?? '' }} 
                            {{ $factura->equipo->modelo ?? '' }}
                        @endif
                        @if($factura->reparacion->periodo_garantia_dias)
                            <br>{{ $factura->reparacion->periodo_garantia_dias }} DÍAS DE GARANTÍA
                        @elseif($factura->reparacion->tipo_servicio === 'reparacion')
                            <br>1 MES DE GARANTÍA
                        @endif
                    @else
                        SERVICIO
                    @endif
                </td>
                <td class="right">{{ $configuracion->simbolo_moneda }}{{ number_format($factura->subtotal, 2) }}</td>
                @if($mostrarImpuestos)
                <td class="right">
                    @if($factura->aplicar_impuesto)
                        {{ $configuracion->simbolo_moneda }}{{ number_format($factura->impuestos, 2) }}
                    @else
                        {{ $configuracion->simbolo_moneda }}0.00
                    @endif
                </td>
                @endif
                <td class="right">{{ $configuracion->simbolo_moneda }}{{ number_format($factura->subtotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TOTALES -->
    <table class="mt-20" style="width: 40%; float:right;">
        <tr>
            <td><strong>Total a Pagar</strong></td>
            <td class="right"><strong>{{ $configuracion->simbolo_moneda }}{{ number_format($factura->total, 2) }}</strong></td>
        </tr>
    </table>

    <div style="clear:both;"></div>

    <!-- FORMA DE PAGO -->
    <div class="mt-40">
        <strong>Forma de Pago:</strong> {{ ucfirst($factura->forma_pago) }}<br>
        <strong>Monto:</strong> {{ $configuracion->simbolo_moneda }}{{ number_format($factura->total, 2) }}
    </div>

    <!-- COMENTARIOS -->
    @if($factura->reparacion && $factura->reparacion->descripcion_cotizacion)
    <div class="mt-40">
        <strong>Comentarios:</strong><br>
        {{ $factura->reparacion->descripcion_cotizacion }}
    </div>
    @elseif($configuracion->pie_factura)
    <div class="mt-40">
        <strong>Comentarios:</strong><br>
        {{ $configuracion->pie_factura }}
    </div>
    @endif

    <!-- FIRMAS -->
    <div class="mt-40">
        ____________________________________ <br>
        Preparado Por<br>
        <strong>{{ $preparadoPor ?? '' }}</strong>
        <br><br>
        ____________________________________ <br>
        Recibido Por<br>
        <strong>{{ $recibidoPor ?? '' }}</strong>
    </div>

</body>
</html>
