var WS_BASE = (typeof WS_SERVER_URL !== 'undefined' && WS_SERVER_URL) ? WS_SERVER_URL : "ws://localhost:5555";
var WS_TRAVEL_URL = (typeof WS_TRAVEL_ID !== 'undefined' && WS_TRAVEL_ID)
    ? WS_BASE + "/ws/" + WS_TRAVEL_ID + "?userId=" + (WS_CURRENT_USER_ID || '') + "&username=" + encodeURIComponent(WS_CURRENT_USERNAME || '')
    : WS_BASE + "/ws";
var socket = null;
var reconnectAttempts = 0;
var maxReconnectAttempts = 10;
var reconnectTimer = null;
var chatHandlers = [];

function onChatMessage(handler) {
    chatHandlers.push(handler);
}

function sendChatMessage(content) {
    if (socket && socket.readyState === WebSocket.OPEN) {
        var msg = JSON.stringify({
            type: 'chat',
            userId: WS_CURRENT_USER_ID || '',
            username: WS_CURRENT_USERNAME || '',
            content: content
        });
        socket.send(msg);
    }
}

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
    console.log("Websocket connection attempt", reconnectAttempts + 1, "to", WS_TRAVEL_URL);

    try {
        socket = new WebSocket(WS_TRAVEL_URL);
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

        if (msg.type === 'chat') {
            chatHandlers.forEach(function(handler) {
                handler(msg);
            });
            return;
        }

        if (msg.type === 'user_joined') {
            chatHandlers.forEach(function(handler) {
                handler(msg);
            });
            return;
        }

        if (msg.type === 'user_left') {
            chatHandlers.forEach(function(handler) {
                handler(msg);
            });
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

            $('#infoTravel').html('<p class="alert alert-info"><strong>' + (loc.addedByUsername || 'A collaborator') + '</strong> added a new location.</p>');
            $('#infoTravel').show().delay(5000).fadeOut();
        }

        if (msg.event === 'location_removed') {
            var myId = (typeof WS_CURRENT_USER_ID !== 'undefined') ? String(WS_CURRENT_USER_ID) : '';
            if (myId && msg.byUserId && String(msg.byUserId) === myId) {
                return;
            }

            if (typeof mPoint === 'undefined') { return; }

            var placeId = msg.locationId;
            var el = document.getElementById(placeId);
            if (el) {
                var l = $.data(el, 'location');
                if (l && l.currentMark && typeof map !== 'undefined') {
                    map.removeLayer(l.currentMark);
                }
            }
            var layer = document.getElementById('layer_' + placeId);
            if (layer) { layer.parentNode.removeChild(layer); }

            $('#infoTravel').html('<p class="alert alert-warning"><strong>' + (msg.byUsername || 'A collaborator') + '</strong> removed a location.</p>');
            $('#infoTravel').show().delay(5000).fadeOut();
        }

        if (msg.event === 'location_updated') {
            var myId = (typeof WS_CURRENT_USER_ID !== 'undefined') ? String(WS_CURRENT_USER_ID) : '';
            if (myId && msg.byUserId && String(msg.byUserId) === myId) {
                return;
            }

            if (typeof mPoint === 'undefined') { return; }

            var loc = msg.location;
            var placeId = loc.id;
            var el = document.getElementById(placeId);
            if (el) {
                var l = $.data(el, 'location');
                if (l) {
                    l.address = loc.title;
                    l.url = loc.url || '';
                    l.description = loc.description || '';
                    l.IdType = loc.typeLocationId || l.IdType;
                    l.typeIcon = loc.typeIcon || l.typeIcon;
                    $.data(el, 'location', l);
                }
                var titleEl = el.querySelector('.title-point b');
                if (titleEl) { titleEl.textContent = loc.title; }
            }

            $('#infoTravel').html('<p class="alert alert-info"><strong>' + (msg.byUsername || 'A collaborator') + '</strong> updated a location.</p>');
            $('#infoTravel').show().delay(5000).fadeOut();
        }

        if (msg.event === 'visit_date_changed') {
            var myId = (typeof WS_CURRENT_USER_ID !== 'undefined') ? String(WS_CURRENT_USER_ID) : '';
            if (myId && msg.byUserId && String(msg.byUserId) === myId) {
                return;
            }

            $('#infoTravel').html('<p class="alert alert-info"><strong>' + (msg.byUsername || 'A collaborator') + '</strong> changed a visit date. Refresh to see updates.</p>');
            $('#infoTravel').show().delay(5000).fadeOut();
        }

        if (msg.event === 'image_uploaded') {
            var myId = (typeof WS_CURRENT_USER_ID !== 'undefined') ? String(WS_CURRENT_USER_ID) : '';
            if (myId && msg.byUserId && String(msg.byUserId) === myId) {
                return;
            }

            $('#infoTravel').html('<p class="alert alert-info"><strong>' + (msg.byUsername || 'A collaborator') + '</strong> uploaded an image.</p>');
            $('#infoTravel').show().delay(5000).fadeOut();
        }

        if (msg.event === 'note_added') {
            var myId = (typeof WS_CURRENT_USER_ID !== 'undefined') ? String(WS_CURRENT_USER_ID) : '';
            if (myId && msg.byUserId && String(msg.byUserId) === myId) {
                return;
            }

            var notesModal = document.getElementById('notesModal');
            if (notesModal && notesModal.classList.contains('is-open') &&
                notesModal.getAttribute('data-location-id') === msg.locationId) {
                mapPoint._loadNotes(msg.locationId);
            }

            $('#infoTravel').html('<p class="alert alert-info"><strong>' + (msg.byUsername || 'A collaborator') + '</strong> added a note.</p>');
            $('#infoTravel').show().delay(5000).fadeOut();
        }

        if (msg.event === 'note_deleted') {
            var myId = (typeof WS_CURRENT_USER_ID !== 'undefined') ? String(WS_CURRENT_USER_ID) : '';
            if (myId && msg.byUserId && String(msg.byUserId) === myId) {
                return;
            }

            var notesModal = document.getElementById('notesModal');
            if (notesModal && notesModal.classList.contains('is-open') &&
                notesModal.getAttribute('data-location-id') === msg.locationId) {
                var noteEl = document.getElementById('note-item-' + msg.noteId);
                if (noteEl) { noteEl.remove(); }
                var list = document.getElementById('notes-list');
                if (list && !list.querySelector('.note-item')) {
                    list.innerHTML = '<div class="notes-empty">No notes yet. Add the first one below.</div>';
                }
            }

            $('#infoTravel').html('<p class="alert alert-info"><strong>' + (msg.byUsername || 'A collaborator') + '</strong> deleted a note.</p>');
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
