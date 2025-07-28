import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
Pusher.logToConsole = true;

window.Echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: import.meta.env.VITE_REVERB_PORT,
  forceTLS: false,
  enabledTransports: ['ws', 'wss'],
});

const currentUserId = window.currentUserId;

// Elements from your Blade modal
const callModal = document.getElementById('call-modal');
const callUserName = document.getElementById('call-user-name');
const callUserImage = document.getElementById('call-user-image');
const callStatusText = document.getElementById('call-status-text');
const ringtone = document.getElementById('ringtone');

window.callerId = null; // track current caller

// Start call (caller sends request to backend)
window.callUser = function(receiverId) {
  fetch('/start-call', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({ receiver_id: receiverId }),
  }).then(res => res.json())
    .then(data => console.log('Call started', data));
};


// Listen to private audio call channel for current user
window.Echo.private(`audio-call.${currentUserId}`)

  .listen('IncomingCall', (e) => {
    console.log('Incoming call event received:', e);

    // Show your call modal here
    showCallModal({
      userId: e.fromUserId,
      userName: e.fromUserName,
      userImage: e.fromUserImage || '/images/default-avatar.png',
      statusText: 'Incoming call...',
      incoming: true
    });
  })
  .listen('CallSignal', e => {
    if (e.toUserId !== currentUserId) return;
    if (e.fromUserId === callerId && peerConnection) {
      peerConnection.signal(e.signalData);
    }
  })
  .listen('CallEnded', e => {
    if (e.toUserId !== currentUserId) return;
    endCall();
    hideCallModal();
  });




// Accept incoming call
window.acceptCall = function() {
  console.log("✅ Call accepted");
  ringtone.pause();
  ringtone.currentTime = 0;
  callModal.style.display = 'none';

  // TODO: Start your WebRTC connection here, e.g. call your startCall(false, callerId)
  if (typeof startCall === 'function' && callerId) {
    startCall(false, callerId);
  }
};

// Reject incoming call
window.endCall = function() {
  console.log("❌ Call rejected");
  ringtone.pause();
  ringtone.currentTime = 0;
  callModal.style.display = 'none';

  // Inform backend about call rejection
  fetch('/audio-call/end', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({
      toUserId: callerId,
      fromUserId: currentUserId,
    }),
  }).catch(() => {});

  // Cleanup
  if (window.peerConnection) {
    window.peerConnection.destroy();
    window.peerConnection = null;
  }
  callerId = null;
};
