# ⚡ Inicio Rápido - MartinezService

## 🚀 Comandos para Iniciar Todo

### **Copiar y pegar en PowerShell:**

```powershell
# Iniciar Apache (MartinezService)
C:\xampp\apache\bin\httpd.exe

# Iniciar Breatech-Landing
cd C:\xampp\php\www\breatech-landing
Start-Process node -ArgumentList "server-with-images.js" -WindowStyle Hidden
```

---

## 🌐 URLs de Acceso

- **MartinezService**: http://demomartinez.bsolutions.dev
- **Breatech-Landing**: http://localhost:3000

---

## 🔐 Credenciales MartinezService

```
Email: martinez@martinezservice.com
Password: martinez123
```

---

## 📱 Verificar en Móvil

1. Abre el navegador en tu celular
2. Ve a: http://demomartinez.bsolutions.dev
3. Click en el **ícono de hamburguesa** (☰) en la esquina superior derecha
4. El menú lateral debe **deslizarse desde la izquierda**
5. Click en cualquier opción del menú → debe navegar y cerrar el menú
6. Click fuera del menú → debe cerrarse

---

## ✅ Mejoras Implementadas para Móvil

✅ **Botón hamburguesa visible** en móviles y tablets  
✅ **Menú se desliza** desde la izquierda con animación  
✅ **Overlay oscuro** cuando el menú está abierto  
✅ **Se cierra** al tocar fuera del menú  
✅ **Se cierra** al seleccionar una opción  
✅ **Funciona** en iPhone, Android, tablets  

---

## 🐛 Si el menú no se despliega

```powershell
# Limpiar cachés
cd C:\xampp\php\www\martinez
php artisan view:clear
php artisan cache:clear

# Reiniciar Apache
Stop-Process -Name "httpd" -Force
Start-Sleep -Seconds 2
C:\xampp\apache\bin\httpd.exe
```

Luego en el móvil:
- Cierra el navegador completamente
- Ábrelo de nuevo
- Limpia caché del navegador (Configuración → Borrar datos)
- Vuelve a entrar al sitio

---

## 📚 Más Documentación

- **[GUIA_SERVIDORES.md](GUIA_SERVIDORES.md)** - Estado y comandos de servidores
- **[GUIA_AGREGAR_SUBDOMINIO.md](GUIA_AGREGAR_SUBDOMINIO.md)** - Cómo agregar subdominios
- **[README.md](README.md)** - Información general del proyecto

---

Fecha: Noviembre 2025


