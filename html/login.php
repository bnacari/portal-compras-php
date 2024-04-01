<?php
include_once 'bd/conexao.php';
session_start();

$_SESSION['sucesso'] = 0;
$_SESSION['perfil'] = 0;

?>

<style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        background-color: #f1f1f1;
    }

    .logoLogin {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 55px;
        height: 70px;
    }

    .modal {
        left: 50% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;
    }

    .modal:focus {
        outline: none;
    }

    .modal h1,
    .modal h2,
    .modal h3,
    .modal h4 {
        margin-top: 0;
    }

    .modal .modal-content {
        padding: 24px;
    }

    .modal .modal-close {
        cursor: pointer;
    }

    .modal .modal-footer {
        border-radius: 0 0 2px 2px;
        background-color: #fafafa;
        padding: 4px 6px;
        height: 56px;
        width: 100%;
        text-align: right;
    }

    .modal .modal-footer .btn,
    .modal .modal-footer .btn-large,
    .modal .modal-footer .btn-small,
    .modal .modal-footer .btn-flat {
        margin: 6px 0;
    }

    .modal-overlay {
        position: fixed;
        z-index: 999;
        top: -25%;
        left: 0;
        bottom: 0;
        right: 0;
        height: 125%;
        width: 100%;
        background: #000;
        display: none;
        will-change: opacity;
    }

    .modal.modal-fixed-footer {
        padding: 0;
        height: 70%;
    }

    .modal.modal-fixed-footer .modal-content {
        position: absolute;
        height: calc(100% - 56px);
        max-height: 100%;
        width: 100%;
        overflow-y: auto;
    }

    .modal.modal-fixed-footer .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        position: absolute;
        bottom: 0;
    }

    .modal.bottom-sheet {
        top: auto;
        bottom: -100%;
        margin: 0;
        width: 100%;
        max-height: 45%;
        border-radius: 0;
        will-change: bottom, opacity;
    }

    #modal.modal {
        width: 90% !important;
        /* Ajuste o valor conforme necessário, pode ser em pixels, porcentagem, etc. */
        max-height: 90% !important;
        /* Ajuste o valor conforme necessário, pode ser em pixels, porcentagem, etc. */
    }

    /* Se você quiser centralizar o modal na tela */
    #modal.modal {
        left: 50% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;
    }

    .mx-auto {
        max-width: 400px;
    }
</style>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">

<div class="container">
    <br>
    <article class="card-body mx-auto">
        <div style="display: flex; align-items: center; justify-content: center;">
            <img src="imagens/logo_icon.png" class="logoLogin">
        </div>
        <h4 class="card-title mt-3 text-center"><a href="index.php">
                <h5>Portal de Compras</h5>
            </a>
        </h4>
        <p class="text-center"></p>

        <form action="bd/ldap.php" method="post">
            <div class="form-group input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                </div>
                <input class="form-control" placeholder="Usuário" type="text" name="login" id="login" maxlength="100" required autofocus>
            </div>

            <div class="form-group input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                </div>
                <input class="form-control" placeholder="Senha" type="password" name="senha" id="senha" required>
            </div>

            <?php
            if (isset($_SESSION['msg'])) : ?>
                <center>
                    <p class="text-center">

                        <?php
                        echo $_SESSION['msg'];
                        $_SESSION['msg'] = '';
                        ?>
                    </p>
                </center>
            <?php endif; ?>


            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block"> Acessar </button>
            </div>
        </form>
    </article>

    <div class="materialize-content">
        <div class="container" style="display: flex; justify-content: center;">
            <div>
                <p><a href="#modalCriarUsuario" class="modal-trigger align-center" onclick="openModalCriaUsuario()">Registrar Usuário</a></p>
                <p><a href="#modalEsqueciSenha" class="modal-trigger align-center" onclick="openModalEsqueciSenha()">Esqueci a senha</a></p>
            </div>
        </div>


        <div id="modalCriarUsuario" class="modal">
            <div class="modal-content">
                <h5>Registrar Usuário</h5>
                <form action="bd/usuario/create.php" method="post" onsubmit="return validarFormulario()">
                    <!-- Adicione os campos necessários (nome, e-mail, senha) aqui -->
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-user"></i> </span>
                        </div>
                        <input class="form-control" placeholder="Nome" type="text" name="nomeUsuarioNovo" id="nomeUsuarioNovo" maxlength="100" required>
                    </div>
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-envelope"></i></span>
                        </div>
                        <input class="form-control" placeholder="E-mail" type="email" name="emailUsuarioNovo" id="emailUsuarioNovo" maxlength="100" required>
                    </div>
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                        </div>
                        <input class="form-control" placeholder="Senha (máximo 12 caracteres)" type="password" name="senhaUsuarioNovo" id="senhaUsuarioNovo" required>
                    </div>
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                        </div>
                        <input class="form-control" placeholder="Repetir Senha" type="password" name="senhaUsuarioNovo2" id="senhaUsuarioNovo2" required>
                    </div>

                    <div class="g-recaptcha" name="captcha" id="captcha" data-sitekey="6Lf2DqspAAAAAMjYJ6qErbW1qSPvTnHDvxxYvnib"></div>

                    <p>&nbsp;</p>

                    <button type="submit" class="btn btn-primary btn-block" onclick="validaCaptcha()"> Criar Usuário </button>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Fechar</button>
                    </div>
                    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
                    <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>

                </form>
            </div>
        </div>

        <div id="modalEsqueciSenha" class="modal">
            <div class="modal-content">
                <h5>Esqueci a Senha</h5>
                <form action="bd/usuario/esqueciSenha.php" method="post">
                    <!-- Adicione os campos necessários (nome, e-mail, senha) aqui -->
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
                        </div>
                        <input class="form-control" placeholder="E-mail" type="email" name="emailEsqueciSenha" id="emailEsqueciSenha" maxlength="100" autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block"> Enviar </button>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Fechar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>

