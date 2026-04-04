package main

import (
	"context"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"os/signal"
	"strings"
	"sync"
	"syscall"
	"time"

	"github.com/gorilla/websocket"
)

var upgrader = websocket.Upgrader{
	ReadBufferSize:  1024,
	WriteBufferSize: 1024,
	CheckOrigin:     func(r *http.Request) bool { return true },
}

type Client struct {
	conn     *websocket.Conn
	username string
	writeMu  sync.Mutex
}

func (c *Client) writeMessage(messageType int, data []byte) error {
	c.writeMu.Lock()
	defer c.writeMu.Unlock()
	return c.conn.WriteMessage(messageType, data)
}

// RoomManager manages WebSocket connections grouped by travelId.
type RoomManager struct {
	rooms map[string]map[*Client]bool
	mutex sync.RWMutex
}

func NewRoomManager() *RoomManager {
	return &RoomManager{
		rooms: make(map[string]map[*Client]bool),
	}
}

func (rm *RoomManager) Join(travelId string, client *Client) {
	rm.mutex.Lock()
	defer rm.mutex.Unlock()
	if rm.rooms[travelId] == nil {
		rm.rooms[travelId] = make(map[*Client]bool)
	}
	rm.rooms[travelId][client] = true
	log.Printf("[room:%s] %s joined. room size: %d", travelId, client.username, len(rm.rooms[travelId]))
}

func (rm *RoomManager) Leave(travelId string, client *Client) {
	rm.mutex.Lock()
	defer rm.mutex.Unlock()
	if room, ok := rm.rooms[travelId]; ok {
		delete(room, client)
		if len(room) == 0 {
			delete(rm.rooms, travelId)
		}
		log.Printf("[room:%s] %s left", travelId, client.username)
	}
}

func (rm *RoomManager) BroadcastToRoom(travelId string, messageType int, message []byte) {
	rm.mutex.RLock()
	defer rm.mutex.RUnlock()
	room, ok := rm.rooms[travelId]
	if !ok {
		log.Printf("[room:%s] broadcast skipped: no connections", travelId)
		return
	}
	for client := range room {
		if err := client.writeMessage(messageType, message); err != nil {
			log.Printf("[room:%s] broadcast error: %v", travelId, err)
		}
	}
	log.Printf("[room:%s] broadcast sent to %d connection(s)", travelId, len(room))
}

func (rm *RoomManager) broadcastPresence(travelId string) {
	room, ok := rm.rooms[travelId]
	if !ok {
		return
	}
	usernames := make([]string, 0, len(room))
	for client := range room {
		usernames = append(usernames, client.username)
	}
	msg, _ := json.Marshal(map[string]interface{}{
		"event":     "presence",
		"travelId":  travelId,
		"count":     len(room),
		"usernames": usernames,
	})
	for client := range room {
		client.writeMessage(websocket.TextMessage, msg)
	}
}

func (rm *RoomManager) TotalConnections() int {
	rm.mutex.RLock()
	defer rm.mutex.RUnlock()
	total := 0
	for _, room := range rm.rooms {
		total += len(room)
	}
	return total
}

func (rm *RoomManager) TotalRooms() int {
	rm.mutex.RLock()
	defer rm.mutex.RUnlock()
	return len(rm.rooms)
}

var roomManager = NewRoomManager()

// reader keeps the connection alive and removes it on disconnect.
func reader(travelId string, client *Client) {
	defer func() {
		roomManager.mutex.Lock()
		if room, ok := roomManager.rooms[travelId]; ok {
			delete(room, client)
			if len(room) == 0 {
				delete(roomManager.rooms, travelId)
			}
			log.Printf("[room:%s] %s left", travelId, client.username)
		}
		roomManager.broadcastPresence(travelId)
		roomManager.mutex.Unlock()
		client.conn.Close()
	}()

	client.conn.SetReadDeadline(time.Now().Add(60 * time.Second))
	client.conn.SetPongHandler(func(string) error {
		client.conn.SetReadDeadline(time.Now().Add(60 * time.Second))
		return nil
	})

	for {
		_, msg, err := client.conn.ReadMessage()
		if err != nil {
			if websocket.IsUnexpectedCloseError(err, websocket.CloseGoingAway, websocket.CloseAbnormalClosure) {
				log.Printf("[room:%s] read error: %v", travelId, err)
			}
			return
		}

		// First message from client identifies the username
		if client.username == "anonymous" {
			var payload map[string]interface{}
			if jsonErr := json.Unmarshal(msg, &payload); jsonErr == nil {
				if u, ok := payload["username"].(string); ok && u != "" {
					client.username = u
					log.Printf("[room:%s] client identified as %s", travelId, client.username)
				}
			}
			roomManager.mutex.Lock()
			roomManager.broadcastPresence(travelId)
			roomManager.mutex.Unlock()
		}
	}
}

