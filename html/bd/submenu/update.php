<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include('protectAdmin.php');

$idSubMenu = $_POST['idSubMenu'];
$nmSubMenu = $_POST['nmSubMenu'];
$linkSubMenu = $_POST['linkSubMenu'];
$idMenu = $_POST['idMenu'];

// var_dump($idPublico);
// var_dump($link);

$queryUpdate = "UPDATE [portalcompras].[dbo].[submenu]
                SET [LINK_SUBMENU]='$linkSubMenu'
                   ,[NM_SUBMENU] = '$nmSubMenu'
                   ,[ID_MENU] = $idMenu
                WHERE [ID_SUBMENU]=$idSubMenu
                ";

// var_dump($queryUpdate);
// exit();

$queryUpdate2 = $pdoCAT->query($queryUpdate);

// var_dump($queryUpdate2);

$_SESSION['msg'] = "<p class='center red-text'>".'<strong>Submenu</strong> atualizado com <strong>sucesso</strong>.'."</p>";

$_SESSION['redirecionar'] = '../../cadSubMenu.php';
$login = $_SESSION['login'];
$tela = 'SubMenu';
$acao = 'SubMenu ' . $idSubMenu . ' ATUALIZADO';
$idEvento = $idSubMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
