<?php
/**
 * ============================================
 * TROCA DE SENHA DO USUÁRIO
 * ============================================
 * 
 * Tela para o usuário alterar sua senha de acesso
 * Disponível apenas para usuários externos (com e-mail)
 * 
 * @author Portal de Compras CESAN
 * @version 2.0 - Layout refatorado
 */

// ============================================
// INCLUDES E INICIALIZAÇÃO
// ============================================
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

// Proteção de acesso
include('protect.php');
?>

<!-- ============================================
     CSS - Estilos da Página
     ============================================ -->
<style>
    /* ============================================
       Container Principal
       ============================================ */
    .page-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 24px;
    }

    /* ============================================
       Header da Página - Gradiente Padrão
       ============================================ */
    .page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 24px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header-content {
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        z-index: 1;
    }

    .page-header-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .page-header-icon ion-icon {
        font-size: 28px;
        color: #93c5fd;
    }

    .page-header h1 {
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 4px 0;
        color: white;
    }

    .page-header-subtitle {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.7);
        margin: 0;
    }

    /* ============================================
       Card Principal
       ============================================ */
    .section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .section-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
        padding: 18px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-header ion-icon {
        font-size: 20px;
        color: #93c5fd;
    }

    .section-header h2 {
        color: #ffffff;
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }

    .section-body {
        padding: 28px;
    }

    /* ============================================
       Formulário
       ============================================ */
    .form-group {
        margin-bottom: 24px;
    }

    .form-group:last-of-type {
        margin-bottom: 0;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .form-group label ion-icon {
        font-size: 18px;
        color: #2d5a87;
    }

    .form-group label .required {
        color: #ef4444;
        font-weight: 700;
    }

    /* Input Wrapper com botão de toggle */
    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .form-control {
        width: 100%;
        height: 50px;
        padding: 0 50px 0 16px;
        font-size: 15px;
        font-family: inherit;
        color: #1e293b;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #2d5a87;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(45, 90, 135, 0.1);
    }

    .form-control::placeholder {
        color: #94a3b8;
    }

    /* Botão Toggle Senha */
    .toggle-password {
        position: absolute;
        right: 4px;
        top: 50%;
        transform: translateY(-50%);
        width: 42px;
        height: 42px;
        background: transparent;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        transition: all 0.2s ease;
    }

    .toggle-password:hover {
        color: #2d5a87;
        background: rgba(45, 90, 135, 0.08);
    }

    .toggle-password ion-icon {
        font-size: 22px;
    }

    /* ============================================
       Indicador de Força da Senha
       ============================================ */
    .password-strength {
        margin-top: 12px;
    }

    .strength-bar-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .strength-bar {
        flex: 1;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }

    .strength-fill {
        height: 100%;
        width: 0%;
        border-radius: 3px;
        transition: all 0.3s ease;
    }

    .strength-fill.weak {
        width: 33%;
        background: linear-gradient(90deg, #ef4444, #f87171);
    }

    .strength-fill.medium {
        width: 66%;
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }

    .strength-fill.strong {
        width: 100%;
        background: linear-gradient(90deg, #10b981, #34d399);
    }

    .strength-text {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        min-width: 60px;
        text-align: right;
    }

    .strength-text.weak { color: #ef4444; }
    .strength-text.medium { color: #f59e0b; }
    .strength-text.strong { color: #10b981; }

    /* ============================================
       Indicador de Senhas Iguais
       ============================================ */
    .password-match {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        font-size: 13px;
        font-weight: 500;
        opacity: 0;
        transform: translateY(-5px);
        transition: all 0.2s ease;
    }

    .password-match.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .password-match.match {
        color: #10b981;
    }

    .password-match.no-match {
        color: #ef4444;
    }

    .password-match ion-icon {
        font-size: 18px;
    }

    /* ============================================
       Helper Text
       ============================================ */
    .form-helper {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: 12px;
        color: #64748b;
    }

    .form-helper ion-icon {
        font-size: 14px;
    }

    /* ============================================
       Dicas de Segurança
       ============================================ */
    .security-tips {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 18px 20px;
        margin-top: 24px;
    }

    .security-tips-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #1e40af;
        margin-bottom: 12px;
    }

    .security-tips-title ion-icon {
        font-size: 18px;
    }

    .security-tips ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .security-tips li {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #1e40af;
        padding: 6px 0;
    }

    .security-tips li ion-icon {
        font-size: 14px;
        color: #3b82f6;
    }

    /* ============================================
       Botões
       ============================================ */
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #e2e8f0;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 50px;
        padding: 0 28px;
        font-size: 15px;
        font-weight: 600;
        font-family: inherit;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn ion-icon {
        font-size: 20px;
    }

    .btn-primary {
        flex: 1;
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30, 58, 95, 0.3);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 2px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        border-color: #cbd5e1;
    }

    /* ============================================
       Responsivo
       ============================================ */
    @media (max-width: 640px) {
        .page-container {
            padding: 16px;
        }

        .page-header {
            padding: 24px 20px;
        }

        .page-header-content {
            flex-direction: column;
            text-align: center;
        }

        .page-header h1 {
            font-size: 20px;
        }

        .section-body {
            padding: 24px 20px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .btn-secondary {
            order: 2;
        }
    }

    @media (max-width: 480px) {
        .page-container {
            padding: 12px;
        }

        .page-header {
            padding: 20px 16px;
            border-radius: 12px;
        }

        .page-header-icon {
            width: 48px;
            height: 48px;
        }

        .page-header-icon ion-icon {
            font-size: 24px;
        }

        .page-header h1 {
            font-size: 18px;
        }

        .section-card {
            border-radius: 12px;
        }

        .section-header {
            padding: 14px 16px;
        }

        .section-body {
            padding: 20px 16px;
        }

        .form-control {
            height: 48px;
            font-size: 14px;
        }

        .security-tips {
            padding: 14px 16px;
        }
    }
</style>

<!-- ============================================
     CONTEÚDO DA PÁGINA
     ============================================ -->
<div class="page-container">

    <!-- ============================================
         Header da Página
         ============================================ -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <ion-icon name="key-outline"></ion-icon>
            </div>
            <div>
                <h1>Trocar Senha</h1>
                <p class="page-header-subtitle">Atualize sua senha de acesso ao sistema</p>
            </div>
        </div>
    </div>

    <!-- ============================================
         Card do Formulário
         ============================================ -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="shield-checkmark-outline"></ion-icon>
            <h2>Alterar Senha de Acesso</h2>
        </div>

        <div class="section-body">
            <form action="bd/usuario/trocaSenha.php" method="post" id="formTrocaSenha">

                <!-- Senha Atual -->
                <div class="form-group">
                    <label>
                        <ion-icon name="lock-open-outline"></ion-icon>
                        Senha Atual <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="password" 
                               id="senhaAtual" 
                               name="senhaAtual" 
                               class="form-control" 
                               placeholder="Digite sua senha atual" 
                               required>
                        <button type="button" class="toggle-password" onclick="togglePassword('senhaAtual', this)">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                </div>

                <!-- Nova Senha -->
                <div class="form-group">
                    <label>
                        <ion-icon name="key-outline"></ion-icon>
                        Nova Senha <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="password" 
                               id="senhaNova" 
                               name="senhaNova" 
                               class="form-control" 
                               maxlength="12" 
                               placeholder="Digite a nova senha" 
                               required
                               oninput="checkStrength(this.value); checkMatch();">
                        <button type="button" class="toggle-password" onclick="togglePassword('senhaNova', this)">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                    
                    <!-- Indicador de Força -->
                    <div class="password-strength">
                        <div class="strength-bar-container">
                            <div class="strength-bar">
                                <div id="strengthFill" class="strength-fill"></div>
                            </div>
                            <span id="strengthText" class="strength-text">—</span>
                        </div>
                    </div>
                    
                    <div class="form-helper">
                        <ion-icon name="information-circle-outline"></ion-icon>
                        Máximo de 12 caracteres
                    </div>
                </div>

                <!-- Confirmar Nova Senha -->
                <div class="form-group">
                    <label>
                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                        Confirmar Nova Senha <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="password" 
                               id="senhaNova2" 
                               name="senhaNova2" 
                               class="form-control" 
                               maxlength="12" 
                               placeholder="Confirme a nova senha" 
                               required
                               oninput="checkMatch()">
                        <button type="button" class="toggle-password" onclick="togglePassword('senhaNova2', this)">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                    
                    <!-- Indicador de Match -->
                    <div id="passwordMatch" class="password-match">
                        <ion-icon name="checkmark-circle"></ion-icon>
                        <span>As senhas coincidem</span>
                    </div>
                </div>

                <!-- Dicas de Segurança -->
                <div class="security-tips">
                    <div class="security-tips-title">
                        <ion-icon name="bulb-outline"></ion-icon>
                        Dicas para uma senha segura
                    </div>
                    <ul>
                        <li>
                            <ion-icon name="checkmark-outline"></ion-icon>
                            Combine letras maiúsculas e minúsculas
                        </li>
                        <li>
                            <ion-icon name="checkmark-outline"></ion-icon>
                            Inclua números e caracteres especiais
                        </li>
                        <li>
                            <ion-icon name="checkmark-outline"></ion-icon>
                            Evite informações pessoais óbvias
                        </li>
                        <li>
                            <ion-icon name="checkmark-outline"></ion-icon>
                            Não reutilize senhas de outros serviços
                        </li>
                    </ul>
                </div>

                <!-- Botões -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="history.back()">
                        <ion-icon name="arrow-back-outline"></ion-icon>
                        Voltar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <ion-icon name="shield-checkmark-outline"></ion-icon>
                        Atualizar Senha
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<!-- ============================================
     JavaScript
     ============================================ -->
<script>
/**
 * Toggle visibilidade da senha
 */
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('ion-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('name', 'eye-off-outline');
    } else {
        input.type = 'password';
        icon.setAttribute('name', 'eye-outline');
    }
}

/**
 * Verificar força da senha
 */
function checkStrength(password) {
    const fill = document.getElementById('strengthFill');
    const text = document.getElementById('strengthText');
    
    if (password.length === 0) {
        fill.className = 'strength-fill';
        text.textContent = '—';
        text.className = 'strength-text';
        return;
    }
    
    let strength = 0;
    
    // Comprimento
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    
    // Caracteres
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    if (strength <= 2) {
        fill.className = 'strength-fill weak';
        text.textContent = 'Fraca';
        text.className = 'strength-text weak';
    } else if (strength <= 4) {
        fill.className = 'strength-fill medium';
        text.textContent = 'Média';
        text.className = 'strength-text medium';
    } else {
        fill.className = 'strength-fill strong';
        text.textContent = 'Forte';
        text.className = 'strength-text strong';
    }
}

/**
 * Verificar se as senhas coincidem
 */
function checkMatch() {
    const senha1 = document.getElementById('senhaNova').value;
    const senha2 = document.getElementById('senhaNova2').value;
    const indicator = document.getElementById('passwordMatch');
    
    if (senha2.length === 0) {
        indicator.classList.remove('visible', 'match', 'no-match');
        return;
    }
    
    indicator.classList.add('visible');
    
    if (senha1 === senha2) {
        indicator.classList.remove('no-match');
        indicator.classList.add('match');
        indicator.innerHTML = '<ion-icon name="checkmark-circle"></ion-icon><span>As senhas coincidem</span>';
    } else {
        indicator.classList.remove('match');
        indicator.classList.add('no-match');
        indicator.innerHTML = '<ion-icon name="close-circle"></ion-icon><span>As senhas não coincidem</span>';
    }
}

/**
 * Validação do formulário antes de enviar
 */
document.getElementById('formTrocaSenha').addEventListener('submit', function(e) {
    const senhaAtual = document.getElementById('senhaAtual').value;
    const senhaNova = document.getElementById('senhaNova').value;
    const senhaNova2 = document.getElementById('senhaNova2').value;
    
    if (!senhaAtual || !senhaNova || !senhaNova2) {
        e.preventDefault();
        alert('Por favor, preencha todos os campos.');
        return false;
    }
    
    if (senhaNova !== senhaNova2) {
        e.preventDefault();
        alert('As senhas não coincidem.');
        document.getElementById('senhaNova2').focus();
        return false;
    }
    
    if (senhaNova.length < 4) {
        e.preventDefault();
        alert('A nova senha deve ter pelo menos 4 caracteres.');
        document.getElementById('senhaNova').focus();
        return false;
    }
    
    return true;
});
</script>