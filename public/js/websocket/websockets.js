var WS_BASE = "ws://localhost:5555";
var WS_URL = (typeof WS_TRAVEL_ID !== 'undefined' && WS_TRAVEL_ID)
    ? WS_BASE + "/ws/" + WS_TRAVEL_ID
    : WS_BASE + "/ws";
var socket = null;
var reconnectAttempts = 0;
var maxReconnectAttempts = 10;
var reconnectTimer = null;

function updateConnectionBadge(state) {
    console.log("updateConnectionBadge called with state:", state);
    var $badge = $('#online-user');
    console.log("Badge found:", $badge.length > 0);

    if (!$badge.length) {
        console.log("Badge element not found");
        return;
    }

    var $label = $('#online-user-label');
    $badge.show();

    $badge.removeClass('st-online-badge--connected st-online-badge--disconnected st-online-badge--connecting');

    var labelText = 'Usuario desconectado';
    var badgeClass = 'st-online-badge--disconnected';

    if (state === 'connected') {
        labelText = 'Usuario conectado';
        badgeClass = 'st-online-badge--connected';
    } else if (state === 'connecting') {
        labelText = 'Conectando…';
        badgeClass = 'st-online-badge--connecting';
    }

    console.log("Setting badge class:", badgeClass, "label:", labelText);
    $badge.addClass(badgeClass);
    if ($label.length) {
        $label.text(labelText);
    }
}

function getReconnectDelay() {
    // Exponential backoff: 1s, 2s, 4s, 8s... max 30s
    var delay = Math.min(1000 * Math.pow(2, reconnectAttempts), 30000);
    return delay;
}

function connectWebSocket() {
    console.log("connectWebSocket called");

    if (socket && (socket.readyState === WebSocket.OPEN || socket.readyState === WebSocket.CONNECTING)) {
        console.log("Socket already open or connecting");
        return;
    }

    updateConnectionBadge('connecting');
    console.log("Websocket connection attempt", reconnectAttempts + 1, "to", WS_URL);

    try {
        socket = new WebSocket(WS_URL);
        console.log("WebSocket object created");
    } catch (e) {
        console.log("WebSocket creation error:", e);
        updateConnectionBadge('disconnected');
        scheduleReconnect();
        return;
    }

    socket.onopen = function (event) {
        console.log("Websocket connected!");
        reconnectAttempts = 0;
        socket.send("Hi from travel server!");
        updateConnectionBadge('connected');
    };

    socket.onmessage = function (event) {
        try {
            var msg = JSON.parse(event.data);
        } catch (e) {
            return;
        }

        if (msg.event === 'location_added') {
            var loc = msg.location;

            // Skip if this is our own add (we already rendered it locally)
            var myId = (typeof WS_CURRENT_USER_ID !== 'undefined') ? String(WS_CURRENT_USER_ID) : '';
            if (myId && loc.addedByUserId && String(loc.addedByUserId) === myId) {
                return;
            }

            if (typeof mPoint === 'undefined' || typeof map === 'undefined') {
                return;
            }

            var popup = '<b>' + loc.title + '</b>';
            var marker = L.marker([loc.latitude, loc.longitude]).bindPopup(popup).addTo(map);

            var locationPoint = mPoint.createLocation(
                loc.id,
                loc.latitude,
                loc.longitude,
                loc.title,
                loc.id,
                '',
                '',
                loc.title,
                marker
            );
            mPoint.addPoint(locationPoint);

            $('#infoTravel').html('<p class="alert alert-info">A collaborator added a new location.</p>');
            $('#infoTravel').show().delay(5000).fadeOut();
        }
    };

    socket.onclose = function (event) {
        console.log("Socket closed", event.code, event.reason);
        updateConnectionBadge('disconnected');
        scheduleReconnect();
    };

    socket.onerror = function (error) {
        console.log("Error websocket", error);
    };
}

function scheduleReconnect() {
    if (reconnectTimer) {
        clearTimeout(reconnectTimer);
    }

    if (reconnectAttempts < maxReconnectAttempts) {
        var delay = getReconnectDelay();
        console.log("Reconnecting in " + (delay / 1000) + "s...");
        reconnectTimer = setTimeout(function () {
            reconnectAttempts++;
            connectWebSocket();
        }, delay);
    } else {
        console.log("Max reconnection attempts reached");
    }
}

// Start connection when DOM is ready
console.log("websockets.js loaded");
$(document).ready(function() {
    console.log("DOM ready, starting WebSocket connection");
    connectWebSocket();
});
