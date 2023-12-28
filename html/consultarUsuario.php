<?php

// // session_start();
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

?>

<!-- FORMULÁRIOS DE PESQUISA -->
<div class="row container">
    <fieldset class="formulario">
        <form action="consultarUsuario.php" method="post" class="col s12 formulario" id="formFiltrar">

            <!-- <legend><img src="imagens/batman-icon.png" alt="[imagem]" width="100"></legend> -->
            <h5 class="light center">Administrar Usuários</h5>

            <div class="input-field col s6">
                <i class="material-icons prefix">input</i>
                <input type="text" name="nome" id="nome" maxlength="100" style="text-transform: uppercase" autofocus>
                <label for="nome">Nome/Login do Usuário</label>
            </div>

            <div class="input-field col s5">
                <select name="perfilUsuario" id="perfilUsuario" required oninvalid="exibirAlertaRespSolicitacao()" onchange="changeRespSolicitacao()">
                    <option value='0' selected>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM portalcompras.dbo.[PERFIL] WHERE DT_EXC_PERFIL IS NULL ORDER BY NM_PERFIL";
                    $querySelect = $pdoCAT->query($querySelect2);
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        // echo "<option value='" . $registros["ID_PERFIL"] . ">" . $registros["NM_PERFIL"] . "</option>";
                        echo "<option value='" . $registros["ID_PERFIL"] . "'>" . $registros["NM_PERFIL"] . "</option>";
                    endwhile;
                    ?>
                </select>
            </div>

            <div class="input-field col s1">
                <input type="submit" value="pesquisar" class="btn blue">
                <!-- <input type="reset" value="limpar" class="btn red"> -->
            </div>
           
        </form>
    </fieldset>
    <p>&nbsp;</p>

    <fieldset class="formulario">
        <div class="col s12">
            <h5 class="light">Usuários Cadastrados</h5>
            <hr>

            <div class="content3">
                <table class="rTableInscritos">
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Login</th>
                            <th>Nome</th>
                            <th>Unidade</th>
                            <th>e-mail</th>
                            <th>Perfil</th>
                            <th>Ativar/Desativar</th>
                            <!-- <th style="text-align: center;">Setar Administrador?</th> -->
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php include_once 'bd/usuario/read.php'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>
</div>