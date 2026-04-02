#!/usr/bin/env node

/**
 * WebSocket Test Client
 *
 * Simple CLI client to test the WebSocket server
 *
 * Usage:
 *   node websocket-client-test.js
 *
 * Commands:
 *   send <message>  - Send a message to the server
 *   quit            - Close the connection
 *   help            - Show this help message
 */

const WebSocket = require('ws');
const readline = require('readline');

const WS_URL = 'ws://localhost:5555/ws';

// Create readline interface for user input
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

// WebSocket connection
let ws = null;
let isConnected = false;

console.log('\n╔════════════════════════════════════════════╗');
console.log('║  WebSocket Test Client                     ║');
console.log('╚════════════════════════════════════════════╝\n');

/**
 * Connect to WebSocket server
 */
function connect() {
    console.log(`📡 Connecting to ${WS_URL}...`);

    try {
        ws = new WebSocket(WS_URL);

        ws.on('open', () => {
            isConnected = true;
            console.log('✓ Connected to WebSocket server\n');
            console.log('Type "help" for commands or type a message to send:\n');
            promptUser();
        });

        ws.on('message', (data) => {
            try {
                const message = JSON.parse(data);
                console.log('\n📨 Message received:');
                console.log(`   Type: ${message.type}`);
                console.log(`   Message: ${message.message || '(no message)'}`);
                if (message.clientId) {
                    console.log(`   Client ID: ${message.clientId}`);
                }
                console.log('');
                promptUser();
            } catch (e) {
                console.log(`\n📨 Message received: ${data}\n`);
                promptUser();
            }
        });

        ws.on('close', () => {
            isConnected = false;
            console.log('\n✗ Disconnected from server');
            process.exit(0);
        });

        ws.on('error', (error) => {
            console.error('\n✗ Connection error:', error.message);
            console.error('Make sure the server is running on port 5555\n');
            process.exit(1);
        });

    } catch (error) {
        console.error('✗ Failed to connect:', error.message);
        process.exit(1);
    }
}

/**
 * Prompt user for input
 */
function promptUser() {
    if (!isConnected) return;

    rl.question('> ', (input) => {
        const trimmed = input.trim().toLowerCase();

        if (!trimmed) {
            promptUser();
            return;
        }

        // Handle commands
        if (trimmed === 'help') {
            showHelp();
            promptUser();
            return;
        }

        if (trimmed === 'quit' || trimmed === 'exit') {
            console.log('\nClosing connection...');
            ws.close();
            return;
        }

        if (trimmed.startsWith('send ')) {
            const message = input.substring(5).trim();
            sendMessage('info', { message: message });
            promptUser();
            return;
        }

        // Send as generic message
        sendMessage('info', { message: input });
        promptUser();
    });
}

/**
 * Send a message to the server
 */
function sendMessage(type, payload) {
    if (!isConnected) {
        console.log('✗ Not connected to server');
        return;
    }

    try {
        const message = {
            type: type,
            ...payload
        };

        ws.send(JSON.stringify(message));
        console.log(`✓ Message sent: ${JSON.stringify(message)}\n`);
    } catch (error) {
        console.error('✗ Failed to send message:', error.message);
    }
}

/**
 * Show help message
 */
function showHelp() {
    console.log('\n╔════════════════════════════════════════════╗');
    console.log('║  Available Commands                        ║');
    console.log('╚════════════════════════════════════════════╝');
    console.log('\n  send <message>   Send a message to server');
    console.log('  quit             Close connection and exit');
    console.log('  help             Show this help message');
    console.log('\nOr just type anything to send it as a message.\n');
}

// Start connection
connect();

// Handle graceful shutdown
process.on('SIGINT', () => {
    console.log('\n\nClosing connection...');
    if (ws) {
        ws.close();
    }
    process.exit(0);
});

