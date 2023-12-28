<?php

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';
include_once 'redirecionar.php';

include('protect.php');

$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

$querySelect2 = "SELECT L.*, DET.*
                    FROM [PortalCompras].[dbo].[LICITACAO] L
                    LEFT JOIN DETALHE_LICITACAO DET ON DET.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN ANEXO A ON A.ID_LICITACAO = L.ID_LICITACAO
                    WHERE L.ID_LICITACAO = $idLicitacao
                ";

$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idLicitacao = $registros['ID_LICITACAO'];
    $dtLicitacao = $registros['DT_LICITACAO'];
    $tituloLicitacao = $registros['COD_LICITACAO'];
    $codLicitacao = $registros['COD_LICITACAO'];
    $statusLicitacao = $registros['STATUS_LICITACAO'];
    $objLicitacao = $registros['OBJETO_LICITACAO'];
    $respLicitacao = $registros['PREG_RESP_LICITACAO'];
    $dtAberLicitacao = date('d/m/Y H:i', strtotime($registros['DT_ABER_LICITACAO']));
    $dtIniSessLicitacao = date('d/m/Y H:i', strtotime($registros['DT_INI_SESS_LICITACAO']));
    $modoLicitacao = $registros['MODO_LICITACAO'];
    $criterioLicitacao = $registros['CRITERIO_LICITACAO'];
    $regimeLicitacao = $registros['REGIME_LICITACAO'];
    $formaLicitacao = $registros['FORMA_LICITACAO'];
    $vlLicitacao = $registros['VL_LICITACAO'];
    $localLicitacao = $registros['LOCAL_ABER_LICITACAO'];
    $identificadorLicitacao = $registros['IDENTIFICADOR_LICITACAO'];
    $obsLicitacao = $registros['OBS_LICITACAO'];
endwhile;

$_SESSION['redirecionar'] = 'viewLicitacao.php?idLicitacao=' . $idLicitacao;
$login = $_SESSION['login'];
$tela = 'Licitação';
$acao = 'Visualizada';
$idEvento = $idLicitacao;
$queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");

