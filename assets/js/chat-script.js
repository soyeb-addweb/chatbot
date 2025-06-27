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
            <div class="user-msg" style="background-color:${addweb_ai_chat.user_chat_bg};color:${addweb_ai_chat.user_chat_text}">${escapeHtml(text)}</div>
        `;
    } else if (type === 'bot') {
        div.className = 'bot-chat';

        // Split bot response into multiple lines/messages (paragraph-style)
        //const lines = text.split(/(?:<br\s*\/?>|\n)/i); // supports <br> and \n
        // const botMsgs = lines.map(line => `<div class="bot-msg">${line.trim()}</div>`).join('');
        const botMsgs = `<div class="bot-msg" style="background-color:${addweb_ai_chat.bot_chat_bg};color:${addweb_ai_chat.bot_chat_text}">${text}</div>`;

        div.innerHTML = `
            <div class="msg-heading">
                <img src="${addweb_ai_chat.bot_image}" alt="chat-bot-icon" width="22px" height="22px">
                <p class="msg-title">${addweb_ai_chat.bot_title}</p>
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
            nonce: addweb_ai_chat.nonce,
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

                // console.log('AI Response:', data.response);
                // console.log('Session ID:', data);
                
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
//     async function sendMessage() {
//     const message = input.value.trim();
//     if (!message) return;

//     appendMessage('user', message);
//     input.value = '';
//     sendButton.disabled = true;
//     loading.style.display = 'block';

//     const sessionId = localStorage.getItem('addweb_chat_session_id') || '';
//     const apiUrl = addweb_ai_chat.api_url; // You must localize this
//     const apiToken = addweb_ai_chat.api_token; // Also localize this securely (or pass via backend proxy)

//     const payload = {
//         query: message
//     };
//     if (sessionId) {
//         payload.session_id = sessionId;
//     }

//     const response = await fetch(apiUrl, {
//         method: 'POST',
//         headers: {
//             'Authorization': 'Bearer ' + apiToken,
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify(payload)
//     });

//     const reader = response.body.getReader();
//     const decoder = new TextDecoder('utf-8');

//     let botMessage = '';
//     let botDiv = null;

//     while (true) {
//         const { done, value } = await reader.read();
//         if (done) break;

//         const chunkText = decoder.decode(value, { stream: true });
//         const lines = chunkText.split('\n').filter(line => line.startsWith('data:'));

//         for (const line of lines) {
//             let json = null;
//             try {
//                 json = JSON.parse(line.replace(/^data:\s*/, ''));
//             } catch (e) {
//                 continue;
//             }

//             switch (json.type) {
//                 case 'start':
//                     botDiv = document.createElement('div');
//                     botDiv.className = 'bot-chat';
//                     botDiv.innerHTML = `
//                         <div class="msg-heading">
//                             <img src="${addweb_ai_chat.bot_image}" alt="chat-bot-icon" width="22px" height="22px">
//                             <p class="msg-title">${addweb_ai_chat.bot_title}</p>
//                         </div>
//                         <div class="bot-msg" style="background-color:${addweb_ai_chat.bot_chat_bg};color:${addweb_ai_chat.bot_chat_text}"></div>
//                     `;
//                     chatBody.appendChild(botDiv);
//                     break;

//                 case 'chunk':
//                     if (botDiv) {
//                         botMessage += json.content;
//                         botDiv.querySelector('.bot-msg').textContent = botMessage;
//                         chatBody.scrollTop = chatBody.scrollHeight;
//                     }
//                     break;

//                 case 'done':
//                     loading.style.display = 'none';
//                     break;

//                 case 'complete':
//                     if (json.result?.session_id) {
//                         localStorage.setItem('addweb_chat_session_id', json.result.session_id);
//                     }
//                     break;
//             }
//         }
//     }

//     sendButton.disabled = false;
// }


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

    //End chat functionality ajax
    const endChatButton = document.querySelector('.popup-btn');

    if (endChatButton) {
        endChatButton.addEventListener('click', function () {
            const emailInput = document.querySelector('.popup-input');
            const email = emailInput ? emailInput.value.trim() : '';
            const sessionId = localStorage.getItem('addweb_chat_session_id');

            if (!email || !sessionId) {
                alert('Please enter a valid email and ensure session ID is available.');
                return;
            }

            const data = new FormData();
            data.append('action', 'addweb_end_chat');
            data.append('email', email);
            data.append('session_id', sessionId);
            data.append('nonce', addweb_ai_chat.nonce); // localized from PHP

            fetch(addweb_ai_chat.ajax_url, {
                method: 'POST',
                body: data
            })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        alert('Chat ended successfully.');
                        document.querySelector('.popup').style.display = 'none';
                        localStorage.removeItem('addweb_chat_session_id');
                        appendMessage('bot', "Thank you for chatting with us! If you have any further questions, feel free to reach out via email or our contact form.");
                        //   window.clearChatSession = function() {
                        //     chatSessionId = '';
                        //     localStorage.removeItem('addweb_chat_session_id');
                        //     // Optionally clear chat history
                        //     chatBody.innerHTML = '';
                        // };
                    } else {
                        alert('Error: ' + (response.data || 'Something went wrong.'));
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Request failed. Check console for details.');
                });
        });
    }

    //display popup for email input
   document.getElementById('endChatBtn').addEventListener('click', function () {
    const popup = document.getElementById('endChatPopup');
    if (popup.classList.contains('addweb-dnone')) {
        popup.classList.remove('addweb-dnone');
        popup.classList.add('addweb-slide-show');

        // Optional: remove animation class after it completes to allow future triggers
        setTimeout(() => popup.classList.remove('addweb-slide-show'), 500);
    }
});
    
});
document.addEventListener('DOMContentLoaded', function () {
const chatBtn = document.querySelector('#ChatBtn');
const msgvector = document.querySelector('#msg-Vector');
const msgVector02 = document.querySelector('#close-Vector');
const cancelbtn = document.querySelector('#cancel-btn');
const chatBoard = document.getElementById("chatBoard");
const chatIcon = document.getElementById("chatIcon");
const closeBtn = document.getElementById("closeChatBtn"); // close button
const popup = document.getElementById('endChatPopup');

chatBtn.addEventListener('click', function () {
msgVector02.classList.toggle('popup-cross-btn');
msgvector.classList.toggle('popup-chat-btn');
});

const closeChatPopup = () => {
chatBoard.classList.remove("show");
msgVector02.classList.remove('popup-cross-btn');
chatIcon.classList.remove("rotated");
msgvector.classList.toggle('popup-chat-btn');
popup.classList.add('addweb-dnone');
};

cancelbtn.addEventListener("click", closeChatPopup);

// Close button logic
if (closeBtn) {
closeBtn.addEventListener("click", closeChatPopup);
}
});
