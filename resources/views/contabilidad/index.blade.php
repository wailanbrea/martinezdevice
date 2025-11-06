@extends('layouts.app')

@section('title', 'Contabilidad - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Contabilidad</h1>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Ingresos del Mes</div>
                <div class="stat-value">${{ number_format($ingresos_mes, 2) }}</div>
            </div>
            <div class="stat-icon" style="background: rgba(45, 206, 137, 0.1); color: #2dce89;">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Ingresos de Hoy</div>
                <div class="stat-value">${{ number_format($ingresos_hoy, 2) }}</div>
            </div>
            <div class="stat-icon" style="background: rgba(94, 114, 228, 0.1); color: #5e72e4;">
                <i class="bi bi-calendar-day"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Facturas Pendientes</div>
                <div class="stat-value">{{ $facturas_pendientes }}</div>
            </div>
            <div class="stat-icon" style="background: rgba(251, 99, 64, 0.1); color: #fb6340;">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Facturas del Mes</div>
                <div class="stat-value">{{ $facturas_mes }}</div>
            </div>
            <div class="stat-icon" style="background: rgba(17, 205, 239, 0.1); color: #11cdef;">
                <i class="bi bi-receipt"></i>
            </div>
        </div>
    </div>
</div>
@endsection
