<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

$queryDeleteLicitacao = "UPDATE [portalcompras].[dbo].[LICITACAO] SET DT_EXC_LICITACAO = getdate() WHERE ID_LICITACAO = $idLicitacao";
$queryDeleteLicitacao2 = $pdoCAT->query($queryDeleteLicitacao);

$queryDeleteDETLicitacao = "UPDATE [portalcompras].[dbo].[DETALHE_LICITACAO] SET DT_EXC_LICITACAO = getdate() WHERE ID_LICITACAO = $idLicitacao";
$queryDeleteDETLicitacao2 = $pdoCAT->query($queryDeleteDETLicitacao);

$_SESSION['msg'] = "Licitação excluída com sucesso.";

// header("Location: ../../consultarUsuario.php");

$_SESSION['redirecionar'] = '../consultarLicitacao.php';
$login = $_SESSION['login'];
$tela = 'Licitação';
$acao = 'Excluída';
$idEvento = $idLicitacao;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
