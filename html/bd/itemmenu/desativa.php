<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idItemMenu = filter_input(INPUT_GET, 'idItemMenu', FILTER_SANITIZE_NUMBER_INT);

$queryUpdateLINK = "UPDATE [portalcompras].[dbo].[itemmenu] SET DT_EXC_ITEMMENU = getdate() WHERE ID_ITEMMENU = $idItemMenu";

// var_dump($queryUpdateLINK);
// exit();

$queryUpdateLINK2 = $pdoCAT->query($queryUpdateLINK);

$_SESSION['msg'] = "ItemMenu desativado com sucesso.";

// header("Location: ../../consultarUsuario.php");

$_SESSION['redirecionar'] = '../../cadItemMenu.php';
$login = $_SESSION['login'];
$tela = 'ItemMenu';
$acao = 'DESATIVADO';
$idEvento = $idItemMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");