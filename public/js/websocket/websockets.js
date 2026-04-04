var WS_BASE = "ws://localhost:5555";
var WS_URL = (typeof WS_TRAVEL_ID !== 'undefined' && WS_TRAVEL_ID)
    ? WS_BASE + "/ws/" + WS_TRAVEL_ID
    : WS_BASE + "/ws";
var socket = null;
var reconnectAttempts = 0;
var maxReconnectAttempts = 10;
var reconnectTimer = null;

function updateConnectionBadge(state, count, usernames) {
    var $badge = $('#online-user');
    if (!$badge.length) return;

    $badge.show();
    $badge.removeClass('st-online-badge--connected st-online-badge--disconnected st-online-badge--connecting');

    if (state === 'connected') {
        $badge.addClass('st-online-badge--connected');
        var n = (typeof count === 'number') ? count : 1;
        var label = n + ' user' + (n !== 1 ? 's' : '') + ' online';
        $('#online-user-label').text(label);
        var $names = $('#online-user-names');
        if ($names.length && usernames && usernames.length) {
            $names.html(usernames.map(function (u) {
                return '<div>&#128100; ' + u.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>';
            }).join(''));
        }
    } else if (state === 'connecting') {
        $badge.addClass('st-online-badge--connecting');
        $('#online-user-label').text('Connecting\u2026');
        $('#online-user-names').html('');
    } else {
        $badge.addClass('st-online-badge--disconnected');
        $('#online-user-label').text('Disconnected');
        $('#online-user-names').html('');
    }
}

function getReconnectDelay() {
    return Math.min(1000 * Math.pow(2, reconnectAttempts), 30000);
}

function connectWebSocket() {
    if (socket && (socket.readyState === WebSocket.OPEN || socket.readyState === WebSocket.CONNECTING)) {
        return;
    }

    updateConnectionBadge('connecting');
    console.log("Websocket connection attempt", reconnectAttempts + 1, "to", WS_URL);

    try {
        socket = new WebSocket(WS_URL);
    } catch (e) {
        console.log("WebSocket creation error:", e);
        updateConnectionBadge('disconnected');
        scheduleReconnect();
        return;
    }

    socket.onopen = function () {
        console.log("Websocket connected!");
        reconnectAttempts = 0;
        var username = (typeof WS_CURRENT_USERNAME !== 'undefined' && WS_CURRENT_USERNAME)
            ? WS_CURRENT_USERNAME
            : 'anonymous';
        socket.send(JSON.stringify({ username: username }));
        updateConnectionBadge('connected', 1, [username]);
    };

    socket.onmessage = function (event) {
        var msg;
        try {
            msg = JSON.parse(event.data);
        } catch (e) {
            return;
        }

        if (msg.event === 'presence') {
            updateConnectionBadge('connected', msg.count, msg.usernames);
            return;
        }

        if (msg.event === 'location_added') {
            var loc = msg.location;

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

console.log("websockets.js loaded");
$(document).ready(function () {
    connectWebSocket();
});
