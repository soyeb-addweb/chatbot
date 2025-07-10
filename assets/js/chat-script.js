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

    // Configuration for external API
    const API_CONFIG = {
        url: addweb_ai_chat.external_api_url || 'https://addwebchatbot.addwebprojects.com/api/chatbot/query'
    };

    // header.addEventListener('click', function () {
    //     chatBox.style.display = (chatBox.style.display === 'none') ? 'block' : 'none';
    // });

    function appendMessage(type, text, isStreaming = false) {
        const div = document.createElement('div');

        if (type === 'user') {
            div.className = 'user-chat';
            div.innerHTML = `
            <p class="msg-title">You</p>
            <div class="user-msg" style="background-color:${addweb_ai_chat.user_chat_bg};color:${addweb_ai_chat.user_chat_text}">${escapeHtml(text)}</div>
        `;
        } else if (type === 'bot') {
            div.className = 'bot-chat';
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

        // Return the div for streaming updates
        return div;
    }

    function updateBotMessage(botElement, content) {
        if (!botElement) return;

        const botMsgDiv = botElement.querySelector('.bot-msg');
        if (botMsgDiv) {
            // Support HTML content and basic markdown formatting
            const formattedContent = formatStreamingContent(content);
            botMsgDiv.innerHTML = formattedContent;
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }

    function formatStreamingContent(content) {
        // Basic formatting for streaming content
        return content
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code>$1</code>');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Original WordPress AJAX version (kept as fallback)
    function sendMessageWordPress() {
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
                    appendMessage('bot', data.response || addweb_ai_chat.no_response_text);

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

    // New streaming version
    async function sendMessage() {
        const message = input.value.trim();
        if (!message) return;

        appendMessage('user', message);
        input.value = '';
        sendButton.disabled = true;
        loading.style.display = 'block';

        // Create bot message element for streaming updates
        const botMessageElement = appendMessage('bot', '');
        let accumulatedResponse = '';

        try {
            // Generate session ID if not exists
            let chatSessionId = localStorage.getItem('addweb_chat_session_id') || '';

            // Prepare URL with query parameters
            const url = new URL(API_CONFIG.url);
            url.searchParams.append('query', message);
            url.searchParams.append('session_id', chatSessionId);
            url.searchParams.append('stream', 'true');

            // Prepare request body
            const requestBody = {
                query: message,
                session_id: chatSessionId,
                stream: true
            };

            const response = await fetch(url.toString(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Handle streaming response
            const reader = response.body.getReader();
            const decoder = new TextDecoder();

            while (true) {
                const { done, value } = await reader.read();

                if (done) {
                    break;
                }

                const chunk = decoder.decode(value);
                const lines = chunk.split('\n');

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        const data = line.slice(6);

                        if (data === '[DONE]') {
                            continue;
                        }

                        try {
                            const parsed = JSON.parse(data);

                            if (parsed.type === 'complete' && parsed.result?.session_id) {
                                localStorage.setItem('addweb_chat_session_id', parsed.result.session_id);
                            }

                            // Handle different response formats
                            let content = '';

                            // Your custom API format - adjust based on actual response structure
                            if (parsed.content) {
                                content = parsed.content;
                            }
                            // Check for text field
                            else if (parsed.text) {
                                content = parsed.text;
                            }
                            // Check for message field
                            else if (parsed.message) {
                                content = parsed.message;
                            }
                            // Check for data.content field
                            else if (parsed.data && parsed.data.content) {
                                content = parsed.data.content;
                            }
                            // OpenAI format (fallback)
                            else if (parsed.choices && parsed.choices[0] && parsed.choices[0].delta && parsed.choices[0].delta.content) {
                                content = parsed.choices[0].delta.content;
                            }
                            // Claude/Anthropic format (fallback)
                            else if (parsed.type === 'content_block_delta' && parsed.delta && parsed.delta.text) {
                                content = parsed.delta.text;
                            }

                            if (content) {
                                accumulatedResponse += content;
                                updateBotMessage(botMessageElement, accumulatedResponse);

                                // Optional: Debug logging (remove in production)
                                console.log('New chunk:', content);
                                console.log('Total so far:', accumulatedResponse);
                            }

                        } catch (parseError) {
                            console.warn('Failed to parse streaming data:', parseError);
                            console.log('Raw data:', data);
                        }
                    }
                }
            }

            // Final update in case there's any remaining content
            if (accumulatedResponse) {
                updateBotMessage(botMessageElement, accumulatedResponse);
            }

        } catch (error) {
            console.error('Streaming error:', error);

            // Fallback to WordPress AJAX if streaming fails
            if (error.message.includes('HTTP error') || error.name === 'TypeError') {
                console.log('Streaming failed, falling back to WordPress AJAX...');

                // Remove the empty bot message
                if (botMessageElement && botMessageElement.parentNode) {
                    botMessageElement.parentNode.removeChild(botMessageElement);
                }

                // Reset input and try WordPress method
                input.value = message;
                sendMessageWordPress();
                return;
            }

            updateBotMessage(botMessageElement, addweb_ai_chat.error_text || 'Sorry, something went wrong.');
        } finally {
            loading.style.display = 'none';
            sendButton.disabled = false;
        }
    }

    sendButton.addEventListener('click', sendMessage);
    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') sendMessage();
    });

    // Optional: Add function to clear chat session
    window.clearChatSession = function () {
        chatSessionId = '';
        localStorage.removeItem('addweb_chat_session_id');
        // Optionally clear chat history
        chatBody.innerHTML = '';
    };

    // Function to switch between streaming and WordPress modes
    window.toggleChatMode = function (useStreaming = true) {
        if (useStreaming) {
            sendButton.removeEventListener('click', sendMessageWordPress);
            sendButton.addEventListener('click', sendMessage);
        } else {
            sendButton.removeEventListener('click', sendMessage);
            sendButton.addEventListener('click', sendMessageWordPress);
        }
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