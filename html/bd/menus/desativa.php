<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include('protectAdmin.php');

$idMenu = filter_input(INPUT_GET, 'idMenu', FILTER_SANITIZE_NUMBER_INT);

$queryUpdateLINK = "UPDATE [portalcompras].[dbo].[menu] SET DT_EXC_MENU = getdate() WHERE ID_MENU = $idMenu";

// var_dump($queryUpdateLINK);
// exit();

$queryUpdateLINK2 = $pdoCAT->query($queryUpdateLINK);

$_SESSION['msg'] = "<p class='center red-text'>".'<strong>Menu</strong> desativado com <strong>sucesso</strong>.'."</p>";

// header("Location: ../../consultarUsuario.php");

$_SESSION['redirecionar'] = '../../cadMenu.php';
$login = $_SESSION['login'];
$tela = 'Menu';
$acao = 'Menu ' . $idMenu . ' DESATIVADO';
$idEvento = $idMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");