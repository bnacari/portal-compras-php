<?php

session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$nmPerfil = filter_input(INPUT_POST, 'nmPerfil', FILTER_SANITIZE_SPECIAL_CHARS);

$login = $_SESSION['login'];

$queryInsert = $pdoCAT->query("INSERT INTO [portalcompras].[dbo].[PERFIL] VALUES ('$nmPerfil', NULL, '$login')");

$querySelectPerfil = "SELECT MAX(ID_PERFIL) AS ID_PERFIL FROM PERFIL";
$querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
    $idPerfil = $registros['ID_PERFIL'];
endwhile;

$_SESSION['msg'] = "Perfil cadastrado com sucesso.";

$_SESSION['redirecionar'] = '../administracao.php?aba=perfis';
$login = $_SESSION['login'];
$tela = 'Perfil';
$acao = 'CRIADO';
$idEvento = $idPerfil;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
