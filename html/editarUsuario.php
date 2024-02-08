<?php

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

// $matricula = filter_input(INPUT_GET, 'matricula', FILTER_SANITIZE_NUMBER_INT);
$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_SPECIAL_CHARS);

/////////////////////////////////////////////////////////////////////////

$queryAdmin = "SELECT * FROM USUARIO WHERE EMAIL_ADM like '$email'";
$querySelect = $pdoCAT->query($queryAdmin);

// $existeUsuario = 0;

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $existeUsuario = $registros['EMAIL_ADM'];
    $idUsuario = $registros['ID_ADM'];
endwhile;

// var_dump($existeUsuario);
// exit();

if (isset($existeUsuario)) {
    $queryLE = "SELECT U.*, TL.*
    FROM USUARIO U 
    LEFT JOIN PERFIL_USUARIO PU ON U.ID_ADM = PU.ID_USUARIO
    LEFT JOIN TIPO_LICITACAO TL ON TL.ID_TIPO = PU.ID_TIPO_LICITACAO 
    WHERE U.ID_ADM = $idUsuario";

    $querySelectLE = $pdoCAT->query($queryLE);

    $perfilUsuario = array();
    while ($registrosLE = $querySelectLE->fetch(PDO::FETCH_ASSOC)) :
        $nmPerfil = $registrosLE['NM_TIPO'];
        $idPerfil = $registrosLE['ID_TIPO'];
        $nmUsuario = $registrosLE['NM_ADM'];
        $registroPU = array(
            'NM_TIPO' => $nmPerfil,
            'ID_TIPO' => $idPerfil
        );

        // Adicione o novo registro ao array $registros
        $perfilUsuario[] = $registroPU;
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
    // var_dump($queryInsert);
    // exit();

    $queryInsert2 = $pdoCAT->query($queryInsert);

    while ($registros = $queryInsert2->fetch(PDO::FETCH_ASSOC)) :
        $matricula = $registros['initials'];
        $nmUsuario = $registros['displayName'];
        $mail = $registros['mail'];
        $login = $registros['sAMAccountName'];
    endwhile;

    $loginCriador = $_SESSION['login'];
    $querySelect2 = "INSERT INTO USUARIO VALUES ($matricula, '$nmUsuario', '$mail', GETDATE(), 'A', '$loginCriador', '$login', NULL, NULL)";
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

            <input type="text" id="idUsuario" name="idUsuario" value="<?php echo $idUsuario ?>" readonly style="display: none">

            <div class="input-field col s4">
                <input type="text" id="email" name="email" value="<?php echo $email ?>" readonly>
                <label>E-mail</label>
            </div>

            <div class="input-field col s4">
                <input type="text" value="<?php echo $nmUsuario ?>" readonly>
                <label>Nome</label>
            </div>

            <div class="input-field col s4">
                <select name="perfilUsuario[]" id="perfilUsuario" multiple>
                    <?php
                    $querySelect2 = "SELECT * FROM TIPO_LICITACAO WHERE DT_EXC_TIPO IS NULL ORDER BY NM_TIPO";
                    $querySelect = $pdoCAT->query($querySelect2);

                    $queryPerfilUsuario = "SELECT TL.*
                                            FROM USUARIO U 
                                            LEFT JOIN PERFIL_USUARIO PU ON U.ID_ADM = PU.ID_USUARIO
                                            LEFT JOIN TIPO_LICITACAO TL ON TL.ID_TIPO = PU.ID_TIPO_LICITACAO 
                                            WHERE U.ID_ADM = $idUsuario";
                    $queryPerfisUsuario = $pdoCAT->query($queryPerfilUsuario);

                    // Obtenha os valores selecionados do banco de dados e armazene-os em $valoresSelecionados
                    $perfisUsuario = array();
                    while ($row = $queryPerfisUsuario->fetch(PDO::FETCH_ASSOC)) {
                        $perfisUsuario[] = $row["ID_TIPO"];
                    }

                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        $valorLE = $registros["ID_TIPO"];
                        $descricaoLE = $registros["NM_TIPO"];

                        // Verifique se o valor está na lista de valores selecionados
                        $selecionadoLE = in_array($valorLE, $perfisUsuario) ? 'selected' : '';

                        echo "<option value='" . $valorLE . "' $selecionadoLE>" . $descricaoLE . "</option>";
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