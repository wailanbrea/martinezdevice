# 📋 Prompt para Recrear Sistema Martínez Service

## Contexto General

Crea un sistema completo de gestión y reparación de equipos electrónicos para un taller técnico llamado "Martínez Service". El sistema debe ser una aplicación web profesional con interfaz moderna y funcionalidades completas de seguimiento de equipos, reparaciones, facturación y contabilidad.

## Stack Tecnológico

**Backend:**
- Laravel 12 (PHP 8.3+)
- MySQL 8.0+
- Laravel Sanctum para autenticación

**Frontend:**
- Blade Templates
- Plantilla Argon Dashboard 2
- JavaScript vanilla
- CSS moderno con modo oscuro/claro

**Librerías Adicionales:**
- SimpleSoftwareIO/simple-qrcode para códigos QR
- Barryvdh/laravel-dompdf para generación de PDFs
- Darkaonline/l5-swagger para documentación API (opcional)

## Estructura de Base de Datos

### 1. Tabla: users
```
- id (bigint, PK, auto_increment)
- name (string)
- email (string, unique)
- password (string, hashed)
- email_verified_at (timestamp, nullable)
- remember_token (string, nullable)
- timestamps
```

### 2. Tabla: roles
```
- id (bigint, PK, auto_increment)
- nombre (string) - Ej: "Administrador", "Técnico", "Recepción", "Contabilidad"
- slug (string, unique) - Ej: "administrador", "tecnico", "recepcion", "contabilidad"
- descripcion (text, nullable)
- timestamps
```

### 3. Tabla: role_user (pivote)
```
- id (bigint, PK, auto_increment)
- user_id (bigint, FK -> users.id)
- role_id (bigint, FK -> roles.id)
- timestamps
```

### 4. Tabla: clientes
```
- id (bigint, PK, auto_increment)
- nombre (string)
- cedula_rnc (string, unique) - Cédula o RNC
- telefono (string)
- correo (string, nullable)
- direccion (text, nullable)
- timestamps
- soft_deletes
```

### 5. Tabla: equipos
```
- id (bigint, PK, auto_increment)
- cliente_id (bigint, FK -> clientes.id)
- codigo_unico (string, unique) - Generado automáticamente (ej: EQ-XXXXX)
- tipo (string) - Ej: "Laptop", "PC", "GPU", "Consola"
- marca (string)
- modelo (string)
- numero_serie (string, nullable)
- descripcion_falla (text)
- foto (string, nullable) - Ruta a la foto del equipo
- codigo_qr (string, nullable) - Ruta al código QR generado
- estado (enum) - "recibido", "diagnostico", "reparacion", "listo", "entregado", "cancelado"
- fecha_recibido (date)
- fecha_entrega_estimada (date, nullable)
- fecha_entregado (date, nullable)
- timestamps
- soft_deletes
```

### 6. Tabla: reparaciones
```
- id (bigint, PK, auto_increment)
- equipo_id (bigint, FK -> equipos.id)
- tecnico_id (bigint, FK -> users.id)
- diagnostico (text)
- piezas_reemplazadas (json) - Array de piezas
- costo_mano_obra (decimal 10,2)
- costo_piezas (decimal 10,2)
- total (decimal 10,2) - Calculado automáticamente
- fecha_inicio (date)
- fecha_finalizacion (date, nullable)
- estado (enum) - "pendiente", "en_proceso", "completada", "cancelada"
- timestamps
- soft_deletes
```

### 7. Tabla: facturas
```
- id (bigint, PK, auto_increment)
- numero_factura (string, unique) - Generado automáticamente (ej: FAC-000001)
- cliente_id (bigint, FK -> clientes.id)
- equipo_id (bigint, FK -> equipos.id)
- reparacion_id (bigint, FK -> reparaciones.id, nullable)
- subtotal (decimal 10,2)
- impuestos (decimal 10,2)
- total (decimal 10,2)
- forma_pago (enum) - "efectivo", "tarjeta", "transferencia", "cheque"
- fecha_emision (date)
- fecha_vencimiento (date, nullable)
- notas (text, nullable)
- timestamps
- soft_deletes
```

