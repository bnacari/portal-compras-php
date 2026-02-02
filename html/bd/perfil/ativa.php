<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idPerfil = filter_input(INPUT_GET, 'idPerfil', FILTER_SANITIZE_NUMBER_INT);

$querySelectPerfil = "SELECT NM_PERFIL FROM PERFIL WHERE ID_PERFIL = $idPerfil";
$querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
while($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)):
    $nmPerfil = $registros['NM_PERFIL'];
endwhile;

$queryUpdatePerfil = "UPDATE [portalcompras].[dbo].[PERFIL] SET DT_EXC_PERFIL = NULL WHERE ID_PERFIL = $idPerfil";
$queryUpdatePerfil2 = $pdoCAT->query($queryUpdatePerfil);

$_SESSION['msg'] = "Perfil ativado com sucesso.";

// header("Location: ../../administracao.php?aba=usuarios");

$_SESSION['redirecionar'] = '../administracao.php?aba=perfis';
$login = $_SESSION['login'];
$tela = 'Perfil';
$acao = 'ATIVADO';
$idEvento = $idPerfil;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
