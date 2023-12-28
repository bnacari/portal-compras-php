<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

?>
<div class="row container">
    <fieldset class="formulario">
        <h5 class="light">Licitações que receberá e-mail em futuras atualizações</h5>
        <hr>
        <div class="content3">
            <?php include_once 'bd/atualizacao/read.php'; ?>
        </div>
    </fieldset>
</div>