@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@push('css')
<style>
    .photos-mobile-actions .btn {
        font-size: 0.8rem;
        line-height: 1.2;
        min-height: 42px;
        white-space: nowrap;
    }

    .camera-panel {
        border: 1px solid #d2d6da;
        border-radius: 0.75rem;
        background: #fff;
    }

    .camera-preview-wrap {
        background: #111827;
        border-radius: 0.75rem;
        overflow: hidden;
        aspect-ratio: 4 / 3;
    }

    .camera-preview-wrap video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .camera-status {
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Registrar Nuevo Equipo'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 mb-4">
                <h2 class="text-white mb-0">Registrar Nuevo Equipo</h2>
                <p class="text-white text-sm opacity-8">Complete los campos para registrar un nuevo equipo para su reparación</p>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('reparaciones.store') }}" enctype="multipart/form-data" id="reparacionForm">
            @csrf

            <!-- Sección de Garantía (AL PRINCIPIO) -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i> ¿Es una Reparación en Garantía?</h6>
                            <p class="text-sm mb-0 mt-1">Si el equipo está siendo recibido por garantía, seleccione la reparación original para autocompletar los datos</p>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="es_garantia" id="es_garantia" value="1" {{ old('es_garantia') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="es_garantia">
                                    Esta es una reparación en garantía
                                </label>
                            </div>

                            <div id="garantia_fields" style="display: {{ old('es_garantia') ? 'block' : 'none' }};">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Buscar Reparación Original por Código * <span class="text-danger">(Requerido si es garantía)</span></label>
                                        <div class="position-relative">
                                            <input type="text" 
                                                   id="buscar_reparacion_codigo" 
                                                   class="form-control @error('reparacion_original_id') is-invalid @enderror" 
                                                   placeholder="Ej: REP-00001, REP-00002..."
                                                   autocomplete="off">
                                            <input type="hidden" name="reparacion_original_id" id="reparacion_original_id" value="{{ old('reparacion_original_id') }}">
                                            <div id="resultados_busqueda" class="position-absolute w-100 bg-white border rounded shadow-lg mt-1" style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000;">
                                                <!-- Los resultados se mostrarán aquí -->
                                            </div>
                                        </div>
                                        <div id="reparacion_seleccionada" class="mt-2" style="display: none;">
                                            <div class="alert mb-0" id="rep_seleccionada_alert" style="border-left: 4px solid;">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                                            <strong id="rep_seleccionada_codigo" style="color: #1a202c; font-size: 1rem;"></strong>
                                                            <span id="rep_garantias_badge" class="badge fw-bold" style="display: none; font-size: 0.75rem; padding: 0.35em 0.65em;"></span>
                                                            <span id="rep_dias_restantes_badge" class="badge fw-bold" style="display: none; font-size: 0.75rem; padding: 0.35em 0.65em;"></span>
                                                        </div>
                                                        <div id="rep_seleccionada_info" class="mb-2" style="color: #4a5568; font-size: 0.875rem; line-height: 1.5;"></div>
                                                        <div id="rep_garantias_info" style="display: none; background-color: rgba(0,0,0,0.05); padding: 0.5rem; border-radius: 0.25rem; margin-top: 0.5rem;">
                                                            <small style="color: #2d3748; font-weight: 500; display: flex; align-items: flex-start; gap: 0.5rem;">
                                                                <i class="fas fa-info-circle mt-1" style="color: #3182ce;"></i>
                                                                <span id="rep_garantias_texto" style="flex: 1;"></span>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="limpiarReparacionSeleccionada()" style="flex-shrink: 0;">
                                                        <i class="fas fa-times" style="font-size: 1.1rem;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @error('reparacion_original_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Escriba el código de la reparación original (ej: REP-00001). Al seleccionar, se autocompletarán automáticamente los datos del equipo, cliente y técnico</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Período de Garantía (días)</label>
                                        <input type="number" name="periodo_garantia_dias" id="periodo_garantia_dias" 
                                               class="form-control @error('periodo_garantia_dias') is-invalid @enderror" 
                                               value="{{ old('periodo_garantia_dias', 30) }}" min="1" max="365" 
                                               placeholder="Ej: 30, 60, 90">
                                        @error('periodo_garantia_dias')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Número de días de garantía (se calculará automáticamente al entregar)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Información del Cliente</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cliente *</label>
                                    <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                                        <option value="">Seleccione un cliente</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->nombre }} - {{ $cliente->telefono }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cliente_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Equipo -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Detalles del Equipo</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Equipo *</label>
                                    <select name="tipo" id="tipo_equipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                        <option value="PC de Escritorio">PC de Escritorio</option>
                                        <option value="Laptop">Laptop</option>
                                        <option value="Tarjeta Gráfica (GPU)">Tarjeta Gráfica (GPU)</option>
                                        <option value="Consola de Videojuegos">Consola de Videojuegos</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="gpu_info" class="alert alert-info mt-2 mb-0" style="display: none;">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>GPU detectada:</strong> Este equipo será identificado como GPU y aparecerá en el widget "Siguiente GPU" del dashboard.
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3" id="tipo_personalizado_container" style="display: none;">
                                    <label class="form-label">Especifique el tipo *</label>
                                    <input type="text" name="tipo_personalizado" id="tipo_personalizado" 
                                           class="form-control @error('tipo_personalizado') is-invalid @enderror" 
                                           placeholder="Ej: Monitor, Impresora, etc." value="{{ old('tipo_personalizado') }}">
                                    @error('tipo_personalizado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Marca *</label>
                                    <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" 
                                           placeholder="Ej: Nvidia" value="{{ old('marca') }}" required>
                                    @error('marca')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Modelo *</label>
                                    <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                                           placeholder="Ej: GeForce RTX 4090" value="{{ old('modelo') }}" required>
                                    @error('modelo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Número de Serie</label>
                                    <input type="text" name="numero_serie" class="form-control @error('numero_serie') is-invalid @enderror" 
                                           placeholder="Ej: SN-9876543210" value="{{ old('numero_serie') }}">
                                    @error('numero_serie')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Servicio *</label>
                                    <select name="tipo_servicio" id="tipo_servicio" class="form-select @error('tipo_servicio') is-invalid @enderror" required>
                                        <option value="reparacion" {{ old('tipo_servicio') == 'reparacion' ? 'selected' : '' }}>Reparación</option>
                                        <option value="mantenimiento" {{ old('tipo_servicio') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                    </select>
                                    @error('tipo_servicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <strong>Reparación:</strong> Requiere diagnóstico y aprobación del cliente<br>
                                        <strong>Mantenimiento:</strong> Se genera factura inmediatamente si hay precio
                                    </small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Personal que Recibió *</label>
                                    <select name="recepcionista_id" class="form-select @error('recepcionista_id') is-invalid @enderror" required>
                                        <option value="">Seleccione quien recibió</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" {{ old('recepcionista_id', auth()->id()) == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->firstname }} {{ $usuario->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('recepcionista_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Técnico Asignado <span class="text-muted">(Opcional - se puede asignar después)</span></label>
                                    <select name="tecnico_id" class="form-select">
                                        <option value="">Sin asignar - Disponible para cualquier técnico</option>
                                        @foreach($tecnicos as $tecnico)
                                            <option value="{{ $tecnico->id }}">{{ $tecnico->firstname }} {{ $tecnico->lastname }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Los técnicos pueden tomar trabajos disponibles después de recibirlos</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha Prometida</label>
                                    <input type="date" name="fecha_prometida" class="form-control" value="{{ old('fecha_prometida') }}">
                                </div>

                                <!-- Precio Cotizado (solo para mantenimiento) -->
                                <div class="col-md-6 mb-3" id="precio_cotizado_container" style="display: none;">
                                    <label class="form-label">Precio Cotizado ($)</label>
                                    <input type="number" name="precio_cotizado" id="precio_cotizado" class="form-control @error('precio_cotizado') is-invalid @enderror" 
                                           step="0.01" min="0" value="{{ old('precio_cotizado') }}" placeholder="0.00">
                                    <small class="text-muted">Para mantenimientos, se genera factura inmediatamente si hay precio</small>
                                    @error('precio_cotizado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($impuestosActivos ?? true)
                                <!-- Checkbox Aplicar Impuesto (solo para mantenimiento con precio) -->
                                <div class="col-md-6 mb-3" id="aplicar_impuesto_container" style="display: none;">
                                    <div class="form-check mt-4">
                                        <input type="hidden" name="aplicar_impuesto" value="0">
                                        <input class="form-check-input" type="checkbox" name="aplicar_impuesto" id="aplicar_impuesto" value="1" {{ old('aplicar_impuesto', false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="aplicar_impuesto">
                                            <strong>Aplicar Impuesto</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Marque esta opción si desea aplicar impuesto a esta factura. Se generará el NCF automáticamente si está configurado.
                                        </small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción del Problema -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Descripción del Problema</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Detalle la falla del equipo *</label>
                                    <textarea name="descripcion_problema" class="form-control @error('descripcion_problema') is-invalid @enderror" 
                                              rows="5" placeholder="Ej: El equipo no enciende, hace ruidos extraños al intentar arrancar..." 
                                              required>{{ old('descripcion_problema') }}</textarea>
                                    @error('descripcion_problema')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fotografías del Equipo -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Fotografías del Equipo</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Opciones para móvil -->
                                <div class="col-12 mb-3 mobile-only">
                                    <label class="form-label">Tomar o subir fotografías</label>
                                    <div class="d-flex gap-2 flex-nowrap photos-mobile-actions">
                                        <button type="button" class="btn btn-sm btn-primary flex-fill px-2" id="btn_camera" data-allow-multiple="true">
                                            <i class="fas fa-camera me-2"></i>Tomar Foto con Cámara
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill px-2" id="btn_gallery" data-allow-multiple="true">
                                            <i class="fas fa-images me-2"></i>Seleccionar de Galería
                                        </button>
                                    </div>
                                    <!-- Input sin multiple para cámara - permite tomar varias fotos una por una -->
                                    <div id="camera_panel" class="camera-panel mt-3 p-2 d-none">
                                        <div class="camera-preview-wrap">
                                            <video id="camera_preview" autoplay playsinline muted></video>
                                        </div>
                                        <canvas id="camera_canvas" class="d-none"></canvas>
                                        <div class="d-flex gap-2 mt-2">
                                            <button type="button" class="btn btn-sm btn-primary flex-fill" id="btn_capture_photo" data-allow-multiple="true">
                                                <i class="fas fa-camera-retro me-1"></i>Capturar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" id="btn_close_camera" data-allow-multiple="true">
                                                <i class="fas fa-times me-1"></i>Cerrar
                                            </button>
                                        </div>
                                        <small id="camera_status" class="camera-status text-muted d-block mt-2">
                                            La camara se abrira dentro de esta pagina para tomar varias fotos seguidas.
                                        </small>
                                    </div>
                                    <input type="file" name="fotos[]" id="fotos_camera" class="d-none" 
                                           accept="image/*" capture="environment" multiple>
                                    <input type="file" name="fotos[]" id="fotos_gallery" class="d-none" 
                                           accept="image/*" multiple>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span class="d-block">Use la camara integrada para capturar varias fotos seguidas. Si el navegador no lo permite, se abrira el selector del telefono como respaldo.</span><span class="d-none">
                                        Puede tomar múltiples fotos. Después de cada foto, presione "Tomar Foto con Cámara" nuevamente para tomar otra.
                                    </span></small>
                                </div>
                                
                                <!-- Opción para escritorio -->
                                <div class="col-12 mb-3 desktop-only">
                                    <label class="form-label">Tomar o subir fotografías (múltiples)</label>
                                    <input type="file" name="fotos[]" id="fotos_input" class="form-control" 
                                           accept="image/*" multiple>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Puede seleccionar múltiples fotos a la vez.
                                    </small>
                                </div>
                                
                                <div class="col-12">
                                    <div id="fotos_count" class="alert alert-info mb-3" style="display: none;">
                                        <i class="fas fa-images me-2"></i>
                                        <strong><span id="fotos_count_text">0 fotos seleccionadas</span></strong>
                                    </div>
                                    <div id="fotos_preview" class="mt-3 row"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-end">
                            <a href="{{ route('reparaciones.index') }}" class="btn btn-light">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Registro
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @include('layouts.footers.auth.footer')
    </div>

    @push('js')
    <script>
        // Variables globales para fotos
        let selectedFiles = [];
        let fotosInput, fotosCamera, fotosGallery, fotosPreview, fotosCount, fotosCountText;
        let cameraPanel, cameraPreview, cameraCanvas, cameraStatus, btnCapturePhoto, btnCloseCamera;
        let cameraStream = null;

        // Función global para abrir la cámara (debe estar disponible antes del DOM)
        

        // Función para procesar archivos seleccionados
        function processFiles(files) {
            const fileArray = Array.from(files);
            
            // Agregar nuevos archivos a la lista (permitir agregar más)
            fileArray.forEach(file => {
                if (file.type.startsWith('image/')) {
                    // Verificar si el archivo ya no está seleccionado
                    // Usar un identificador único basado en nombre, tamaño y timestamp
                    const fileId = file.name + '_' + file.size + '_' + file.lastModified;
                    if (!selectedFiles.find(f => {
                        const fId = f.name + '_' + f.size + '_' + f.lastModified;
                        return fId === fileId;
                    })) {
                        selectedFiles.push(file);
                    }
                }
            });

            // Actualizar preview y contador
            updatePreview();
            updateCount();
            updateInputs();
        }

        // Funciones para preview y contador
        function updatePreview() {
            if (!fotosPreview) return;
            
            fotosPreview.innerHTML = '';
            
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-4 col-6 mb-3';
                    col.innerHTML = `
                        <div class="card position-relative">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                    onclick="removePhoto(${index})" style="z-index: 10;">
                                <i class="fas fa-times"></i>
                            </button>
                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <small class="text-muted d-block text-truncate" title="${file.name}">${file.name}</small>
                                <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                            </div>
                        </div>
                    `;
                    fotosPreview.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        }

        function updateCount() {
            if (!fotosCount || !fotosCountText) return;
            
            const count = selectedFiles.length;
            if (count > 0) {
                fotosCount.style.display = 'block';
                fotosCountText.textContent = count + (count === 1 ? ' foto seleccionada' : ' fotos seleccionadas');
            } else {
                fotosCount.style.display = 'none';
            }
        }

        function updateInputs() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            
            // Actualizar todos los inputs
            if (fotosInput) {
                fotosInput.files = dt.files;
            }
            if (fotosCamera) {
                fotosCamera.files = dt.files;
            }
            if (fotosGallery) {
                fotosGallery.files = dt.files;
            }
        }

        function setCameraStatus(message, isError = false) {
            if (!cameraStatus) return;

            cameraStatus.textContent = message;
            cameraStatus.classList.toggle('text-danger', isError);
            cameraStatus.classList.toggle('text-muted', !isError);
        }

        function canUseInlineCamera() {
            const localhostHosts = ['localhost', '127.0.0.1', '::1'];

            return !!(
                navigator.mediaDevices &&
                navigator.mediaDevices.getUserMedia &&
                (window.isSecureContext || localhostHosts.includes(window.location.hostname))
            );
        }

        function openNativeCameraFallback() {
            stopInlineCamera();

            if (fotosCamera) {
                const originalStyle = fotosCamera.getAttribute('style') || '';

                fotosCamera.style.position = 'fixed';
                fotosCamera.style.left = '-9999px';
                fotosCamera.style.top = '0';
                fotosCamera.style.opacity = '0';
                fotosCamera.style.pointerEvents = 'none';
                fotosCamera.classList.remove('d-none');

                if (typeof fotosCamera.showPicker === 'function') {
                    fotosCamera.showPicker();
                } else {
                    fotosCamera.click();
                }

                setTimeout(() => {
                    fotosCamera.setAttribute('style', originalStyle);
                    fotosCamera.classList.add('d-none');
                }, 300);

                setCameraStatus('Se abrio el selector nativo del telefono como respaldo.', true);
            }
        }

        function stopInlineCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }

            if (cameraPreview) {
                cameraPreview.srcObject = null;
            }

            if (cameraPanel) {
                cameraPanel.classList.add('d-none');
            }
        }

        async function startInlineCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                return false;
            }

            try {
                stopInlineCamera();

                let stream;

                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' }
                        },
                        audio: false
                    });
                } catch (primaryError) {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: false
                    });
                }

                cameraStream = stream;

                if (cameraPreview) {
                    cameraPreview.srcObject = stream;
                    await cameraPreview.play();
                }

                if (cameraPanel) {
                    cameraPanel.classList.remove('d-none');
                }

                setCameraStatus('Camara lista. Puede capturar varias fotos seguidas.');

                return true;
            } catch (error) {
                stopInlineCamera();
                return false;
            }
        }

        async function openPreferredCamera() {
            if (!canUseInlineCamera()) {
                openNativeCameraFallback();
                return false;
            }

            const opened = await startInlineCamera();

            if (!opened) {
                setCameraStatus('No se pudo abrir la camara integrada. Revise permisos del navegador.', true);
            }

            return opened;
        }

        function openPreferredGallery() {
            stopInlineCamera();

            if (fotosGallery) {
                fotosGallery.click();
            }
        }

        function capturePhotoFromStream() {
            if (!cameraPreview || !cameraCanvas || !cameraStream) {
                return;
            }

            const width = cameraPreview.videoWidth || 1280;
            const height = cameraPreview.videoHeight || 960;

            cameraCanvas.width = width;
            cameraCanvas.height = height;

            const context = cameraCanvas.getContext('2d');
            context.drawImage(cameraPreview, 0, 0, width, height);

            cameraCanvas.toBlob(function(blob) {
                if (!blob) {
                    setCameraStatus('No se pudo capturar la foto.', true);
                    return;
                }

                const file = new File([blob], 'camara-' + Date.now() + '.jpg', {
                    type: 'image/jpeg',
                    lastModified: Date.now(),
                });

                processFiles([file]);
                setCameraStatus('Foto agregada. Puede capturar otra inmediatamente.');
            }, 'image/jpeg', 0.9);
        }

        // Función global para eliminar una foto
        window.removePhoto = function(index) {
            selectedFiles.splice(index, 1);
            updatePreview();
            updateCount();
            updateInputs();
        };

            // Inicializar cuando el DOM esté listo
            document.addEventListener('DOMContentLoaded', function() {
                // Mostrar/ocultar campo tipo personalizado y info de GPU
                const tipoEquipo = document.getElementById('tipo_equipo');
                if (tipoEquipo) {
                    tipoEquipo.addEventListener('change', function() {
                        const tipoPersonalizadoContainer = document.getElementById('tipo_personalizado_container');
                        const tipoPersonalizadoInput = document.getElementById('tipo_personalizado');
                        const gpuInfo = document.getElementById('gpu_info');
                        
                        if (this.value === 'Otro') {
                            tipoPersonalizadoContainer.style.display = 'block';
                            tipoPersonalizadoInput.setAttribute('required', 'required');
                            gpuInfo.style.display = 'none';
                        } else if (this.value === 'Tarjeta Gráfica (GPU)') {
                            tipoPersonalizadoContainer.style.display = 'none';
                            tipoPersonalizadoInput.removeAttribute('required');
                            tipoPersonalizadoInput.value = '';
                            gpuInfo.style.display = 'block';
                        } else {
                            tipoPersonalizadoContainer.style.display = 'none';
                            tipoPersonalizadoInput.removeAttribute('required');
                            tipoPersonalizadoInput.value = '';
                            gpuInfo.style.display = 'none';
                        }
                    });

                    // Ejecutar al cargar la página si hay un valor seleccionado
                    if (tipoEquipo.value === 'Tarjeta Gráfica (GPU)') {
                        const gpuInfo = document.getElementById('gpu_info');
                        if (gpuInfo) {
                            gpuInfo.style.display = 'block';
                        }
                    }
                }

                // Mostrar/ocultar campos de precio cotizado y aplicar impuesto según tipo de servicio
                const tipoServicio = document.getElementById('tipo_servicio');
                const precioCotizadoContainer = document.getElementById('precio_cotizado_container');
                const aplicarImpuestoContainer = document.getElementById('aplicar_impuesto_container');
                const precioCotizadoInput = document.getElementById('precio_cotizado');
                const impuestosActivos = @json($impuestosActivos ?? true);
                
                function togglePrecioFields() {
                    if (tipoServicio && tipoServicio.value === 'mantenimiento') {
                        if (precioCotizadoContainer) precioCotizadoContainer.style.display = 'block';
                        if (aplicarImpuestoContainer) aplicarImpuestoContainer.style.display = impuestosActivos ? 'block' : 'none';
                    } else {
                        if (precioCotizadoContainer) precioCotizadoContainer.style.display = 'none';
                        if (aplicarImpuestoContainer) aplicarImpuestoContainer.style.display = 'none';
                        if (precioCotizadoInput) precioCotizadoInput.value = '';
                    }
                }
                
                if (tipoServicio) {
                    tipoServicio.addEventListener('change', togglePrecioFields);
                    // Ejecutar al cargar si ya hay un valor
                    togglePrecioFields();
                }

            // Inicializar variables de fotos
            fotosInput = document.getElementById('fotos_input');
            fotosCamera = document.getElementById('fotos_camera');
            fotosGallery = document.getElementById('fotos_gallery');
            fotosPreview = document.getElementById('fotos_preview');
            fotosCount = document.getElementById('fotos_count');
            fotosCountText = document.getElementById('fotos_count_text');
            cameraPanel = document.getElementById('camera_panel');
            cameraPreview = document.getElementById('camera_preview');
            cameraCanvas = document.getElementById('camera_canvas');
            cameraStatus = document.getElementById('camera_status');
            btnCapturePhoto = document.getElementById('btn_capture_photo');
            btnCloseCamera = document.getElementById('btn_close_camera');

            // Event listeners para botones móviles
            const btnCamera = document.getElementById('btn_camera');
            const btnGallery = document.getElementById('btn_gallery');
            
            // Event listeners para botones móviles
            if (false && btnCamera) {
                btnCamera.onclick = async function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (!fotosCamera) {
                        return false;
                    }
                    
                    try {
                        await openPreferredCamera();
                    } catch (error) {
                        // Error silencioso - el navegador mostrará su propio mensaje si es necesario
                    }
                    return false;
                };
            }

            if (false && btnGallery) {
                btnGallery.onclick = async function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (!fotosGallery) {
                        return false;
                    }
                    
                    try {
                        await openPreferredGallery();
                    } catch (error) {
                        // Error silencioso - el navegador mostrará su propio mensaje si es necesario
                    }
                    return false;
                };
            }

            if (btnCamera) {
                btnCamera.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (canUseInlineCamera()) {
                        openPreferredCamera();
                    } else {
                        openNativeCameraFallback();
                    }

                    return false;
                };
            }

            if (btnGallery) {
                btnGallery.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    openPreferredGallery();
                    return false;
                };
            }

            if (btnCapturePhoto) {
                btnCapturePhoto.addEventListener('click', function() {
                    capturePhotoFromStream();
                });
            }

            if (btnCloseCamera) {
                btnCloseCamera.addEventListener('click', function() {
                    stopInlineCamera();
                });
            }

            // Event listeners para escritorio
            if (fotosInput) {
                fotosInput.addEventListener('change', function(e) {
                    processFiles(e.target.files);
                });
            }

            // Mostrar/ocultar campos de garantía
            const esGarantiaCheckbox = document.getElementById('es_garantia');
            const garantiaFields = document.getElementById('garantia_fields');
            
            if (esGarantiaCheckbox && garantiaFields) {
                esGarantiaCheckbox.addEventListener('change', function() {
                    garantiaFields.style.display = this.checked ? 'block' : 'none';
                    if (!this.checked) {
                        // Limpiar campos si se desmarca
                        limpiarReparacionSeleccionada();
                        // Asegurar que todos los campos estén habilitados
                        deshabilitarCamposGarantia(false);
                    }
                });
            }

            // Búsqueda de reparación por código
            const buscarReparacionInput = document.getElementById('buscar_reparacion_codigo');
            const resultadosBusqueda = document.getElementById('resultados_busqueda');
            const reparacionSeleccionadaDiv = document.getElementById('reparacion_seleccionada');
            const reparacionOriginalIdInput = document.getElementById('reparacion_original_id');
            let timeoutBusqueda = null;

            // Función para deshabilitar/habilitar campos cuando hay garantías previas
            function deshabilitarCamposGarantia(deshabilitar) {
                const clienteSelect = document.getElementById('cliente_id');
                const tipoSelect = document.getElementById('tipo_equipo');
                const tipoPersonalizadoInput = document.getElementById('tipo_personalizado');
                const marcaInput = document.querySelector('input[name="marca"]');
                const modeloInput = document.querySelector('input[name="modelo"]');
                const numeroSerieInput = document.querySelector('input[name="numero_serie"]');
                const tecnicoSelect = document.querySelector('select[name="tecnico_id"]');
                
                const campos = [
                    clienteSelect,
                    tipoSelect,
                    tipoPersonalizadoInput,
                    marcaInput,
                    modeloInput,
                    numeroSerieInput,
                    tecnicoSelect
                ];
                
                campos.forEach(campo => {
                    if (campo) {
                        campo.disabled = deshabilitar;
                        if (deshabilitar) {
                            campo.style.backgroundColor = '#f3f4f6';
                            campo.style.cursor = 'not-allowed';
                            campo.title = 'Este campo está deshabilitado porque la información ya está disponible de la reparación original';
                        } else {
                            campo.style.backgroundColor = '';
                            campo.style.cursor = '';
                            campo.title = '';
                        }
                    }
                });
            }

            // Función para autocompletar campos desde datos de reparación
            function autocompletarDesdeReparacion(datos) {
                if (!datos) {
                    return;
                }

                // Autocompletar cliente
                const clienteSelect = document.getElementById('cliente_id');
                if (clienteSelect && datos.cliente_id) {
                    clienteSelect.value = datos.cliente_id;
                }

                // Autocompletar tipo de equipo
                const tipoSelect = document.getElementById('tipo_equipo');
                if (tipoSelect && datos.tipo) {
                    tipoSelect.value = datos.tipo;
                    // Mostrar/ocultar campo personalizado
                    const tipoPersonalizadoContainer = document.getElementById('tipo_personalizado_container');
                    if (datos.tipo === 'Otro' && tipoPersonalizadoContainer) {
                        tipoPersonalizadoContainer.style.display = 'block';
                        const tipoPersonalizadoInput = document.getElementById('tipo_personalizado');
                        if (tipoPersonalizadoInput && datos.tipo_personalizado) {
                            tipoPersonalizadoInput.value = datos.tipo_personalizado;
                        }
                    } else if (tipoPersonalizadoContainer) {
                        tipoPersonalizadoContainer.style.display = 'none';
                    }
                    // Mostrar info de GPU si aplica
                    const gpuInfo = document.getElementById('gpu_info');
                    if (gpuInfo) {
                        gpuInfo.style.display = datos.tipo === 'Tarjeta Gráfica (GPU)' ? 'block' : 'none';
                    }
                }

                // Autocompletar marca
                const marcaInput = document.querySelector('input[name="marca"]');
                if (marcaInput && datos.marca) {
                    marcaInput.value = datos.marca;
                }

                // Autocompletar modelo
                const modeloInput = document.querySelector('input[name="modelo"]');
                if (modeloInput && datos.modelo) {
                    modeloInput.value = datos.modelo;
                }

                // Autocompletar número de serie
                const numeroSerieInput = document.querySelector('input[name="numero_serie"]');
                if (numeroSerieInput && datos.numero_serie) {
                    numeroSerieInput.value = datos.numero_serie;
                }

                // Autocompletar técnico
                const tecnicoSelect = document.querySelector('select[name="tecnico_id"]');
                if (tecnicoSelect && datos.tecnico_id) {
                    tecnicoSelect.value = datos.tecnico_id;
                }
            }

            // Función para buscar reparación
            function buscarReparacion(codigo) {
                if (!codigo || codigo.length < 3) {
                    resultadosBusqueda.style.display = 'none';
                    return;
                }

                // Limpiar timeout anterior
                if (timeoutBusqueda) {
                    clearTimeout(timeoutBusqueda);
                }

                // Esperar 500ms antes de buscar (debounce)
                timeoutBusqueda = setTimeout(() => {
                    fetch(`/api/reparaciones/buscar/${encodeURIComponent(codigo)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data) {
                            // Mostrar resultado único
                            mostrarResultadoUnico(data.data);
                        } else {
                            // No se encontró
                            resultadosBusqueda.innerHTML = '<div class="p-3 text-muted text-center">No se encontró una reparación finalizada/entregada con ese código</div>';
                            resultadosBusqueda.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error al buscar reparación:', error);
                        resultadosBusqueda.innerHTML = '<div class="p-3 text-danger text-center">Error al buscar reparación</div>';
                        resultadosBusqueda.style.display = 'block';
                    });
                }, 500);
            }

            // Función para mostrar resultado único y seleccionarlo automáticamente
            function mostrarResultadoUnico(datos) {
                // Ocultar resultados
                resultadosBusqueda.style.display = 'none';
                
                // Establecer el ID de la reparación
                if (reparacionOriginalIdInput) {
                    reparacionOriginalIdInput.value = datos.id;
                }

                // Mostrar información de la reparación seleccionada
                document.getElementById('rep_seleccionada_codigo').textContent = datos.codigo_reparacion;
                
                let infoHTML = `<span style="color: #4a5568;"><strong>Cliente:</strong> ${datos.cliente_nombre}</span><br>` +
                    `<span style="color: #4a5568;"><strong>Equipo:</strong> ${datos.marca} ${datos.modelo}</span><br>` +
                    `<span style="color: #4a5568;"><strong>Finalizada:</strong> ${datos.fecha_finalizacion || 'N/A'}</span>`;
                
                // Agregar información de días restantes de garantía si existe
                if (datos.dias_restantes_garantia !== null && datos.dias_restantes_garantia !== undefined) {
                    infoHTML += `<br><span style="color: #4a5568;"><strong>Vence:</strong> ${datos.fecha_vencimiento_garantia || 'N/A'}</span>`;
                }
                
                document.getElementById('rep_seleccionada_info').innerHTML = infoHTML;
                
                // Mostrar información sobre garantías
                const garantiasBadge = document.getElementById('rep_garantias_badge');
                const diasRestantesBadge = document.getElementById('rep_dias_restantes_badge');
                const garantiasInfo = document.getElementById('rep_garantias_info');
                const garantiasTexto = document.getElementById('rep_garantias_texto');
                const alertDiv = document.getElementById('rep_seleccionada_alert');
                
                // Mostrar días restantes de garantía de la reparación original
                if (datos.dias_restantes_garantia !== null && datos.dias_restantes_garantia !== undefined) {
                    let textoDias = '';
                    let colorBadge = '';
                    
                    if (datos.estado_garantia_original === 'vencida') {
                        textoDias = `Vencida hace ${datos.dias_restantes_garantia} ${datos.dias_restantes_garantia === 1 ? 'día' : 'días'}`;
                        colorBadge = 'bg-danger';
                    } else if (datos.estado_garantia_original === 'por_vencer') {
                        textoDias = `Vence en ${datos.dias_restantes_garantia} ${datos.dias_restantes_garantia === 1 ? 'día' : 'días'}`;
                        colorBadge = 'bg-warning text-dark';
                    } else {
                        textoDias = `${datos.dias_restantes_garantia} ${datos.dias_restantes_garantia === 1 ? 'día' : 'días'} restantes`;
                        colorBadge = 'bg-success';
                    }
                    
                    diasRestantesBadge.textContent = textoDias;
                    diasRestantesBadge.className = `badge fw-bold ${colorBadge}`;
                    diasRestantesBadge.style.display = 'inline-block';
                } else {
                    diasRestantesBadge.style.display = 'none';
                }
                
                if (datos.tiene_garantias) {
                    // Ya tiene garantías asociadas
                    garantiasBadge.textContent = `${datos.cantidad_garantias} ${datos.cantidad_garantias === 1 ? 'Garantía' : 'Garantías'}`;
                    garantiasBadge.className = 'badge fw-bold';
                    garantiasBadge.style.display = 'inline-block';
                    garantiasBadge.style.backgroundColor = '#d69e2e';
                    garantiasBadge.style.color = '#ffffff';
                    garantiasBadge.style.border = 'none';
                    
                    // Listar las garantías de forma más legible con días restantes
                    let garantiasLista = datos.garantias_asociadas.map(g => {
                        let infoGarantia = `<strong>${g.codigo}</strong> (${g.estado}) - ${g.fecha_ingreso}`;
                        if (g.dias_restantes !== null && g.dias_restantes !== undefined) {
                            let textoDias = '';
                            if (g.estado_garantia === 'vencida') {
                                textoDias = ` - <span class="text-danger">Vencida hace ${g.dias_restantes} ${g.dias_restantes === 1 ? 'día' : 'días'}</span>`;
                            } else if (g.estado_garantia === 'por_vencer') {
                                textoDias = ` - <span class="text-warning">Vence en ${g.dias_restantes} ${g.dias_restantes === 1 ? 'día' : 'días'}</span>`;
                            } else {
                                textoDias = ` - <span class="text-success">${g.dias_restantes} ${g.dias_restantes === 1 ? 'día' : 'días'} restantes</span>`;
                            }
                            infoGarantia += textoDias;
                        }
                        return infoGarantia;
                    }).join('<br>');
                    garantiasTexto.innerHTML = `<strong>Esta reparación ya tiene ${datos.cantidad_garantias} ${datos.cantidad_garantias === 1 ? 'garantía asociada' : 'garantías asociadas'}:</strong><br>${garantiasLista}`;
                    garantiasInfo.style.display = 'block';
                    
                    // Cambiar color del alert a warning con mejor contraste
                    alertDiv.className = 'alert mb-0';
                    alertDiv.style.backgroundColor = '#fef3c7';
                    alertDiv.style.borderColor = '#d69e2e';
                    alertDiv.style.color = '#1a202c';
                    
                    // Deshabilitar campos innecesarios ya que la información ya está disponible
                    deshabilitarCamposGarantia(true);
                } else {
                    // No tiene garantías
                    garantiasBadge.textContent = 'Sin Garantías';
                    garantiasBadge.className = 'badge fw-bold';
                    garantiasBadge.style.display = 'inline-block';
                    garantiasBadge.style.backgroundColor = '#10b981';
                    garantiasBadge.style.color = '#ffffff';
                    garantiasBadge.style.border = 'none';
                    
                    garantiasInfo.style.display = 'none';
                    
                    // Mantener color del alert como info con mejor contraste
                    alertDiv.className = 'alert mb-0';
                    alertDiv.style.backgroundColor = '#dbeafe';
                    alertDiv.style.borderColor = '#3b82f6';
                    alertDiv.style.color = '#1a202c';
                    
                    // Habilitar todos los campos
                    deshabilitarCamposGarantia(false);
                }
                
                reparacionSeleccionadaDiv.style.display = 'block';

                // Limpiar el input de búsqueda
                if (buscarReparacionInput) {
                    buscarReparacionInput.value = datos.codigo_reparacion;
                }

                // Autocompletar campos
                autocompletarDesdeReparacion(datos);
            }

            // Función para limpiar reparación seleccionada
            window.limpiarReparacionSeleccionada = function() {
                if (reparacionOriginalIdInput) {
                    reparacionOriginalIdInput.value = '';
                }
                if (buscarReparacionInput) {
                    buscarReparacionInput.value = '';
                }
                reparacionSeleccionadaDiv.style.display = 'none';
                resultadosBusqueda.style.display = 'none';
                
                // Habilitar todos los campos nuevamente
                deshabilitarCamposGarantia(false);
            };

            // Event listener para el input de búsqueda
            if (buscarReparacionInput) {
                buscarReparacionInput.addEventListener('input', function(e) {
                    const codigo = e.target.value.trim();
                    buscarReparacion(codigo);
                });

                // Ocultar resultados al hacer click fuera
                document.addEventListener('click', function(e) {
                    if (buscarReparacionInput && resultadosBusqueda) {
                        if (!buscarReparacionInput.contains(e.target) && !resultadosBusqueda.contains(e.target)) {
                            resultadosBusqueda.style.display = 'none';
                        }
                    }
                });
            }

            // Event listeners para móvil (cámara y galería)
            if (fotosCamera) {
                fotosCamera.addEventListener('change', function(e) {
                    if (e.target.files && e.target.files.length > 0) {
                        // Procesar la foto tomada
                        processFiles(e.target.files);
                        
                        // Limpiar el input inmediatamente para permitir agregar mÃ¡s fotos
                        // Algunos navegadores mÃ³viles siguen entregando una sola captura por evento
                        setTimeout(() => {
                            e.target.value = '';
                        }, 50);
                        
                    }
                });
            }

            if (fotosGallery) {
                fotosGallery.addEventListener('change', function(e) {
                    if (e.target.files && e.target.files.length > 0) {
                        processFiles(e.target.files);
                    }
                });
            }

            // Interceptar el envío del formulario para combinar todos los archivos
            const reparacionForm = document.getElementById('reparacionForm');
            if (reparacionForm) {
                reparacionForm.addEventListener('submit', function(e) {
                    stopInlineCamera();

                    // Crear un input file oculto con todos los archivos combinados
                    const combinedInput = document.createElement('input');
                    combinedInput.type = 'file';
                    combinedInput.name = 'fotos[]';
                    combinedInput.multiple = true;
                    combinedInput.style.display = 'none';
                    
                    const dt = new DataTransfer();
                    selectedFiles.forEach(file => dt.items.add(file));
                    combinedInput.files = dt.files;
                    
                    // Agregar al formulario
                    this.appendChild(combinedInput);
                    
                    // Limpiar los inputs originales para evitar duplicados
                    if (fotosCamera) {
                        fotosCamera.removeAttribute('name');
                    }
                    if (fotosGallery) {
                        fotosGallery.removeAttribute('name');
                    }
                    if (fotosInput) {
                        fotosInput.removeAttribute('name');
                    }
                });
            }

            window.addEventListener('beforeunload', function() {
                stopInlineCamera();
            });

        });
    </script>
    @endpush
@endsection
