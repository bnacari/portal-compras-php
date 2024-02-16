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

        <form action="bd/menus/create.php" method="post" class="col s12 formulario" id="formFiltrar">

            <h5 class="light" style="color: #404040">Administrar Menus</h5>

            <div class="input-field col s12">
                <label>Nome Menu</label>
                <input type="text" id="nmMenu" name="nmMenu" required autofocus>
            </div>

            <div class="input-field col s12">
                <label>Link Menu</label>
                <input type="text" id="linkMenu" name="linkMenu">
            </div>

            <div class="input-field col s2">
                <button type="submit" class="btn blue">CADASTRAR</button>
            </div>

        </form>
    </fieldset>

    <p>&nbsp;</p>

    <fieldset class="formulario">
        <h5 class="light" style="color: #404040">Menus Cadastrados</h5>
        <hr>
        <div class="content3">
            <?php include_once 'bd/menus/read.php'; ?>
        </div>
    </fieldset>
</div>