// ping sends periodic pings to keep the connection alive.
func ping(client *Client, done chan struct{}) {
	ticker := time.NewTicker(30 * time.Second)
	defer ticker.Stop()
	for {
		select {
		case <-ticker.C:
			if err := client.writeMessage(websocket.PingMessage, nil); err != nil {
				return
			}
		case <-done:
			return
		}
	}
}

// GET / — status page
func homePage(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "text/plain")
	fmt.Fprintf(w, "WebSocket Server — Planning Travels\nActive rooms: %d\nTotal connections: %d\n",
		roomManager.TotalRooms(), roomManager.TotalConnections())
}

// GET /ws/{travelId} — WebSocket upgrade; client joins the travel room
func wsEndpoint(w http.ResponseWriter, r *http.Request) {
	travelId := strings.TrimPrefix(r.URL.Path, "/ws/")
	if travelId == "" {
		http.Error(w, "travelId required", http.StatusBadRequest)
		return
	}

	conn, err := upgrader.Upgrade(w, r, nil)
	if err != nil {
		log.Printf("upgrade error: %v", err)
		return
	}

	client := &Client{conn: conn, username: "anonymous"}

	roomManager.mutex.Lock()
	if roomManager.rooms[travelId] == nil {
		roomManager.rooms[travelId] = make(map[*Client]bool)
	}
	roomManager.rooms[travelId][client] = true
	log.Printf("[room:%s] %s joined. room size: %d", travelId, client.username, len(roomManager.rooms[travelId]))
	roomManager.broadcastPresence(travelId)
	roomManager.mutex.Unlock()

	done := make(chan struct{})
	go ping(client, done)

	reader(travelId, client)

	close(done)
}

// POST /travel/{travelId}/broadcast — PHP calls this to push an event to all room members
func broadcastEndpoint(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "method not allowed", http.StatusMethodNotAllowed)
		return
	}

	travelId := strings.TrimPrefix(r.URL.Path, "/travel/")
	travelId = strings.TrimSuffix(travelId, "/broadcast")
	if travelId == "" {
		http.Error(w, "travelId required", http.StatusBadRequest)
		return
	}

	body, err := io.ReadAll(r.Body)
	if err != nil {
		http.Error(w, "cannot read body", http.StatusBadRequest)
		return
	}
	defer r.Body.Close()

	roomManager.BroadcastToRoom(travelId, websocket.TextMessage, body)

	w.WriteHeader(http.StatusOK)
	fmt.Fprint(w, "ok")
}

func setupRoutes() {
	http.HandleFunc("/ws/", wsEndpoint)
	http.HandleFunc("/travel/", broadcastEndpoint)
	http.HandleFunc("/", homePage)
}

func main() {
	setupRoutes()

	server := &http.Server{
		Addr: ":5555",
	}

	go func() {
		sigChan := make(chan os.Signal, 1)
		signal.Notify(sigChan, syscall.SIGINT, syscall.SIGTERM)
		<-sigChan

		log.Println("shutting down server...")
		ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
		defer cancel()

		if err := server.Shutdown(ctx); err != nil {
			log.Printf("shutdown error: %v", err)
		}
	}()

	log.Println("WebSocket server started at :5555")
	if err := server.ListenAndServe(); err != http.ErrServerClosed {
		log.Fatalf("server error: %v", err)
	}
	log.Println("server stopped")
}
