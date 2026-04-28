# Documentación de API Pública - MartinezService

## Base URL
```
https://demomartinez.bsolutions.dev/api/public
```

## Endpoints Disponibles

### 1. Consultar Estado de Reparación

Obtiene la información completa del estado de una reparación usando el código único del equipo.

**Endpoint:** `GET /status/{codigo}`

**Parámetros:**
- `codigo` (string, requerido): Código único del equipo (UUID)

**Ejemplo de URL:**
```
GET https://demomartinez.bsolutions.dev/api/public/status/a0f4aa8b-07d8-4c52-b640-94d5414773f3
```

**Respuesta Exitosa (200 OK):**
```json
{
  "success": true,
  "equipo": {
    "tipo": "Tarjeta Gráfica (GPU)",
    "tipo_personalizado": null,
    "marca": "NVIDIA",
    "modelo": "GeForce RTX 3080",
    "numero_serie": "SN-54321-ABC",
    "estado": "activo"
  },
  "cliente": {
    "nombre": "Juan Pérez",
    "telefono": "809-555-1234"
  },
  "reparacion": {
    "codigo": "REP-00002",
    "estado": "Esperando Aprobación",
    "tipo_servicio": "reparacion",
    "fecha_ingreso": "04/11/2025",
    "fecha_prometida": "14/11/2025",
    "precio_cotizado": "750.00",
    "descripcion_cotizacion": "Problema detectado: La tarjeta gráfica presenta sobrecalentamiento...\n\nPiezas necesarias:\n- Chip VRAM de reemplazo...",
    "fecha_cotizacion": "12/11/2025 21:02",
    "cliente_aprobado": null,
    "fecha_aprobacion": null,
    "puede_aprobar": true,
    "historial": [
      {
        "estado": "Esperando Aprobación",
        "comentario": "Cotización enviada al cliente por un monto de $750.00",
        "fecha": "12/11/2025 21:02"
      },
      {
        "estado": "Recibido",
        "comentario": "GPU recibida para diagnóstico",
        "fecha": "07/11/2025 03:08"
      }
    ]
  }
}
```

**Respuesta de Error (404 Not Found):**
```json
{
  "success": false,
  "message": "Equipo no encontrado"
}
```

---

### 2. Aprobar Cotización

Aprueba una cotización pendiente de aprobación.

**Endpoint:** `POST /aprobar/{codigo}`

**Parámetros:**
- `codigo` (string, requerido): Código único del equipo (UUID)

**Headers Requeridos:**
```
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: {token} (solo para aplicaciones web)
```

**Ejemplo de URL:**
```
POST https://demomartinez.bsolutions.dev/api/public/aprobar/a0f4aa8b-07d8-4c52-b640-94d5414773f3
```

**Body:** (vacío, no requiere body)

**Respuesta Exitosa (200 OK):**
```json
{
  "success": true,
  "message": "Cotización aprobada exitosamente. El técnico procederá con la reparación."
}
```

**Respuestas de Error:**

**400 Bad Request - Estado incorrecto:**
```json
{
  "success": false,
  "message": "Esta reparación no está esperando aprobación. Estado actual: Aprobado"
}
```

**400 Bad Request - Ya fue procesada:**
```json
{
  "success": false,
  "message": "Esta cotización ya fue aprobada anteriormente"
}
```

**404 Not Found:**
```json
{
  "success": false,
  "message": "Equipo no encontrado"
}
```

---

### 3. Rechazar Cotización

Rechaza una cotización pendiente de aprobación.

**Endpoint:** `POST /rechazar/{codigo}`

**Parámetros:**
- `codigo` (string, requerido): Código único del equipo (UUID)

**Headers Requeridos:**
```
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: {token} (solo para aplicaciones web)
```

**Body (opcional):**
```json
{
  "comentario": "El precio es muy alto, no puedo proceder"
}
```

**Ejemplo de URL:**
```
POST https://demomartinez.bsolutions.dev/api/public/rechazar/a0f4aa8b-07d8-4c52-b640-94d5414773f3
```

