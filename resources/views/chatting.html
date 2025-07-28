@extends('layouts.app')

@section('content')
<style>
  * { box-sizing: border-box; }
  body { background: #f0f2f5; }
  .chat-container { display: flex; height: calc(100vh - 56px); }
  .user-list { width: 250px; background-color: #fff; border-right: 1px solid #ddd; overflow-y: auto; }
  .user-item { padding: 15px; cursor: pointer; border-bottom: 1px solid #eee; }
  .user-item:hover { background-color: #f5f5f5; }
  .user-item.active { background-color: #e7f3ff; font-weight: bold; }
  .chat-area { flex: 1; display: flex; flex-direction: column; background-color: #fff; }
  .chat-header { padding: 15px; background: #0084ff; color: white; font-size: 18px; font-weight: bold; border-bottom: 1px solid #006fe6; }
  .chat-messages { flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background-color: #f0f2f5; }
  .message { max-width: 70%; padding: 10px 15px; border-radius: 20px; font-size: 14px; line-height: 1.4; word-wrap: break-word; position: relative; }
  .message.sent { align-self: flex-end; background-color: #6dd81bff; }
  .message.received { align-self: flex-start; background-color: #aa7272ff; }
  .seen-status, .status-text { font-size: 11px; color: #555; margin-top: 4px; text-align: right; }
  .chat-input { display: flex; padding: 10px; border-top: 1px solid #ddd; background: #fff; }
  .chat-input input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 20px; outline: none; }
  .chat-input button { padding: 0 20px; margin-left: 10px; border: none; background-color: #0084ff; color: white; border-radius: 20px; cursor: pointer; }
  @media (max-width: 768px) { .user-list { display: none; } }
</style>

<div class="chat-container">
  <div class="user-list">
    @foreach ($users as $u)
      @if ($u->id !== auth()->id())
        <div class="user-item" data-id="{{ $u->id }}">{{ $u->name }}</div>
      @endif
    @endforeach
  </div>

  <div class="chat-area">
    <div class="chat-header" id="chat-header">Chat</div>
    <div class="chat-messages" id="chat-box"></div>
    <form id="chat-form" class="chat-input">
      <input type="text" id="message" placeholder="Type a message..." required autocomplete="off">
      <button type="submit">Send</button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
  window.currentUserId = {{ auth()->id() }};
  window.selectedUserId = null;
</script>

@vite(['resources/js/echo.js'])

<script>
document.addEventListener('DOMContentLoaded', function () {
  const chatBox = document.getElementById('chat-box');
  const chatHeader = document.getElementById('chat-header');
  const messageInput = document.getElementById('message');
  const userItems = document.querySelectorAll('.user-item');
  let currentUserId = window.currentUserId;
  let selectedUserId = null;

  let oldestMessageId = null; // track earliest message ID loaded
  let loadingOlderMessages = false; // prevent multiple loads

  window.appendMessage = function(data) {
    if (data.id && document.querySelector(`[data-message-id="${data.id}"]`)) return;
    if (data.temp_id && document.querySelector(`[data-temp-id="${data.temp_id}"]`)) return;

    const bubble = document.createElement('div');
    bubble.className = `message ${data.is_you ? 'sent' : 'received'}`;
    if (data.id) bubble.dataset.messageId = data.id;
    if (data.temp_id) bubble.dataset.tempId = data.temp_id;

    let html = `${data.message}`;

    if (data.is_you) {
      if (data.is_read && data.read_at) {
        html += `<div class="seen-status">${formatSeenTime(data.read_at)}</div>`;
      } else {
        let statusText = data.status || 'Sent';
        html += `<div class="status-text">${statusText}</div>`;
      }
    }

    bubble.innerHTML = html;
    chatBox.appendChild(bubble);
    chatBox.scrollTop = chatBox.scrollHeight;
    return bubble;
  };

  function formatSeenTime(readAt) {
    const now = new Date();
    const readDate = new Date(readAt);
    const diffSec = Math.floor((now - readDate) / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHour = Math.floor(diffMin / 60);
    const diffDay = Math.floor(diffHour / 24);

    const time = readDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const weekday = readDate.toLocaleDateString(undefined, { weekday: 'long' });
    const fullDate = readDate.toLocaleDateString();

    if (diffSec < 60) return '✓ Seen';
    else if (diffMin < 60) return `✓ Seen ${diffMin} minute${diffMin > 1 ? 's' : ''} ago`;
    else if (diffDay === 0) return `✓ Seen at ${time}`;
    else if (diffDay === 1) return `✓ Seen yesterday at ${time}`;
    else if (diffDay < 7) return `✓ Seen on ${weekday} at ${time}`;
    else return `✓ Seen on ${fullDate} at ${time}`;
  }

  async function loadMessages(userId, prepend = false) {
    if (loadingOlderMessages) return;
    loadingOlderMessages = true;

    try {
      let url = `/chat/${userId}/messages`;
      if (prepend && oldestMessageId) {
        url += `?before=${oldestMessageId}`;
      }

      const response = await axios.get(url);

      if (!prepend) {
        chatBox.innerHTML = '';
        chatHeader.innerText = document.querySelector(`.user-item[data-id="${userId}"]`).innerText;
      }

      if (response.data.length === 0 && prepend) {
        loadingOlderMessages = false;
        return; // no more older messages
      }

      let oldScrollHeight = chatBox.scrollHeight;

      if (prepend) {
        // Prepend older messages on top
        for (const msg of response.data) {
          if (!document.querySelector(`[data-message-id="${msg.id}"]`)) {
            const bubble = appendMessage({
              id: msg.id,
              message: msg.message,
              is_you: msg.sender_id === currentUserId,
              is_read: msg.is_read,
              read_at: msg.read_at,
              created_at: msg.created_at,
              status: msg.is_read ? '✓ Seen' : 'Delivered'
            });
            chatBox.insertBefore(bubble, chatBox.firstChild);
          }
        }
        let newScrollHeight = chatBox.scrollHeight;
        chatBox.scrollTop = newScrollHeight - oldScrollHeight;
      } else {
        // Normal load: append messages bottom
        response.data.forEach(msg => {
          appendMessage({
            id: msg.id,
            message: msg.message,
            is_you: msg.sender_id === currentUserId,
            is_read: msg.is_read,
            read_at: msg.read_at,
            created_at: msg.created_at,
            status: msg.is_read ? '✓ Seen' : 'Delivered'
          });
        });
        chatBox.scrollTop = chatBox.scrollHeight;
      }

      if (response.data.length > 0) {
        oldestMessageId = response.data[0].id;
      }

      if (!prepend) {
        await markAsRead(userId);
      }
    } catch (error) {
      console.error('Error loading messages:', error);
      if (!prepend) chatBox.innerHTML = '<em>Could not load messages.</em>';
    }
    loadingOlderMessages = false;
  }

  async function markAsRead(senderId) {
    try {
      await axios.post(`/messages/mark-as-read/${senderId}`);
    } catch (err) {
      console.error('Error marking as read', err);
    }
  }

  function updateMessageStatus(idOrTempId, newStatus, isTemp = false) {
    let selector = isTemp
      ? `[data-temp-id="${idOrTempId}"]`
      : `[data-message-id="${idOrTempId}"]`;
    const messageElem = document.querySelector(selector);
    if (!messageElem) return;

    const oldStatus = messageElem.querySelector('.status-text');
    const oldSeen = messageElem.querySelector('.seen-status');
    if (oldStatus) oldStatus.remove();
    if (oldSeen) oldSeen.remove();

    if (newStatus === 'seen') {
      const seenDiv = document.createElement('div');
      seenDiv.className = 'seen-status';
      seenDiv.innerText = '✓ Seen';
      messageElem.appendChild(seenDiv);
    } else {
      const statusDiv = document.createElement('div');
      statusDiv.className = 'status-text';
      statusDiv.innerText = newStatus;
      messageElem.appendChild(statusDiv);
    }
  }

  userItems.forEach(item => {
    item.addEventListener('click', async () => {
      userItems.forEach(i => i.classList.remove('active'));
      item.classList.add('active');
      selectedUserId = parseInt(item.getAttribute('data-id'));
      window.selectedUserId = selectedUserId;
      oldestMessageId = null;
      await loadMessages(selectedUserId, false);
    });
  });

  if (userItems.length > 0) userItems[0].click();

  chatBox.addEventListener('scroll', async () => {
    if (chatBox.scrollTop < 100 && !loadingOlderMessages && oldestMessageId) {
      await loadMessages(selectedUserId, true);
    }
  });

  document.getElementById('chat-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const message = messageInput.value.trim();
    if (!message || !selectedUserId) return;

    const tempId = Date.now();

    appendMessage({
      message: message,
      is_you: true,
      created_at: new Date().toISOString(),
      temp_id: tempId,
      status: 'Sent'
    });

    try {
      const response = await axios.post(`/chat/${selectedUserId}/send`, {
        message: message,
        temp_id: tempId
      });
      messageInput.value = '';

      updateMessageStatus(tempId, 'Delivered', true);

    } catch (error) {
      console.error('Failed to send message:', error);
      updateMessageStatus(tempId, 'Failed', true);
    }
  });

  // Laravel Echo real-time listeners
  window.Echo.private(`chat.${currentUserId}`)
    .listen('MessageDelivered', (e) => {
      updateMessageStatus(e.message.temp_id || e.message.id, 'Delivered');
    })
    .listen('MessageSeen', (e) => {
      updateMessageStatus(e.message.id, 'seen');
    });
});
</script>
@endsection
