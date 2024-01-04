<?php

session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include('protectAdmin.php');

// if(isset($_POST["descricao"])) {
//     //vem da tela de cadastro de OCORRENCIA
//     $dscParteCorpo = $_POST["descricao"];
// } 

$nmSubMenu = filter_input(INPUT_POST, 'nmSubMenu',   FILTER_SANITIZE_SPECIAL_CHARS) ;
$idMenu = filter_input(INPUT_POST, 'idMenu',   FILTER_SANITIZE_SPECIAL_CHARS) ;
$linkSubMenu = filter_input(INPUT_POST, 'linkSubMenu',   FILTER_SANITIZE_SPECIAL_CHARS) ;

// var_dump($linkOrientacoes);

$queryInsert = $pdoCAT->query("INSERT INTO [portalcompras].[dbo].[submenu] VALUES ($idMenu,'$nmSubMenu', '$linkSubMenu', NULL)");

$querySelectPerfil = "SELECT MAX(ID_SUBMENU) AS ID_SUBMENU FROM SUBMENU";
$querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
    $idSubMenu = $registros['ID_SUBMENU'];
endwhile;
// var_dump($queryInsert);

$_SESSION['msg'] = "<p class='center red-text'>".'<strong>SubMenu</strong> cadastrado com <strong>sucesso</strong>.'."</p>";

$_SESSION['redirecionar'] = '../../cadSubMenu.php';
$login = $_SESSION['login'];
$tela = 'SubMenu';
$acao = 'CRIADO';
$idEvento = $idSubMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
