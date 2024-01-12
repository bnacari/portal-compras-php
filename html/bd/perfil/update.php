<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protectAdmin.php');

$idPerfil = filter_input(INPUT_GET, 'idPerfil', FILTER_SANITIZE_SPECIAL_CHARS);
$nmPerfil = filter_input(INPUT_GET, 'nmPerfil', FILTER_SANITIZE_SPECIAL_CHARS);

// var_dump($idPerfil);
// var_dump($nmPerfil);

$queryUpdate = "UPDATE [portalcompras].[dbo].[PERFIL]
                SET [NM_PERFIL]='$nmPerfil'
                WHERE [ID_PERFIL]=$idPerfil
                ";

// var_dump($queryUpdate);

$queryUpdate2 = $pdoCAT->query($queryUpdate);

// var_dump($queryUpdate2);

$_SESSION['msg'] = "Perfil atualizado com sucesso.";

$_SESSION['redirecionar'] = '../cadPerfil.php';
$login = $_SESSION['login'];
$tela = 'Perfil';
$acao = 'ATUALIZADO';
$idEvento = $idPerfil;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
