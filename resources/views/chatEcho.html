import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

// Enable verbose logging
Pusher.logToConsole = true;

window.Echo = new Echo({
  broadcaster: "reverb",
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: import.meta.env.VITE_REVERB_PORT,
  forceTLS: false,
  enabledTransports: ["ws", "wss"],
});

// Connection monitoring
window.Echo.connector.pusher.connection.bind("connected", () => {
  console.log("✅ WebSocket connected! Socket ID:", window.Echo.socketId());
});

window.Echo.connector.pusher.connection.bind("error", (err) => {
  console.error("❌ Connection error:", err);
});

  window.Echo.channel('public-chat')
    .listen('.message.sent', (data) => {
        console.log('New message received:', data);
        
        // Only show messages for the currently selected conversation
        if (selectedUserId && 
            
            (data.sender.id === currentUserId || 
             data.receiver_id === selectedUserId || 
             data.sender.id === selectedUserId)) {
            appendMessage({
                ...data,
                is_you: data.sender.id === currentUserId
            });
                   

        }
    });