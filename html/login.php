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
        <br/>
        <h5><a href="index.php">Portal de Compras</a></h5>
        <br/>

        <form action="bd/ldap.php" method="post">
            <fieldset class="login">
                <!-- <legend><img src="imagens/batman-icon.png" alt="[imagem]" width="100"></legend> -->
                <h5 class="light center">Login</h5> 

                <div class="input-field col s12">
                    <i class="material-icons prefix">person</i>
                    <input type="text" name="login" id="login" maxlength="100" required autofocus>
                    <label for="usuario">Login</label>
                </div>
                
                <div class="input-field col s12">
                    <i class="material-icons prefix">password</i>
                    <input type="password" name="senha" id="senha" maxlength="15" required>
                    <label for="nome">Senha</label>
                </div>
                
                <center>
                    <h8>
                        <?php 
                            if(isset($_SESSION['msg'])):
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
    </div>


</body>
</html>
 

