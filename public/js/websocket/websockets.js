/**
 * WebSocket Connection Handler
 * Manages real-time communication with the travel server
 */

// Configuration
const WS_URL = "ws://localhost:5555/ws";
const MAX_RETRIES = 5;
const RETRY_DELAY = 3000; // 3 seconds

// State
let socket = null;
let isConnected = false;
let retryCount = 0;

/**
 * Display a message in the messages container
 */
function displayMessage(message, type = 'info') {
    // Create container if it doesn't exist
    if ($('#websocket-messages').length === 0) {
        $('body').prepend('<div id="websocket-messages"></div>');
    }

    // Create message element
    const messageEl = $(`<div class="ws-message ${type}">${message}</div>`);

    // Add to container
    $('#websocket-messages').append(messageEl);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        messageEl.fadeOut(300, function() {
            $(this).remove();
        });
    }, 5000);

    console.log(`[WebSocket ${type.toUpperCase()}]`, message);
}

/**
 * Initialize WebSocket connection
 */
function initWebSocket() {
    try {
        console.log(`[WebSocket] Attempting connection to ${WS_URL}...`);
        socket = new WebSocket(WS_URL);

        socket.onopen = function (event) {
            isConnected = true;
            retryCount = 0;
            console.log("[WebSocket] Connection established");

            // Show online badge
            $('#online-user').show();
            displayMessage('✓ Connected to server', 'success');

            // Send initial handshake
            socket.send(JSON.stringify({
                type: 'handshake',
                message: 'Hi from travel server!'
            }));
        };

        socket.onmessage = function (event) {
            console.log("[WebSocket] Message received:", event.data);

            try {
                const data = JSON.parse(event.data);
                handleWebSocketMessage(data);
            } catch (e) {
                // If not JSON, treat as plain text
                displayMessage(event.data, 'info');
            }
        };

        socket.onclose = function (event) {
            isConnected = false;
            $('#online-user').hide();
            console.log("[WebSocket] Connection closed");

            if (event.wasClean) {
                displayMessage('Connection closed', 'info');
            } else {
                displayMessage('Connection lost. Retrying...', 'error');
                attemptReconnect();
            }
        };

        socket.onerror = function (error) {
            isConnected = false;
            $('#online-user').hide();
            console.error("[WebSocket] Error:", error);
            displayMessage('Connection error. Check console for details.', 'error');
        };

    } catch (error) {
        console.error("[WebSocket] Failed to create connection:", error);
        displayMessage('Failed to initialize WebSocket', 'error');
    }
}

/**
 * Handle incoming messages from server
 */
function handleWebSocketMessage(data) {
    if (!data.type) {
        displayMessage(data.message || data, 'info');
        return;
    }

    switch (data.type) {
        case 'info':
            displayMessage(data.message, 'info');
            break;
        case 'success':
            displayMessage(data.message, 'success');
            break;
        case 'error':
            displayMessage(data.message, 'error');
            break;
        case 'update':
            handleUpdate(data);
            break;
        default:
            console.log("[WebSocket] Unknown message type:", data.type, data);
            displayMessage(data.message || JSON.stringify(data), 'info');
    }
}

/**
 * Handle update messages (e.g., location updates, user activity)
 */
function handleUpdate(data) {
    console.log("[WebSocket] Update received:", data);
    // TODO: Implement specific update handlers
    // For example: update map, refresh data, etc.
}

/**
 * Attempt to reconnect with exponential backoff
 */
function attemptReconnect() {
    if (retryCount >= MAX_RETRIES) {
        console.error("[WebSocket] Max retries reached. Giving up.");
        displayMessage('Failed to connect. Please refresh the page.', 'error');
        return;
    }

    retryCount++;
    const delay = RETRY_DELAY * retryCount;
    console.log(`[WebSocket] Reconnecting in ${delay}ms (attempt ${retryCount}/${MAX_RETRIES})...`);

    setTimeout(() => {
        initWebSocket();
    }, delay);
}

/**
 * Send a message to the server
 */
function sendMessage(type, payload = {}) {
    if (!isConnected) {
        console.warn("[WebSocket] Not connected. Message not sent.");
        return false;
    }

    try {
        const message = {
            type: type,
            ...payload
        };
        socket.send(JSON.stringify(message));
        return true;
    } catch (error) {
        console.error("[WebSocket] Failed to send message:", error);
        return false;
    }
}

/**
 * Initialize on document ready
 */
$(document).ready(function() {
    initWebSocket();

    // Optional: Log connection status periodically
    setInterval(() => {
        if (isConnected) {
            console.log("[WebSocket] Still connected ✓");
        }
    }, 30000); // Every 30 seconds
});
