@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Editar Entrada - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Editar Entrada</h1>
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
        <form action="{{ route('equipos.update', $equipo->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Cliente *</label>
                <select name="cliente_id" class="form-control" required>
                    <option value="">Seleccione un cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ old('cliente_id', $equipo->cliente_id) == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre }} - {{ $cliente->cedula_rnc }}
                        </option>
                    @endforeach
                </select>
                @error('cliente_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tipo de Equipo *</label>
                <input type="text" name="tipo" class="form-control" value="{{ old('tipo', $equipo->tipo) }}" placeholder="Ej: Laptop, Smartphone, Tablet, PC" required>
                @error('tipo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Marca</label>
                    <input type="text" name="marca" class="form-control" value="{{ old('marca', $equipo->marca) }}" placeholder="Ej: HP, Apple, Samsung">
                    @error('marca')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ old('modelo', $equipo->modelo) }}" placeholder="Ej: Pavilion 15, iPhone 13">
                    @error('modelo')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Número de Serie</label>
                <input type="text" name="numero_serie" class="form-control" value="{{ old('numero_serie', $equipo->numero_serie) }}" placeholder="Número de serie del equipo">
                @error('numero_serie')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Foto del Equipo</label>
                @if($equipo->foto)
                    <div style="margin-bottom: 0.5rem;">
                        <img src="{{ Storage::url($equipo->foto) }}" alt="Foto actual" style="max-width: 200px; max-height: 200px; border-radius: 0.75rem; border: 1px solid var(--border-color);">
                    </div>
                @endif
                <div class="photo-upload-container">
                    <input type="file" name="foto" id="foto" accept="image/*" capture="environment" class="form-control" style="display: none;">
                    <div class="photo-preview-container" id="photoPreviewContainer" style="display: none;">
                        <img id="photoPreview" src="" alt="Vista previa" style="max-width: 100%; max-height: 300px; border-radius: 0.75rem; margin-top: 0.5rem;">
                        <button type="button" id="removePhoto" class="btn btn-sm btn-danger" style="margin-top: 0.5rem;">
                            <i class="bi bi-trash"></i> Eliminar foto
                        </button>
                    </div>
                    <button type="button" id="capturePhotoBtn" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem;">
                        <i class="bi bi-camera"></i> {{ $equipo->foto ? 'Cambiar Foto' : 'Tomar Foto o Seleccionar Imagen' }}
                    </button>
                </div>
                <small class="form-text">Puedes tomar una foto con la cámara o seleccionar una imagen existente</small>
                @error('foto')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Falla Reportada por el Cliente *</label>
                <textarea name="descripcion_falla" class="form-control" rows="4" placeholder="Descripción detallada de la falla reportada..." required>{{ old('descripcion_falla', $equipo->descripcion_falla) }}</textarea>
                @error('descripcion_falla')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="recibido" {{ old('estado', $equipo->estado) == 'recibido' ? 'selected' : '' }}>Recibido</option>
                    <option value="diagnostico" {{ old('estado', $equipo->estado) == 'diagnostico' ? 'selected' : '' }}>En Diagnóstico</option>
                    <option value="reparacion" {{ old('estado', $equipo->estado) == 'reparacion' ? 'selected' : '' }}>En Reparación</option>
                    <option value="listo" {{ old('estado', $equipo->estado) == 'listo' ? 'selected' : '' }}>Listo</option>
                    <option value="entregado" {{ old('estado', $equipo->estado) == 'entregado' ? 'selected' : '' }}>Entregado</option>
                    <option value="garantia" {{ old('estado', $equipo->estado) == 'garantia' ? 'selected' : '' }}>En Garantía</option>
                </select>
                @error('estado')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('equipos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar Equipo</button>
            </div>
        </form>
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
});
</script>
@endpush

