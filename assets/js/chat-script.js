document.addEventListener('DOMContentLoaded', function () {
   // const header = document.getElementById('addweb-ai-chat-header');
    //const chatBox = document.getElementById('addweb-ai-chat-box');
    const input = document.getElementById('addweb-chat-user-input');
    const sendButton = document.getElementById('addweb-chat-send-button');
    const loading = document.querySelector('.addweb-chat-loading');
    const chatBody = document.querySelector('.addweb-chat-body');

    //new desing start
      const chatBtn = document.getElementById("ChatBtn");
      const chatBoard = document.getElementById("chatBoard");
      const chatIcon = document.getElementById("chatIcon");
      const msgVector = document.getElementById("msg-Vector");
      const closeBtn = document.getElementById("closeChatBtn");

      chatBtn.addEventListener("click", () => {
        chatBoard.classList.toggle("show");
        chatIcon.classList.toggle("rotated");
        msgVector.classList.toggle("vector-moved");
      });

      closeBtn.addEventListener("click", () => {
        chatBoard.classList.remove("show");
        chatIcon.classList.remove("rotated");
        msgVector.classList.remove("vector-moved");
      });
   // new desing end

    // Store session_id for chat continuity
    let chatSessionId = localStorage.getItem('addweb_chat_session_id') || '';

    // header.addEventListener('click', function () {
    //     chatBox.style.display = (chatBox.style.display === 'none') ? 'block' : 'none';
    // });

  function appendMessage(type, text) {
    const div = document.createElement('div');

    if (type === 'user') {
        div.className = 'user-chat';
        div.innerHTML = `
            <p class="msg-title">You</p>
            <div class="user-msg">${escapeHtml(text)}</div>
        `;
    } else if (type === 'bot') {
        div.className = 'bot-chat';

        // Split bot response into multiple lines/messages (paragraph-style)
        //const lines = text.split(/(?:<br\s*\/?>|\n)/i); // supports <br> and \n
        // const botMsgs = lines.map(line => `<div class="bot-msg">${line.trim()}</div>`).join('');
        const botMsgs = `<div class="bot-msg">${text}</div>`;

        div.innerHTML = `
            <div class="msg-heading">
                <img src="https://cdn.prod.website-files.com/675a7db640d825582387a1fc/683009a59bbaa43a2d9487a4_chat-icon.svg" alt="chat-bot-icon" width="22px" height="22px">
                <p class="msg-title">Ticket Bot</p>
            </div>
            ${botMsgs}
        `;
    }

    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
}
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

    function sendMessage() {
        const message = input.value.trim();
        if (!message) return;

        appendMessage('user', message);
        input.value = '';
        sendButton.disabled = true;
        loading.style.display = 'block';

        // Prepare form data
        const formData = new URLSearchParams({
            action: 'addweb_ai_chat_send_message',
            message: message,
        });

        // Add session_id if it exists
        if (chatSessionId) {
            formData.append('session_id', chatSessionId);
        }

        fetch(addweb_ai_chat.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Display the AI response (now supports HTML)
                appendMessage('bot', data.response || addweb_ai_chat.no_response_text);
                
                // Store/update session_id for future requests
                if (data.session_id) {
                    chatSessionId = data.session_id;
                    localStorage.setItem('addweb_chat_session_id', chatSessionId);
                }
            } else {
                appendMessage('bot', data.response || addweb_ai_chat.error_text);
            }
        })
        .catch(() => {
            appendMessage('bot', addweb_ai_chat.error_text);
        })
        .finally(() => {
            loading.style.display = 'none';
            sendButton.disabled = false;
        });
    }

    sendButton.addEventListener('click', sendMessage);
    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') sendMessage();
    });

    // Optional: Add function to clear chat session
    window.clearChatSession = function() {
        chatSessionId = '';
        localStorage.removeItem('addweb_chat_session_id');
        // Optionally clear chat history
        chatBody.innerHTML = '';
    };
});