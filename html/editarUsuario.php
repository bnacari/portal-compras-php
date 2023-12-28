<?php

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

if ($_SESSION['admin'] == 5) {
    // Não faça nada ou redirecione para onde for necessário se essas condições forem atendidas.
} else {
    header('Location: index.php');
}

// $matricula = filter_input(INPUT_GET, 'matricula', FILTER_SANITIZE_NUMBER_INT);
$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_SPECIAL_CHARS);

/////////////////////////////////////////////////////////////////////////

$queryAdmin = "SELECT EMAIL_ADM FROM ADMINISTRADOR WHERE EMAIL_ADM like '$email'";
$querySelect = $pdoCAT->query($queryAdmin);

// $existeUsuario = 0;

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $existeUsuario = $registros['EMAIL_ADM'];
endwhile;

// var_dump($existeUsuario);
// exit();

if (isset($existeUsuario)) {
    $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].ADMINISTRADOR A 
                        LEFT JOIN PERFIL P ON P.ID_PERFIL = A.ID_PERFIL
                        WHERE EMAIL_ADM = '$email'";
    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
        $idPerfilUsuario = $registros['ID_PERFIL'];
        $nmUsuario = $registros['NM_ADM'];
        $nmPerfilUsuario = $registros['NM_PERFIL'];
    endwhile;

} else {
    $queryInsert = "SELECT [ID]
                    ,[sAMAccountName]
                    ,[initials]
                    ,[department]
                    ,[physicalDeliveryOfficeName]
                    ,[displayName]
                    ,[telephoneNumber]
                    ,[mobile]
                    ,[mail]
                    ,[accountExpires]
                    ,[IsEnabled]
                    ,[objectCategory]
                FROM [ADCache].[dbo].[Users]
                where mail like '$email'";
    var_dump($queryInsert);
    // exit();

    $queryInsert2 = $pdoCAT->query($queryInsert);

    while ($registros = $queryInsert2->fetch(PDO::FETCH_ASSOC)) :
        $matricula = $registros['initials'];
        $nmUsuario = $registros['displayName'];
        $mail = $registros['mail'];
        $login = $registros['sAMAccountName'];
    endwhile;

    $loginCriador = $_SESSION['login'];
    $querySelect2 = "INSERT INTO ADMINISTRADOR VALUES ($matricula, '$nmUsuario', '$mail', GETDATE(), 'A', '$loginCriador', '$login', NULL, NULL)";
    // var_dump($querySelect2);
    // exit();
    $querySelect = $pdoCAT->query($querySelect2);

    // REGISTRO DE LOG
    $login = $_SESSION['login'];
    $tela = 'Usuário';
    $acao = 'Perfil CRIADO para ' . $nmUsuario;
    $idEvento = $matricula;
    $queryLOG = $pdoCAT->query("INSERT INTO auditoria VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");
}

/////////////////////////////////////////////////////////////////////////

?>

<!-- FORMULÁRIOS DE CADASTRO -->
<div class="row container">
    <form action="bd/usuario/update.php" method="post" class="col s12 formulario" id="formFiltrar">
        <fieldset class="formulario col s12" style="padding:15px; border-color:#eee; border-radius:10px">
            <h5 class="light center">Editar Usuário</h5>
        </fieldset>

        <div id="idUsuario" data-id="<?php echo $idUsuario; ?>"></div>

        <p>&nbsp;</p>
        <fieldset class="formulario" style="padding:15px; border-color:#eee; border-radius:10px">
            <!-- <h6><strong>Local a Visitar</strong></h6> -->

            <div class="input-field col s4">
                <input type="text" id="email" name="email" value="<?php echo $email ?>" readonly>
                <label>E-mail</label>
            </div>

            <div class="input-field col s4">
                <input type="text" value="<?php echo $nmUsuario ?>" readonly>
                <label>Nome</label>
            </div>

            <div class="input-field col s4">
                <select name="perfilUsuario" id="perfilUsuario" required>
                    <option value='' disabled>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM portalcompras.dbo.[PERFIL] WHERE DT_EXC_PERFIL IS NULL";
                    $querySelect = $pdoCAT->query($querySelect2);

                    echo "<option value='" . $idPerfilUsuario . "' selected>" . $nmPerfilUsuario . "</option>";
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        // Verifica se o ID é diferente do ID já selecionado
                        if ($registros["ID_PERFIL"] != $idPerfilUsuario) {
                            echo "<option value='" . $registros["ID_PERFIL"] . "'>" . $registros["NM_PERFIL"] . "</option>";
                        }
                    endwhile;
                    ?>
                </select>
            </div>
        </fieldset>

        <p>&nbsp;</p>

        <div class="input-field col s2">
            <button type="submit" class="btn blue">Salvar</button>
        </div>
    </form>
</div>