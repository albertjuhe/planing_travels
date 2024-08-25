let socket = new WebSocket("ws://localhost:5555/ws");
console.log("Websocket connection");

socket.onopen = function (event) {
    socket.send("Hi from travel server!");
    $('#online-user').show();
};

socket.onclose = function (event) {
    console.log("CLose socket");
    socket.close();
}

socket.onerror = function (error) {
    console.log("Error websocket", error);
}