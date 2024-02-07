<?php

include_once 'bd/conexao.php';
include_once 'redirecionar.php';
include_once('../../protect.php');

$login = $_SESSION['login'];

// $tituloLicitacaoFilter = filter_input(INPUT_POST, 'tituloLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
// $statusLicitacaoFilter = filter_input(INPUT_POST, 'statusLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
// $dtIniLicitacaoFilter = filter_input(INPUT_POST, 'dtIniLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
// $dtFimLicitacaoFilter = filter_input(INPUT_POST, 'dtFimLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);

$querySelect2 = "SELECT A.ID_ATUALIZACAO, ADM.NM_ADM, A.EMAIL_ADM, A.DT_EXC_ATUALIZACAO, DL.*
                    FROM ATUALIZACAO A 
                    LEFT JOIN ADMINISTRADOR ADM ON ADM.ID_ADM = A.ID_ADM
                    LEFT JOIN DETALHE_LICITACAO DL ON A.ID_LICITACAO = DL.ID_LICITACAO
                    WHERE ADM.STATUS LIKE 'A'
                    AND A.DT_EXC_ATUALIZACAO IS NULL
                    AND ADM.LGN_ADM LIKE '$login'
                ";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

// var_dump($querySelect);
// exit();

echo "<table class='rTableLicitacao'>";

echo "<tbody>";

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idAtualizacao = $registros['ID_ATUALIZACAO'];
    $dtExcAtualizacao = $registros['DT_EXC_ATUALIZACAO'];
    $nmUsuario = $registros['NM_ADM'];
    $email = $registros['EMAIL_ADM'];
    $idLicitacao = $registros['ID_LICITACAO'];
    $codLicitacao = $registros['COD_LICITACAO'];
    $statusLicitacao = $registros['STATUS_LICITACAO'];
    $objLicitacao = $registros['OBJETO_LICITACAO'];
    $respLicitacao = $registros['PREG_RESP_LICITACAO'];
    $dtAbertura = $registros['DT_ABER_LICITACAO'];
    $dtIniSessao = $registros['DT_INI_SESS_LICITACAO'];
    $modoLicitacao = $registros['MODO_LICITACAO'];
    $criterioLicitacao = $registros['CRITERIO_LICITACAO'];
    $regimeLicitacao = $registros['REGIME_LICITACAO'];
    $formaLicitacao = $registros['FORMA_LICITACAO'];
    $vlLicitacao = $registros['VL_LICITACAO'];
    $localLicitacao = $registros['LOCAL_ABER_LICITACAO'];
    $identificadorLicitacao = $registros['IDENTIFICADOR_LICITACAO'];
    $obsLicitacao = $registros['OBS_LICITACAO'];

    echo "<tr>";

    // echo "<td>$idLicitacao</td>";
    // echo "<td title='$objLicitacao'><strong>$tituloLicitacao</strong></td>";
    echo "<td title='$objLicitacao'>
            <p>            
                <a href='viewLicitacao.php?idLicitacao=$idLicitacao'>
                    <strong><h7>$codLicitacao</h7></strong>
                </a> ";
    
    echo "</p>
            <p style='color:#9E9E9E'>$objLicitacao</p>
            <label class='custom-label'><strong>$dtLicitacao</strong></label>";
    if ($statusLicitacao == 'Em Andamento') {
        echo "<fieldset class='custom-fieldset'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    } else if ($statusLicitacao == 'Suspenso') {
        echo "<fieldset class='custom-fieldset suspenso'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    } else if ($statusLicitacao == 'Rascunho') {
        echo "<fieldset class='custom-fieldset rascunho'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    } else {
        echo "<fieldset class='custom-fieldset encerrado'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    }
    echo "</td>";
    // echo "<td>$dtExcAtualizacao</td>";
    if (!isset($dtExcAtualizacao)) {
        echo "<td style='text-align: center;'><a href='bd/atualizacao/desativar.php?idAtualizacao=$idAtualizacao' style='color:red' title='Não desejo ser lembrado sobre futuras atualizações'><i class='bi bi-dash'></i></a></td>";
    }
    echo "</tr>";
endwhile;

echo "</tbody>";
echo "</table>";
