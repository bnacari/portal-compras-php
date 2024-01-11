<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idMenu = filter_input(INPUT_GET, 'idMenu', FILTER_SANITIZE_NUMBER_INT);

$queryUpdateLINK = "UPDATE [portalcompras].[dbo].[menu] SET DT_EXC_MENU = getdate() WHERE ID_MENU = $idMenu";

// var_dump($queryUpdateLINK);
// exit();

$queryUpdateLINK2 = $pdoCAT->query($queryUpdateLINK);

$_SESSION['msg'] = "Menu desativado com sucesso.";

// header("Location: ../../consultarUsuario.php");

$_SESSION['redirecionar'] = '../../cadMenu.php';
$login = $_SESSION['login'];
$tela = 'Menu';
$acao = 'DESATIVADO';
$idEvento = $idMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");