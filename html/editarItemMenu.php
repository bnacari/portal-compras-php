<?php

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

$idItemMenu = filter_input(INPUT_GET, 'idItemMenu', FILTER_SANITIZE_NUMBER_INT);

$queryAdmin = "SELECT * 
                FROM [PortalCompras].[dbo].ITEMMENU IM
                LEFT JOIN SUBMENU SM ON SM.ID_SUBMENU = IM.ID_SUBMENU
                WHERE IM.ID_ITEMMENU = $idItemMenu";

// var_dump($queryAdmin);
// exit();

$querySelect = $pdoCAT->query($queryAdmin);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $nmItemMenu = $registros['NM_ITEMMENU'];
    $linkItemMenu = $registros['LINK_ITEMMENU'];
    $idSubMenu = $registros['ID_SUBMENU'];
    $nmSubMenu = $registros['NM_SUBMENU'];

endwhile;

/////////////////////////////////////////////////////////////////////////

?>

<!-- FORMULÁRIOS DE CADASTRO -->
<div class="row container">
    <form action="bd/itemmenu/update.php" method="post" class="col s12 formulario" id="formFiltrar">
        <fieldset class="formulario col s12">
            <h5 class="light" style="color: #404040">Editar Itemmenu</h5>
        </fieldset>

        <input style="display:none" type="text" id="idItemMenu" name="idItemMenu" value="<?php echo $idItemMenu ?>">
        
        <p>&nbsp;</p>
        <fieldset class="formulario">
            <!-- <h6><strong>Local a Visitar</strong></h6> -->
            <div class="input-field col s4">
                <input type="text" id="nmItemMenu" name="nmItemMenu" value="<?php echo $nmItemMenu ?>" autofocus>
                <label>Nome</label>
            </div>

            <div class="input-field col s4">
                <input type="text" id="linkItemMenu" name="linkItemMenu" value="<?php echo $linkItemMenu ?>">
                <label>Link</label>
            </div>

            <div class="input-field col s4">
                <select name="idSubMenu" id="idSubMenu" required>
                    <option value='' disabled>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM portalcompras.dbo.[submenu] WHERE DT_EXC_SUBMENU IS NULL ORDER BY NM_SUBMENU";
                    $querySelect = $pdoCAT->query($querySelect2);

                    echo "<option value='" . $idSubMenu . "' selected>" . $nmSubMenu . "</option>";
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        // Verifica se o ID é diferente do ID já selecionado
                        if ($registros["ID_SUBMENU"] != $idSubMenu) {
                            echo "<option value='" . $registros["ID_SUBMENU"] . "'>" . $registros["NM_SUBMENU"] . "</option>";
                        }
                    endwhile;
                    ?>
                </select>
                <label>SubMenu</label>
            </div>
        </fieldset>

        <p>&nbsp;</p>

        <div class="input-field col s2">
            <button type="submit" class="btn blue">Salvar</button>
        </div>
    </form>
</div>