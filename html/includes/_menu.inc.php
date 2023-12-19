<!-- ÍCONES -->
<!-- https://fonts.google.com/icons -->
<!-- https://ionicframework.com/docs/v3/ionicons/ -->
<!-- https://materializecss.com/icons.html -->

<!-- TABELA DE CORES -->
<!-- https://material.io/design/color/the-color-system.html#tools-for-picking-colors -->

<?php
//ARQUIVO QUE FAZ A VALIDAÇÃO SE O USUÁRIO ESTÁ LOGADO NO SISTEMA
//SE TENTAR ACESSAR ALGUMA PÁGINA DIRETAMENTE, ELE DIRECIONA PARA A PÁGINA DE LOGOUT
session_start();
include('protectAdmin.php');

?>
<input type="checkbox" id="check" checked="checked"> <!--deixa o menu fechado-->

<header class="header2">
    <label class="label2" for="check">
        <!-- <ion-icon name="menu-outline" id="sidebar_btn"></ion-icon> -->
    </label>
    <div class="left2">
        <img src="imagens/logo_icon.png" class="imageMenu" alt="">
    </div>
    <div class="left2">
        <a href="index.php">
            <h3>Portal de Compras</h3>
        </a>
    </div>
    <div>
        <?php if (isset($_SESSION['login'])) { ?>
            <label class="userLogin">Bem-Vindo, <?php echo $_SESSION['login'] ?></label>
            <a href="logout.php" class="sair_btn"><ion-icon name="exit-outline"></ion-icon></a>
        <?php } else { ?>
            <a href="login.php" class="sair_btn"><ion-icon name="exit-outline"></ion-icon></a>
        <?php } ?>
    </div>
</header>

<!--SIDEBAR começo-->
<div class="sidebar2">
    <ul>
        <li>
            <a href="consultarLicitacao.php"><i class='material-icons' title="Consultar Licitação">playlist_add_check</i><span>Consultar Licitação</span></a>

        </li>
        <?php
        if ($_SESSION['admin'] == 5) { ?>
            <hr style="border-color: #132835; margin-bottom: 15px;"> <!-- LINHA HORIZONTAL -->
            <li>
                <a href="consultarUsuario.php"><i class='material-icons' title="Administrar Usuários">manage_accounts</i><span>Usuários</span></a>
            </li>
            <li>
                <a href="cadPerfil.php"><i class='material-icons' title="Administrar Perfis">people</i><span>Adm Perfil</span></a>
            </li>
            <li>
                <a href="consultarVisita.php"><i class='material-icons' title="Consultar Visitas Agendadas">assignment</i><span>Consultar Visita Agendada</span></a>
            </li>
            <li>
                <a href="cadastrarLocal.php"><i class='material-icons' title="Administrar Local">place</i><span>Adm Local</span></a>
            </li>
            <li>
                <a href="consultarCancel.php"><i class='material-icons' title="Administrar Solicitações de Cancelamento">do_not_disturb_on</i><span>Adm Solicitações de Cancelamento</span></a>
            </li>
            <li>
                <a href="cadastrarDocOrientacoes.php"><i class='material-icons' title="Link de Documento de Orientação">chat</i><span>Link de Documento de Orientação</span></a>
                <ul class="submenu">
                    <li><a href="#" style="font-size:small;">Usuário Item 1</a></li>
                    <li><a href="#" style="font-size:small;">Usuário Item 2</a></li>
                </ul>
            </li>
        <?php } ?>
    </ul>
</div>


<!--sidebar final-->

<div class="content2"></div>