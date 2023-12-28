<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';
include('protectAdmin.php');

$pattern = "/^\d{4}-\d{2}-\d{2}$/";
$patternTime = "/^\d{2}:\d{2}$/";

$login = $_SESSION['login'];
$emailUsuario = $_SESSION['email'];
$idUsuario = $_SESSION['idUsuario'];

$_SESSION['msg'] = '';

// $idLicitacao = $_POST['idLicitacao'];
$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

// ATUALIZA TABELA LICITAÇÃO
$queryUpdateLicitacao = "INSERT INTO [portalcompras].[dbo].ATUALIZACAO VALUES ($idUsuario, '$emailUsuario', $idLicitacao, NULL)";
// var_dump($queryUpdateLicitacao);
// exit();
$queryUpdateLici2 = $pdoCAT->query($queryUpdateLicitacao);

$_SESSION['msg'] = "Usuário " . $emailUsuario . " receberá atualizações.";

$_SESSION['redirecionar'] = '../../viewLicitacao.php?idLicitacao='.$idLicitacao;
$login = $_SESSION['login'];
$tela = 'Licitação';
$acao = 'Receber Atualização';
$idEvento = $idLicitacao;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
