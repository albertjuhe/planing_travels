#!/usr/bin/env node

/**
 * Simple WebSocket Server for Travel Planner
 *
 * This server handles WebSocket connections from the travel planner web application
 * and broadcasts messages to connected clients.
 *
 * Usage:
 *   node websocket-server.js
 *
 * Requirements:
 *   npm install ws
 */

const WebSocket = require('ws');
const http = require('http');
const url = require('url');

// Configuration
const PORT = 5555;
const HOST = 'localhost';

// Create HTTP server
const server = http.createServer((req, res) => {
    // Respond to health checks
    if (req.url === '/health') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ status: 'ok', timestamp: new Date().toISOString() }));
    } else {
        res.writeHead(404);
        res.end('WebSocket server running');
    }
});

// Create WebSocket server
const wss = new WebSocket.Server({ server });

// Track connected clients
const clients = new Map();
let clientCount = 0;

/**
 * Handle new WebSocket connections
 */
wss.on('connection', (ws, req) => {
    const clientId = ++clientCount;
    const clientIP = req.socket.remoteAddress;
    const connectionTime = new Date().toISOString();

    clients.set(clientId, {
        ws: ws,
        ip: clientIP,
        connectedAt: connectionTime
    });

    console.log(`\n✓ [${connectionTime}] Client #${clientId} connected from ${clientIP}`);
    console.log(`  Connected clients: ${clients.size}`);

    // Send welcome message
    const welcomeMsg = {
        type: 'success',
        message: `✓ Connected to WebSocket server (ID: ${clientId})`
    };
    ws.send(JSON.stringify(welcomeMsg));

    /**
     * Handle incoming messages
     */
    ws.on('message', (rawMessage) => {
        try {
            const message = JSON.parse(rawMessage);
            const timestamp = new Date().toISOString();

            console.log(`\n→ [${timestamp}] Message from client #${clientId}:`);
            console.log(`  Type: ${message.type}`);
            console.log(`  Message: ${message.message || '(no message)'}`);

            // Handle different message types
            switch (message.type) {
                case 'handshake':
                    handleHandshake(clientId, message);
                    break;
                case 'update':
                    broadcastToOthers(clientId, {
                        type: 'info',
                        message: `Client #${clientId} sent update: ${message.message || 'empty'}`
                    });
                    break;
                case 'ping':
                    ws.send(JSON.stringify({
                        type: 'pong',
                        message: 'pong',
                        clientId: clientId
                    }));
                    break;
                default:
                    broadcastToAll({
                        type: 'info',
                        message: `Message from client #${clientId}: ${message.message || 'empty'}`
                    });
            }
        } catch (error) {
            console.error(`✗ Failed to parse message from client #${clientId}:`, error.message);
            ws.send(JSON.stringify({
                type: 'error',
                message: 'Invalid message format. Expected JSON.'
            }));
        }
    });

    /**
     * Handle client disconnect
     */
    ws.on('close', () => {
        const timestamp = new Date().toISOString();
        clients.delete(clientId);

        console.log(`\n✗ [${timestamp}] Client #${clientId} disconnected`);
        console.log(`  Connected clients: ${clients.size}`);

        // Notify other clients
        if (clients.size > 0) {
            broadcastToAll({
                type: 'info',
                message: `Client #${clientId} disconnected (${clients.size} client(s) remaining)`
            });
        }
    });

    /**
     * Handle errors
     */
    ws.on('error', (error) => {
        console.error(`\n✗ Error from client #${clientId}:`, error.message);
    });

    // Send initial status to client
    sendStatusUpdate(clientId);
});

/**
 * Handle handshake messages
 */
function handleHandshake(clientId, message) {
    const response = {
        type: 'info',
        message: `Handshake received: ${message.message}`,
        clientId: clientId,
        connectedAt: new Date().toISOString()
    };

    broadcastToAll(response);
}

/**
 * Send status update to a specific client
 */
function sendStatusUpdate(clientId) {
    const client = clients.get(clientId);
    if (client && client.ws.readyState === WebSocket.OPEN) {
        client.ws.send(JSON.stringify({
            type: 'info',
            message: `You are client #${clientId}. ${clients.size} total client(s) connected.`
        }));
    }
}

/**
 * Broadcast message to all connected clients
 */
function broadcastToAll(message) {
    const json = JSON.stringify(message);

    clients.forEach((client) => {
        if (client.ws.readyState === WebSocket.OPEN) {
            client.ws.send(json);
        }
    });
}

/**
 * Broadcast message to all clients except the sender
 */
function broadcastToOthers(senderId, message) {
    const json = JSON.stringify(message);

    clients.forEach((client, clientId) => {
        if (clientId !== senderId && client.ws.readyState === WebSocket.OPEN) {
            client.ws.send(json);
        }
    });
}

/**
 * Start the server
 */
server.listen(PORT, HOST, () => {
    console.log('\n╔════════════════════════════════════════════╗');
    console.log('║  WebSocket Server for Travel Planner       ║');
    console.log('╚════════════════════════════════════════════╝');
    console.log(`\n✓ Server listening on ws://${HOST}:${PORT}/ws`);
    console.log(`✓ Health check: http://${HOST}:${PORT}/health`);
    console.log('\n📝 Features:');
    console.log('  • Auto-reconnection with exponential backoff');
    console.log('  • JSON message format');
    console.log('  • Broadcast to all connected clients');
    console.log('  • Detailed logging');
    console.log('\n⌨️  Press Ctrl+C to stop the server');
    console.log('─'.repeat(44));
});

/**
 * Graceful shutdown
 */
process.on('SIGTERM', () => {
    console.log('\n\n⚠️  Received SIGTERM signal. Closing connections...');

    // Close all client connections
    clients.forEach((client) => {
        if (client.ws.readyState === WebSocket.OPEN) {
            client.ws.close(1001, 'Server shutting down');
        }
    });

    // Close server
    wss.close(() => {
        server.close(() => {
            console.log('✓ Server closed gracefully');
            process.exit(0);
        });
    });

    // Force close after 5 seconds
    setTimeout(() => {
        console.log('✗ Force closing server');
        process.exit(1);
    }, 5000);
});

process.on('SIGINT', () => {
    console.log('\n\n⚠️  Received SIGINT signal. Closing connections...');

    // Close all client connections
    clients.forEach((client) => {
        if (client.ws.readyState === WebSocket.OPEN) {
            client.ws.close(1001, 'Server shutting down');
        }
    });

    // Close server
    wss.close(() => {
        server.close(() => {
            console.log('✓ Server closed gracefully');
            process.exit(0);
        });
    });

    // Force close after 5 seconds
    setTimeout(() => {
        console.log('✗ Force closing server');
        process.exit(1);
    }, 5000);
});

module.exports = { server, wss };

