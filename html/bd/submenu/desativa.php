<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idSubMenu = filter_input(INPUT_GET, 'idSubMenu', FILTER_SANITIZE_NUMBER_INT);

$queryUpdateLINK = "UPDATE [portalcompras].[dbo].[submenu] SET DT_EXC_SUBMENU = getdate() WHERE ID_SUBMENU = $idSubMenu";

// var_dump($queryUpdateLINK);
// exit();

$queryUpdateLINK2 = $pdoCAT->query($queryUpdateLINK);

$_SESSION['msg'] = "Submenu desativado com sucesso.";

// header("Location: ../../administracao.php?aba=usuarios");

$_SESSION['redirecionar'] = '../../administracao.php?aba=estrutura';
$login = $_SESSION['login'];
$tela = 'SubMenu';
$acao = 'DESATIVADO';
$idEvento = $idSubMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");