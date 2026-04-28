<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            margin-bottom: 30px;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .company-info, .client-info {
            margin-bottom: 20px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .client-info {
            float: right;
            width: 45%;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        .terms {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            @if(file_exists(public_path('img/logo-factura.png')))
            <img src="{{ public_path('img/logo-factura.png') }}" alt="Logo" class="logo">
            @elseif($configuracion->mostrar_logo && $configuracion->logo_path)
            <img src="{{ public_path('storage/' . $configuracion->logo_path) }}" alt="Logo" class="logo">
            @endif
            <h2>{{ $configuracion->empresa_nombre }}</h2>
            @if($configuracion->empresa_cedula_rnc)
            <p>Cédula/RNC: {{ $configuracion->empresa_cedula_rnc }}</p>
            @endif
            @if($configuracion->empresa_direccion)
            <p>{{ $configuracion->empresa_direccion }}</p>
            @endif
            @if($configuracion->empresa_telefono)
            <p>Tel: {{ $configuracion->empresa_telefono }}</p>
            @endif
            @if($configuracion->empresa_email)
            <p>Email: {{ $configuracion->empresa_email }}</p>
            @endif
        </div>
        <div class="client-info">
            <h3>FACTURA</h3>
            <p><strong>Número:</strong> {{ $factura->numero_factura }}</p>
            @if($factura->aplicar_impuesto && $factura->ncf)
            <p><strong>NCF:</strong> {{ $factura->ncf }}</p>
            @endif
            <p><strong>Fecha:</strong> {{ $factura->fecha_emision->format('d/m/Y') }}</p>
            <h4>Cliente</h4>
            <p><strong>{{ $factura->cliente->nombre }}</strong></p>
            @if($factura->cliente->cedula_rnc)
            <p>Cédula/RNC: {{ $factura->cliente->cedula_rnc }}</p>
            @endif
            @if($factura->cliente->direccion)
            <p>{{ $factura->cliente->direccion }}</p>
            @endif
            @if($factura->cliente->telefono)
            <p>Tel: {{ $factura->cliente->telefono }}</p>
            @endif
            @if($factura->cliente->email)
            <p>Email: {{ $factura->cliente->email }}</p>
            @endif
        </div>
        <div class="clear"></div>
    </div>

    @if($configuracion->encabezado_factura)
    <div style="margin-bottom: 20px; padding: 10px; background-color: #e7f3ff;">
        {!! nl2br(e($configuracion->encabezado_factura)) !!}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Impuestos</th>
                <th class="text-right">Total</th>
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
                <td class="text-right">{{ $configuracion->simbolo_moneda }}{{ number_format($factura->subtotal, 2) }}</td>
                <td class="text-right">
                    @if($factura->aplicar_impuesto)
                        {{ $configuracion->simbolo_moneda }}{{ number_format($factura->impuestos, 2) }}
                    @else
                        {{ $configuracion->simbolo_moneda }}0.00
                    @endif
                </td>
                <td class="text-right"><strong>{{ $configuracion->simbolo_moneda }}{{ number_format($factura->total, 2) }}</strong></td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="3" class="text-right">Total:</th>
                <th class="text-right">{{ $configuracion->simbolo_moneda }}{{ number_format($factura->total, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <p><strong>Forma de Pago:</strong> {{ ucfirst($factura->forma_pago) }}</p>

    @if($configuracion->mostrar_terminos && $configuracion->terminos_condiciones)
    <div class="terms">
        <h4>Términos y Condiciones</h4>
        {!! nl2br(e($configuracion->terminos_condiciones)) !!}
    </div>
    @endif

    @if($configuracion->pie_factura)
    <div class="footer">
        {!! nl2br(e($configuracion->pie_factura)) !!}
    </div>
    @endif
</body>
</html>

