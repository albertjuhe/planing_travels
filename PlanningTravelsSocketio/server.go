package main

import (
	"context"
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

// RoomManager manages WebSocket connections grouped by travelId.
type RoomManager struct {
	rooms map[string]map[*websocket.Conn]bool
	mutex sync.RWMutex
}

func NewRoomManager() *RoomManager {
	return &RoomManager{
		rooms: make(map[string]map[*websocket.Conn]bool),
	}
}

func (rm *RoomManager) Join(travelId string, conn *websocket.Conn) {
	rm.mutex.Lock()
	defer rm.mutex.Unlock()
	if rm.rooms[travelId] == nil {
		rm.rooms[travelId] = make(map[*websocket.Conn]bool)
	}
	rm.rooms[travelId][conn] = true
	log.Printf("[room:%s] connection joined. room size: %d", travelId, len(rm.rooms[travelId]))
}

func (rm *RoomManager) Leave(travelId string, conn *websocket.Conn) {
	rm.mutex.Lock()
	defer rm.mutex.Unlock()
	if room, ok := rm.rooms[travelId]; ok {
		delete(room, conn)
		if len(room) == 0 {
			delete(rm.rooms, travelId)
		}
		log.Printf("[room:%s] connection left", travelId)
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
	for conn := range room {
		if err := conn.WriteMessage(messageType, message); err != nil {
			log.Printf("[room:%s] broadcast error: %v", travelId, err)
		}
	}
	log.Printf("[room:%s] broadcast sent to %d connection(s)", travelId, len(room))
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

var rooms = NewRoomManager()

// reader keeps the connection alive and removes it on disconnect.
func reader(travelId string, conn *websocket.Conn) {
	defer func() {
		rooms.Leave(travelId, conn)
		conn.Close()
	}()

	conn.SetReadDeadline(time.Now().Add(60 * time.Second))
	conn.SetPongHandler(func(string) error {
		conn.SetReadDeadline(time.Now().Add(60 * time.Second))
		return nil
	})

	for {
		_, _, err := conn.ReadMessage()
		if err != nil {
			if websocket.IsUnexpectedCloseError(err, websocket.CloseGoingAway, websocket.CloseAbnormalClosure) {
				log.Printf("[room:%s] read error: %v", travelId, err)
			}
			return
		}
	}
}

// ping sends periodic pings to keep the connection alive.
func ping(conn *websocket.Conn, done chan struct{}) {
	ticker := time.NewTicker(30 * time.Second)
	defer ticker.Stop()
	for {
		select {
		case <-ticker.C:
			if err := conn.WriteMessage(websocket.PingMessage, nil); err != nil {
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
		rooms.TotalRooms(), rooms.TotalConnections())
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

	rooms.Join(travelId, conn)

	done := make(chan struct{})
	go ping(conn, done)

	reader(travelId, conn)

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

	rooms.BroadcastToRoom(travelId, websocket.TextMessage, body)

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
		Addr:         ":5555",
		ReadTimeout:  10 * time.Second,
		WriteTimeout: 10 * time.Second,
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
