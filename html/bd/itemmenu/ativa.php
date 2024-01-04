<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include('protectAdmin.php');

$idItemMenu = filter_input(INPUT_GET, 'idItemMenu', FILTER_SANITIZE_NUMBER_INT);

$queryUpdateLINK = "UPDATE [portalcompras].[dbo].[itemmenu] SET DT_EXC_ITEMMENU = NULL WHERE ID_ITEMMENU = $idItemMenu";

$queryUpdateLINK2 = $pdoCAT->query($queryUpdateLINK);

$_SESSION['msg'] = "<p class='center red-text'>".'<strong>ItemMenu</strong> ativado com <strong>sucesso</strong>.'."</p>";

// header("Location: ../../consultarUsuario.php");

$_SESSION['redirecionar'] = '../../cadItemMenu.php';
$login = $_SESSION['login'];
$tela = 'ItemMenu';
$acao = 'ATIVADO';
$idEvento = $idItemMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");