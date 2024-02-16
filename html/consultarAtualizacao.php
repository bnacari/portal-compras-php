<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

// include('protect.php');

?>
<div class="row container">
    <fieldset class="formulario">
        <h5 class="light" style="color: #404040">Licitações que receberá e-mail em futuras atualizações</h5>
        <hr>
        <div class="content3">
            <?php include_once 'bd/atualizacao/read.php'; ?>
        </div>
    </fieldset>
</div>