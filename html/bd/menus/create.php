<?php

session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

// if(isset($_POST["descricao"])) {
//     //vem da tela de cadastro de OCORRENCIA
//     $dscParteCorpo = $_POST["descricao"];
// } 

$nmMenu = filter_input(INPUT_POST, 'nmMenu',   FILTER_SANITIZE_SPECIAL_CHARS) ;
$linkMenu = filter_input(INPUT_POST, 'linkMenu',   FILTER_SANITIZE_SPECIAL_CHARS) ;

// var_dump($linkOrientacoes);

$queryInsert = $pdoCAT->query("INSERT INTO [portalcompras].[dbo].[menu] VALUES ('$nmMenu', '$linkMenu', NULL)");

$querySelectPerfil = "SELECT MAX(ID_MENU) AS ID_MENU FROM MENU";
$querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
    $idMenu = $registros['ID_MENU'];
endwhile;
// var_dump($queryInsert);

$_SESSION['msg'] = "Menu cadastrado com sucesso.";

$_SESSION['redirecionar'] = '../../administracao.php?aba=estrutura';
$login = $_SESSION['login'];
$tela = 'Menu';
$acao = 'CRIADO';
$idEvento = $idMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
