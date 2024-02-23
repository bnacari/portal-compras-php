<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

// include('protect.php');

?>
<div class="row container">
    <fieldset class="formulario">
        <form action="consultarLicitacao.php" method="post" class="col s12 formulario" id="formFiltrar">
            <h5 class="light" style="color: #404040">Filtrar Licitações</h5>

            <div class="input-field col s4">
                <i class="material-icons prefix">input</i>
                <input type="text" name="tituloLicitacao" id="tituloLicitacao" maxlength="100">
                <label for="tituloLicitacao">Buscar por Título ou Objeto</label>
            </div>

            <div class="input-field col s2">
                <!-- <i class="material-icons prefix">event</i> -->
                <input type="date" name="dtIniLicitacao" id="dtIniLicitacao" maxlength="100">
                <label for="dtIniLicitacao">Data de Abertura</label>
            </div>

            <div class="input-field col s2">
                <!-- <i class="material-icons prefix">event</i> -->
                <input type="date" name="dtFimLicitacao" id="dtFimLicitacao" maxlength="100" value="<?php echo date('Y-m-d', strtotime('+1 day')) ?>" required>
                <label for="dtFimLicitacao">Até</label>
            </div>

            <div class="input-field col dropdown s2">
                <div>
                    <label>Status</label>
                </div>
                <select name="statusLicitacao" id="statusLicitacao">
                    <option value='vazio'>Selecione uma opção</option>
                    <option value='Em Andamento' selected>Em Andamento</option>
                    <option value='Suspenso'>Suspensa</option>
                    <option value='Encerrado'>Encerrada</option>
                    <?php if (isset($_SESSION['isAdmin'])) { ?>
                        <option value='Rascunho'>Rascunho</option>
                    <?php } ?>
                </select>
            </div>

            <div class="input-field col s2">
                <input type="submit" value="pesquisar" class="btn blue">
                <!-- <input type="reset" value="limpar" class="btn red"> -->
            </div>
           
        </form>
    </fieldset>

    <p>&nbsp;</p>

    <fieldset class="formulario">
        <h5 class="light" style="color: #404040">Licitações</h5>
        <hr>
        <div class="content3">
            <?php include_once 'bd/licitacao/read.php'; ?>
        </div>
    </fieldset>
</div>