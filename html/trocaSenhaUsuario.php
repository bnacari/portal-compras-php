<?php

// session_start();
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

// include('protect.php');

?>

<div class="row container">
    <fieldset class="formulario">

        <form action="bd/usuario/trocaSenha.php" method="post" class="col s12 formulario" id="formFiltrar">

            <h5 class="light" style="color: #404040">Trocar Senha</h5>

            <div class="input-field col s12">
                <label>Senha Atual</label>
                <input type="password" id="senhaAtual" name="senhaAtual" required>
            </div>

            <div class="input-field col s12">
                <input type="password" name="senhaNova" id="senhaNova" maxlength="12" required>
                <label for="senha">Senha (máximo 12 caracteres)</label>
            </div>

            <div class="input-field col s12">
                <input type="password" name="senhaNova2" id="senhaNova2" maxlength="12" required>
                <label for="senha">Repetir Senha</label>
            </div>

            <div class="input-field col s2">
                <button type="submit" class="btn blue">Trocar</button>
            </div>

        </form>
    </fieldset>

</div>