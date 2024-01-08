<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protectAdmin.php');

$idCriterio = filter_input(INPUT_GET, 'idCriterio', FILTER_SANITIZE_NUMBER_INT);

$querySelectCriterio = "SELECT NM_CRITERIO FROM CRITERIO_LICITACAO WHERE ID_CRITERIO = $idCriterio";
$querySelectCriterio2 = $pdoCAT->query($querySelectCriterio);
while($registros = $querySelectCriterio2->fetch(PDO::FETCH_ASSOC)):
    $nmCriterio = $registros['NM_CRITERIO'];
endwhile;

$queryUpdateCriterio = "UPDATE [portalcompras].[dbo].[CRITERIO_LICITACAO] SET DT_EXC_CRITERIO = NULL WHERE ID_CRITERIO = $idCriterio";
$queryUpdateCriterio2 = $pdoCAT->query($queryUpdateCriterio);

$_SESSION['msg'] = "Criterio ativado com <strong>sucesso.";

// header("Location: ../../consultarUsuario.php");

$_SESSION['redirecionar'] = '../cadCriterio.php';
$login = $_SESSION['login'];
$tela = 'Criterio';
$acao = 'ATIVADO';
$idEvento = $idCriterio;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
