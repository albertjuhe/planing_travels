# 🚀 Quick Start Guide - WebSocket Implementation

## El Problema Original
❌ El mensaje del WebSocket no se mostraba en la página  
❌ El badge `#online-user` no estaba visible  
❌ No había contenedor para los mensajes  

## ✅ La Solución (Implementada)

Se han creado/actualizado 7 archivos:

1. **websockets.js** - Cliente completamente mejorado
2. **main.css** - Estilos para badge y mensajes
3. **showTravel.html.twig** - Contenedor para mensajes
4. **websocket-server.js** - Servidor Node.js
5. **start-websocket.sh** - Script de arranque
6. **WEBSOCKET_SETUP.md** - Documentación técnica
7. **websocket-client-test.js** - Cliente de testing

---

## 📋 Pasos para Probar (5 minutos)

### Paso 1: Abre 2 terminales

**Terminal 1** - Para el servidor WebSocket:
```bash
cd /Users/albert.juhe/code/planing_travels
./start-websocket.sh
```

**Terminal 2** - Para testing (opcional):
```bash
cd /Users/albert.juhe/code/planing_travels
npm install ws  # Si no está instalado
node websocket-client-test.js
```

### Paso 2: Abre el navegador

Navega a:
```
http://localhost:8000/public/index.php/en/travel/toscana-italia-1
```

### Paso 3: Verifica la conexión

Deberías ver:

1. **Badge Verde** en esquina superior derecha
   ```
   ● Connected
   ```

2. **Mensaje de éxito** brevemente
   ```
   ✓ Connected to server
   ```

3. **Console (F12)** mostrará:
   ```
   [WebSocket] Attempting connection to ws://localhost:5555/ws...
   [WebSocket] Connection established
   [WEBSOCKET SUCCESS] ✓ Connected to server
   ```

---

## 🧪 Testing desde Browser Console (F12)

Abre DevTools (F12) y ejecuta en Console:

```javascript
// Ver estado
console.log(isConnected)  // true

// Enviar mensaje
sendMessage('info', { message: 'Hola servidor!' })

// Mostrar notificación manual
displayMessage('Test exitoso', 'success')
```

---

## 🧪 Testing desde Terminal

Si usas `websocket-client-test.js`:

```
> Escriba un mensaje
> send Hola desde terminal
> quit  (para salir)
```

El servidor mostrará:
```
→ Message from client #2:
  Type: info
  Message: Hola desde terminal
```

Y todos los navegadores conectados verán el mensaje.

---

## 📊 Visualización en Browser

### Badge (Esquina Superior Derecha)
```
╔──────────────────╗
│ ● Connected      │  ← Verde
│   (pulsa)        │     Top-right
└──────────────────┘
```

### Mensajes (Bajo el Badge)
```
╔────────────────────────────╗
│ ✓ Connected to server      │  ← Verde (success)
└────────────────────────────┘
     (auto-desaparece en 5s)

╔────────────────────────────╗
│ Info message               │  ← Azul (info)
└────────────────────────────┘

╔────────────────────────────╗
│ Error message              │  ← Rojo (error)
└────────────────────────────┘
```

---

## 🔧 Configuración Rápida

### Cambiar Puerto del Servidor
Edita `websocket-server.js` línea 13:
```javascript
const PORT = 3000;  // Cambiar aquí
```

### Cambiar URL del WebSocket (para producción)
Edita `public/js/websocket/websockets.js` línea 7:
```javascript
const WS_URL = "ws://tu-dominio.com:5555/ws";
```

### Cambiar Número de Reintentos
Edita `public/js/websocket/websockets.js` línea 8:
```javascript
const MAX_RETRIES = 10;  // Aumentar reintentos
```

---

## 🎯 Casos de Uso

### 1. Notificaciones en Tiempo Real
```javascript
// En el servidor
ws.send(JSON.stringify({
    type: 'info',
    message: 'Nuevo usuario se conectó'
}));
```

### 2. Alertas de Errores
```javascript
// En el servidor
ws.send(JSON.stringify({
    type: 'error',
    message: 'Error de conexión a base de datos'
}));
```

### 3. Mensajes de Éxito
```javascript
// En el servidor
ws.send(JSON.stringify({
    type: 'success',
    message: 'Datos guardados correctamente'
}));
```

