<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
// include_once 'includes/menu.inc.php';
$_SESSION['sucesso'] = 0;
$_SESSION['admin'] = 0;

?>

<html>

<head>
</head>

<body class="container2">
    <div>
        <img src="imagens/logo_icon.png" class="logoLogin">
        <br />
        <h5><a href="index.php">Portal de Compras</a></h5>
        <br />

        <form action="bd/ldap.php" method="post">
            <fieldset class="login">
                <!-- <legend><img src="imagens/batman-icon.png" alt="[imagem]" width="100"></legend> -->
                <h5 class="light center">Login</h5>

                <div class="input-field col s12">
                    <i class="material-icons prefix">person</i>
                    <input type="text" name="login" id="login" maxlength="100" required autofocus>
                    <label for="usuario">Login / E-mail</label>
                </div>

                <div class="input-field col s12">
                    <i class="material-icons prefix">password</i>
                    <input type="password" name="senha" id="senha" maxlength="15" required>
                    <label for="nome">Senha</label>
                </div>

                <center>
                    <h8>
                        <?php
                        if (isset($_SESSION['msg'])) :
                            echo $_SESSION['msg'];
                            $_SESSION['msg'] = '';
                        endif;
                        ?>
                    </h8>
                </center>

                <div class="input-field col s6">
                    <center>
                        <input type="submit" value="Login" class="btn blue">
                        <!-- <input type="reset" value="limpar" class="btn red"> -->
                    </center>
                </div>

            </fieldset>
        </form>

        <div class="materialize-content">
            <a href="#modalCriarUsuario" class="modal-trigger align-right">Registrar Usuário</a>
            <a href="#modalEsqueciSenha" class="modal-trigger align-left">Esqueci a senha</a>

            <div id="modalCriarUsuario" class="modal">
                <div class="modal-content">
                    <h5>Registrar Usuário</h5>
                    <form action="bd/usuario/create.php" method="post">
                        <!-- Adicione os campos necessários (nome, e-mail, senha) aqui -->
                        <div class="input-field">
                            <input type="text" name="nomeUsuarioNovo" id="nomeUsuarioNovo" required>
                            <label for="nome">Nome</label>
                        </div>
                        <div class="input-field">
                            <input type="email" name="emailUsuarioNovo" id="emailUsuarioNovo" required>
                            <label for="email">E-mail</label>
                        </div>
                        <div class="input-field">
                            <input type="password" name="senhaUsuarioNovo" id="senhaUsuarioNovo" maxlength="12" required>
                            <label for="senha">Senha (máximo 12 caracteres)</label>
                        </div>

                        <div class="input-field">
                            <input type="password" name="senhaUsuarioNovo2" id="senhaUsuarioNovo2" maxlength="12" required>
                            <label for="senha">Repetir Senha</label>
                        </div>
                        <button type="submit" class="btn blue">Criar Usuário</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="#!" class="modal-close waves-effect waves-green btn-flat">Fechar</a>
                </div>
            </div>

            <div id="modalEsqueciSenha" class="modal">
                <div class="modal-content">
                    <h5>Esqueci a Senha</h5>
                    <form action="bd/usuario/esqueciSenha.php" method="post">
                        <!-- Adicione os campos necessários (nome, e-mail, senha) aqui -->
                        <div class="input-field">
                            <input type="email" name="emailUsuarioNovo" id="emailUsuarioNovo" required>
                            <label for="email">E-mail</label>
                        </div>
                        <button type="submit" class="btn blue">Enviar</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="#!" class="modal-close waves-effect waves-green btn-flat">Fechar</a>
                </div>
            </div>


        </div>

    </div>


</body>

</html>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

<script>
    $(document).ready(function() {
        $('.modal').modal();
    });
</script>