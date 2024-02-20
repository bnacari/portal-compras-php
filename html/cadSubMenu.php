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

            <h5 class="light" style="color: #404040">Administrar SubMenus</h5>

            <div class="input-field col s6">
                <label>Nome SubMenu</label>
                <input type="text" id="nmSubMenu" name="nmSubMenu" required autofocus>
            </div>

            <div class="input-field col s6">
                <select name="idMenu" id="idMenu" required oninvalid="exibirAlertaIdMenu()">
                    <option value='' disabled selected>Selecione uma opção</option>
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
        <h5 class="light" style="color: #404040">SubMenus Cadastrados</h5>
        <hr>
        <div class="content3">
            <?php include_once 'bd/submenu/read.php'; ?>
        </div>
    </fieldset>
</div>


<!-- Inclua o jQuery Mask Plugin -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script> -->

<!-- JS for jQuery -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
<!-- CSS for searching -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- JS for searching -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


<script>
    function exibirAlertaIdMenu() {
        alert("Por favor, preencha o campo 'Menu relacionado'");
    }

    $(document).ready(function() {
        $('#idMenu').select2({
            width: '100%',
            // placeholder: 'Selecione as partes do corpo',
            allowClear: true
        });
    });
</script>