<?php

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';
include_once 'redirecionar.php';

// include('protect.php');

$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

$querySelect2 = "SELECT TIPO.SGL_TIPO AS SGL_TIPO, L.*, DET.COD_LICITACAO AS COD_LIC, DET.*
                    FROM [PortalCompras].[dbo].[LICITACAO] L
                    LEFT JOIN DETALHE_LICITACAO DET ON DET.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN ANEXO A ON A.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN TIPO_LICITACAO TIPO ON TIPO.ID_TIPO = DET.TIPO_LICITACAO
                    WHERE L.ID_LICITACAO = $idLicitacao
                ";

$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idLicitacao = $registros['ID_LICITACAO'];
    $dtLicitacao = $registros['DT_LICITACAO'];
    $codLicitacao = $registros['SGL_TIPO'] . ' ' . $registros['COD_LIC'];
    $statusLicitacao = $registros['STATUS_LICITACAO'];
    $objLicitacao = $registros['OBJETO_LICITACAO'];
    $respLicitacao = $registros['PREG_RESP_LICITACAO'];
    $dtAberLicitacao = date('d/m/Y H:i', strtotime($registros['DT_ABER_LICITACAO']));
    $dtIniSessLicitacao = date('d/m/Y H:i', strtotime($registros['DT_INI_SESS_LICITACAO']));
    $modoLicitacao = $registros['MODO_LICITACAO'];
    $criterioLicitacao = $registros['CRITERIO_LICITACAO'];
    $tipoLicitacao = $registros['TIPO_LICITACAO'];
    $regimeLicitacao = $registros['REGIME_LICITACAO'];
    $formaLicitacao = $registros['FORMA_LICITACAO'];
    $vlLicitacao = $registros['VL_LICITACAO'];
    $localLicitacao = $registros['LOCAL_ABER_LICITACAO'];
    $identificadorLicitacao = $registros['IDENTIFICADOR_LICITACAO'];
    $obsLicitacao = $registros['OBS_LICITACAO'];
// $permitirAtualizacao = $registros['ENVIO_ATUALIZACAO_LICITACAO'];
endwhile;

if (isset($tipoLicitacao)) {
    $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[TIPO_LICITACAO] WHERE ID_TIPO = $tipoLicitacao";
    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
        $idTipo = $registros['ID_TIPO'];
        $nmTipo = $registros['NM_TIPO'];
    endwhile;

    $tituloLicitacao = $nmTipo . ' - ' . $codLicitacao;
} else {
    $tituloLicitacao = $codLicitacao;
}

$_SESSION['redirecionar'] = 'viewLicitacao.php?idLicitacao=' . $idLicitacao;
$login = $_SESSION['login'];
$tela = 'Licitação';
$acao = 'Visualizada';
$idEvento = $idLicitacao;
$queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");

?>
<!-- FORMULÁRIOS DE CADASTRO -->
<div class="row container">
    <form action="consultarLicitacao.php" enctype="multipart/form-data">
        <fieldset class="formulario col s12">
            <p>&nbsp;</p>
            <h5 class="light" style="color: #404040"><?php echo $tituloLicitacao ?></h5>
            <p>&nbsp;</p>
        </fieldset>

        <p>&nbsp;</p>

        <fieldset class="formulario">
            <!-- <h6><strong>Dados cadastrados</strong></h6> -->
            <?php if (isset($tipoLicitacao) && $tipoLicitacao !== '') { ?>
                <div class="input-field col s3">
                    <select name="tipoLicitacao" id="tipoLicitacao">
                        <?php
                        $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE ID_TIPO = $tipoLicitacao";
                        $querySelect = $pdoCAT->query($querySelect2);
                        while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                            echo "<option value='" . $registros["ID_TIPO"] . "'>" . $registros["NM_TIPO"] . "</option>";
                        endwhile;
                        ?>
                    </select>
                    <label>Tipo de Contratação</label>
                </div>
            <?php } ?>

            <?php if (isset($codLicitacao) && $codLicitacao !== '') { ?>
                <div class="input-field col s3">
                    <input type="text" value="<?php echo $codLicitacao ?>" readonly>
                    <label>Código</label>
                </div>
            <?php } ?>

            <?php if (isset($statusLicitacao) && $statusLicitacao !== '') { ?>
                <div class="input-field col s3">
                    <input type="text" value="<?php echo $statusLicitacao ?>" readonly>
                    <label>Status</label>
                </div>
            <?php } ?>

            <?php if (isset($respLicitacao) && $respLicitacao !== '') { ?>
                <div class="input-field col s3">
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

            <?php
            echo "<script>alert($dtAberLicitacao);</script>";

            if (isset($dtAberLicitacao) && !strpos($dtAberLicitacao, '/1969')) { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $dtAberLicitacao ?>" readonly>
                    <label>Data e Horário de Abertura</label>
                </div>
            <?php } ?>

            <?php if (isset($dtIniSessLicitacao) && !strpos($dtIniSessLicitacao, '/1969')) { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $dtIniSessLicitacao ?>" readonly>
                    <label>Início da Sessão de Disputa de Preços</label>
                </div>
            <?php } ?>

            <?php if (isset($modoLicitacao) && $modoLicitacao != '0') { ?>
                <div class="input-field col s4">
                    <input type="text" value="<?php echo $modoLicitacao ?>" readonly>
                    <label>Modo de Disputa</label>
                </div>
            <?php } ?>

            <?php if (isset($criterioLicitacao) && $criterioLicitacao != '0') { ?>
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

            <?php
            if (isset($formaLicitacao) && $formaLicitacao != 0) { ?>
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
                    <a href="<?php echo $localLicitacao ?>" target="_blank" class="input-field">
                        <label>Local de Abertura</label>
                        <input type="text" value="<?php echo $localLicitacao ?>" readonly>
                    </a>
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
                                    AND DT_EXC_ANEXO IS NULL
                                )
                                SELECT
                                    ID_LICITACAO,
                                    MAX(CASE WHEN NM_ANEXO like '%_descricao' THEN LINK_ANEXO END) AS NM_ANEXO,
                                    MAX(CASE WHEN NM_ANEXO like '%_arquivo' THEN LINK_ANEXO END) AS LINK_ANEXO
                                FROM RankedAnexos
                                GROUP BY ID_LICITACAO, rn;";
            } else {
                // TRECHO PARA LICITAÇÕES TACLACODE
                $queryAnexo = "SELECT ID_LICITACAO, NM_ANEXO, LINK_ANEXO FROM ANEXO WHERE ID_LICITACAO = $idLicitacao AND DT_EXC_ANEXO IS NULL";
            }

            $queryAnexo2 = $pdoCAT->query($queryAnexo);

            // Obtenha anexos do banco de dados
            while ($registros = $queryAnexo2->fetch(PDO::FETCH_ASSOC)) {
                $anexos[] = array(
                    'nmAnexo' => $registros['NM_ANEXO'],
                    'linkAnexo' => $registros['LINK_ANEXO'],
                    'timestamp' => time(), // Usando o timestamp atual para os anexos do banco de dados
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
                        'timestamp' => filemtime($directory . '/' . $file), // Obtém o timestamp do arquivo
                    );
                }
            }

            usort($anexos, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });

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
                    echo '<td><a href="' . $anexo['linkAnexo'] . '" target="_blank">' . $anexo['nmAnexo'] . '</a></td>';
                    echo '</tr>';
                }

                echo '</tbody>
            </table>';
                echo '</div>';
            }
            ?>
        </fieldset>



        <div class="input-field col s12">
            <button type="submit" class="btn blue">Voltar</button>
        </div>
    </form>
</div>