**Respuesta Exitosa (200 OK):**
```json
{
  "success": true,
  "message": "Cotización rechazada. La reparación ha sido cancelada."
}
```

**Respuestas de Error:** (iguales que aprobar)

---

## Ejemplos de Código

### JavaScript (Fetch API)

```javascript
// Consultar estado
async function consultarEstado(codigo) {
  try {
    const response = await fetch(
      `https://demomartinez.bsolutions.dev/api/public/status/${codigo}`
    );
    const data = await response.json();
    
    if (data.success) {
      console.log('Estado:', data.reparacion.estado);
      console.log('Precio:', data.reparacion.precio_cotizado);
      console.log('Descripción:', data.reparacion.descripcion_cotizacion);
      return data;
    } else {
      console.error('Error:', data.message);
      return null;
    }
  } catch (error) {
    console.error('Error de red:', error);
    return null;
  }
}

// Aprobar cotización
async function aprobarCotizacion(codigo, csrfToken = null) {
  try {
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    };
    
    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken;
    }
    
    const response = await fetch(
      `https://demomartinez.bsolutions.dev/api/public/aprobar/${codigo}`,
      {
        method: 'POST',
        headers: headers
      }
    );
    
    const data = await response.json();
    
    if (data.success) {
      console.log('Cotización aprobada:', data.message);
      return true;
    } else {
      console.error('Error:', data.message);
      return false;
    }
  } catch (error) {
    console.error('Error de red:', error);
    return false;
  }
}

// Rechazar cotización
async function rechazarCotizacion(codigo, comentario = '', csrfToken = null) {
  try {
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    };
    
    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken;
    }
    
    const response = await fetch(
      `https://demomartinez.bsolutions.dev/api/public/rechazar/${codigo}`,
      {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({ comentario: comentario })
      }
    );
    
    const data = await response.json();
    
    if (data.success) {
      console.log('Cotización rechazada:', data.message);
      return true;
    } else {
      console.error('Error:', data.message);
      return false;
    }
  } catch (error) {
    console.error('Error de red:', error);
    return false;
  }
}

// Uso
const codigoEquipo = 'a0f4aa8b-07d8-4c52-b640-94d5414773f3';
consultarEstado(codigoEquipo).then(data => {
  if (data && data.reparacion.puede_aprobar) {
    // El cliente puede aprobar
    aprobarCotizacion(codigoEquipo);
  }
});
```

---

### PHP (cURL)

```php
<?php

class MartinezServiceAPI {
    private $baseUrl = 'https://demomartinez.bsolutions.dev/api/public';
    
    /**
     * Consultar estado de reparación
     */
    public function consultarEstado($codigo) {
        $url = $this->baseUrl . '/status/' . urlencode($codigo);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return null;
    }
    
    /**
     * Aprobar cotización
     */
    public function aprobarCotizacion($codigo) {
        $url = $this->baseUrl . '/aprobar/' . urlencode($codigo);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return null;
    }
    
    /**
     * Rechazar cotización
     */
    public function rechazarCotizacion($codigo, $comentario = '') {
        $url = $this->baseUrl . '/rechazar/' . urlencode($codigo);
        
        $data = json_encode(['comentario' => $comentario]);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return null;
    }
}

// Uso
$api = new MartinezServiceAPI();
$codigo = 'a0f4aa8b-07d8-4c52-b640-94d5414773f3';

// Consultar estado
$estado = $api->consultarEstado($codigo);
if ($estado && $estado['success']) {
    echo "Estado: " . $estado['reparacion']['estado'] . "\n";
    echo "Precio: $" . $estado['reparacion']['precio_cotizado'] . "\n";
    
    // Si puede aprobar
    if ($estado['reparacion']['puede_aprobar']) {
        $resultado = $api->aprobarCotizacion($codigo);
        if ($resultado && $resultado['success']) {
            echo "Cotización aprobada exitosamente\n";
        }
    }
}
```

---

### Python (requests)

```python
import requests
import json

