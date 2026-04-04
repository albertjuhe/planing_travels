const WS_URL = 'ws://localhost:5555';

/**
 * Connect to the travel-specific WebSocket room.
 *
 * @param {string}   travelId          - The travel UUID to join
 * @param {Function} onLocationAdded   - Called with location data when a collaborator adds a location
 * @returns {WebSocket}                - The open connection; call ws.close() on unmount
 */
export const connectToTravelRoom = (travelId, onLocationAdded) => {
    const ws = new WebSocket(`${WS_URL}/ws/${travelId}`);

    ws.onopen = () => {
        console.log(`[ws] joined travel room: ${travelId}`);
    };

    ws.onmessage = (event) => {
        try {
            const msg = JSON.parse(event.data);
            if (msg.event === 'location_added') {
                onLocationAdded(msg.location);
            }
        } catch (e) {
            // ignore non-JSON messages (e.g. welcome ping)
        }
    };

    ws.onerror = (e) => {
        console.error('[ws] error', e);
    };

    ws.onclose = () => {
        console.log(`[ws] left travel room: ${travelId}`);
    };

    return ws;
};
