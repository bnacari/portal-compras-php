<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idItemMenu = $_POST['idItemMenu'];
$nmItemMenu = $_POST['nmItemMenu'];
$linkItemMenu = $_POST['linkItemMenu'];
$idSubMenu = $_POST['idSubMenu'];

// var_dump($idPublico);
// var_dump($link);

$queryUpdate = "UPDATE [portalcompras].[dbo].[itemmenu]
                SET [LINK_ITEMMENU]='$linkItemMenu'
                   ,[NM_ITEMMENU] = '$nmItemMenu'
                   ,[ID_SUBMENU] = $idSubMenu
                WHERE [ID_ITEMMENU]=$idItemMenu
                ";

// var_dump($queryUpdate);
// exit();

$queryUpdate2 = $pdoCAT->query($queryUpdate);

// var_dump($queryUpdate2);

$_SESSION['msg'] = "ItemMenu atualizado com sucesso.";

$_SESSION['redirecionar'] = '../../administracao.php?aba=estrutura';
$login = $_SESSION['login'];
$tela = 'ItemMenu';
$acao = 'ATUALIZADO';
$idEvento = $idItemMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
