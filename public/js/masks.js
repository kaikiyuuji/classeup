/**
 * Máscaras de formatação para CPF e Telefone
 * Aplicadas tanto na entrada de dados quanto na exibição
 */

// Função para aplicar máscara de CPF
function formatCPF(value) {
    // Remove tudo que não é dígito
    value = value.replace(/\D/g, '');
    
    // Aplica a máscara XXX.XXX.XXX-XX
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    
    return value;
}

// Função para aplicar máscara de telefone
function formatPhone(value) {
    // Remove tudo que não é dígito
    value = value.replace(/\D/g, '');
    
    // Aplica a máscara (XX) XXXXX-XXXX ou (XX) XXXX-XXXX
    if (value.length <= 10) {
        // Telefone fixo: (XX) XXXX-XXXX
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    } else {
        // Celular: (XX) XXXXX-XXXX
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }
    
    return value;
}

// Função para remover máscara (manter apenas números)
function removeMask(value) {
    return value.replace(/\D/g, '');
}

// Aplicar máscaras em tempo real nos campos de input
function applyMasks() {
    // Máscara para CPF
    const cpfInputs = document.querySelectorAll('input[name="cpf"], input[id="cpf"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = formatCPF(e.target.value);
        });
        
        // Formatar valor inicial se existir
        if (input.value) {
            input.value = formatCPF(input.value);
        }
    });
    
    // Máscara para telefone
    const phoneInputs = document.querySelectorAll('input[name="telefone"], input[id="telefone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = formatPhone(e.target.value);
        });
        
        // Formatar valor inicial se existir
        if (input.value) {
            input.value = formatPhone(input.value);
        }
    });
}

// Função para formatar CPF na exibição
function formatCPFDisplay(cpf) {
    if (!cpf) return '';
    return formatCPF(cpf.toString());
}

// Função para formatar telefone na exibição
function formatPhoneDisplay(phone) {
    if (!phone) return '';
    return formatPhone(phone.toString());
}

// Aplicar máscaras quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    applyMasks();
    
    // Formatar CPFs e telefones já exibidos na página
    const cpfElements = document.querySelectorAll('[data-format="cpf"]');
    cpfElements.forEach(element => {
        element.textContent = formatCPFDisplay(element.textContent);
    });
    
    const phoneElements = document.querySelectorAll('[data-format="phone"]');
    phoneElements.forEach(element => {
        element.textContent = formatPhoneDisplay(element.textContent);
    });
});

// Exportar funções para uso global
window.formatCPF = formatCPF;
window.formatPhone = formatPhone;
window.formatCPFDisplay = formatCPFDisplay;
window.formatPhoneDisplay = formatPhoneDisplay;
window.removeMask = removeMask;