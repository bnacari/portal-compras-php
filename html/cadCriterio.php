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
        <form action="bd/criterio/create.php" method="post" class="col s12 formulario" id="formFiltrar">
            <h5 class="light" style="color: #404040">Administrar Critérios</h5>
            <div class="input-field col s10">
                <label>Critério</label>
                <input type="text" id="nmCriterio" name="nmCriterio" required autofocus>
            </div>

            <div class="input-field col s12">
                <button type="submit" class="btn blue">CADASTRAR</button>
            </div>
        </form>
    </fieldset>
    <p>&nbsp;</p>

    <fieldset class="formulario">
        <div>
            <h5 class="light" style="color: #404040">Critérios Cadastrados</h5>
            <hr>
        </div>

        <div class="content3">
            <?php include_once 'bd/criterio/read.php'; ?>
        </div>
    </fieldset>
</div>