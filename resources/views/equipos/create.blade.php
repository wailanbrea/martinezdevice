@extends('layouts.app')

@section('title', 'Nueva Entrada - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Nueva Entrada</h1>
    <div class="page-actions">
        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">
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
        <h3 class="card-title">Información del Equipo</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('equipos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Cliente *</label>
                <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 0.5rem; align-items: center;">
                    <select name="cliente_id" id="clienteSelect" class="form-control" required>
                        <option value="">Seleccione un cliente</option>
                        <option value="generico" {{ old('cliente_id') === 'generico' ? 'selected' : '' }}>Cliente Genérico</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} - {{ $cliente->cedula_rnc }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="nuevoClienteBtn" class="btn btn-secondary">
                        <i class="bi bi-person-plus"></i> Nuevo Cliente
                    </button>
                </div>
                <small class="form-text">Seleccione "Cliente Genérico" si no desea registrar datos del cliente.</small>
                @error('cliente_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tipo de Equipo *</label>
                <input type="text" name="tipo" class="form-control" value="{{ old('tipo') }}" placeholder="Ej: Laptop, Smartphone, Tablet, PC" required>
                @error('tipo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Marca</label>
                    <input type="text" name="marca" class="form-control" value="{{ old('marca') }}" placeholder="Ej: HP, Apple, Samsung">
                    @error('marca')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ old('modelo') }}" placeholder="Ej: Pavilion 15, iPhone 13">
                    @error('modelo')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Número de Serie</label>
                <input type="text" name="numero_serie" class="form-control" value="{{ old('numero_serie') }}" placeholder="Número de serie del equipo">
                @error('numero_serie')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Foto del Equipo</label>
                <div class="photo-upload-container">
                    <input type="file" name="foto" id="foto" accept="image/*" capture="environment" class="form-control" style="display: none;">
                    <div class="photo-preview-container" id="photoPreviewContainer" style="display: none;">
                        <img id="photoPreview" src="" alt="Vista previa" style="max-width: 100%; max-height: 300px; border-radius: 0.75rem; margin-top: 0.5rem;">
                        <button type="button" id="removePhoto" class="btn btn-sm btn-danger" style="margin-top: 0.5rem;">
                            <i class="bi bi-trash"></i> Eliminar foto
                        </button>
                    </div>
                    <button type="button" id="capturePhotoBtn" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem;">
                        <i class="bi bi-camera"></i> Tomar Foto o Seleccionar Imagen
                    </button>
                </div>
                <small class="form-text">Puedes tomar una foto con la cámara o seleccionar una imagen existente</small>
                @error('foto')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Falla Reportada por el Cliente *</label>
                <textarea name="descripcion_falla" class="form-control" rows="4" placeholder="Descripción detallada de la falla reportada..." required>{{ old('descripcion_falla') }}</textarea>
                @error('descripcion_falla')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Estado Inicial</label>
                <select name="estado" class="form-control">
                    <option value="recibido" {{ old('estado', 'recibido') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                    <option value="diagnostico" {{ old('estado') == 'diagnostico' ? 'selected' : '' }}>En Diagnóstico</option>
                    <option value="reparacion" {{ old('estado') == 'reparacion' ? 'selected' : '' }}>En Reparación</option>
                    <option value="listo" {{ old('estado') == 'listo' ? 'selected' : '' }}>Listo</option>
                    <option value="entregado" {{ old('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                    <option value="garantia" {{ old('estado') == 'garantia' ? 'selected' : '' }}>En Garantía</option>
                </select>
                @error('estado')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('equipos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Entrada</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Nuevo Cliente -->
<div class="modal" id="modalNuevoCliente">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Nuevo Cliente</h4>
            <button class="modal-close" type="button" data-close>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formNuevoCliente">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Cédula/RNC *</label>
                    <input type="text" name="cedula_rnc" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="2"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-close>Cancelar</button>
            <button type="button" class="btn btn-primary" id="guardarClienteBtn">Guardar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('foto');
    const capturePhotoBtn = document.getElementById('capturePhotoBtn');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewContainer = document.getElementById('photoPreviewContainer');
    const removePhotoBtn = document.getElementById('removePhoto');

    if (capturePhotoBtn && fotoInput) {
        capturePhotoBtn.addEventListener('click', function() {
            fotoInput.click();
        });

        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoPreviewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        if (removePhotoBtn) {
            removePhotoBtn.addEventListener('click', function() {
                fotoInput.value = '';
                photoPreview.src = '';
                photoPreviewContainer.style.display = 'none';
            });
        }
    }

    // Modal Nuevo Cliente
    const modal = document.getElementById('modalNuevoCliente');
    const abrirBtn = document.getElementById('nuevoClienteBtn');
    const cerrarBtns = modal.querySelectorAll('[data-close]');
    const guardarBtn = document.getElementById('guardarClienteBtn');
    const form = document.getElementById('formNuevoCliente');
    const clienteSelect = document.getElementById('clienteSelect');

    abrirBtn.addEventListener('click', () => {
        modal.classList.add('active');
    });
    cerrarBtns.forEach(btn => btn.addEventListener('click', () => modal.classList.remove('active')));

    guardarBtn.addEventListener('click', async () => {
        const formData = new FormData(form);
        try {
            const resp = await fetch("{{ route('clientes.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            });
            if (resp.ok) {
                const data = await resp.json();
                const nuevo = data.cliente;
                const opt = document.createElement('option');
                opt.value = nuevo.id;
                opt.textContent = `${nuevo.nombre} - ${nuevo.cedula_rnc ?? ''}`;
                clienteSelect.appendChild(opt);
                clienteSelect.value = nuevo.id;
                modal.classList.remove('active');
                form.reset();
            } else {
                const errorData = await resp.json().catch(() => ({}));
                alert('No se pudo crear el cliente. Verifique los datos.');
            }
        } catch (e) {
            alert('Error de conexión al crear el cliente.');
        }
    });
});
</script>
@endpush

