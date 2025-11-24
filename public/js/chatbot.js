/**
 * Chatbot para CRM Livraria Angola
 */
class Chatbot {
    constructor() {
        this.messages = [];
        this.isOpen = false;
        this.isWaitingResponse = false;
        this.token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    init() {
        this.createChatbotUI();
        this.bindEvents();
        this.addMessage('bot', 'Olá! Bem-vindo à Livraria Angola. Como posso ajudar?', [
            'Buscar livros',
            'Meus pedidos',
            'Pontos de fidelidade',
            'Falar com atendente'
        ]);
    }

    createChatbotUI() {
        // Criar o botão do chatbot
        const chatbotButton = document.createElement('div');
        chatbotButton.id = 'chatbot-button';
        chatbotButton.innerHTML = '<i class="fas fa-comments"></i>';
        chatbotButton.className = 'chatbot-button';
        document.body.appendChild(chatbotButton);

        // Criar o container do chatbot
        const chatbotContainer = document.createElement('div');
        chatbotContainer.id = 'chatbot-container';
        chatbotContainer.className = 'chatbot-container';
        chatbotContainer.style.display = 'none';
        
        chatbotContainer.innerHTML = `
            <div class="chatbot-header">
                <div class="chatbot-title">Assistente Virtual</div>
                <div class="chatbot-close">&times;</div>
            </div>
            <div class="chatbot-messages"></div>
            <div class="chatbot-input-container">
                <input type="text" class="chatbot-input" placeholder="Digite sua mensagem...">
                <button class="chatbot-send"><i class="fas fa-paper-plane"></i></button>
            </div>
        `;
        
        document.body.appendChild(chatbotContainer);

        // Adicionar estilos
        if (!document.getElementById('chatbot-styles')) {
            const style = document.createElement('style');
            style.id = 'chatbot-styles';
            style.textContent = `
                .chatbot-button {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    background-color: #3490dc;
                    color: white;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    cursor: pointer;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
                    z-index: 1000;
                    font-size: 24px;
                }
                
                .chatbot-container {
                    position: fixed;
                    bottom: 90px;
                    right: 20px;
                    width: 350px;
                    height: 500px;
                    border-radius: 10px;
                    background-color: white;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                    display: flex;
                    flex-direction: column;
                    z-index: 1000;
                    overflow: hidden;
                }
                
                .chatbot-header {
                    background-color: #3490dc;
                    color: white;
                    padding: 15px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .chatbot-title {
                    font-weight: bold;
                }
                
                .chatbot-close {
                    cursor: pointer;
                    font-size: 20px;
                }
                
                .chatbot-messages {
                    flex: 1;
                    padding: 15px;
                    overflow-y: auto;
                }
                
                .chatbot-message {
                    margin-bottom: 15px;
                    max-width: 80%;
                    padding: 10px 15px;
                    border-radius: 18px;
                    word-wrap: break-word;
                }
                
                .chatbot-message.bot {
                    background-color: #f1f0f0;
                    align-self: flex-start;
                    border-bottom-left-radius: 5px;
                    margin-right: auto;
                }
                
                .chatbot-message.user {
                    background-color: #3490dc;
                    color: white;
                    align-self: flex-end;
                    border-bottom-right-radius: 5px;
                    margin-left: auto;
                }
                
                .chatbot-input-container {
                    display: flex;
                    padding: 15px;
                    border-top: 1px solid #e0e0e0;
                }
                
                .chatbot-input {
                    flex: 1;
                    padding: 10px;
                    border: 1px solid #e0e0e0;
                    border-radius: 20px;
                    outline: none;
                }
                
                .chatbot-send {
                    background-color: #3490dc;
                    color: white;
                    border: none;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    margin-left: 10px;
                    cursor: pointer;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                
                .chatbot-options {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-top: 10px;
                }
                
                .chatbot-option {
                    background-color: #e9ecef;
                    padding: 8px 15px;
                    border-radius: 15px;
                    cursor: pointer;
                    font-size: 14px;
                    transition: background-color 0.2s;
                }
                
                .chatbot-option:hover {
                    background-color: #d0d7de;
                }
                
                @media (max-width: 480px) {
                    .chatbot-container {
                        width: calc(100% - 40px);
                        height: 60vh;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    bindEvents() {
        // Abrir/fechar chatbot
        document.getElementById('chatbot-button').addEventListener('click', () => this.toggleChatbot());
        document.querySelector('.chatbot-close').addEventListener('click', () => this.toggleChatbot());
        
        // Enviar mensagem
        document.querySelector('.chatbot-send').addEventListener('click', () => this.sendMessage());
        document.querySelector('.chatbot-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });
        
        // Delegação de eventos para opções de chatbot
        document.querySelector('.chatbot-messages').addEventListener('click', (e) => {
            if (e.target.classList.contains('chatbot-option')) {
                const message = e.target.textContent;
                this.handleUserInput(message);
            }
        });
    }

    toggleChatbot() {
        this.isOpen = !this.isOpen;
        document.getElementById('chatbot-container').style.display = this.isOpen ? 'flex' : 'none';
        
        if (this.isOpen) {
            document.querySelector('.chatbot-input').focus();
        }
    }

    sendMessage() {
        const input = document.querySelector('.chatbot-input');
        const message = input.value.trim();
        
        if (message && !this.isWaitingResponse) {
            this.handleUserInput(message);
            input.value = '';
        }
    }

    handleUserInput(message) {
        this.addMessage('user', message);
        this.isWaitingResponse = true;
        
        // Simular digitação
        this.addTypingIndicator();
        
        // Processar a mensagem no servidor
        fetch('/api/chatbot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.token
            },
            body: JSON.stringify({ message })
        })
        .then(response => response.json())
        .then(data => {
            this.removeTypingIndicator();
            this.addMessage('bot', data.message, data.options);
            this.isWaitingResponse = false;
        })
        .catch(error => {
            console.error('Erro ao processar mensagem:', error);
            this.removeTypingIndicator();
            this.addMessage('bot', 'Desculpe, ocorreu um erro ao processar sua mensagem. Por favor, tente novamente.');
            this.isWaitingResponse = false;
        });
    }

    addMessage(sender, content, options = null) {
        const messagesContainer = document.querySelector('.chatbot-messages');
        const messageElement = document.createElement('div');
        messageElement.className = `chatbot-message ${sender}`;
        messageElement.textContent = content;
        messagesContainer.appendChild(messageElement);
        
        // Adicionar opções de resposta rápida se fornecidas
        if (options && options.length > 0) {
            const optionsContainer = document.createElement('div');
            optionsContainer.className = 'chatbot-options';
            
            options.forEach(option => {
                const optionElement = document.createElement('div');
                optionElement.className = 'chatbot-option';
                optionElement.textContent = option;
                optionsContainer.appendChild(optionElement);
            });
            
            messagesContainer.appendChild(optionsContainer);
        }
        
        // Rolar para a mensagem mais recente
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Armazenar a mensagem
        this.messages.push({
            sender,
            content,
            timestamp: new Date()
        });
    }

    addTypingIndicator() {
        const messagesContainer = document.querySelector('.chatbot-messages');
        const typingElement = document.createElement('div');
        typingElement.className = 'chatbot-message bot chatbot-typing';
        typingElement.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span>';
        messagesContainer.appendChild(typingElement);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Adicionar estilos para os pontos de digitação
        if (!document.querySelector('#chatbot-styles .dot')) {
            const styleElement = document.getElementById('chatbot-styles');
            styleElement.textContent += `
                .chatbot-typing {
                    display: flex;
                    align-items: center;
                    padding: 10px 15px;
                }
                
                .dot {
                    height: 8px;
                    width: 8px;
                    background-color: #777;
                    border-radius: 50%;
                    margin: 0 2px;
                    display: inline-block;
                    animation: pulse 1.5s infinite ease-in-out;
                }
                
                .dot:nth-child(2) {
                    animation-delay: 0.2s;
                }
                
                .dot:nth-child(3) {
                    animation-delay: 0.4s;
                }
                
                @keyframes pulse {
                    0%, 100% {
                        transform: scale(0.7);
                        opacity: 0.5;
                    }
                    50% {
                        transform: scale(1);
                        opacity: 1;
                    }
                }
            `;
        }
    }

    removeTypingIndicator() {
        const typingElement = document.querySelector('.chatbot-typing');
        if (typingElement) {
            typingElement.remove();
        }
    }
}

// Inicializar o chatbot quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    const chatbot = new Chatbot();
    chatbot.init();
});