?>
<!-- FORMULÁRIOS DE CADASTRO -->
<div class="row container">
    <form action="consultarLicitacao.php" class="col s12" enctype="multipart/form-data">
        <fieldset class="formulario col s12">
            <p>&nbsp;</p>
            <h5 class="light center"><?php echo $tituloLicitacao ?></h5>
            <p>&nbsp;</p>
        </fieldset>

        <p>&nbsp;</p>

        <fieldset class="formulario">
            <!-- <h6><strong>Dados cadastrados</strong></h6> -->
            <?php if (isset($codLicitacao) && $codLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $codLicitacao ?>" readonly>
                    <label>Código</label>
                </div>
            <?php } ?>

            <?php if (isset($statusLicitacao) && $statusLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $statusLicitacao ?>" readonly>
                    <label>Status</label>
                </div>
            <?php } ?>

            <?php if (isset($respLicitacao) && $respLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $respLicitacao ?>" readonly>
                    <label>Responsável</label>
                </div>
            <?php } ?>

            <?php if (isset($objLicitacao) && $objLicitacao !== '') { ?>
                <div class="input-field col s12">
                    <textarea type="text" readonly><?php echo $objLicitacao ?> </textarea>
                    <label>Objeto</label>
                </div>
            <?php } ?>

            <?php if (isset($dtAberLicitacao) && $dtAberLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $dtAberLicitacao ?>" readonly>
                    <label>Data e Horário de Abertura</label>
                </div>
            <?php } ?>

            <?php if (isset($dtIniSessLicitacao) && $dtIniSessLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $dtIniSessLicitacao ?>" readonly>
                    <label>Início da Sessão de Disputa de Preços</label>
                </div>
            <?php } ?>

            <?php if (isset($modoLicitacao) && $modoLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $modoLicitacao ?>" readonly>
                    <label>Modo de Disputa</label>
                </div>
            <?php } ?>

            <?php if (isset($criterioLicitacao) && $criterioLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <select name="criterioLicitacao" id="criterioLicitacao">
                        <?php
                        $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE ID_CRITERIO = $criterioLicitacao";
                        $querySelect = $pdoCAT->query($querySelect2);
                        while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                            echo "<option value='" . $registros["ID_CRITERIO"] . "'>" . $registros["NM_CRITERIO"] . "</option>";
                        endwhile;
                        ?>
                    </select>
                    <label>Critério de Julgamento</label>
                </div>
            <?php } ?>

            <?php if (isset($regimeLicitacao) && $regimeLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $regimeLicitacao ?>" readonly>
                    <label>Regime de Execução</label>
                </div>
            <?php } ?>

            <?php if (isset($formaLicitacao) && $formaLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <select name="regimeLicitacao" id="regimeLicitacao">
                        <?php
                        $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] WHERE ID_FORMA = $formaLicitacao";
                        $querySelect = $pdoCAT->query($querySelect2);
                        while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                            echo "<option value='" . $registros["ID_FORMA"] . "'>" . $registros["NM_FORMA"] . "</option>";
                        endwhile;
                        ?>
                    </select>
                    <label>Forma</label>
                </div>
            <?php } ?>

            <?php if (isset($vlLicitacao) && $vlLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $vlLicitacao ?>" readonly>
                    <label>Valor Estimado</label>
                </div>
            <?php } ?>

            <?php if (isset($identificadorLicitacao) && $identificadorLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $identificadorLicitacao ?>" readonly>
                    <label>Identificador</label>
                </div>
            <?php } ?>

            <?php if (isset($localLicitacao) && $localLicitacao !== '') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $localLicitacao ?>" readonly>
                    <label>Local de Abertura</label>
                </div>
            <?php } ?>
            <?php if (isset($obsLicitacao) && $obsLicitacao !== '') { ?>
                <div class="input-field col s12">
                    <textarea type="text" readonly><?php echo $obsLicitacao ?> </textarea>
                    <label>Observação</label>
                </div>
            <?php } ?>
        </fieldset>

        <p>&nbsp;</p>

        <fieldset class="formulario">
            <?php
            $directory = "uploads" . '/' . $idLicitacao;

            // Verifique se o diretório existe
            $isDirectory = is_dir($directory);

            // Array para armazenar os anexos
            $anexos = array();

            // TRECHO PARA LICITAÇÕES 13.303
            if ($idLicitacao > 2000) {
                $queryAnexo = "WITH RankedAnexos AS (
            SELECT
                ID_LICITACAO,
                NM_ANEXO,
                LINK_ANEXO,
                ROW_NUMBER() OVER (PARTITION BY ID_LICITACAO, CASE WHEN NM_ANEXO LIKE '%_descricao' THEN 1 ELSE 2 END ORDER BY NM_ANEXO) AS rn
            FROM ANEXO
            WHERE ID_LICITACAO = $idLicitacao
        )
        SELECT
            ID_LICITACAO,
            MAX(CASE WHEN NM_ANEXO like '%_descricao' THEN LINK_ANEXO END) AS NM_ANEXO,
            MAX(CASE WHEN NM_ANEXO like '%_arquivo' THEN LINK_ANEXO END) AS LINK_ANEXO
        FROM RankedAnexos
        GROUP BY ID_LICITACAO, rn;";
            } else {
                // TRECHO PARA LICITAÇÕES TACLACODE
                $queryAnexo = "SELECT ID_LICITACAO, NM_ANEXO, LINK_ANEXO FROM ANEXO WHERE ID_LICITACAO = $idLicitacao";
            }

            $queryAnexo2 = $pdoCAT->query($queryAnexo);

            // Obtenha anexos do banco de dados
            while ($registros = $queryAnexo2->fetch(PDO::FETCH_ASSOC)) {
                $anexos[] = array(
                    'nmAnexo' => $registros['NM_ANEXO'],
                    'linkAnexo' => $registros['LINK_ANEXO'],
                );
            }

            // Verifique se há arquivos no diretório
            if ($isDirectory) {
                // Liste os arquivos no diretório
                $files = scandir($directory);

                // Exclua . e ..
                $files = array_diff($files, array('.', '..'));

                foreach ($files as $file) {
                    $anexos[] = array(
                        'nmAnexo' => $file,
                        'linkAnexo' => $directory . '/' . $file,
                    );
                }
            }

            // Exiba os anexos
            if (!empty($anexos)) {
                echo '<div class="grid">';
                echo '<table>
            <thead>
                <tr>
                    <th>
                        <h6><strong>Anexos</strong></h6>
                    </th>
                </tr>
            </thead>
            <tbody>';

                foreach ($anexos as $anexo) {
                    echo '<tr>';
                    echo '<td><a href="' . $anexo['linkAnexo'] . '" target="_blank" download>' . $anexo['nmAnexo'] . '</a></td>';
                    echo '</tr>';
                }

                echo '</tbody>
            </table>';
                echo '</div>';
            }
            ?>
        </fieldset>



        <div class="input-field col s2">
            <button type="submit" class="btn blue">Voltar</button>
        </div>
    </form>
</div>

<!-- MODAL ============================================================================= -->
<?php 
// VERIFICO SE O USUÁRIO JÁ ESTÁ CADASTRADO PARA RECEBER FUTURAS ATUALIZAÇÕES NA LICITAÇÃO
$email = $_SESSION['email'];
$queryUpdateLicitacao = "SELECT ID_ATUALIZACAO 
                            FROM ATUALIZACAO 
                            WHERE ID_LICITACAO = $idLicitacao 
                            AND EMAIL_ADM LIKE '$email' 
                            AND DT_EXC_ATUALIZACAO IS NULL";
$queryUpdateLici2 = $pdoCAT->query($queryUpdateLicitacao);
while ($registros = $queryUpdateLici2->fetch(PDO::FETCH_ASSOC)) :
    $idAtualizacao = $registros['ID_ATUALIZACAO'];
endwhile;

if (!isset($idAtualizacao)) { ?>
<div class="materialize-content">
    <div id="modalAtualizacao" class="modal">
        <div class="modal-content">
            <h5>Receber Atualizações</h5>
            <form action='bd/licitacao/enviarAtualizacao.php?idLicitacao=<?php echo $idLicitacao; ?>' method="post">
                <br>
                <div class="input-field">
                    <input type="checkbox" name="enviarAtualizacao" id="enviarAtualizacao" required>
                    <label for="enviarAtualizacao">Tenho interesse em receber atualizações sobre essa licitação.</label>
                </div>
                <br>
                <button type="submit" class="btn blue">Enviar</button>
            </form>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Fechar</a>
        </div>
    </div>
</div>
<?php } ?>
<!--FIM MODAL ============================================================================= -->

<script>
    $(document).ready(function() {
        // Inicializa o modal
        $('.modal').modal();

        // Aguarda 500 milissegundos (ou ajuste conforme necessário) antes de abrir o modal
        $('#modalAtualizacao').modal('open');

    });
</script>