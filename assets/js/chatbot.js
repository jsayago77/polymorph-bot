class ChatBot extends HTMLElement {
    constructor(name, animations) {
        super();

        this.animations = animations;

        this.attachShadow({ mode: 'open' });

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'http://localhost/example-site/wp-content/plugins/polymorph-bot/assets/public/output.css';
        this.shadowRoot.appendChild(link);

        const fa = document.createElement('link');
        fa.rel = 'stylesheet';
        fa.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css';
        this.shadowRoot.appendChild(fa);

        // Estructura del componente
        this.shadowRoot.innerHTML += `
            <div class="flex flex-col bg-white rounded-lg shadow-lg w-96 h-[500px]">
                <!-- Cabecera del Chatbot -->
                <div class="bg-blue-500 text-white p-4 rounded-t-lg">
                    <div class="flex items-center space-x-2">
                        <div id="avatar" class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        <div class="text-lg font-semibold">${name}</div>
                    </div>
                </div>

                <!-- Área de Mensajes -->
                <div class="flex-1 p-4 overflow-y-auto space-y-2" id="chat-container">
                    <!-- Mensajes se agregarán aquí dinámicamente -->
                </div>

                <!-- Campo de Entrada y Botón de Enviar -->
                <div class="p-4 border-t border-gray-200">
                    <div class="flex">
                        <input type="text" id="chat-input" placeholder="Escribe un mensaje..." class="flex-1 p-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button id="send-btn" class="bg-blue-500 text-white p-2 rounded-r-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fa-regular fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Referencias a los elementos del DOM
        this.avatar = this.shadowRoot.querySelector('#avatar');
        this.chatContainer = this.shadowRoot.querySelector('.chat-container');
        this.chatInput = this.shadowRoot.querySelector('#chat-input');
        this.sendButton = this.shadowRoot.querySelector('#send-btn');

        // Evento para enviar mensaje
        this.sendButton.addEventListener('click', () => this.sendMessage());
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            } else {
                this.updateAvatar('userWriting')
            }
        });

    }

    updateAvatar(action) {
        this.avatar.innerHTML = `
        <img src="${this.animations[action]}">
        `;
    }
    sendMessage() {
        const messageText = this.chatInput.value.trim();
        if (messageText === '') return;

        // Añadir mensaje del usuario
        this.addMessage('user', messageText);
        this.chatInput.value = '';

        // Simular respuesta del bot
        setTimeout(() => {
            this.addMessage('bot', 'Gracias por tu mensaje. ¿En qué más puedo ayudarte?');
        }, 1000);
    }

    addMessage(sender, text) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', sender);
        messageElement.textContent = text;
        this.chatContainer.appendChild(messageElement);

        // Desplazar el contenedor hacia abajo para mostrar el último mensaje
        this.chatContainer.scrollTop = this.chatContainer.scrollHeight;
    }
}