### 8. Tabla: pagos
```
- id (bigint, PK, auto_increment)
- factura_id (bigint, FK -> facturas.id)
- monto (decimal 10,2)
- tipo_pago (enum) - "efectivo", "tarjeta", "transferencia", "cheque"
- referencia (string, nullable)
- fecha (date)
- usuario_id (bigint, FK -> users.id) - Usuario que registró el pago
- observaciones (text, nullable)
- timestamps
```

### 9. Tabla: garantias
```
- id (bigint, PK, auto_increment)
- equipo_id (bigint, FK -> equipos.id)
- reparacion_id (bigint, FK -> reparaciones.id)
- fecha_inicio (date)
- fecha_vencimiento (date)
- duracion_dias (integer) - Ej: 30, 60, 90
- descripcion (text)
- activa (boolean)
- timestamps
```

### 10. Tabla: historial_estados
```
- id (bigint, PK, auto_increment)
- equipo_id (bigint, FK -> equipos.id)
- estado_anterior (string, nullable)
- estado_nuevo (string)
- usuario_id (bigint, FK -> users.id) - Usuario que hizo el cambio
- observaciones (text, nullable)
- timestamps
```

### 11. Tabla: sessions (Laravel)
```
- id (string, PK)
- user_id (bigint, nullable)
- ip_address (string, nullable)
- user_agent (text, nullable)
- payload (longtext)
- last_activity (integer, indexed)
```

## Funcionalidades Requeridas

### 1. Sistema de Autenticación
- Login con email y contraseña
- Logout
- Remember me
- Gestión de sesiones en base de datos
- Middleware de autenticación

### 2. Sistema de Roles y Permisos
**Roles:**
- **Administrador:** Acceso completo al sistema
- **Técnico:** Gestión de reparaciones, diagnósticos
- **Recepción:** Registro de clientes, equipos, entrega
- **Contabilidad:** Facturas, pagos, reportes financieros

**Implementación:**
- Relación muchos a muchos entre usuarios y roles
- Middleware personalizado para verificar roles
- Métodos helper en modelo User: `hasRole()`, `hasAnyRole()`

### 3. Gestión de Clientes
**Funcionalidades:**
- CRUD completo (Crear, Leer, Actualizar, Eliminar)
- Búsqueda por nombre, cédula/RNC, teléfono
- Validación de cédula/RNC única
- Ver historial de equipos del cliente
- Soft delete

**Campos del formulario:**
- Nombre (requerido)
- Cédula/RNC (requerido, único)
- Teléfono (requerido)
- Correo (opcional)
- Dirección (opcional)

### 4. Gestión de Equipos
**Funcionalidades:**
- CRUD completo
- Generación automática de código único (EQ-XXXXXXXX)
- Generación automática de código QR con el código único
- Upload de foto del equipo
- Estados: recibido → diagnóstico → reparación → listo → entregado
- Búsqueda por código único, cliente, marca, modelo
- Filtros por estado
- Historial automático de cambios de estado
- Soft delete

**Campos del formulario:**
- Cliente (select, requerido)
- Tipo (select: Laptop, PC, GPU, Consola, Otro)
- Marca (requerido)
- Modelo (requerido)
- Número de serie (opcional)
- Descripción de la falla (requerido, textarea)
- Foto (opcional, upload)
- Estado (automático al crear: "recibido")

**Eventos automáticos:**
- Al crear equipo: generar código único y QR
- Al cambiar estado: registrar en historial_estados
- Al subir foto: almacenar en storage/app/public/equipos

### 5. Gestión de Reparaciones
**Funcionalidades:**
- CRUD completo
- Asignar técnico responsable
- Registrar diagnóstico
- Agregar piezas reemplazadas (campo JSON, array dinámico)
- Calcular total automáticamente (mano de obra + piezas)
- Estados: pendiente → en_proceso → completada → cancelada
- Vincular con equipo
- Soft delete

