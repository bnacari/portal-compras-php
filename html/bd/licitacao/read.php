<?php

include_once 'bd/conexao.php';
include_once 'redirecionar.php';

// include('protectAdmin.php');

$lgnCriador = $_SESSION['login'];

$tituloLicitacaoFilter = filter_input(INPUT_POST, 'tituloLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$statusLicitacaoFilter = filter_input(INPUT_POST, 'statusLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$dtIniLicitacaoFilter = filter_input(INPUT_POST, 'dtIniLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$dtFimLicitacaoFilter = filter_input(INPUT_POST, 'dtFimLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);

if (!isset($statusLicitacaoFilter) || empty($statusLicitacaoFilter)) {
    $statusLicitacaoFilter = 'Em Andamento';
}
// var_dump($dtIniLicitacaoFilter);

if (isset($tituloLicitacaoFilter)) {
    $tituloLicitacaoFilterSQL = " like '%$tituloLicitacaoFilter%'";
} else {
    $tituloLicitacaoFilterSQL = " like '%%'";
}

if ($statusLicitacaoFilter !== 'vazio') {
    if ($statusLicitacaoFilter == 'Em Andamento') {
        $statusLicitacaoFilter = "like 'Em Andamento'";
    } else if ($statusLicitacaoFilter == 'Suspenso') {
        $statusLicitacaoFilter = "like 'Suspenso'";
    } else if ($statusLicitacaoFilter == 'Encerrado') {
        $statusLicitacaoFilter = "like 'Encerrado'";
    } else if ($statusLicitacaoFilter == 'Rascunho') {
        $statusLicitacaoFilter = "like 'Rascunho'";
    }
} else {
    $statusLicitacaoFilter = "NOT LIKE 'Rascunho'";
}

if (isset($dtIniLicitacaoFilter)) {
    $dtIniLicitacaoFilterSQL = " between '$dtIniLicitacaoFilter' and '$dtFimLicitacaoFilter'";
} else {
    $dtIniLicitacaoFilterSQL = " IS NOT NULL";
    $dtFimLicitacaoFilter = " IS NOT NULL";

}

$querySelect2 = "SELECT  
                    DISTINCT(L.ID_LICITACAO), L.COD_LICITACAO, D.STATUS_LICITACAO, L.DT_LICITACAO, D.OBJETO_LICITACAO
                FROM
                    LICITACAO L
                    LEFT JOIN ANEXO A ON L.ID_LICITACAO = A.ID_LICITACAO
                    LEFT JOIN DETALHE_LICITACAO D ON D.ID_LICITACAO = L.ID_LICITACAO
                WHERE
                    D.STATUS_LICITACAO $statusLicitacaoFilter
                ";

$querySelect2 .= " AND (L.COD_LICITACAO $tituloLicitacaoFilterSQL OR D.OBJETO_LICITACAO $tituloLicitacaoFilterSQL)";
// var_dump($tituloLicitacaoFilterSQL);

$querySelect2 .= " AND L.DT_LICITACAO $dtIniLicitacaoFilterSQL ";

$querySelect2 .= " ORDER BY L.[DT_LICITACAO] DESC";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

// var_dump($querySelect);
// exit();

echo "<table class='rTableLicitacao'>";
// echo "<thead>";
// echo "<tr>";

// echo "<th>Id</th>";
// echo "<th>Título</th>";
// echo "<th>Status</th>";
// echo "<th>Data</th>";
// echo "<th style='text-align: center;'>Visualizar</th>";

// if ($_SESSION['admin']) {
// echo "<th style='text-align: center;'>Editar</th>";
// }

// echo "</tr>";
// echo "</thead>";
echo "<tbody>";

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idLicitacao = $registros['ID_LICITACAO'];
    $tituloLicitacao = $registros['COD_LICITACAO'];
    $statusLicitacao = $registros['STATUS_LICITACAO'];
    $dtLicitacao = date('d/m/Y', strtotime($registros['DT_LICITACAO']));
    $objLicitacao = $registros['OBJETO_LICITACAO'];

    echo "<tr>";

    // echo "<td>$idLicitacao</td>";
    // echo "<td title='$objLicitacao'><strong>$tituloLicitacao</strong></td>";
    echo "<td title='$objLicitacao'>
            <p>            
                <a href='viewLicitacao.php?idLicitacao=$idLicitacao'>
                    <strong><h7>$tituloLicitacao</h7></strong>
                </a> ";
    if ($_SESSION['admin'] == 5) {
        echo "<a href='editarLicitacao.php?idLicitacao=$idLicitacao'><i class='material-icons'>edit</i></a>";
        // echo "<a href='editarLicitacao.php?idLicitacao=$idLicitacao'><fieldset class='custom-fieldset-editar'><h7>Editar</h7></fieldset></a>";
    }
    echo "</p>
            <p style='color:#9E9E9E'>$objLicitacao</p>
            <label class='custom-label'><strong>$dtLicitacao</strong></label>";
    if ($statusLicitacao == 'Em Andamento') {
        echo "<fieldset class='custom-fieldset'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    } else if ($statusLicitacao == 'Suspenso') {
        echo "<fieldset class='custom-fieldset suspenso'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    } else {
        echo "<fieldset class='custom-fieldset encerrado'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    }
    echo "</td>";

    // echo "<td>$objLicitacao</td>";
    // echo "<td><fieldset class='custom-fieldset'><label style='color:#FEFEFE'>$statusLicitacao</label></fieldset></td>";

    // echo "<td>$dtLicitacao</td>";

    // if ($dtAprovacao == null) {
    //     echo "<td style='text-align: center;'><a href='bd/visita/aprova.php?idLicitacao=$idLicitacao' title='Aprovar Visita' ><i class='material-icons'>check</i></a></td>";
    // } else {
    //     echo "<td style='text-align: center;'><a href='bd/visita/desaprova.php?idLicitacao=$idLicitacao' title='Desaprovar Visita' style='color: red;'><i class='material-icons'>close</i></a></td>";
    // }

    // echo "<td style='text-align: center;'><a href='viewLicitacao.php?idLicitacao=$idLicitacao'><i class='material-icons'>remove_red_eye</i></a></td>";

    // if ($_SESSION['admin']) {
    //     echo "<td style='text-align: center;'><a href='editarLicitacao.php?idLicitacao=$idLicitacao'><i class='material-icons'>edit</i></a></td>";
    // }

    echo "</tr>";
endwhile;

echo "</tbody>";
echo "</table>";
