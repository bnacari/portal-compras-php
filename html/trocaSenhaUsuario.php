<?php

// session_start();
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protect.php');

?>

<style>
    /* ============================================
       CSS Variables
       ============================================ */
    :root {
        --primary-50: #eff6ff;
        --primary-100: #dbeafe;
        --primary-500: #3b82f6;
        --primary-600: #2563eb;
        --primary-700: #1d4ed8;
        --dark-50: #f8fafc;
        --dark-100: #f1f5f9;
        --dark-200: #e2e8f0;
        --dark-300: #cbd5e1;
        --dark-400: #94a3b8;
        --dark-500: #64748b;
        --dark-600: #475569;
        --dark-700: #334155;
        --dark-800: #1e293b;
        --dark-900: #0f172a;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --transition-fast: 0.15s ease;
    }

    /* ============================================
       Page Container
       ============================================ */
    .page-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 40px 24px;
    }

    /* ============================================
       Page Header
       ============================================ */
    .page-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: var(--radius-xl);
        padding: 32px 40px;
        margin-bottom: 24px;
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
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header-content {
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        z-index: 1;
    }

    .page-header-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 28px;
    }

    .page-header h1 {
        color: #ffffff;
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 6px 0;
    }

    .page-header p {
        color: #94a3b8;
        font-size: 14px;
        margin: 0;
    }

    /* ============================================
       Form Card
       ============================================ */
    .form-card {
        background: #ffffff;
        border: 1px solid var(--dark-200);
        border-radius: var(--radius-xl);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .form-card-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-card-header ion-icon {
        font-size: 20px;
        color: #ffffff;
    }

    .form-card-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #ffffff;
    }

    .form-card-body {
        padding: 32px;
    }

    /* ============================================
       Form Styles
       ============================================ */
    .form-group {
        margin-bottom: 24px;
    }

    .form-group:last-of-type {
        margin-bottom: 32px;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--dark-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 10px;
    }

    .form-label ion-icon {
        font-size: 16px;
        color: var(--dark-400);
    }

    .form-label .required {
        color: #ef4444;
    }

    .input-wrapper {
        position: relative;
    }

    .form-control {
        width: 100%;
        height: 50px;
        padding: 0 48px 0 16px;
        border: 1px solid var(--dark-300);
        border-radius: var(--radius-lg);
        font-size: 15px;
        color: var(--dark-800);
        background: #ffffff;
        transition: all var(--transition-fast);
        box-sizing: border-box;
    }

    .form-control:hover {
        border-color: var(--dark-400);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px var(--primary-100);
    }

    .form-control::placeholder {
        color: var(--dark-400);
    }

    /* Toggle Password Visibility */
    .toggle-password {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--dark-400);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color var(--transition-fast);
    }

    .toggle-password:hover {
        color: var(--dark-600);
    }

    .toggle-password ion-icon {
        font-size: 20px;
    }

    /* Helper Text */
    .form-helper {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: 12px;
        color: var(--dark-500);
    }

    .form-helper ion-icon {
        font-size: 14px;
    }

    /* ============================================
       Button
       ============================================ */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        height: 50px;
        padding: 0 28px;
        font-size: 15px;
        font-weight: 600;
        border-radius: var(--radius-lg);
        border: none;
        cursor: pointer;
        transition: all var(--transition-fast);
        text-decoration: none;
    }

    .btn ion-icon {
        font-size: 20px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    /* ============================================
       Responsive
       ============================================ */
    @media (max-width: 768px) {
        .page-container {
            padding: 24px 16px;
        }

        .page-header {
            padding: 24px;
        }

        .page-header-content {
            flex-direction: column;
            text-align: center;
        }

        .page-header h1 {
            font-size: 20px;
        }

        .form-card-body {
            padding: 24px 20px;
        }
    }
</style>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <ion-icon name="key-outline"></ion-icon>
            </div>
            <div>
                <h1>Trocar Senha</h1>
                <p>Atualize sua senha de acesso ao sistema</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <div class="form-card-header">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <h3>Nova Senha</h3>
        </div>
        <div class="form-card-body">
            <form action="bd/usuario/trocaSenha.php" method="post" id="formTrocaSenha">
                
                <!-- Senha Atual -->
                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="lock-open-outline"></ion-icon>
                        Senha Atual <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="password" id="senhaAtual" name="senhaAtual" class="form-control" 
                            placeholder="Digite sua senha atual" required>
                        <button type="button" class="toggle-password" data-target="senhaAtual">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                </div>

                <!-- Nova Senha -->
                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="key-outline"></ion-icon>
                        Nova Senha <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="password" name="senhaNova" id="senhaNova" class="form-control" 
                            maxlength="12" placeholder="Digite a nova senha" required>
                        <button type="button" class="toggle-password" data-target="senhaNova">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                    <div class="form-helper">
                        <ion-icon name="information-circle-outline"></ion-icon>
                        Máximo de 12 caracteres
                    </div>
                </div>

                <!-- Repetir Senha -->
                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                        Confirmar Nova Senha <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="password" name="senhaNova2" id="senhaNova2" class="form-control" 
                            maxlength="12" placeholder="Repita a nova senha" required>
                        <button type="button" class="toggle-password" data-target="senhaNova2">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">
                    <ion-icon name="shield-checkmark-outline"></ion-icon>
                    Alterar Senha
                </button>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('ion-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('name', 'eye-off-outline');
            } else {
                input.type = 'password';
                icon.setAttribute('name', 'eye-outline');
            }
        });
    });

    // Validate passwords match
    document.getElementById('formTrocaSenha').addEventListener('submit', function(e) {
        var senhaNova = document.getElementById('senhaNova').value;
        var senhaNova2 = document.getElementById('senhaNova2').value;
        
        if (senhaNova !== senhaNova2) {
            e.preventDefault();
            alert('As senhas não coincidem. Por favor, verifique.');
            document.getElementById('senhaNova2').focus();
        }
    });
});
</script>