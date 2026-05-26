// main.js
document.addEventListener('DOMContentLoaded', function() {
    // Basic interaction for cart and alerts could go here
    
    // Auto-hide alerts after 4 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 4000);
    });

    // Chatbot Logic
    const toggleBtn = document.getElementById('chatbot-toggle-btn');
    const closeBtn = document.getElementById('chatbot-close-btn');
    const chatbotContainer = document.getElementById('chatbot-container');
    const sendBtn = document.getElementById('chatbot-send-btn');
    const inputField = document.getElementById('chatbot-input-field');
    const messagesContainer = document.getElementById('chatbot-messages');

    if (toggleBtn && chatbotContainer) {
        toggleBtn.addEventListener('click', () => {
            chatbotContainer.classList.add('active');
        });

        closeBtn.addEventListener('click', () => {
            chatbotContainer.classList.remove('active');
        });

        const chatHistory = [];

        const appendMessage = (text, sender) => {
            const msgDiv = document.createElement('div');
            msgDiv.classList.add('message');
            msgDiv.classList.add(sender === 'bot' ? 'bot-message' : 'user-message');
            
            // basic markdown to html conversion for bold
            text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
            
            msgDiv.innerHTML = text;
            messagesContainer.appendChild(msgDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        };

        const showLoading = () => {
            const loadingDiv = document.createElement('div');
            loadingDiv.classList.add('loading-dots');
            loadingDiv.id = 'chatbot-loading';
            loadingDiv.innerHTML = '<div class="dot"></div><div class="dot"></div><div class="dot"></div>';
            messagesContainer.appendChild(loadingDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        };

        const hideLoading = () => {
            const loadingDiv = document.getElementById('chatbot-loading');
            if (loadingDiv) {
                loadingDiv.remove();
            }
        };

        const sendMessage = async () => {
            const text = inputField.value.trim();
            if (!text) return;

            inputField.value = '';
            appendMessage(text, 'user');
            showLoading();

            try {
                const response = await fetch('chatbot_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: text,
                        history: chatHistory
                    })
                });

                hideLoading();

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                if (data.error) {
                    appendMessage("Sorry, I encountered an error: " + data.error, 'bot');
                } else if (data.reply) {
                    appendMessage(data.reply, 'bot');
                    chatHistory.push({ role: 'user', content: text });
                    chatHistory.push({ role: 'bot', content: data.reply });
                }

            } catch (error) {
                hideLoading();
                appendMessage("Sorry, there was a problem communicating with the server.", 'bot');
                console.error("Chatbot Error:", error);
            }
        };

        sendBtn.addEventListener('click', sendMessage);

        inputField.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
});