</html>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

<script>
    var onRecaptchaLoad = function() {
        // O script do reCAPTCHA foi carregado, agora você pode chamar sua função
        validaCaptcha();
    };

    function validaCaptcha() {
        var response = grecaptcha.getResponse();

        alert('2');

        if (response.length === 0) {
            alert("Favor clicar no CAPTCHA para validar seu formulário.");
            return false; // Impede a submissão do formulário
        }

        // Se o reCAPTCHA foi preenchido, continue com a submissão do formulário
        return true;
    }

    $(document).ready(function() {
        $('.modal').modal();
    });

    function closeModal() {
        $('.modal-overlay').hide(); // Fechar o modal-overlay
        $('.modal').hide();
    }

    function openModalCriaUsuario() {
        $('#modalCriarUsuario').show();
        $('.modal-overlay').show(); // Fechar o modal-overlay
    }

    function openModalEsqueciSenha() {
        $('#modalEsqueciSenha').show();
        $('.modal-overlay').show(); // Fechar o modal-overlay
    }

    function validarFormulario() {
        var senhaUsuarioNovo = $('#senhaUsuarioNovo').val();
        var senhaUsuarioNovo2 = $('#senhaUsuarioNovo2').val();

        if (senhaUsuarioNovo !== senhaUsuarioNovo2) {
            alert('Senhas diferentes!');
            return false; // Evita o envio do formulário se a validação falhar
        }

        return true;
    }

    function validarRegistrarUsuario() {
        if (!validaCaptcha()) {
            return false;
        }
        var emailUsuarioNovo = document.getElementById('emailUsuarioNovo').value;

        var emailPattern = /@cesan\.com\.br/i; // Expressão regular para procurar '@cesan.com.br'

        if (emailPattern.test(emailUsuarioNovo)) {
            // Se o email contiver '@cesan.com.br'
            alert('Usuários da Cesan devem usar login/senha do AD para acessar o Portal de Compras.');
        }
    }
    document.getElementById('emailUsuarioNovo').addEventListener('input', validarRegistrarUsuario);

    function validarEsqueciSenha() {
        var emailEsqueciSenha = document.getElementById('emailEsqueciSenha').value;

        var emailPattern = /@cesan\.com\.br/i; // Expressão regular para procurar '@cesan.com.br'

        if (emailPattern.test(emailEsqueciSenha)) {
            // Se o email contiver '@cesan.com.br'
            alert('Usuários da Cesan devem usar login/senha do AD para acessar o Portal de Compras.');
        }
    }
    document.getElementById('emailEsqueciSenha').addEventListener('input', validarEsqueciSenha);

    function validarLogin() {
        var loginInput = document.getElementById('login'); // Referência ao elemento input
        var login = loginInput.value;

        var emailPattern = /@cesan\.com\.br/i; // Expressão regular para procurar '@cesan.com.br'

        if (emailPattern.test(login)) {
            // Se o email contiver '@cesan.com.br'
            alert('Usuários da Cesan devem usar login/senha do AD sem o @cesan.com.br');

            // Remover '@cesan.com.br' do email
            var novoLogin = login.replace('@cesan.com.br', '');

            // Atribuir o novo valor do login ao elemento input
            loginInput.value = novoLogin;
        }
    }
    document.getElementById('login').addEventListener('input', validarLogin);
</script>