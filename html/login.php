<?php
include_once 'bd/conexao.php';
session_start();

$_SESSION['sucesso'] = 0;
$_SESSION['perfil'] = 0;

// Mensagem do sistema
$msgSistema = '';
if (isset($_SESSION['msg'])) {
    $msgSistema = $_SESSION['msg'];
    $_SESSION['msg'] = '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Portal de Compras CESAN</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">
    
    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <style>
        /* ============================================
           ESTILOS DE LOGIN - Padrão Rede de Ideias
           ============================================ */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Main Login Container */
        .login-page {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            padding: 48px;
            width: 100%;
            max-width: 420px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }

        .logoLogin {
            width: 60px;
            height: auto;
            border-radius: 12px;
            display: block;
            margin: 0 auto 24px auto;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header h2 {
            font-weight: 700;
            color: #0f172a;
            font-size: 26px;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .login-header p {
            color: #64748b;
            font-size: 14px;
        }

        .login-card .form-control {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 12px 16px;
            padding-left: 42px;
            font-size: 14px;
            color: #334155;
            transition: all 0.2s ease;
            width: 100%;
            font-family: 'Inter', sans-serif;
        }

        .login-card .form-control:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .login-card .form-control::placeholder {
            color: #94a3b8;
        }

        .input-icon {
            position: relative;
            margin-bottom: 20px;
        }

        .input-icon i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }

        .btn-login {
            background: #0f172a;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            letter-spacing: 0.3px;
            width: 100%;
            transition: all 0.2s ease;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: 'Inter', sans-serif;
        }

        .btn-login:hover {
            background-color: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .footer-text {
            text-align: center;
            margin-top: 32px;
            font-size: 12px;
            color: #94a3b8;
        }

        .login-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .login-links a {
            font-size: 13px;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .login-links a:hover {
            color: #2563eb;
        }

        /* Mensagem de erro */
        .mensagem-sistema {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        /* Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-container {
            background: #ffffff;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalAppear 0.3s ease-out;
        }

        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-header {
            padding: 24px 32px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 24px 24px 0 0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #0f172a;
        }

        .modal-close {
            background: #f1f5f9;
            border: none;
            color: #64748b;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .modal-body {
            padding: 32px;
        }

        .modal-footer {
            padding: 20px 32px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-radius: 0 0 24px 24px;
        }

        .btn-primary {
            background: #0f172a;
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background: #ffffff;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            color: #334155;
        }

        /* Form no modal */
        .modal-form-group {
            margin-bottom: 20px;
        }

        .modal-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .modal-form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .modal-form-group input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        /* Mensagem de feedback */
        .mensagem {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            display: none;
            align-items: center;
            gap: 10px;
        }

        .mensagem.active {
            display: flex;
        }

        .mensagem-sucesso {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
        }

        .mensagem-erro {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .mensagem-info {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
        }

        /* reCAPTCHA */
        .g-recaptcha {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
                border-radius: 20px;
            }

            .login-header h2 {
                font-size: 22px;
            }

            .modal-header,
            .modal-body,
            .modal-footer {
                padding-left: 20px;
                padding-right: 20px;
            }

            .modal-container {
                margin: 10px;
                border-radius: 20px;
            }

            .modal-header {
                border-radius: 20px 20px 0 0;
            }

            .modal-footer {
                border-radius: 0 0 20px 20px;
                flex-direction: column;
            }

            .modal-footer button {
                width: 100%;
            }

            .g-recaptcha {
                transform: scale(0.9);
                transform-origin: center;
            }
        }
    </style>
</head>
<body>

<div class="login-page">
    <div class="login-card">
        <div>
            <img src="imagens/logo_icon.png" class="logoLogin" alt="Logo CESAN">
        </div>

        <div class="login-header">
            <h2>Portal de Compras</h2>
            <p>Acesse o portal de licitações e compras</p>
        </div>

        <?php if (!empty($msgSistema)): ?>
        <div class="mensagem-sistema">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $msgSistema; ?>
        </div>
        <?php endif; ?>

        <form action="bd/ldap.php" method="POST">
            <div class="input-icon">
                <i class="fas fa-user"></i>
                <input type="text" name="login" id="login" class="form-control" placeholder="Usuário (nome.sobrenome)" required autofocus maxlength="100">
            </div>

            <div class="input-icon">
                <i class="fas fa-lock"></i>
                <input type="password" name="senha" id="senha" class="form-control" placeholder="Sua senha" required>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
                <a href="#" onclick="openModal(event)" style="font-size: 13px; color: #3b82f6; text-decoration: none; font-weight: 600; transition: color 0.2s;">
                    Esqueceu a senha?
                </a>
            </div>

            <button type="submit" class="btn-login">
                Entrar no Sistema
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="login-links">
            <a href="javascript:void(0)" onclick="abrirModalRegistro()">
                <i class="fas fa-user-plus"></i>
                Registrar novo usuário externo
            </a>
        </div>

        <div class="footer-text">
            © <?php echo date('Y'); ?> CESAN. Todos os direitos reservados.
        </div>
    </div>
</div>

<!-- Modal Ajuda de Acesso -->
<div id="modalEsqueceuSenha" class="modal-overlay">
    <div class="modal-container" style="max-width: 480px;">
        <div class="modal-header">
            <h3>
                <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
                Ajuda de Acesso
            </h3>
            <button type="button" class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body" style="text-align: center; padding: 40px 32px;">
            <p style="font-size: 15px; color: #64748b; line-height: 1.7; margin: 0 0 20px 0;">
                <strong style="color: #0f172a;">Usuários CESAN:</strong><br>
                Utilize o mesmo usuário (nome.sobrenome) e senha de acesso ao computador.
            </p>
            
            <div style="padding: 16px; background: #f1f5f9; border-radius: 12px; border: 1px solid #e2e8f0;">
                <p style="font-size: 14px; color: #475569; margin: 0 0 8px 0; font-weight: 500;">
                    Usuário externo?
                </p>
                <a href="javascript:void(0)" onclick="abrirModalRecuperacao()" style="color: #3b82f6; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <span style="font-size: 18px;">🔐</span>
                    Clique aqui para recuperar sua senha
                </a>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-primary" onclick="closeModal()">
                Entendi
            </button>
        </div>
    </div>
</div>

<!-- Modal Recuperação de Senha -->
<div id="modalRecuperacao" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>
                <i class="fas fa-lock" style="color: #3b82f6;"></i>
                Recuperar Senha
            </h3>
            <button type="button" class="modal-close" onclick="fecharModalRecuperacao()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="formRecuperacao" method="POST" action="bd/usuario/esqueciSenha.php">
            <div class="modal-body">
                <div style="text-align: center; margin-bottom: 24px;">
                    <div style="width: 60px; height: 60px; background: #dbeafe; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; font-size: 28px;">
                        📧
                    </div>
                    <p style="font-size: 14px; color: #64748b; margin: 0;">
                        Digite seu e-mail cadastrado e enviaremos uma nova senha temporária.
                    </p>
                </div>

                <div class="modal-form-group">
                    <label>E-mail</label>
                    <input type="email" name="emailEsqueciSenha" id="emailRecuperacao" placeholder="seu.email@exemplo.com" required>
                </div>

                <div id="mensagemRecuperacao" class="mensagem"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="fecharModalRecuperacao()">
                    Cancelar
                </button>
                <button type="submit" class="btn-primary" id="btnEnviarRecuperacao">
                    Enviar Nova Senha
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Registrar Usuário -->
<div id="modalRegistro" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>
                <i class="fas fa-user-plus" style="color: #3b82f6;"></i>
                Registrar Usuário Externo
            </h3>
            <button type="button" class="modal-close" onclick="fecharModalRegistro()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="formRegistro" method="POST" action="bd/usuario/create.php" onsubmit="return validarFormularioRegistro()">
            <div class="modal-body">
                <div style="text-align: center; margin-bottom: 24px;">
                    <div style="width: 60px; height: 60px; background: #dcfce7; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; font-size: 28px;">
                        👤
                    </div>
                    <p style="font-size: 14px; color: #64748b; margin: 0;">
                        Preencha os dados abaixo para criar sua conta de acesso.
                    </p>
                </div>

                <div class="modal-form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nomeUsuarioNovo" id="nomeUsuarioNovo" placeholder="Seu nome completo" required maxlength="100">
                </div>

                <div class="modal-form-group">
                    <label>E-mail</label>
                    <input type="email" name="emailUsuarioNovo" id="emailUsuarioNovo" placeholder="seu.email@exemplo.com" required maxlength="100">
                </div>

                <div class="modal-form-group">
                    <label>Senha</label>
                    <input type="password" name="senhaUsuarioNovo" id="senhaUsuarioNovo" placeholder="Mínimo 6 caracteres" required maxlength="12">
                </div>

                <div class="modal-form-group">
                    <label>Confirmar Senha</label>
                    <input type="password" name="senhaUsuarioNovo2" id="senhaUsuarioNovo2" placeholder="Repita a senha" required maxlength="12">
                </div>

                <div class="g-recaptcha" data-sitekey="6LfMGqspAAAAAMtu9aVSVmCeIYxq_NIOv8boj6Go"></div>

                <div id="mensagemRegistro" class="mensagem"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="fecharModalRegistro()">
                    Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    Criar Conta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // ============================================
    // VALIDAÇÕES DE INPUT
    // ============================================
    
    // Validar login (remover @cesan.com.br)
    document.getElementById('login').addEventListener('input', function() {
        const emailPattern = /@cesan\.com\.br/i;
        if (emailPattern.test(this.value)) {
            alert('Usuários da CESAN devem usar login/senha do AD sem o @cesan.com.br');
            this.value = this.value.replace('@cesan.com.br', '');
        }
    });

    // Validar email de registro (não permitir @cesan.com.br)
    document.getElementById('emailUsuarioNovo')?.addEventListener('input', function() {
        const emailPattern = /@cesan\.com\.br/i;
        if (emailPattern.test(this.value)) {
            alert('Usuários da CESAN devem usar login/senha do AD para acessar o Portal de Compras.');
        }
    });

    // Validar email de recuperação (não permitir @cesan.com.br)
    document.getElementById('emailRecuperacao')?.addEventListener('input', function() {
        const emailPattern = /@cesan\.com\.br/i;
        if (emailPattern.test(this.value)) {
            alert('Usuários da CESAN devem usar login/senha do AD para acessar o Portal de Compras.');
        }
    });

    // ============================================
    // MODAL AJUDA DE ACESSO
    // ============================================
    function openModal(e) {
        e.preventDefault();
        document.getElementById('modalEsqueceuSenha').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modalEsqueceuSenha').classList.remove('active');
        document.body.style.overflow = '';
    }

    // ============================================
    // MODAL RECUPERAÇÃO
    // ============================================
    function abrirModalRecuperacao() {
        closeModal();
        document.getElementById('modalRecuperacao').classList.add('active');
        document.getElementById('emailRecuperacao').focus();
    }

    function fecharModalRecuperacao() {
        document.getElementById('modalRecuperacao').classList.remove('active');
        document.getElementById('formRecuperacao').reset();
        document.getElementById('mensagemRecuperacao').classList.remove('active');
        document.body.style.overflow = '';
    }

    // ============================================
    // MODAL REGISTRO
    // ============================================
    function abrirModalRegistro() {
        document.getElementById('modalRegistro').classList.add('active');
        document.getElementById('nomeUsuarioNovo').focus();
        document.body.style.overflow = 'hidden';
    }

    function fecharModalRegistro() {
        document.getElementById('modalRegistro').classList.remove('active');
        document.getElementById('formRegistro').reset();
        document.getElementById('mensagemRegistro').classList.remove('active');
        document.body.style.overflow = '';
        if (typeof grecaptcha !== 'undefined') {
            grecaptcha.reset();
        }
    }

    // ============================================
    // VALIDAÇÃO DO FORMULÁRIO DE REGISTRO
    // ============================================
    function validarFormularioRegistro() {
        const senha1 = document.getElementById('senhaUsuarioNovo').value;
        const senha2 = document.getElementById('senhaUsuarioNovo2').value;
        const mensagemDiv = document.getElementById('mensagemRegistro');

        if (senha1 !== senha2) {
            mensagemDiv.className = 'mensagem mensagem-erro active';
            mensagemDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> As senhas não coincidem!';
            return false;
        }

        if (senha1.length < 6) {
            mensagemDiv.className = 'mensagem mensagem-erro active';
            mensagemDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> A senha deve ter no mínimo 6 caracteres!';
            return false;
        }

        if (typeof grecaptcha !== 'undefined') {
            const response = grecaptcha.getResponse();
            if (response.length === 0) {
                mensagemDiv.className = 'mensagem mensagem-erro active';
                mensagemDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Por favor, complete o CAPTCHA!';
                return false;
            }
        }

        return true;
    }

    // ============================================
    // SUBMIT RECUPERAÇÃO (via form tradicional)
    // ============================================
    document.getElementById('formRecuperacao')?.addEventListener('submit', function(e) {
        const email = document.getElementById('emailRecuperacao').value;
        const mensagemDiv = document.getElementById('mensagemRecuperacao');

        // Validar se é email CESAN
        const emailPattern = /@cesan\.com\.br/i;
        if (emailPattern.test(email)) {
            e.preventDefault();
            mensagemDiv.className = 'mensagem mensagem-erro active';
            mensagemDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Usuários CESAN devem usar o AD.';
            return false;
        }

        // Se passou na validação, permitir o submit normal
        return true;
    });

    // ============================================
    // FECHAR MODAIS COM CLICK FORA OU ESC
    // ============================================
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            fecharModalRecuperacao();
            fecharModalRegistro();
        }
    });
</script>

</body>
</html>