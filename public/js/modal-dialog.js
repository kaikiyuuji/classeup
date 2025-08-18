/**
 * Modal Dialog Component
 * Substitui os alerts e confirms nativos por modais mais elegantes
 */
class ModalDialog {
    constructor() {
        this.modals = new Map();
    }

    /**
     * Exibe um modal de confirmação
     * @param {Object} options - Opções do modal
     * @param {string} options.message - Mensagem a ser exibida
     * @param {string} options.title - Título do modal
     * @param {string} options.confirmText - Texto do botão de confirmação
     * @param {string} options.cancelText - Texto do botão de cancelamento
     * @param {string} options.type - Tipo do modal (confirm, error, warning, info)
     * @param {string} options.confirmClass - Classes CSS do botão de confirmação
     * @returns {Promise<boolean>} - Retorna true se confirmado, false se cancelado
     */
    confirm(options = {}) {
        return new Promise((resolve) => {
            const modalId = 'modal-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            const config = {
                title: options.title || 'Confirmação',
                message: options.message || 'Tem certeza?',
                confirmText: options.confirmText || 'Confirmar',
                cancelText: options.cancelText || 'Cancelar',
                type: options.type || 'confirm',
                confirmClass: options.confirmClass || this.getConfirmClass(options.type || 'confirm')
            };

            const modalHTML = this.createModalHTML(modalId, config);
            document.body.insertAdjacentHTML('beforeend', modalHTML);

            const modal = document.getElementById(modalId);
            const confirmBtn = document.getElementById(modalId + '-confirm');
            const cancelBtn = document.getElementById(modalId + '-cancel');
            const backdrop = modal.querySelector('.modal-backdrop');

            // Armazena referência do modal
            this.modals.set(modalId, { modal, resolve });

            // Exibe o modal com animação
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                const modalPanel = modal.querySelector('.modal-panel');
                modalPanel.classList.remove('scale-95');
                modalPanel.classList.add('scale-100');
            });

            // Bloqueia scroll do body
            document.body.classList.add('overflow-hidden');

            // Handlers de eventos
            const cleanup = (result) => {
                this.hideModal(modalId, result);
            };

            confirmBtn.addEventListener('click', () => cleanup(true));
            cancelBtn.addEventListener('click', () => cleanup(false));
            backdrop.addEventListener('click', () => cleanup(false));

            // Fechar com ESC
            const escHandler = (e) => {
                if (e.key === 'Escape') {
                    document.removeEventListener('keydown', escHandler);
                    cleanup(false);
                }
            };
            document.addEventListener('keydown', escHandler);

            // Foco no botão de confirmação
            setTimeout(() => confirmBtn.focus(), 100);
        });
    }

    /**
     * Exibe um modal de alerta (apenas informativo)
     * @param {Object} options - Opções do modal
     * @returns {Promise<boolean>} - Sempre retorna true
     */
    alert(options = {}) {
        const alertOptions = {
            ...options,
            confirmText: options.confirmText || 'OK',
            type: options.type || 'info',
            showCancel: false
        };

        return this.confirm(alertOptions);
    }

    /**
     * Esconde um modal específico
     * @param {string} modalId - ID do modal
     * @param {boolean} result - Resultado da ação
     */
    hideModal(modalId, result) {
        const modalData = this.modals.get(modalId);
        if (!modalData) return;

        const { modal, resolve } = modalData;

        // Animação de saída
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        const modalPanel = modal.querySelector('.modal-panel');
        modalPanel.classList.remove('scale-100');
        modalPanel.classList.add('scale-95');

        // Remove após animação
        setTimeout(() => {
            modal.remove();
            document.body.classList.remove('overflow-hidden');
            this.modals.delete(modalId);
            resolve(result);
        }, 150);
    }

    /**
     * Cria o HTML do modal
     * @param {string} modalId - ID único do modal
     * @param {Object} config - Configurações do modal
     * @returns {string} - HTML do modal
     */
    createModalHTML(modalId, config) {
        const iconSVG = this.getIconSVG(config.type);
        const iconBgClass = this.getIconBgClass(config.type);
        const iconColorClass = this.getIconColorClass(config.type);

        return `
            <div id="${modalId}" class="fixed inset-0 z-50 overflow-y-auto opacity-0 transition-opacity duration-150" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="modal-backdrop fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="modal-panel inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all scale-95 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full ${iconBgClass} sm:mx-0 sm:h-10 sm:w-10">
                                    ${iconSVG.replace('ICON_COLOR', iconColorClass)}
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        ${config.title}
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            ${config.message}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" id="${modalId}-confirm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 ${config.confirmClass} text-base font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-opacity-50 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-150">
                                ${config.confirmText}
                            </button>
                            ${config.showCancel !== false ? `
                            <button type="button" id="${modalId}-cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-150">
                                ${config.cancelText}
                            </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Retorna as classes CSS do botão de confirmação baseado no tipo
     * @param {string} type - Tipo do modal
     * @returns {string} - Classes CSS
     */
    getConfirmClass(type) {
        const classes = {
            confirm: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
            error: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
            warning: 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
            info: 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            success: 'bg-green-600 hover:bg-green-700 focus:ring-green-500'
        };
        return classes[type] || classes.confirm;
    }

    /**
     * Retorna as classes de background do ícone
     * @param {string} type - Tipo do modal
     * @returns {string} - Classes CSS
     */
    getIconBgClass(type) {
        const classes = {
            confirm: 'bg-red-100',
            error: 'bg-red-100',
            warning: 'bg-yellow-100',
            info: 'bg-blue-100',
            success: 'bg-green-100'
        };
        return classes[type] || 'bg-gray-100';
    }

    /**
     * Retorna as classes de cor do ícone
     * @param {string} type - Tipo do modal
     * @returns {string} - Classes CSS
     */
    getIconColorClass(type) {
        const classes = {
            confirm: 'text-red-600',
            error: 'text-red-600',
            warning: 'text-yellow-600',
            info: 'text-blue-600',
            success: 'text-green-600'
        };
        return classes[type] || 'text-gray-600';
    }

    /**
     * Retorna o SVG do ícone baseado no tipo
     * @param {string} type - Tipo do modal
     * @returns {string} - SVG do ícone
     */
    getIconSVG(type) {
        const icons = {
            confirm: `<svg class="h-6 w-6 ICON_COLOR" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                      </svg>`,
            error: `<svg class="h-6 w-6 ICON_COLOR" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>`,
            warning: `<svg class="h-6 w-6 ICON_COLOR" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                      </svg>`,
            info: `<svg class="h-6 w-6 ICON_COLOR" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                   </svg>`,
            success: `<svg class="h-6 w-6 ICON_COLOR" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>`
        };
        return icons[type] || icons.info;
    }
}

// Instância global
const modalDialog = new ModalDialog();

// Funções globais para compatibilidade
window.confirmModal = function(message, title = 'Confirmação', options = {}) {
    return modalDialog.confirm({
        message: message,
        title: title,
        type: 'confirm',
        ...options
    });
};

window.alertModal = function(message, title = 'Aviso', type = 'info', options = {}) {
    return modalDialog.alert({
        message: message,
        title: title,
        type: type,
        ...options
    });
};

// Função para substituir confirm nativo
window.confirmDialog = window.confirmModal;

// Função para substituir alert nativo
window.alertDialog = window.alertModal;

// Exporta para uso em módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModalDialog;
}

// Disponibiliza globalmente
window.ModalDialog = ModalDialog;
window.modalDialog = modalDialog;