**Campos del formulario:**
- Equipo (select, requerido)
- Técnico (select de usuarios con rol técnico, requerido)
- Diagnóstico (textarea, requerido)
- Piezas reemplazadas (array dinámico: nombre de pieza)
- Costo mano de obra (decimal, requerido)
- Costo de piezas (decimal, requerido)
- Total (calculado automáticamente)
- Fecha inicio (date, requerido)
- Fecha finalización (date, opcional)
- Estado (select, requerido)

### 6. Gestión de Facturas
**Funcionalidades:**
- CRUD completo
- Generación automática de número de factura (FAC-000001, incremental)
- Calcular subtotal, impuestos (18% ITBIS), total
- Vincular con cliente, equipo, reparación
- Generar PDF de la factura
- Descargar factura en PDF
- Imprimir factura
- Registrar pagos asociados
- Estado de pago (pagado, pendiente, parcial)
- Soft delete

**Campos del formulario:**
- Cliente (select, requerido)
- Equipo (select filtrado por cliente, requerido)
- Reparación (select filtrado por equipo, opcional)
- Subtotal (calculado desde reparación)
- Impuestos (18% del subtotal)
- Total (subtotal + impuestos)
- Forma de pago (select: efectivo, tarjeta, transferencia, cheque)
- Fecha emisión (date, default: hoy)
- Notas (textarea, opcional)

**Diseño del PDF:**
- Logo de la empresa
- Información de la empresa
- Número de factura
- Datos del cliente
- Detalles del equipo y reparación
- Desglose de costos
- Subtotal, impuestos, total
- Forma de pago
- Fecha de emisión

### 7. Gestión de Pagos (Contabilidad)
**Funcionalidades:**
- Registrar pagos de facturas
- Pagos parciales permitidos
- Múltiples pagos por factura
- Estado de factura actualizado automáticamente
- Filtrar por fecha, tipo de pago

**Campos del formulario:**
- Factura (select, requerido)
- Monto (decimal, requerido, validar <= saldo pendiente)
- Tipo de pago (select: efectivo, tarjeta, transferencia, cheque)
- Referencia (opcional, para transferencias/cheques)
- Fecha (date, default: hoy)
- Observaciones (textarea, opcional)

### 8. Módulo de Contabilidad
**Reportes requeridos:**
- Ingresos por fecha (diario, semanal, mensual, personalizado)
- Gastos por piezas
- Facturas pendientes de pago
- Facturas pagadas
- Resumen financiero
- Exportar a CSV
- Exportar a PDF

**Gráficos:**
- Ingresos por mes (últimos 6 meses)
- Tipos de equipos más reparados
- Estados de equipos (pie chart)

### 9. Dashboard
**Estadísticas principales:**
- Total de equipos en el taller
- Equipos por estado (recibido, diagnóstico, reparación, listo)
- Reparaciones en proceso
- Facturas pendientes de pago
- Ingresos del mes actual
- Gráfico de ingresos últimos 6 meses
- Lista de equipos recién recibidos
- Lista de equipos listos para entregar

### 10. Consulta Pública de Estado
**Funcionalidad:**
- Ruta pública (sin autenticación): `/consulta/{codigo_unico}`
- Permite al cliente consultar el estado de su equipo con el código QR
- Mostrar:
  - Código único
  - Tipo, marca, modelo
  - Estado actual
  - Fecha recibido
  - Fecha estimada de entrega
  - Historial de estados
  - Mensaje del técnico (si hay)

### 11. Sistema de Garantías
**Funcionalidades:**
- Generar garantía automáticamente al completar reparación
- Duración configurable (30, 60, 90 días)
- Verificar si garantía está activa
- Listar garantías activas
- Listar garantías vencidas
- Alertas de garantías próximas a vencer

### 12. Tutorial Guiado
**Implementación:**
- Sistema de tour interactivo con tooltips
- Mostrar tutorial al primer inicio de sesión
- Poder saltar o continuar el tutorial
- Botón para reiniciar tutorial
- Guardado en localStorage del progreso

