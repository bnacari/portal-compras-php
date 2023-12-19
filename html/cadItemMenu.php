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

        <form action="bd/itemmenu/create.php" method="post" class="col s12 formulario" id="formFiltrar">

            <h5 class="light center">Administrar ItemMenus</h5>

            <div class="input-field col s6">
                <label>Nome ItemMenu</label>
                <input type="text" id="nmItemMenu" name="nmItemMenu" required autofocus>
            </div>

            <div class="input-field col s6">
                <select name="idSubMenu" id="idSubMenu" required>
                    <option value='' disabled>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[submenu] WHERE DT_EXC_SUBMENU IS NULL ORDER BY NM_SUBMENU";
                    $querySelect = $pdoCAT->query($querySelect2);
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        echo "<option value='" . $registros["ID_SUBMENU"] . "'>" . $registros["NM_SUBMENU"] . "</option>";
                    endwhile;
                    ?>
                </select>
            </div>

            <div class="input-field col s12">
                <label>Link ItemMenu</label>
                <input type="text" id="linkItemMenu" name="linkItemMenu">
            </div>

            <div class="input-field col s2">
                <button type="submit" class="btn blue">CADASTRAR</button>
            </div>

            <div class="input-field col s12">
                <?php
                if (isset($_SESSION['msg'])) :
                    echo $_SESSION['msg'];
                    $_SESSION['msg'] = '';
                endif;
                ?>
            </div>
        </form>
    </fieldset>
    <p>&nbsp;</p>

    <fieldset class="formulario">
        <h5 class="light">ItensMenus Cadastrados</h5>
        <hr>
        <div class="content3">
            <?php include_once 'bd/itemmenu/read.php'; ?>
        </div>
    </fieldset>
</div>