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
            'Meus pedidos especiais',
            'Pedido especial',
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
                
                // Verificar se é uma ação especial
                if (message === 'Abrir WhatsApp') {
                    this.openWhatsApp();
                    return;
                }
                
                // Verificar se é para criar formulário de pedido especial
                if (message === 'Informar dados do livro') {
                    this.showSpecialOrderForm();
                    return;
                }
                
                // Verificar se é para ver detalhes completos dos pedidos especiais
                if (message === 'Ver detalhes completos') {
                    this.redirectToSpecialOrders();
                    return;
                }
                
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

    openWhatsApp() {
        // Buscar configurações do Laravel via meta tags
        const whatsappNumber = document.querySelector('meta[name="whatsapp-number"]')?.content || '244923456789';
        const whatsappMessage = document.querySelector('meta[name="whatsapp-message"]')?.content || 'Olá! Vim através do chatbot da Livraria CRM e gostaria de mais informações sobre os livros.';
        const message = encodeURIComponent(whatsappMessage);
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${message}`;
        
        // Abrir WhatsApp em nova aba
        window.open(whatsappUrl, '_blank');
        
        // Adicionar mensagem de confirmação no chat
        this.addMessage('bot', '✅ Abrindo WhatsApp... Você será redirecionado para conversar com nosso atendimento!', [
            'Voltar ao menu',
            'Buscar livros'
        ]);
    }

    showSpecialOrderForm() {
        // Adicionar mensagem explicativa
        this.addMessage('bot', '📝 **Formulário de Pedido Especial**\n\nPreencha os dados do livro que você deseja solicitar:');
        
        // Criar formulário
        const messagesContainer = document.querySelector('.chatbot-messages');
        const formContainer = document.createElement('div');
        formContainer.className = 'chatbot-form-container';
        
        formContainer.innerHTML = `
            <form id="special-order-form" class="chatbot-form">
                <div class="form-group">
                    <label for="book_title">📚 Título do Livro *</label>
                    <input type="text" id="book_title" name="book_title" required placeholder="Ex: Dom Casmurro">
                </div>
                
                <div class="form-group">
                    <label for="book_author">👤 Autor</label>
                    <input type="text" id="book_author" name="book_author" placeholder="Ex: Machado de Assis">
                </div>
                
                <div class="form-group">
                    <label for="book_publisher">🏢 Editora</label>
                    <input type="text" id="book_publisher" name="book_publisher" placeholder="Ex: Companhia das Letras">
                </div>
                
                <div class="form-group">
                    <label for="book_isbn">🔢 ISBN</label>
                    <input type="text" id="book_isbn" name="book_isbn" placeholder="Ex: 978-85-359-0277-5">
                </div>
                
                <div class="form-group">
                    <label for="quantity">📦 Quantidade *</label>
                    <select id="quantity" name="quantity" required>
                        <option value="1">1 exemplar</option>
                        <option value="2">2 exemplares</option>
                        <option value="3">3 exemplares</option>
                        <option value="4">4 exemplares</option>
                        <option value="5">5 exemplares</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="delivery_preference">🚚 Forma de Entrega *</label>
                    <select id="delivery_preference" name="delivery_preference" required>
                        <option value="pickup">Retirada na loja</option>
                        <option value="delivery">Entrega em domicílio</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="customer_notes">💬 Observações</label>
                    <textarea id="customer_notes" name="customer_notes" placeholder="Informações adicionais sobre o livro..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">✅ Solicitar Pedido</button>
                    <button type="button" class="btn-cancel">❌ Cancelar</button>
                </div>
            </form>
        `;
        
        messagesContainer.appendChild(formContainer);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Adicionar estilos do formulário
        this.addFormStyles();
        
        // Bind eventos do formulário
        this.bindFormEvents();
    }

    addFormStyles() {
        if (document.getElementById('chatbot-form-styles')) return;
        
        const style = document.createElement('style');
        style.id = 'chatbot-form-styles';
        style.textContent = `
            .chatbot-form-container {
                margin: 10px 0;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                border: 1px solid #e9ecef;
            }
            
            .chatbot-form .form-group {
                margin-bottom: 15px;
            }
            
            .chatbot-form label {
                display: block;
                margin-bottom: 5px;
                font-weight: 500;
                color: #495057;
                font-size: 14px;
            }
            
            .chatbot-form input,
            .chatbot-form select,
            .chatbot-form textarea {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #ced4da;
                border-radius: 4px;
                font-size: 14px;
                font-family: inherit;
                box-sizing: border-box;
            }
            
            .chatbot-form input:focus,
            .chatbot-form select:focus,
            .chatbot-form textarea:focus {
                outline: none;
                border-color: #3490dc;
                box-shadow: 0 0 0 2px rgba(52, 144, 220, 0.2);
            }
            
            .chatbot-form textarea {
                resize: vertical;
                min-height: 60px;
            }
            
            .form-actions {
                display: flex;
                gap: 10px;
                margin-top: 20px;
            }
            
            .btn-submit,
            .btn-cancel {
                flex: 1;
                padding: 10px 15px;
                border: none;
                border-radius: 4px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .btn-submit {
                background: #28a745;
                color: white;
            }
            
            .btn-submit:hover {
                background: #218838;
            }
            
            .btn-cancel {
                background: #6c757d;
                color: white;
            }
            
            .btn-cancel:hover {
                background: #5a6268;
            }
        `;
        
        document.head.appendChild(style);
    }

    bindFormEvents() {
        const form = document.getElementById('special-order-form');
        const cancelBtn = form.querySelector('.btn-cancel');
        
        // Evento de submit
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitSpecialOrder(form);
        });
        
        // Evento de cancelar
        cancelBtn.addEventListener('click', () => {
            form.closest('.chatbot-form-container').remove();
            this.addMessage('bot', 'Formulário cancelado. Como posso ajudar?', [
                'Buscar livros',
                'Meus pedidos',
                'Pedido especial',
                'Voltar ao menu'
            ]);
        });
    }

    submitSpecialOrder(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Mostrar loading
        this.addMessage('user', '📝 Enviando pedido especial...');
        this.addTypingIndicator();
        
        // Enviar para o servidor
        fetch('/api/chatbot/special-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.token
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            this.removeTypingIndicator();
            form.closest('.chatbot-form-container').remove();
            
            if (data.success) {
                this.addMessage('bot', data.message, data.options);
            } else {
                this.addMessage('bot', data.message, data.options);
            }
        })
        .catch(error => {
            console.error('Erro ao enviar pedido especial:', error);
            this.removeTypingIndicator();
            form.closest('.chatbot-form-container').remove();
            this.addMessage('bot', 'Erro ao processar pedido. Tente novamente ou entre em contato conosco.', [
                'Tentar novamente',
                'Falar com atendente',
                'Voltar ao menu'
            ]);
        });
    }

    redirectToSpecialOrders() {
        // Adicionar mensagem de confirmação
        this.addMessage('bot', '🔄 Redirecionando para a página de pedidos especiais...', []);
        
        // Redirecionar após um pequeno delay
        setTimeout(() => {
            window.location.href = '/cliente/pedidos-especiais';
        }, 1000);
    }
}

// Inicializar o chatbot quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    const chatbot = new Chatbot();
    chatbot.init();
});
