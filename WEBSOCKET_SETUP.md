# WebSocket Setup Guide

## Overview

This application includes a WebSocket client (`public/js/websocket/websockets.js`) that connects to a WebSocket server at `ws://localhost:5555/ws`. The client automatically shows connection status and displays messages from the server.

## Client Features

### Components:
1. **Online Badge** (`#online-user`): Fixed position badge in top-right corner showing "Connected" when WebSocket is active
2. **Messages Container** (`#websocket-messages`): Auto-generated container that displays server messages
3. **Auto-reconnect**: Attempts to reconnect up to 5 times with exponential backoff (3s, 6s, 9s, 12s, 15s)

### Client Functions:
- `initWebSocket()`: Initialize WebSocket connection
- `displayMessage(message, type)`: Display a message on screen (types: 'info', 'success', 'error')
- `sendMessage(type, payload)`: Send a JSON message to the server
- `handleWebSocketMessage(data)`: Process incoming messages based on type
- `attemptReconnect()`: Attempt reconnection with exponential backoff

## Server Implementation Options

### Option 1: Node.js + Socket.io (Recommended)

```bash
mkdir -p websocket-server
cd websocket-server
npm init -y
npm install ws --save
```

Create `server.js`:
```javascript
const WebSocket = require('ws');
const http = require('http');

const server = http.createServer();
const wss = new WebSocket.Server({ server });

wss.on('connection', (ws) => {
    console.log('[WebSocket] Client connected');

    // Send connection confirmation
    ws.send(JSON.stringify({
        type: 'success',
        message: '✓ Connected to WebSocket server'
    }));

    // Handle incoming messages
    ws.on('message', (message) => {
        try {
            const data = JSON.parse(message);
            console.log('[WebSocket] Received:', data);

            // Echo back to all clients
            wss.clients.forEach((client) => {
                if (client.readyState === WebSocket.OPEN) {
                    client.send(JSON.stringify({
                        type: 'info',
                        message: `Message from client: ${data.message}`
                    }));
                }
            });
        } catch (e) {
            console.error('Failed to parse message:', e);
        }
    });

    ws.on('close', () => {
        console.log('[WebSocket] Client disconnected');
    });

    ws.on('error', (error) => {
        console.error('[WebSocket] Error:', error);
    });
});

server.listen(5555, () => {
    console.log('[WebSocket] Server listening on ws://localhost:5555');
});
```

Start the server:
```bash
node server.js
```

### Option 2: Python + websockets

```bash
pip install websockets asyncio
```

Create `server.py`:
```python
import asyncio
import websockets
import json
from datetime import datetime

async def handler(websocket, path):
    client_ip = websocket.remote_address[0]
    print(f'[WebSocket] Client connected from {client_ip}')
    
    try:
        # Send connection confirmation
        await websocket.send(json.dumps({
            'type': 'success',
            'message': '✓ Connected to WebSocket server'
        }))
        
        async for message in websocket:
            try:
                data = json.loads(message)
                print(f'[WebSocket] Received: {data}')
                
                # Echo back to all clients
                response = {
                    'type': 'info',
                    'message': f"Message from {client_ip}: {data.get('message', 'empty')}",
                    'timestamp': datetime.now().isoformat()
                }
                
                # Broadcast to all connected clients
                for ws in websockets.WebSocketServerProtocol.connections:
                    await ws.send(json.dumps(response))
                    
            except json.JSONDecodeError:
                print(f'[WebSocket] Invalid JSON received')
                
    except websockets.exceptions.ConnectionClosed:
        print(f'[WebSocket] Client disconnected from {client_ip}')

async def main():
    async with websockets.serve(handler, 'localhost', 5555):
        print('[WebSocket] Server listening on ws://localhost:5555')
        await asyncio.Future()  # run forever

if __name__ == '__main__':
    asyncio.run(main())
```

Start the server:
```bash
python server.py
```

### Option 3: PHP + Ratchet

```bash
composer require cboden/ratchet
```

Create `websocket_server.php`:
```php
<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

class WebSocketHandler implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "[WebSocket] Client connected. Total: " . count($this->clients) . "\n";
        
        // Send connection confirmation
        $message = json_encode([
            'type' => 'success',
            'message' => '✓ Connected to WebSocket server'
        ]);
        $conn->send($message);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        echo "[WebSocket] Received: " . json_encode($data) . "\n";

        // Broadcast to all clients
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $response = json_encode([
                    'type' => 'info',
                    'message' => "Message: " . ($data['message'] ?? 'empty')
                ]);
                $client->send($response);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "[WebSocket] Client disconnected. Total: " . count($this->clients) . "\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "[WebSocket] Error: " . $e->getMessage() . "\n";
        $conn->close();
    }
}

$app = new WebSocketHandler();
$wsServer = new WsServer($app);
$httpServer = new HttpServer($wsServer);
$server = IoServer::factory($httpServer, 5555);

echo "[WebSocket] Server listening on ws://localhost:5555\n";
$server->run();
```

Start with:
```bash
php websocket_server.php
```

## Testing the WebSocket

1. Navigate to `http://localhost:8000/public/index.php/en/travel/toscana-italia-1`
2. Open browser DevTools (F12)
3. Check the Console tab for WebSocket logs
4. You should see:
   - `[WebSocket] Attempting connection to ws://localhost:5555/ws...`
   - Connection status messages
   - A green "Connected" badge in the top-right corner

### Sample Console Messages:
```
[WebSocket] Attempting connection to ws://localhost:5555/ws...
[WebSocket] Connection established
[WEBSOCKET SUCCESS] ✓ Connected to server
[WebSocket] Message received: {"type":"success","message":"✓ Connected to WebSocket server"}
```

## Client-to-Server Communication

Send a message from the client:
```javascript
// In browser console
sendMessage('info', { message: 'Hello from client!' });
```

## Message Protocol

### Client to Server
```json
{
  "type": "handshake|info|update",
  "message": "optional message",
  "...": "additional payload fields"
}
```

### Server to Client
```json
{
  "type": "info|success|error|update",
  "message": "display message",
  "...": "additional data fields"
}
```

### Message Types
- **info**: General information message (blue)
- **success**: Success message (green)
- **error**: Error message (red)
- **update**: Data update (processed in `handleUpdate()`)

## Styling

CSS classes for styling in `public/css/main.css`:
- `.st-online-badge`: Online connection badge
- `.ws-message`: Message container
- `.ws-message.success`: Success message style
- `.ws-message.error`: Error message style

## Troubleshooting

### "WebSocket is closed before the connection is established"
- Make sure your WebSocket server is running on port 5555
- Check firewall settings
- Verify the server is accessible on `ws://localhost:5555/ws`

### Messages not appearing
- Open browser DevTools Console to see logs
- Check that jQuery is loaded (`$` function available)
- Verify the server is sending valid JSON

### Badge not showing
- Ensure `#online-user` element exists in HTML
- Check CSS for `.st-online-badge` styling
- Verify WebSocket successfully connected (check console)

## Production Deployment

For production:
1. Use `wss://` (WebSocket Secure) instead of `ws://`
2. Configure proper SSL certificates
3. Update `WS_URL` in `websockets.js` to your production domain
4. Implement authentication/authorization
5. Add message validation and sanitization
6. Use environment variables for configuration
7. Implement proper error handling and logging

