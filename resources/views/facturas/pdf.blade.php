<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $factura->numero_factura ?? $factura->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #5e72e4;
        }
        .header h1 {
            color: #5e72e4;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-info-left, .invoice-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-info-right {
            text-align: right;
        }
        .section-title {
            font-weight: bold;
            color: #5e72e4;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #5e72e4;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .totals-row {
            margin-bottom: 8px;
        }
        .totals-label {
            display: inline-block;
            width: 150px;
            text-align: right;
            margin-right: 20px;
        }
        .totals-value {
            display: inline-block;
            width: 120px;
            text-align: right;
            font-weight: bold;
        }
        .total-final {
            font-size: 18px;
            color: #5e72e4;
            border-top: 2px solid #5e72e4;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MARTÍNEZ SERVICE</h1>
        <p>Sistema de Gestión y Reparación de Equipos</p>
        <p>FACTURA</p>
    </div>

    <div class="invoice-info">
        <div class="invoice-info-left">
            <div class="section-title">Información del Cliente</div>
            <div class="info-row">
                <span class="info-label">Cliente:</span>
                {{ $factura->cliente->nombre ?? 'N/A' }}
            </div>
            @if($factura->cliente->cedula_rnc)
            <div class="info-row">
                <span class="info-label">Cédula/RNC:</span>
                {{ $factura->cliente->cedula_rnc }}
            </div>
            @endif
            @if($factura->cliente->telefono)
            <div class="info-row">
                <span class="info-label">Teléfono:</span>
                {{ $factura->cliente->telefono }}
            </div>
            @endif
            @if($factura->cliente->correo)
            <div class="info-row">
                <span class="info-label">Correo:</span>
                {{ $factura->cliente->correo }}
            </div>
            @endif
        </div>
        <div class="invoice-info-right">
            <div class="section-title">Datos de la Factura</div>
            <div class="info-row">
                <span class="info-label">Número:</span>
                #{{ $factura->numero_factura ?? $factura->id }}
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Emisión:</span>
                {{ $factura->fecha_emision ? \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y') : 'N/A' }}
            </div>
            @if($factura->equipo)
            <div class="info-row">
                <span class="info-label">Equipo:</span>
                {{ $factura->equipo->tipo ?? 'N/A' }}
            </div>
            @endif
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th style="text-align: right;">Cantidad</th>
                <th style="text-align: right;">Precio Unitario</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($factura->reparacion)
                @php
                    $reparacion = $factura->reparacion;
                    $piezas = is_array($reparacion->piezas_reemplazadas) ? $reparacion->piezas_reemplazadas : [];
                @endphp
                
                {{-- Mano de Obra --}}
                @if($reparacion->costo_mano_obra > 0)
                <tr>
                    <td>
                        <strong>Mano de Obra</strong>
                        @if($reparacion->diagnostico)
                            <br><small style="color: #666;">{{ Str::limit($reparacion->diagnostico, 100) }}</small>
                        @endif
                    </td>
                    <td style="text-align: right;">1</td>
                    <td style="text-align: right;">${{ number_format($reparacion->costo_mano_obra ?? 0, 2) }}</td>
                    <td style="text-align: right;">${{ number_format($reparacion->costo_mano_obra ?? 0, 2) }}</td>
                </tr>
                @endif
                
                {{-- Piezas Reemplazadas --}}
                @if(!empty($piezas))
                    @foreach($piezas as $pieza)
                        @php
                            $nombrePieza = is_array($pieza) ? ($pieza['nombre'] ?? 'Pieza') : $pieza;
                            $precioPieza = is_array($pieza) ? (floatval($pieza['precio'] ?? 0)) : 0;
                        @endphp
                        @if($precioPieza > 0 || !empty($nombrePieza))
                        <tr>
                            <td>{{ $nombrePieza }}</td>
                            <td style="text-align: right;">1</td>
                            <td style="text-align: right;">${{ number_format($precioPieza, 2) }}</td>
                            <td style="text-align: right;">${{ number_format($precioPieza, 2) }}</td>
                        </tr>
                        @endif
                    @endforeach
                @endif
                
                {{-- Si no hay piezas ni mano de obra, mostrar servicio general --}}
                @if(empty($piezas) && ($reparacion->costo_mano_obra ?? 0) == 0)
                <tr>
                    <td>
                        Reparación de {{ $factura->equipo->tipo ?? 'Equipo' }}
                        @if($factura->equipo)
                            - {{ $factura->equipo->marca ?? '' }} {{ $factura->equipo->modelo ?? '' }}
                        @endif
                        @if($reparacion->diagnostico)
                            <br><small>{{ Str::limit($reparacion->diagnostico, 100) }}</small>
                        @endif
                    </td>
                    <td style="text-align: right;">1</td>
                    <td style="text-align: right;">${{ number_format($factura->subtotal ?? 0, 2) }}</td>
                    <td style="text-align: right;">${{ number_format($factura->subtotal ?? 0, 2) }}</td>
                </tr>
                @endif
            @else
            <tr>
                <td>Servicio de Reparación</td>
                <td style="text-align: right;">1</td>
                <td style="text-align: right;">${{ number_format($factura->subtotal ?? 0, 2) }}</td>
                <td style="text-align: right;">${{ number_format($factura->subtotal ?? 0, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span class="totals-label">Subtotal:</span>
            <span class="totals-value">${{ number_format($factura->subtotal ?? 0, 2) }}</span>
        </div>
        <div class="totals-row">
            <span class="totals-label">Impuestos (ITBIS 18%):</span>
            <span class="totals-value">${{ number_format($factura->impuestos ?? 0, 2) }}</span>
        </div>
        <div class="totals-row total-final">
            <span class="totals-label">TOTAL:</span>
            <span class="totals-value">${{ number_format($factura->total ?? 0, 2) }}</span>
        </div>
    </div>

    <div class="info-row" style="margin-top: 20px;">
        <span class="info-label">Forma de Pago:</span>
        @php
            $formasPago = [
                'efectivo' => 'Efectivo',
                'transferencia' => 'Transferencia Bancaria',
                'tarjeta' => 'Tarjeta de Crédito/Débito',
            ];
        @endphp
        {{ $formasPago[$factura->forma_pago] ?? ucfirst($factura->forma_pago ?? 'N/A') }}
    </div>

    <div class="footer">
        <p>Gracias por su preferencia</p>
        <p>Martínez Service - Sistema de Gestión y Reparación de Equipos</p>
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>

