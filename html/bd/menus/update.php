<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include('protectAdmin.php');

$idMenu = filter_input(INPUT_GET, 'idMenu', FILTER_SANITIZE_SPECIAL_CHARS);
$nmMenu = filter_input(INPUT_GET, 'nmMenu', FILTER_SANITIZE_SPECIAL_CHARS);
$linkMenu = filter_input(INPUT_GET, 'linkMenu', FILTER_SANITIZE_SPECIAL_CHARS);

// var_dump($idPublico);
// var_dump($link);

$queryUpdate = "UPDATE [portalcompras].[dbo].[menu]
                SET [LINK_MENU]='$linkMenu'
                   ,[NM_MENU] = '$nmMenu'
                WHERE [ID_MENU]=$idMenu
                ";

// var_dump($queryUpdate);
// exit();

$queryUpdate2 = $pdoCAT->query($queryUpdate);

// var_dump($queryUpdate2);

$_SESSION['msg'] = "<p class='center red-text'>".'<strong>Menu</strong> atualizado com <strong>sucesso</strong>.'."</p>";


$_SESSION['redirecionar'] = '../../cadMenu.php';
$login = $_SESSION['login'];
$tela = 'Menu';
$acao = 'ATUALIZADO';
$idEvento = $idMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
