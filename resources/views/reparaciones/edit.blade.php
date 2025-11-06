@extends('layouts.app')

@section('title', 'Editar Reparación - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Editar Reparación</h1>
    <div class="page-actions">
        <a href="{{ route('reparaciones.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>¡Error!</strong> Por favor corrige los siguientes errores:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Reparación</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('reparaciones.update', $reparacion->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Equipo *</label>
                <select name="equipo_id" class="form-control" required>
                    <option value="">Seleccione un equipo</option>
                    @foreach($equipos as $equipo)
                        <option value="{{ $equipo->id }}" {{ old('equipo_id', $reparacion->equipo_id) == $equipo->id ? 'selected' : '' }}>
                            {{ $equipo->tipo }} - {{ $equipo->marca }} {{ $equipo->modelo }} ({{ $equipo->cliente->nombre ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('equipo_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Técnico *</label>
                <select name="tecnico_id" class="form-control" required>
                    <option value="">Seleccione un técnico</option>
                    @foreach($tecnicos as $tecnico)
                        <option value="{{ $tecnico->id }}" {{ old('tecnico_id', $reparacion->tecnico_id) == $tecnico->id ? 'selected' : '' }}>
                            {{ $tecnico->name }}
                        </option>
                    @endforeach
                </select>
                @error('tecnico_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Diagnóstico y Detalles</label>
                <textarea name="diagnostico" class="form-control" rows="4" placeholder="Descripción del problema y solución aplicada...">{{ old('diagnostico', $reparacion->diagnostico) }}</textarea>
                @error('diagnostico')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Costo Mano de Obra</label>
                <input type="number" name="costo_mano_obra" id="costoManoObra" class="form-control" step="0.01" min="0" value="{{ old('costo_mano_obra', $reparacion->costo_mano_obra) }}" placeholder="0.00" onchange="calcularTotal()">
                @error('costo_mano_obra')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Piezas Reemplazadas</label>
                <small class="form-text d-block mb-2">Agregue las piezas utilizadas en la reparación con su nombre y precio</small>
                
                <div id="piezasContainer">
                    <!-- Las piezas se agregarán dinámicamente aquí -->
                </div>
                
                <button type="button" class="btn btn-secondary btn-sm" onclick="agregarPieza()" style="margin-top: 0.5rem;">
                    <i class="bi bi-plus-circle"></i> Agregar Pieza
                </button>
                
                <input type="hidden" name="piezas_reemplazadas" id="piezasInput">
                <input type="hidden" name="costo_piezas" id="costoPiezasInput">
                
                <div style="margin-top: 1rem; padding: 0.75rem; background: var(--bg-tertiary); border-radius: var(--border-radius);">
                    <strong>Subtotal Piezas: $<span id="subtotalPiezas">0.00</span></strong>
                </div>
                
                @error('piezas_reemplazadas')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="padding: 1rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%); border-radius: var(--border-radius); color: white;">
                <label class="form-label" style="color: white; font-weight: 600;">Total de la Reparación</label>
                <div style="font-size: 1.5rem; font-weight: bold;">
                    $<span id="totalReparacion">0.00</span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="3" placeholder="Observaciones adicionales...">{{ old('observaciones', $reparacion->observaciones) }}</textarea>
                @error('observaciones')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', $reparacion->fecha_inicio ? \Carbon\Carbon::parse($reparacion->fecha_inicio)->format('Y-m-d') : '') }}">
                    @error('fecha_inicio')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha de Finalización</label>
                    <input type="date" name="fecha_finalizacion" class="form-control" value="{{ old('fecha_finalizacion', $reparacion->fecha_finalizacion ? \Carbon\Carbon::parse($reparacion->fecha_finalizacion)->format('Y-m-d') : '') }}">
                    @error('fecha_finalizacion')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="pendiente" {{ old('estado', $reparacion->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_proceso" {{ old('estado', $reparacion->estado) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="completada" {{ old('estado', $reparacion->estado) == 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ old('estado', $reparacion->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
                @error('estado')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('reparaciones.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar Reparación</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let piezasCount = 0;
const piezas = [];

function agregarPieza(nombre = '', precio = '') {
    const container = document.getElementById('piezasContainer');
    const piezaId = `pieza_${piezasCount++}`;
    
    const piezaDiv = document.createElement('div');
    piezaDiv.className = 'pieza-item';
    piezaDiv.id = piezaId;
    piezaDiv.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr auto; gap: 0.5rem; align-items: end; margin-bottom: 0.75rem; padding: 0.75rem; background: var(--bg-tertiary); border-radius: var(--border-radius);';
    
    piezaDiv.innerHTML = `
        <div class="form-group" style="margin: 0;">
            <label class="form-label" style="font-size: 0.875rem;">Nombre de la Pieza</label>
            <input type="text" class="form-control pieza-nombre" placeholder="Ej: Pantalla OLED 15 pulgadas" value="${nombre}" onchange="actualizarPiezas()">
        </div>
        <div class="form-group" style="margin: 0;">
            <label class="form-label" style="font-size: 0.875rem;">Precio</label>
            <input type="number" class="form-control pieza-precio" step="0.01" min="0" placeholder="0.00" value="${precio}" onchange="actualizarPiezas()">
        </div>
        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarPieza('${piezaId}')" style="height: fit-content;">
            <i class="bi bi-trash"></i>
        </button>
    `;
    
    container.appendChild(piezaDiv);
    actualizarPiezas();
}

function eliminarPieza(piezaId) {
    const piezaDiv = document.getElementById(piezaId);
    if (piezaDiv) {
        piezaDiv.remove();
        actualizarPiezas();
    }
}

function actualizarPiezas() {
    const piezasItems = document.querySelectorAll('.pieza-item');
    const piezasArray = [];
    let totalPiezas = 0;
    
    piezasItems.forEach(item => {
        const nombre = item.querySelector('.pieza-nombre').value.trim();
        const precio = parseFloat(item.querySelector('.pieza-precio').value) || 0;
        
        if (nombre) {
            piezasArray.push({
                nombre: nombre,
                precio: precio
            });
            totalPiezas += precio;
        }
    });
    
    document.getElementById('piezasInput').value = JSON.stringify(piezasArray);
    document.getElementById('costoPiezasInput').value = totalPiezas.toFixed(2);
    document.getElementById('subtotalPiezas').textContent = totalPiezas.toFixed(2);
    
    calcularTotal();
}

function calcularTotal() {
    const costoManoObra = parseFloat(document.getElementById('costoManoObra').value) || 0;
    const costoPiezas = parseFloat(document.getElementById('costoPiezasInput').value) || 0;
    const total = costoManoObra + costoPiezas;
    
    document.getElementById('totalReparacion').textContent = total.toFixed(2);
}

// Preparar datos antes de enviar el formulario
document.querySelector('form').addEventListener('submit', function(e) {
    actualizarPiezas();
});

// Cargar piezas existentes al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    @php
        $piezasExistentes = is_array($reparacion->piezas_reemplazadas) ? $reparacion->piezas_reemplazadas : [];
        if (empty($piezasExistentes) && !empty($reparacion->piezas_reemplazadas)) {
            // Compatibilidad con formato antiguo (array de strings)
            if (is_array($reparacion->piezas_reemplazadas)) {
                $piezasExistentes = array_map(function($p) {
                    return is_string($p) ? ['nombre' => $p, 'precio' => 0] : $p;
                }, $reparacion->piezas_reemplazadas);
            }
        }
    @endphp
    
    @if(!empty($piezasExistentes))
        const piezasExistentes = @json($piezasExistentes);
        if (Array.isArray(piezasExistentes)) {
            piezasExistentes.forEach(pieza => {
                if (typeof pieza === 'object' && pieza.nombre) {
                    agregarPieza(pieza.nombre, pieza.precio || '');
                } else if (typeof pieza === 'string') {
                    agregarPieza(pieza, '');
                }
            });
        }
    @endif
    
    // Calcular total inicial
    calcularTotal();
});
</script>
@endpush
@endsection

