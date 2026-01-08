<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idMenu = filter_input(INPUT_GET, 'idMenu', FILTER_SANITIZE_NUMBER_INT);

$queryUpdateLINK = "UPDATE [portalcompras].[dbo].[menu] SET DT_EXC_MENU = NULL WHERE ID_MENU = $idMenu";

$queryUpdateLINK2 = $pdoCAT->query($queryUpdateLINK);

$_SESSION['msg'] = "Menu ativado com sucesso.";

// header("Location: ../../administracao.php?aba=usuarios");

$_SESSION['redirecionar'] = '../../administracao.php?aba=menus';
$login = $_SESSION['login'];
$tela = 'Menu';
$acao = 'ATIVADO';
$idEvento = $idMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");