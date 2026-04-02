package main

import (
	"context"
	"fmt"
	"log"
	"net/http"
	"os"
	"os/signal"
	"sync"
	"syscall"
	"time"

	"github.com/gorilla/websocket"
)

// Upgrader configuration for WebSocket connections
var upgrader = websocket.Upgrader{
	ReadBufferSize:  1024,
	WriteBufferSize: 1024,
	// Allow connections from any origin (adjust for production)
	CheckOrigin: func(r *http.Request) bool { return true },
}

// ConnectionManager handles all active WebSocket connections
type ConnectionManager struct {
	connections map[*websocket.Conn]bool
	mutex       sync.RWMutex
}

func NewConnectionManager() *ConnectionManager {
	return &ConnectionManager{
		connections: make(map[*websocket.Conn]bool),
	}
}

func (cm *ConnectionManager) Add(conn *websocket.Conn) {
	cm.mutex.Lock()
	defer cm.mutex.Unlock()
	cm.connections[conn] = true
	log.Printf("Connection added. Total connections: %d", len(cm.connections))
}

func (cm *ConnectionManager) Remove(conn *websocket.Conn) {
	cm.mutex.Lock()
	defer cm.mutex.Unlock()
	delete(cm.connections, conn)
	log.Printf("Connection removed. Total connections: %d", len(cm.connections))
}

func (cm *ConnectionManager) Broadcast(messageType int, message []byte) {
	cm.mutex.RLock()
	defer cm.mutex.RUnlock()
	for conn := range cm.connections {
		if err := conn.WriteMessage(messageType, message); err != nil {
			log.Printf("Broadcast error: %v", err)
		}
	}
}

func (cm *ConnectionManager) Count() int {
	cm.mutex.RLock()
	defer cm.mutex.RUnlock()
	return len(cm.connections)
}

var manager = NewConnectionManager()

// reader listens for new messages on a WebSocket connection
func reader(conn *websocket.Conn) {
	defer func() {
		manager.Remove(conn)
		conn.Close()
	}()

	// Set read deadline and pong handler for connection health
	conn.SetReadDeadline(time.Now().Add(60 * time.Second))
	conn.SetPongHandler(func(string) error {
		conn.SetReadDeadline(time.Now().Add(60 * time.Second))
		return nil
	})

	for {
		messageType, p, err := conn.ReadMessage()
		if err != nil {
			if websocket.IsUnexpectedCloseError(err, websocket.CloseGoingAway, websocket.CloseAbnormalClosure) {
				log.Printf("Read error: %v", err)
			}
			return
		}

		log.Printf("Received: %s", string(p))

		// Echo the message back
		if err := conn.WriteMessage(messageType, p); err != nil {
			log.Printf("Write error: %v", err)
			return
		}
	}
}

// ping sends periodic pings to keep connection alive
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

func homePage(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "text/plain")
	fmt.Fprintf(w, "WebSocket Server - Planning Travels\nActive connections: %d", manager.Count())
}

func wsEndpoint(w http.ResponseWriter, r *http.Request) {
	ws, err := upgrader.Upgrade(w, r, nil)
	if err != nil {
		log.Printf("Upgrade error: %v", err)
		return
	}

	manager.Add(ws)
	log.Printf("User connected from %s", r.RemoteAddr)

	// Send welcome message
	if err := ws.WriteMessage(websocket.TextMessage, []byte("Hi User!")); err != nil {
		log.Printf("Welcome message error: %v", err)
		manager.Remove(ws)
		ws.Close()
		return
	}

	// Start ping goroutine to keep connection alive
	done := make(chan struct{})
	go ping(ws, done)

	// Listen for messages (blocking)
	reader(ws)

	// Signal ping goroutine to stop
	close(done)
}

func setupRoutes() {
	http.HandleFunc("/", homePage)
	http.HandleFunc("/ws", wsEndpoint)
}

func main() {
	setupRoutes()

	server := &http.Server{
		Addr:         ":5555",
		ReadTimeout:  10 * time.Second,
		WriteTimeout: 10 * time.Second,
	}

	// Graceful shutdown
	go func() {
		sigChan := make(chan os.Signal, 1)
		signal.Notify(sigChan, syscall.SIGINT, syscall.SIGTERM)
		<-sigChan

		log.Println("Shutting down server...")
		ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
		defer cancel()

		if err := server.Shutdown(ctx); err != nil {
			log.Printf("Shutdown error: %v", err)
		}
	}()

	log.Println("WebSocket server started at :5555")
	if err := server.ListenAndServe(); err != http.ErrServerClosed {
		log.Fatalf("Server error: %v", err)
	}
	log.Println("Server stopped")
}
