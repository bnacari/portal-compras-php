<?php

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

$idSubMenu = filter_input(INPUT_GET, 'idSubMenu', FILTER_SANITIZE_NUMBER_INT);

/////////////////////////////////////////////////////////////////////////


$queryAdmin = "SELECT * 
                FROM [PortalCompras].[dbo].SUBMENU SM
                LEFT JOIN MENU M ON M.ID_MENU = SM.ID_MENU
                WHERE SM.ID_SUBMENU = $idSubMenu";

$querySelect = $pdoCAT->query($queryAdmin);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $nmSubMenu = $registros['NM_SUBMENU'];
    $linkSubMenu = $registros['LINK_SUBMENU'];
    $idMenu = $registros['ID_MENU'];
    $nmMenu = $registros['NM_MENU'];

endwhile;

/////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////

?>

<!-- FORMULÁRIOS DE CADASTRO -->
<div class="row container">
    <form action="bd/submenu/update.php" method="post" class="col s12 formulario" id="formFiltrar">
        <fieldset class="formulario col s12">
            <h5 class="light" style="color: #404040">Editar Submenu</h5>
        </fieldset>

        <input style="display:none" type="text" id="idSubMenu" name="idSubMenu" value="<?php echo $idSubMenu ?>">
        
        <p>&nbsp;</p>
        <fieldset class="formulario">
            <!-- <h6><strong>Local a Visitar</strong></h6> -->
            <div class="input-field col s4">
                <input type="text" id="nmSubMenu" name="nmSubMenu" value="<?php echo $nmSubMenu ?>" autofocus>
                <label>Nome</label>
            </div>

            <div class="input-field col s4">
                <input type="text" id="linkSubMenu" name="linkSubMenu" value="<?php echo $linkSubMenu ?>">
                <label>Link</label>
            </div>

            <div class="input-field col s4">
                <select name="idMenu" id="idMenu" required>
                    <option value='' disabled>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM portalcompras.dbo.[menu] WHERE DT_EXC_MENU IS NULL";
                    $querySelect = $pdoCAT->query($querySelect2);

                    echo "<option value='" . $idMenu . "' selected>" . $nmMenu . "</option>";
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        // Verifica se o ID é diferente do ID já selecionado
                        if ($registros["ID_MENU"] != $idMenu) {
                            echo "<option value='" . $registros["ID_MENU"] . "'>" . $registros["NM_MENU"] . "</option>";
                        }
                    endwhile;
                    ?>
                </select>
                <!-- <label>Menu</label> -->

            </div>
        </fieldset>

        <p>&nbsp;</p>

        <div class="input-field col s12">
            <button type="submit" class="btn blue">Salvar</button>
        </div>
    </form>
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