<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idTipo = filter_input(INPUT_GET, 'idTipo', FILTER_SANITIZE_NUMBER_INT);

$querySelectTipo = "SELECT NM_TIPO FROM TIPO_LICITACAO WHERE ID_TIPO = $idTipo";
$querySelectTipo2 = $pdoCAT->query($querySelectTipo);
while($registros = $querySelectTipo2->fetch(PDO::FETCH_ASSOC)):
    $nmTipo = $registros['NM_TIPO'];
endwhile;

$queryUpdateTipo = "UPDATE [portalcompras].[dbo].[TIPO_LICITACAO] SET DT_EXC_TIPO = GETDATE() WHERE ID_TIPO = $idTipo";
$queryUpdateTipo2 = $pdoCAT->query($queryUpdateTipo);

$_SESSION['msg'] = "Tipo desativado com sucesso.";

// header("Location: ../../administracao.php?aba=usuarios");

$_SESSION['redirecionar'] = '../administracao.php?aba=tipos';
$login = $_SESSION['login'];
$tela = 'Tipo';
$acao = 'DESATIVADO';
$idEvento = $idTipo;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