class MartinezServiceAPI:
    def __init__(self):
        self.base_url = 'https://demomartinez.bsolutions.dev/api/public'
    
    def consultar_estado(self, codigo):
        """Consultar estado de reparación"""
        url = f"{self.base_url}/status/{codigo}"
        
        try:
            response = requests.get(url, headers={'Accept': 'application/json'})
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            print(f"Error: {e}")
            return None
    
    def aprobar_cotizacion(self, codigo):
        """Aprobar cotización"""
        url = f"{self.base_url}/aprobar/{codigo}"
        
        try:
            response = requests.post(
                url,
                headers={
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            )
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            print(f"Error: {e}")
            return None
    
    def rechazar_cotizacion(self, codigo, comentario=''):
        """Rechazar cotización"""
        url = f"{self.base_url}/rechazar/{codigo}"
        
        data = {'comentario': comentario}
        
        try:
            response = requests.post(
                url,
                json=data,
                headers={
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            )
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            print(f"Error: {e}")
            return None

# Uso
api = MartinezServiceAPI()
codigo = 'a0f4aa8b-07d8-4c52-b640-94d5414773f3'

# Consultar estado
estado = api.consultar_estado(codigo)
if estado and estado.get('success'):
    print(f"Estado: {estado['reparacion']['estado']}")
    print(f"Precio: ${estado['reparacion']['precio_cotizado']}")
    
    # Si puede aprobar
    if estado['reparacion'].get('puede_aprobar'):
        resultado = api.aprobar_cotizacion(codigo)
        if resultado and resultado.get('success'):
            print("Cotización aprobada exitosamente")
```

---

## Estados de Reparación

Los posibles estados de una reparación son:

- `Recibido`: Equipo recibido, esperando diagnóstico
- `En Diagnóstico`: Equipo en proceso de diagnóstico
- `Esperando Aprobación`: Cotización enviada, esperando aprobación del cliente
- `Aprobado`: Cliente aprobó la cotización
- `Esperando Pieza`: Esperando llegada de piezas
- `En Proceso`: Reparación en ejecución
- `Finalizado`: Reparación completada
- `Entregado`: Equipo entregado al cliente
- `Cancelado`: Reparación cancelada

---

## Validaciones Importantes

### Para Aprobar/Rechazar:

1. **Estado debe ser "Esperando Aprobación"**
2. **Debe existir un precio_cotizado > 0**
3. **cliente_aprobado debe ser null** (no haber sido procesada antes)

### Campos Importantes:

- `puede_aprobar`: Indica si el cliente puede aprobar/rechazar (boolean)
- `cliente_aprobado`: 
  - `null`: No procesada
  - `true`: Aprobada
  - `false`: Rechazada

---

## Manejo de Errores

Todos los endpoints devuelven un objeto JSON con:
- `success`: boolean indicando si la operación fue exitosa
- `message`: string con mensaje descriptivo

**Códigos HTTP:**
- `200`: Operación exitosa
- `400`: Error de validación (estado incorrecto, ya procesada, etc.)
- `404`: Recurso no encontrado
- `500`: Error del servidor

---

## Notas Importantes

1. **CSRF Token**: Solo es necesario para aplicaciones web que usen formularios. Para aplicaciones móviles o APIs externas, no es requerido.

2. **Código Único**: El código único del equipo es un UUID que se genera automáticamente cuando se crea un equipo. Este código se debe compartir con el cliente para que pueda consultar su reparación.

3. **URL de Consulta Pública**: Los clientes también pueden acceder directamente a:
   ```
   https://demomartinez.bsolutions.dev/consulta/{codigo}
   ```
   Esta es una página web completa con interfaz visual.

4. **Rate Limiting**: Actualmente no hay límites de tasa, pero se recomienda implementar throttling en aplicaciones cliente.

---

## Soporte

Para más información o soporte técnico, contactar al equipo de desarrollo.

