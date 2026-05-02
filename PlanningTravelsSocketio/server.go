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

type ConnectedUser struct {
	UserID   string `json:"userId"`
	Username string `json:"username"`
	Conn     *websocket.Conn
}

type ChatMessage struct {
	Type     string `json:"type"`
	UserID   string `json:"userId"`
	Username string `json:"username"`
	Content  string `json:"content"`
	Time     string `json:"time"`
}

type UserJoinedMessage struct {
	Type     string `json:"type"`
	UserID   string `json:"userId"`
	Username string `json:"username"`
}

type UserLeftMessage struct {
	Type     string `json:"type"`
	UserID   string `json:"userId"`
	Username string `json:"username"`
}

type RoomManager struct {
	rooms    map[string]map[*websocket.Conn]bool
	users    map[string]map[*websocket.Conn]ConnectedUser
	mutex    sync.RWMutex
}

func NewRoomManager() *RoomManager {
	return &RoomManager{
		rooms: make(map[string]map[*websocket.Conn]bool),
		users: make(map[string]map[*websocket.Conn]ConnectedUser),
	}
}

func (rm *RoomManager) Join(travelId string, conn *websocket.Conn, userID, username string) {
	rm.mutex.Lock()
	defer rm.mutex.Unlock()
	if rm.rooms[travelId] == nil {
		rm.rooms[travelId] = make(map[*websocket.Conn]bool)
	}
	if rm.users[travelId] == nil {
		rm.users[travelId] = make(map[*websocket.Conn]ConnectedUser)
	}
	rm.rooms[travelId][conn] = true
	rm.users[travelId][conn] = ConnectedUser{
		UserID:   userID,
		Username: username,
		Conn:     conn,
	}
	log.Printf("[room:%s] user %s (%s) joined. room size: %d", travelId, username, userID, len(rm.rooms[travelId]))
}

func (rm *RoomManager) Leave(travelId string, conn *websocket.Conn) {
	rm.mutex.Lock()
	defer rm.mutex.Unlock()
	if room, ok := rm.rooms[travelId]; ok {
		delete(room, conn)

		var leftUser *ConnectedUser
		if users, userOk := rm.users[travelId]; userOk {
			if u, found := users[conn]; found {
				leftUser = &u
				delete(users, conn)
			}
			if len(users) == 0 {
				delete(rm.users, travelId)
			}
		}

		if len(room) == 0 {
			delete(rm.rooms, travelId)
		}

		if leftUser != nil {
			log.Printf("[room:%s] user %s left", travelId, leftUser.Username)
			joinedMsg := UserLeftMessage{
				Type:     "user_left",
				UserID:   leftUser.UserID,
				Username: leftUser.Username,
			}
			data, _ := json.Marshal(joinedMsg)
			rm.broadcastToRoomUnsafe(travelId, websocket.TextMessage, data)
		} else {
			log.Printf("[room:%s] connection left", travelId)
		}
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

func (rm *RoomManager) broadcastToRoomUnsafe(travelId string, messageType int, message []byte) {
	room, ok := rm.rooms[travelId]
	if !ok {
		return
	}
	for conn := range room {
		conn.WriteMessage(messageType, message)
	}
}

func (rm *RoomManager) HandleChatMessage(travelId string, senderConn *websocket.Conn, message []byte) {
	rm.mutex.RLock()
	defer rm.mutex.RUnlock()

	var chatMsg ChatMessage
	if err := json.Unmarshal(message, &chatMsg); err != nil {
		log.Printf("[room:%s] invalid chat message: %v", travelId, err)
		return
	}

	if chatMsg.Type != "chat" || chatMsg.Content == "" {
		return
	}

	chatMsg.Time = time.Now().UTC().Format(time.RFC3339)
	data, _ := json.Marshal(chatMsg)

	room, ok := rm.rooms[travelId]
	if !ok {
		return
	}

	for conn := range room {
		if conn != senderConn {
			if err := conn.WriteMessage(websocket.TextMessage, data); err != nil {
				log.Printf("[room:%s] chat broadcast error: %v", travelId, err)
			}
		}
	}
}

func (rm *RoomManager) GetConnectedUsers(travelId string) []ConnectedUser {
	rm.mutex.RLock()
	defer rm.mutex.RUnlock()

	var result []ConnectedUser
	if users, ok := rm.users[travelId]; ok {
		for _, u := range users {
			result = append(result, u)
		}
	}
	return result
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
		_, message, err := conn.ReadMessage()
		if err != nil {
			if websocket.IsUnexpectedCloseError(err, websocket.CloseGoingAway, websocket.CloseAbnormalClosure) {
				log.Printf("[room:%s] read error: %v", travelId, err)
			}
			return
		}

		rooms.HandleChatMessage(travelId, conn, message)
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

// GET /ws/{travelId}?userId=xxx&username=yyy — WebSocket upgrade; client joins the travel room
func wsEndpoint(w http.ResponseWriter, r *http.Request) {
	travelId := strings.TrimPrefix(r.URL.Path, "/ws/")
	if travelId == "" {
		http.Error(w, "travelId required", http.StatusBadRequest)
		return
	}

	userID := r.URL.Query().Get("userId")
	username := r.URL.Query().Get("username")

	// Basic validation: require userId and username
	if userID == "" || username == "" {
		http.Error(w, "missing userId or username", http.StatusUnauthorized)
		return
	}

	conn, err := upgrader.Upgrade(w, r, nil)
	if err != nil {
		log.Printf("upgrade error: %v", err)
		return
	}

	rooms.Join(travelId, conn, userID, username)

	joinedMsg := UserJoinedMessage{
		Type:     "user_joined",
		UserID:   userID,
		Username: username,
	}
	data, _ := json.Marshal(joinedMsg)
	rooms.BroadcastToRoom(travelId, websocket.TextMessage, data)

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

	// TODO: Add authentication check here (e.g., validate token from PHP)
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

// GET /travel/{travelId}/users — get connected users in a travel room
func getUsersEndpoint(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "method not allowed", http.StatusMethodNotAllowed)
		return
	}

	travelId := strings.TrimPrefix(r.URL.Path, "/travel/")
	travelId = strings.TrimSuffix(travelId, "/users")
	if travelId == "" {
		http.Error(w, "travelId required", http.StatusBadRequest)
		return
	}

	users := rooms.GetConnectedUsers(travelId)
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(users)
}

func setupRoutes() {
	http.HandleFunc("/ws/", wsEndpoint)
	http.HandleFunc("/travel/", func(w http.ResponseWriter, r *http.Request) {
		if strings.HasSuffix(r.URL.Path, "/users") {
			getUsersEndpoint(w, r)
		} else if strings.HasSuffix(r.URL.Path, "/broadcast") {
			broadcastEndpoint(w, r)
		} else {
			http.NotFound(w, r)
		}
	})
	http.HandleFunc("/", homePage)
}

func main() {
	setupRoutes()

	server := &http.Server{
		Addr:         ":5555",
		ReadTimeout:  60 * time.Second,
		WriteTimeout: 60 * time.Second,
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