## Interfaz de Usuario

### Diseño General
- Plantilla Argon Dashboard 2 (profesional y moderna)
- Sidebar lateral con navegación
- Navbar superior con:
  - Búsqueda rápida
  - Toggle modo oscuro/claro
  - Notificaciones (opcional)
  - Perfil de usuario con dropdown (logout)
- Breadcrumbs en cada página
- Responsive design (adaptado a tablets y móviles)

### Componentes UI
- Cards con sombras y bordes redondeados
- Tablas con paginación, búsqueda y filtros
- Formularios con validación en tiempo real
- Modales para confirmaciones
- Toasts/alerts para notificaciones
- Badges para estados (colores según estado)
- Botones con iconos (FontAwesome o similar)
- Spinners de carga

### Paleta de Colores para Estados
- **Recibido:** Azul (#007bff)
- **Diagnóstico:** Amarillo (#ffc107)
- **Reparación:** Naranja (#fd7e14)
- **Listo:** Verde (#28a745)
- **Entregado:** Gris (#6c757d)
- **Cancelado:** Rojo (#dc3545)

### Páginas Requeridas

1. **Login** (`/login`)
   - Formulario de login
   - Remember me
   - Fondo atractivo

2. **Dashboard** (`/dashboard`)
   - Estadísticas principales
   - Gráficos
   - Accesos rápidos

3. **Clientes** (`/clientes`)
   - `/clientes` - Lista
   - `/clientes/create` - Crear
   - `/clientes/{id}` - Ver detalle
   - `/clientes/{id}/edit` - Editar

4. **Equipos** (`/equipos`)
   - `/equipos` - Lista con filtros
   - `/equipos/create` - Crear
   - `/equipos/{id}` - Ver detalle con historial
   - `/equipos/{id}/edit` - Editar
   - `/equipos/{id}/qr` - Ver/descargar QR

5. **Reparaciones** (`/reparaciones`)
   - `/reparaciones` - Lista
   - `/reparaciones/create` - Crear
   - `/reparaciones/{id}` - Ver detalle
   - `/reparaciones/{id}/edit` - Editar

6. **Facturas** (`/facturas`)
   - `/facturas` - Lista
   - `/facturas/create` - Crear
   - `/facturas/{id}` - Ver detalle
   - `/facturas/{id}/edit` - Editar
   - `/facturas/{id}/pdf` - Descargar PDF
   - `/facturas/{id}/print` - Imprimir

7. **Contabilidad** (`/contabilidad`)
   - Dashboard de contabilidad
   - Reportes
   - Gráficos
   - Exportar datos

8. **Usuarios** (`/usuarios`) - Solo administradores
   - `/usuarios` - Lista
   - `/usuarios/create` - Crear
   - `/usuarios/{id}/edit` - Editar
   - Asignar roles

9. **Consulta Pública** (`/consulta/{codigo}`)
   - Sin autenticación
   - Mostrar estado del equipo

## Validaciones Requeridas

### Clientes
- Nombre: requerido, máximo 255 caracteres
- Cédula/RNC: requerido, único, formato válido
- Teléfono: requerido
- Correo: email válido (opcional)

### Equipos
- Cliente: requerido, debe existir
- Tipo: requerido
- Marca: requerido, máximo 100 caracteres
- Modelo: requerido, máximo 100 caracteres
- Descripción falla: requerido
- Foto: imagen (jpg, png), máximo 5MB
- Estado: debe ser uno de los valores permitidos

### Reparaciones
- Equipo: requerido, debe existir
- Técnico: requerido, debe ser usuario con rol técnico
- Diagnóstico: requerido
- Costo mano de obra: requerido, numérico, mínimo 0
- Costo piezas: requerido, numérico, mínimo 0
- Fecha inicio: requerido, fecha
- Fecha finalización: fecha, después de fecha inicio

### Facturas
- Cliente: requerido, debe existir
- Equipo: requerido, debe existir, debe pertenecer al cliente
- Subtotal: requerido, numérico, mínimo 0
- Impuestos: requerido, numérico, mínimo 0
- Total: requerido, numérico, mínimo 0
- Forma de pago: requerido, valor válido

### Pagos
- Factura: requerido, debe existir
- Monto: requerido, numérico, mínimo 0.01, máximo saldo pendiente
- Tipo de pago: requerido, valor válido
- Fecha: requerido, fecha, no puede ser futura

### Usuarios
- Nombre: requerido, máximo 255 caracteres
- Email: requerido, email válido, único
- Password: requerido (al crear), mínimo 8 caracteres, confirmado

## Seeders y Datos de Prueba

### 1. RolSeeder
Crear 4 roles:
- Administrador (slug: administrador)
- Técnico (slug: tecnico)
- Recepción (slug: recepcion)
- Contabilidad (slug: contabilidad)

### 2. DatabaseSeeder
Crear usuario administrador:
- Email: admin@martinezservice.com
- Password: password
- Rol: Administrador

### 3. DatosPruebaSeeder (opcional)
Crear datos de prueba:
- 4 usuarios adicionales (1 de cada rol)
- 5 clientes
- 8 equipos en diferentes estados
- 6 reparaciones (algunas completadas, otras en proceso)
- 3 facturas
- Pagos asociados
- 2 garantías

**Credenciales de usuarios de prueba:**
- juan.perez@martinezservice.com (Técnico) - password
- maria.garcia@martinezservice.com (Técnico) - password
- carlos.rodriguez@martinezservice.com (Recepción) - password
- ana.martinez@martinezservice.com (Contabilidad) - password

## Configuración y Variables de Entorno

**Archivo .env importante:**
```
APP_NAME="Martínez Service"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_LOCALE=es
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=es_ES

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=martinez_device
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## Rutas

### Rutas Web (con autenticación)
```
GET  /login                          - auth.login
POST /login                          - auth.login.submit
POST /logout                         - auth.logout
GET  /dashboard                      - dashboard
GET  /clientes                       - clientes.index
GET  /clientes/create                - clientes.create
POST /clientes                       - clientes.store
GET  /clientes/{id}                  - clientes.show
GET  /clientes/{id}/edit             - clientes.edit
PUT  /clientes/{id}                  - clientes.update
DELETE /clientes/{id}                - clientes.destroy
GET  /equipos                        - equipos.index
GET  /equipos/create                 - equipos.create
POST /equipos                        - equipos.store
GET  /equipos/{id}                   - equipos.show
GET  /equipos/{id}/edit              - equipos.edit
PUT  /equipos/{id}                   - equipos.update
DELETE /equipos/{id}                 - equipos.destroy
GET  /reparaciones                   - reparaciones.index
GET  /reparaciones/create            - reparaciones.create
POST /reparaciones                   - reparaciones.store
GET  /reparaciones/{id}              - reparaciones.show
GET  /reparaciones/{id}/edit         - reparaciones.edit
PUT  /reparaciones/{id}              - reparaciones.update
DELETE /reparaciones/{id}            - reparaciones.destroy
GET  /facturas                       - facturas.index
GET  /facturas/create                - facturas.create
POST /facturas                       - facturas.store
GET  /facturas/{id}                  - facturas.show
GET  /facturas/{id}/edit             - facturas.edit
PUT  /facturas/{id}                  - facturas.update
DELETE /facturas/{id}                - facturas.destroy
GET  /facturas/{id}/pdf              - facturas.pdf
GET  /contabilidad                   - contabilidad.index
GET  /usuarios                       - usuarios.index (solo admin)
GET  /usuarios/create                - usuarios.create (solo admin)
POST /usuarios                       - usuarios.store (solo admin)
GET  /usuarios/{id}/edit             - usuarios.edit (solo admin)
PUT  /usuarios/{id}                  - usuarios.update (solo admin)
DELETE /usuarios/{id}                - usuarios.destroy (solo admin)
```

### Rutas Públicas
```
GET  /consulta/{codigo_unico}        - consulta.show
```

### Rutas API (opcional, con Sanctum)
```
POST /api/login                      - auth.api.login
GET  /api/public/status/{codigo}     - status público
GET  /api/clientes                   - clientes.api.index
POST /api/clientes                   - clientes.api.store
GET  /api/clientes/{id}              - clientes.api.show
PUT  /api/clientes/{id}              - clientes.api.update
DELETE /api/clientes/{id}            - clientes.api.destroy
GET  /api/equipos                    - equipos.api.index
POST /api/equipos                    - equipos.api.store
GET  /api/equipos/{id}               - equipos.api.show
PUT  /api/equipos/{id}               - equipos.api.update
DELETE /api/equipos/{id}             - equipos.api.destroy
POST /api/equipos/{id}/generar-qr    - equipos.api.generarQr
GET  /api/reparaciones               - reparaciones.api.index
POST /api/reparaciones               - reparaciones.api.store
GET  /api/reparaciones/{id}          - reparaciones.api.show
PUT  /api/reparaciones/{id}          - reparaciones.api.update
DELETE /api/reparaciones/{id}        - reparaciones.api.destroy
GET  /api/facturas                   - facturas.api.index
POST /api/facturas                   - facturas.api.store
GET  /api/facturas/{id}              - facturas.api.show
PUT  /api/facturas/{id}              - facturas.api.update
DELETE /api/facturas/{id}            - facturas.api.destroy
GET  /api/facturas/{id}/pdf          - facturas.api.pdf
GET  /api/reportes/ingresos          - reportes.ingresos
GET  /api/reportes/equipos-estado    - reportes.equipos
```

## Modelos Eloquent - Relaciones

### User
```php
- belongsToMany(Rol::class, 'role_user')
- hasMany(Reparacion::class, 'tecnico_id')
- hasMany(Pago::class, 'usuario_id')
- hasMany(HistorialEstado::class, 'usuario_id')
```

### Rol
```php
- belongsToMany(User::class, 'role_user')
```

### Cliente
```php
- hasMany(Equipo::class)
- hasMany(Factura::class)
```

### Equipo
```php
- belongsTo(Cliente::class)
- hasMany(Reparacion::class)
- hasMany(Factura::class)
- hasMany(Garantia::class)
- hasMany(HistorialEstado::class)
```

### Reparacion
```php
- belongsTo(Equipo::class)
- belongsTo(User::class, 'tecnico_id')
- hasMany(Factura::class)
- hasMany(Garantia::class)
```

### Factura
```php
- belongsTo(Cliente::class)
- belongsTo(Equipo::class)
- belongsTo(Reparacion::class)
- hasMany(Pago::class)
```

### Pago
```php
- belongsTo(Factura::class)
- belongsTo(User::class, 'usuario_id')
```

### Garantia
```php
- belongsTo(Equipo::class)
- belongsTo(Reparacion::class)
```

### HistorialEstado
```php
- belongsTo(Equipo::class)
- belongsTo(User::class, 'usuario_id')
```

## Eventos y Observers

### EquipoObserver
```php
creating: Generar código único y código QR
updated: Si cambia estado, crear registro en historial_estados
```

### ReparacionObserver
```php
creating/updating: Calcular total automáticamente (mano_obra + piezas)
updated: Si cambia a completada, crear garantía automáticamente
```

### FacturaObserver
```php
creating: Generar número de factura automáticamente (FAC-000001, incremental)
```

## Middleware Personalizado

### CheckRole
Verificar que el usuario tiene uno de los roles requeridos:
```php
Route::middleware(['auth', 'role:administrador'])->group(function() {
    // Rutas solo para administradores
});
```

## Comandos Artisan Personalizados

### user:assign-admin
Asignar rol de administrador a un usuario:
```bash
php artisan user:assign-admin email@example.com
```

## Archivos de Configuración

### config/app.php
```php
'timezone' => 'America/Santo_Domingo', // o tu zona horaria
'locale' => 'es',
'faker_locale' => 'es_ES',
```

### config/database.php
```php
'default' => env('DB_CONNECTION', 'mysql'),
```

### config/session.php
```php
'driver' => env('SESSION_DRIVER', 'database'),
```

## Storage y Archivos Públicos

### Estructura de storage:
```
storage/
  app/
    public/
      equipos/         - Fotos de equipos
        {id}/
          foto.jpg
      qrcodes/         - Códigos QR generados
        {codigo_unico}.png
      facturas/        - PDFs de facturas (opcional)
        {numero_factura}.pdf
```

### Enlace simbólico:
```bash
php artisan storage:link
```

## Testing (opcional)

### Tests unitarios básicos:
- Generación de código único de equipo
- Cálculo de total en reparaciones
- Generación de número de factura
- Validación de roles

### Tests de integración:
- Crear cliente completo
- Crear equipo con foto y QR
- Procesar reparación completa
- Generar factura y PDF

## Documentación Adicional

### README.md
- Descripción del sistema
- Requisitos (PHP, MySQL, Composer, Node.js)
- Instrucciones de instalación
- Credenciales por defecto
- Comandos útiles

### DEPLOYMENT.md
- Pasos para deployment en servidor
- Configuración de Nginx/Apache
- Permisos de archivos
- Solución de problemas comunes

### API.md (si implementas API)
- Documentación de endpoints
- Ejemplos de requests/responses
- Autenticación con Sanctum
- Códigos de error

## Mejoras Opcionales

1. **Notificaciones:**
   - Email al cliente cuando equipo esté listo
   - SMS para notificaciones urgentes
   - Notificaciones en la aplicación

2. **Reportes Avanzados:**
   - Dashboard de gerencia
   - Análisis de productividad de técnicos
   - Equipos más frecuentes
   - Clientes más frecuentes

3. **Historial de Comunicaciones:**
   - Registro de llamadas/mensajes con clientes
   - Seguimiento de promesas de entrega

4. **Inventario de Piezas:**
   - Gestión de stock de piezas
   - Alertas de stock bajo
   - Valorización de inventario

5. **Agenda de Técnicos:**
   - Calendario de trabajos asignados
   - Priorización de reparaciones

6. **Multi-sucursal:**
   - Gestión de múltiples talleres
   - Transferencia de equipos entre sucursales

7. **Portal del Cliente:**
   - Registro de clientes
   - Consulta de historial
   - Solicitud de reparaciones online

8. **Integración con Pasarelas de Pago:**
   - Pagos online
   - Registro automático en el sistema

## Checklist de Implementación

- [ ] Configurar proyecto Laravel 12
- [ ] Instalar dependencias (Sanctum, QRCode, DomPDF)
- [ ] Crear todas las migraciones
- [ ] Crear todos los modelos con relaciones
- [ ] Crear seeders (Roles, Usuario Admin, Datos de prueba)
- [ ] Implementar autenticación
- [ ] Implementar middleware de roles
- [ ] Crear controladores web
- [ ] Crear vistas Blade con Argon Dashboard
- [ ] Implementar generación de QR
- [ ] Implementar generación de PDF
- [ ] Implementar upload de fotos
- [ ] Crear observers para eventos automáticos
- [ ] Implementar tutorial guiado
- [ ] Implementar modo oscuro/claro
- [ ] Crear API con Sanctum (opcional)
- [ ] Implementar reportes de contabilidad
- [ ] Crear comando artisan personalizado
- [ ] Escribir documentación
- [ ] Testing básico
- [ ] Deployment en servidor

## Notas Finales

Este sistema debe ser:
- **Profesional:** Diseño limpio y moderno
- **Funcional:** Todas las operaciones CRUD completas
- **Intuitivo:** Fácil de usar para personal no técnico
- **Robusto:** Validaciones completas y manejo de errores
- **Escalable:** Arquitectura limpia y modular
- **Documentado:** README y comentarios claros
- **Seguro:** Autenticación, roles, validaciones

El objetivo es crear un sistema completo y funcional que pueda ser usado inmediatamente en producción por un taller de reparación real.

