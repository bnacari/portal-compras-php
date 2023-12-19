<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include('protectAdmin.php');

$idCriterio = filter_input(INPUT_GET, 'idCriterio', FILTER_SANITIZE_NUMBER_INT);

$querySelectCriterio = "SELECT NM_CRITERIO FROM CRITERIO_LICITACAO WHERE ID_CRITERIO = $idCriterio";
$querySelectCriterio2 = $pdoCAT->query($querySelectCriterio);
while($registros = $querySelectCriterio2->fetch(PDO::FETCH_ASSOC)):
    $nmCriterio = $registros['NM_CRITERIO'];
endwhile;

$queryUpdateCriterio = "UPDATE [portalcompras].[dbo].[CRITERIO_LICITACAO] SET DT_EXC_CRITERIO = GETDATE() WHERE ID_CRITERIO = $idCriterio";
$queryUpdateCriterio2 = $pdoCAT->query($queryUpdateCriterio);

$_SESSION['msg'] = "<p class='center red-text'>".'<strong>Criterio</strong> desativado com <strong>sucesso</strong>.'."</p>";

// header("Location: ../../consultarUsuario.php");

$_SESSION['redirecionar'] = '../cadCriterio.php';
$login = $_SESSION['login'];
$tela = 'Criterio';
$acao = 'Criterio ' . $nmCriterio . ' DESATIVADO';
$idEvento = $idCriterio;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