### 4. Actualizaciones de Datos
```javascript
// En el servidor
ws.send(JSON.stringify({
    type: 'update',
    entity: 'location',
    id: 123,
    data: { name: 'Nuevo nombre' }
}));
```

---

## 🆘 Solución de Problemas

### El badge no aparece
**Causa**: Servidor WebSocket no corre o no conecta  
**Solución**:
1. Verifica que `./start-websocket.sh` esté corriendo
2. Abre Console (F12) y busca errores
3. Intenta: `telnet localhost 5555`

### No veo mensajes
**Causa**: jQuery no cargado o error de JavaScript  
**Solución**:
1. Abre Console (F12) - busca errores rojos
2. Verifica que jQuery esté disponible: `typeof $` debe ser 'function'
3. Recarga la página

### "Connection error"
**Causa**: Puerto 5555 ocupado u otro proceso  
**Solución**:
```bash
# Matar proceso en puerto 5555
lsof -i :5555
kill -9 <PID>

# O cambiar el puerto en websocket-server.js
```

### Server se detiene inesperadamente
**Causa**: Error no manejado  
**Solución**:
1. Revisar output del terminal del server
2. Revisar logs en Console del navegador
3. Verificar que el mensaje JSON sea válido

---

## 📁 Archivos Modificados

```
📝 MODIFICADOS (3 archivos):
├── public/js/websocket/websockets.js
│   └── Completamente reescrito (194 líneas)
├── public/css/main.css
│   └── Agregados estilos WebSocket (70+ líneas)
└── src/UI/templates/travel/showTravel.html.twig
    └── Agregado contenedor de mensajes

📄 CREADOS (4 archivos):
├── websocket-server.js
│   └── Servidor Node.js (220+ líneas)
├── start-websocket.sh
│   └── Script de arranque automático
├── websocket-client-test.js
│   └── Cliente de testing
└── WEBSOCKET_SETUP.md
    └── Documentación técnica completa
```

---

## 🚀 Siguientes Pasos

1. **Probar ahora** - Sigue los "Pasos para Probar" arriba
2. **Personalizar** - Modifica los mensajes y estilos según tus necesidades
3. **Integrar** - Conecta el servidor a tu lógica de negocio
4. **Desplegar** - Usa `wss://` en producción para seguridad

---

## 📚 Recursos

- **WebSocket API**: https://developer.mozilla.org/en-US/docs/Web/API/WebSocket
- **ws Package**: https://github.com/websockets/ws
- **RFC 6455**: https://tools.ietf.org/html/rfc6455 (Especificación WebSocket)

---

## ✨ Características Principales

✅ **Auto-reconexión** - Reintentos automáticos si se pierde conexión  
✅ **Exponential Backoff** - Espera crece: 3s, 6s, 9s, 12s, 15s  
✅ **Mensajes JSON** - Protocolo estructurado  
✅ **Múltiples Tipos** - info, success, error, update  
✅ **Animaciones** - Badge con pulso, mensajes con slide-in  
✅ **Auto-desaparición** - Mensajes se van solos en 5 segundos  
✅ **Logging** - Logs detallados en Console  
✅ **Sin Dependencias** - Solo necesita Node.js (no jQuery required)  

---

## 🎓 Ejemplo Completo

**Servidor envia mensaje a todos:**
```javascript
// En websocket-server.js - línea ~180
clients.forEach((client) => {
    if (client.ws.readyState === WebSocket.OPEN) {
        client.ws.send(JSON.stringify({
            type: 'success',
            message: '¡Todos reciben esto!'
        }));
    }
});
```

**Cliente recibe y muestra:**
```
1. Navegador recibe JSON
2. handleWebSocketMessage() lo procesa
3. displayMessage() lo muestra en pantalla
4. Badge cambia a verde
5. Mensaje aparece por 5 segundos
```

---

## 📞 Soporte

Si tienes problemas:

1. **Revisa Console (F12)** - Busca mensajes de error
2. **Verifica que el server corre** - `./start-websocket.sh`
3. **Revisa los logs** - Mira output del server en terminal
4. **Lee WEBSOCKET_SETUP.md** - Documentación detallada

---

**Status**: ✅ Listo para usar  
**Fecha**: Abril 2, 2026  
**Versión**: 1.0  
**Soporte**: Documentación completa incluida

