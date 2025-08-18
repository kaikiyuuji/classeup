/**
 * Funções auxiliares para modais
 * Evita duplicação de código entre as páginas
 */

/**
 * Manipula a confirmação de exclusão usando modal
 * @param {Event} event - Evento do formulário
 * @param {string} message - Mensagem principal
 * @param {string} subtitle - Mensagem secundária
 * @param {Object} options - Opções adicionais do modal
 * @returns {boolean} - false para prevenir submit padrão
 */
function handleDeleteConfirm(event, message, subtitle = '', options = {}) {
    event.preventDefault();
    
    const form = event.target;
    const fullMessage = subtitle ? `${message}<br><small class="text-gray-400">${subtitle}</small>` : message;
    
    const defaultOptions = {
        type: 'confirm',
        confirmText: 'Excluir',
        cancelText: 'Cancelar',
        confirmClass: 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
    };
    
    const modalOptions = { ...defaultOptions, ...options };
    
    confirmModal(fullMessage, 'Confirmar Exclusão', modalOptions).then(confirmed => {
        if (confirmed) {
            form.submit();
        }
    });
    
    return false;
}

/**
 * Manipula confirmações genéricas usando modal
 * @param {Event} event - Evento do formulário
 * @param {string} message - Mensagem principal
 * @param {string} title - Título do modal
 * @param {Object} options - Opções adicionais do modal
 * @returns {boolean} - false para prevenir submit padrão
 */
function handleFormConfirm(event, message, title = 'Confirmação', options = {}) {
    event.preventDefault();
    
    const form = event.target;
    
    const defaultOptions = {
        type: 'confirm',
        confirmText: 'Confirmar',
        cancelText: 'Cancelar',
        confirmClass: 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'
    };
    
    const modalOptions = { ...defaultOptions, ...options };
    
    confirmModal(message, title, modalOptions).then(confirmed => {
        if (confirmed) {
            form.submit();
        }
    });
    
    return false;
}

/**
 * Manipula alertas usando modal
 * @param {string} message - Mensagem do alerta
 * @param {string} title - Título do alerta
 * @param {string} type - Tipo do alerta (info, warning, error, success)
 * @param {Object} options - Opções adicionais
 */
function showAlert(message, title = 'Aviso', type = 'info', options = {}) {
    alertModal(message, title, type, options);
}

/**
 * Manipula confirmações de desvinculação
 * @param {Event} event - Evento do formulário
 * @param {string} itemName - Nome do item a ser desvinculado
 * @param {string} contextName - Nome do contexto (turma, disciplina, etc.)
 * @returns {boolean} - false para prevenir submit padrão
 */
function handleUnlinkConfirm(event, itemName, contextName) {
    const message = `Tem certeza que deseja desvincular ${itemName} ${contextName ? 'de ' + contextName : ''}?`;
    const subtitle = 'Esta ação pode ser revertida posteriormente.';
    
    return handleFormConfirm(event, `${message}<br><small class="text-gray-400">${subtitle}</small>`, 'Confirmar Desvinculação', {
        confirmText: 'Desvincular',
        confirmClass: 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500'
    });
}

/**
 * Manipula confirmações de ativação/desativação
 * @param {Event} event - Evento do formulário
 * @param {string} action - Ação a ser realizada (ativar/desativar)
 * @param {string} itemName - Nome do item
 * @returns {boolean} - false para prevenir submit padrão
 */
function handleToggleConfirm(event, action, itemName) {
    const message = `Deseja ${action} ${itemName}?`;
    const isActivating = action.toLowerCase().includes('ativar');
    
    return handleFormConfirm(event, message, `Confirmar ${action.charAt(0).toUpperCase() + action.slice(1)}`, {
        confirmText: action.charAt(0).toUpperCase() + action.slice(1),
        confirmClass: isActivating 
            ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500'
            : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
    });
}

/**
 * Manipula confirmações de criação
 * @param {Event} event - Evento do formulário
 * @param {string} message - Mensagem de confirmação
 * @param {string} itemType - Tipo do item a ser criado
 * @returns {boolean} - false para prevenir submit padrão
 */
function handleCreateConfirm(event, message, itemType = 'item') {
    return handleFormConfirm(event, message, `Criar ${itemType}`, {
        confirmText: 'Criar',
        confirmClass: 'bg-green-600 hover:bg-green-700 focus:ring-green-500'
    });
}

/**
 * Manipula confirmações de remoção de justificativas
 * @param {Event} event - Evento do formulário
 * @returns {boolean} - false para prevenir submit padrão
 */
function handleRemoveJustificationConfirm(event) {
    const message = 'Tem certeza que deseja remover a justificativa?';
    const subtitle = 'A falta voltará a ser considerada injustificada.';
    
    return handleFormConfirm(event, `${message}<br><small class="text-gray-400">${subtitle}</small>`, 'Remover Justificativa', {
        confirmText: 'Remover',
        confirmClass: 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
    });
}

/**
 * Manipula confirmações de exclusão de chamadas
 * @param {Event} event - Evento do formulário
 * @param {string} scope - Escopo da exclusão (dia, período, etc.)
 * @returns {boolean} - false para prevenir submit padrão
 */
function handleDeleteCallsConfirm(event, scope = 'chamadas') {
    const message = `Tem certeza que deseja excluir todas as ${scope}?`;
    const subtitle = 'Esta ação não pode ser desfeita.';
    
    return handleDeleteConfirm(event, message, subtitle);
}

// Disponibiliza as funções globalmente
window.handleDeleteConfirm = handleDeleteConfirm;
window.handleFormConfirm = handleFormConfirm;
window.showAlert = showAlert;
window.handleUnlinkConfirm = handleUnlinkConfirm;
window.handleToggleConfirm = handleToggleConfirm;
window.handleCreateConfirm = handleCreateConfirm;
window.handleRemoveJustificationConfirm = handleRemoveJustificationConfirm;
window.handleDeleteCallsConfirm = handleDeleteCallsConfirm;