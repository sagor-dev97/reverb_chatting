<!-- resources/views/audio_call.blade.php -->
@extends('layouts.app')

@section('content')
<style>
  .chat-container {
    display: flex;
    height: calc(100vh - 56px);
  }
  .user-list {
    width: 250px;
    background-color: #fff;
    border-right: 1px solid #ddd;
    overflow-y: auto;
  }
  .chat-box {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    background: #f5f6fa;
  }
  .chat-header {
    padding: 10px;
    background: #fff;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .messages {
    flex-grow: 1;
    padding: 15px;
    overflow-y: auto;
  }
  .input-box {
    padding: 10px;
    background: #fff;
    border-top: 1px solid #ddd;
    display: flex;
    gap: 10px;
  }
  #start-audio-call {
    display: none;
  }
</style>
<div class="chat-container">
  <div class="user-list">
    @foreach ($users as $user)
      @if ($user->id !== auth()->id())
        <div class="user-item" data-id="{{ $user->id }}" data-name="{{ $user->name }}">
          {{ $user->name }}
        </div>
      @endif
    @endforeach
  </div>
  <div class="chat-box">
    <div class="chat-header">
      <div id="selected-user-name"></div>
      <div id="call-icons">
        <button id="start-audio-call">📞 Call</button>
      </div>
    </div>
    <div class="messages" id="messages"></div>
    <div class="input-box">
      <input type="text" id="message-input" placeholder="Type a message...">
      <button id="send-button">Send</button>
    </div>
  </div>
</div>

<audio id="remoteAudio" autoplay></audio>
<audio id="ringtone" src="/ringtone.mp3" loop></audio>

<script src="https://cdn.jsdelivr.net/npm/peerjs@1.5.2/dist/peerjs.min.js"></script>
<script>
  let selectedUserId = null;
  let selectedUserName = '';
  const userItems = document.querySelectorAll('.user-item');
  const selectedUserNameDiv = document.getElementById('selected-user-name');
  const callButton = document.getElementById('start-audio-call');
  const callStatus = document.getElementById('call-status');

  userItems.forEach(item => {
    item.addEventListener('click', () => {
      userItems.forEach(i => i.classList.remove('active'));
      item.classList.add('active');
      selectedUserId = item.getAttribute('data-id');
      selectedUserName = item.getAttribute('data-name');
      selectedUserNameDiv.textContent = selectedUserName;
      callButton.style.display = 'inline-block';
    });
  });

  callButton.addEventListener('click', () => {
    if (!selectedUserId) {
      alert('Please select a user to call.');
      return;
    }
    startCall(false, selectedUserId);
  });

  // Initialize PeerJS
  const myId = '{{ auth()->id() }}';
  const peer = new Peer(myId, {
    host: window.location.hostname,
    port: 9000,
    path: '/peerjs'
  });

  const remoteAudio = document.getElementById('remoteAudio');

  peer.on('call', call => {
    navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
      call.answer(stream);
      call.on('stream', remoteStream => {
        remoteAudio.srcObject = remoteStream;
      });
    });
  });

  function startCall(isInitiator, recipientId) {
    navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
      const call = peer.call(recipientId, stream);
      call.on('stream', remoteStream => {
        remoteAudio.srcObject = remoteStream;
      });
    });
  }
</script>
@endsection