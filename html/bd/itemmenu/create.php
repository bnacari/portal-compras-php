<?php

session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include('protectAdmin.php');

$nmItemMenu = filter_input(INPUT_POST, 'nmItemMenu',   FILTER_SANITIZE_SPECIAL_CHARS) ;
$idSubMenu = filter_input(INPUT_POST, 'idSubMenu',   FILTER_SANITIZE_SPECIAL_CHARS) ;
$linkItemMenu = filter_input(INPUT_POST, 'linkItemMenu',   FILTER_SANITIZE_SPECIAL_CHARS) ;

// var_dump($linkOrientacoes);

$queryInsert = $pdoCAT->query("INSERT INTO [portalcompras].[dbo].[itemmenu] VALUES ($idSubMenu,'$nmItemMenu', '$linkItemMenu', NULL)");

// var_dump($queryInsert);
// exit();

$querySelectPerfil = "SELECT MAX(ID_ITEMMENU) AS ID_ITEMMENU FROM ITEMMENU";
$querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
    $idItemMenu = $registros['ID_ITEMMENU'];
endwhile;
// var_dump($queryInsert);

$_SESSION['msg'] = "<p class='center red-text'>".'<strong>ItemMenu</strong> cadastrado com <strong>sucesso</strong>.'."</p>";

$_SESSION['redirecionar'] = '../../cadItemMenu.php';
$login = $_SESSION['login'];
$tela = 'ItemMenu';
$acao = 'CRIADO';
$idEvento = $idItemMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
