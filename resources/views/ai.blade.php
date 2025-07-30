@extends('layouts.app')

@section('content')
<style>
    .chat-container {
        max-width: 800px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        height: 80vh;
        display: flex;
        flex-direction: column;
    }
    .chat-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f9fafb;
        border-radius: 8px 8px 0 0;
    }
    .chat-title {
        font-weight: 600;
        font-size: 1.25rem;
        color: #374151;
    }
    .plus-badge {
        background: #10a37f;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
    }
    .message {
        margin-bottom: 20px;
        max-width: 80%;
    }
    .user-message {
        margin-left: auto;
        background: #7ad8ae;
        padding: 12px 16px;
        border-radius: 18px 18px 4px 18px;
        color: #1a2e22;
    }
    .ai-message {
        margin-right: auto;
        background: #f3f4f6;
        padding: 12px 16px;
        border-radius: 18px 18px 18px 4px;
        color: #374151;
    }
    .input-area {
        padding: 15px 20px;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
        border-radius: 0 0 8px 8px;
    }
    .message-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        resize: none;
        font-size: 1rem;
    }
    .send-button {
        background: #10a37f;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        margin-top: 10px;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
    }
    .send-button:hover {
        background: #0e8e6d;
    }
    .welcome-message {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }
</style>

<div class="chat-container">
    <div class="chat-header">
        <div class="chat-title">ChatGPT</div>
        <div class="plus-badge">Get Plus</div>
    </div>

    <div class="chat-messages" id="chatMessages">
        <div class="welcome-message" id="welcomeMessage">
            <p>Hey, {{ Auth::user()->name ?? 'Guest' }}. Ready to dive in?</p>
            <p><strong>Ask anything</strong></p>
        </div>
    </div>

    <div class="input-area">
        <form id="chatForm">
            @csrf
            <textarea name="subject" id="subject" class="message-input" rows="3" placeholder="Type your question..."></textarea>
            <button type="submit" class="send-button">Send</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    $('#chatForm').on('submit', function(e) {
        e.preventDefault();

        let prompt = $('#subject').val().trim();
        if (!prompt) return;

        // Append user message instantly
        $('#welcomeMessage').hide();
        $('#chatMessages').append(`
            <div class="message user-message">
                <strong>You:</strong> ${escapeHtml(prompt)}
            </div>
        `);

        $('#subject').val(''); // clear input
        scrollToBottom();

        $.ajax({
            url: "{{ route('ai.suggest') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                subject: prompt
            },
            success: function(response) {
                $('#chatMessages').append(`
                    <div class="message ai-message">
                        <strong>AI:</strong> ${escapeHtml(response.aiMessage)}
                    </div>
                `);
                scrollToBottom();
            },
            error: function() {
                $('#chatMessages').append(`
                    <div class="message ai-message">
                        <strong>AI:</strong> Sorry, something went wrong. Please try again.
                    </div>
                `);
                scrollToBottom();
            }
        });
    });

    function scrollToBottom() {
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Basic HTML escape to prevent XSS
    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>
@endsection
