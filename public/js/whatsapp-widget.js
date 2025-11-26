/**
 * Widget de WhatsApp Flutuante
 * Adiciona um botão flutuante de WhatsApp na página
 */

class WhatsAppWidget {
    constructor() {
        this.whatsappNumber = document.querySelector('meta[name="whatsapp-number"]')?.content || '244923456789';
        this.whatsappMessage = document.querySelector('meta[name="whatsapp-message"]')?.content || 'Olá! Gostaria de mais informações sobre os livros.';
        this.init();
    }

    init() {
        this.createWidget();
        this.bindEvents();
    }

    createWidget() {
        // Criar o botão flutuante
        const widget = document.createElement('div');
        widget.id = 'whatsapp-widget';
        widget.innerHTML = `
            <div class="whatsapp-button" id="whatsapp-button" title="Falar no WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </div>
            <div class="whatsapp-tooltip" id="whatsapp-tooltip">
                <span>Precisa de ajuda? Fale conosco!</span>
                <button class="whatsapp-close" id="whatsapp-close">×</button>
            </div>
        `;

        // Adicionar estilos
        const style = document.createElement('style');
        style.textContent = `
            #whatsapp-widget {
                position: fixed;
                bottom: 20px;
                right: 90px;
                z-index: 999;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            .whatsapp-button {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
                transition: all 0.3s ease;
                animation: whatsapp-pulse 2s infinite;
            }

            .whatsapp-button:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 25px rgba(37, 211, 102, 0.6);
            }

            .whatsapp-button i {
                font-size: 28px;
                color: white;
            }

            .whatsapp-tooltip {
                position: absolute;
                bottom: 70px;
                right: 0;
                background: white;
                padding: 12px 16px;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                max-width: 250px;
                opacity: 0;
                visibility: hidden;
                transform: translateY(10px);
                transition: all 0.3s ease;
                border: 1px solid #e0e0e0;
            }

            .whatsapp-tooltip.show {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }

            .whatsapp-tooltip::after {
                content: '';
                position: absolute;
                bottom: -8px;
                right: 20px;
                width: 0;
                height: 0;
                border-left: 8px solid transparent;
                border-right: 8px solid transparent;
                border-top: 8px solid white;
            }

            .whatsapp-tooltip span {
                color: #333;
                font-size: 14px;
                font-weight: 500;
                display: block;
                margin-bottom: 8px;
            }

            .whatsapp-close {
                position: absolute;
                top: 4px;
                right: 8px;
                background: none;
                border: none;
                font-size: 18px;
                color: #999;
                cursor: pointer;
                padding: 0;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .whatsapp-close:hover {
                color: #666;
            }

            @keyframes whatsapp-pulse {
                0% {
                    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
                }
                50% {
                    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.6), 0 0 0 10px rgba(37, 211, 102, 0.1);
                }
                100% {
                    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
                }
            }

            /* Responsivo */
            @media (max-width: 768px) {
                #whatsapp-widget {
                    bottom: 15px;
                    right: 15px;
                }

                .whatsapp-button {
                    width: 55px;
                    height: 55px;
                }

                .whatsapp-button i {
                    font-size: 24px;
                }

                .whatsapp-tooltip {
                    max-width: 200px;
                    right: -10px;
                }
            }
        `;

        document.head.appendChild(style);
        document.body.appendChild(widget);

        // Mostrar tooltip após 3 segundos
        setTimeout(() => {
            this.showTooltip();
        }, 3000);

        // Esconder tooltip após 8 segundos
        setTimeout(() => {
            this.hideTooltip();
        }, 8000);
    }

    bindEvents() {
        const button = document.getElementById('whatsapp-button');
        const closeBtn = document.getElementById('whatsapp-close');

        button.addEventListener('click', () => {
            this.openWhatsApp();
        });

        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.hideTooltip();
        });

        // Mostrar tooltip ao passar o mouse
        button.addEventListener('mouseenter', () => {
            this.showTooltip();
        });

        // Esconder tooltip ao sair com o mouse (após delay)
        button.addEventListener('mouseleave', () => {
            setTimeout(() => {
                this.hideTooltip();
            }, 2000);
        });
    }

    showTooltip() {
        const tooltip = document.getElementById('whatsapp-tooltip');
        tooltip.classList.add('show');
    }

    hideTooltip() {
        const tooltip = document.getElementById('whatsapp-tooltip');
        tooltip.classList.remove('show');
    }

    openWhatsApp() {
        const message = encodeURIComponent(this.whatsappMessage);
        const whatsappUrl = `https://wa.me/${this.whatsappNumber}?text=${message}`;
        
        // Abrir WhatsApp em nova aba
        window.open(whatsappUrl, '_blank');
        
        // Esconder tooltip
        this.hideTooltip();
        
        // Analytics (opcional)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'whatsapp_click', {
                event_category: 'engagement',
                event_label: 'whatsapp_widget'
            });
        }
    }
}

// Inicializar o widget quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    // Verificar se não é uma página de admin
    if (!window.location.pathname.includes('/admin')) {
        new WhatsAppWidget();
    }
});
