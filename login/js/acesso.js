'use strict';
const alternarSenha = document.getElementById('alternar-senha');
const campoSenha = document.getElementById('senha_operador');
if (alternarSenha && campoSenha) {
    alternarSenha.hidden = false;
    campoSenha.parentElement.classList.add('com-alternador');
    alternarSenha.addEventListener('click', () => {
        const mostrar = campoSenha.type === 'password';
        campoSenha.type = mostrar ? 'text' : 'password';
        alternarSenha.textContent = mostrar ? 'Ocultar' : 'Mostrar';
        alternarSenha.setAttribute('aria-label', mostrar ? 'Ocultar senha' : 'Mostrar senha');
        alternarSenha.setAttribute('aria-pressed', String(mostrar));
    });
}
