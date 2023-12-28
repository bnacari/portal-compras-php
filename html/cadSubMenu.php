<?php

// session_start();
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

?>

<div class="row container">
    <fieldset class="formulario">
        <form action="bd/submenu/create.php" method="post" class="col s12 formulario" id="formFiltrar">

            <h5 class="light center">Administrar SubMenus</h5>

            <div class="input-field col s6">
                <label>Nome SubMenu</label>
                <input type="text" id="nmSubMenu" name="nmSubMenu" required autofocus>
            </div>

            <div class="input-field col s6">
                <select name="idMenu" id="idMenu" required>
                    <option value='' disabled>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[menu] WHERE DT_EXC_MENU IS NULL ORDER BY NM_MENU";
                    $querySelect = $pdoCAT->query($querySelect2);
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        echo "<option value='" . $registros["ID_MENU"] . "'>" . $registros["NM_MENU"] . "</option>";
                    endwhile;
                    ?>
                </select>
            </div>

            <div class="input-field col s12">
                <label>Link SubMenu</label>
                <input type="text" id="linkSubMenu" name="linkSubMenu">
            </div>

            <div class="input-field col s2">
                <button type="submit" class="btn blue">CADASTRAR</button>
            </div>

        </form>
    </fieldset>
    <p>&nbsp;</p>

    <fieldset class="formulario">
        <h5 class="light">SubMenus Cadastrados</h5>
        <hr>
        <div class="content3">
            <?php include_once 'bd/submenu/read.php'; ?>
        </div>
    </fieldset>
</div>