<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protectAdmin.php');

$idTipo = filter_input(INPUT_GET, 'idTipo', FILTER_SANITIZE_SPECIAL_CHARS);
$nmTipo = filter_input(INPUT_GET, 'nmTipo', FILTER_SANITIZE_SPECIAL_CHARS);
$sglTipo = filter_input(INPUT_GET, 'sglTipo', FILTER_SANITIZE_SPECIAL_CHARS);

// var_dump($idTipo);
// var_dump($sglTipo);

$queryUpdate = "UPDATE [portalcompras].[dbo].[TIPO_LICITACAO]
                SET [NM_TIPO]='$nmTipo', [SGL_TIPO] = '$sglTipo'
                WHERE [ID_TIPO]=$idTipo
                ";

// var_dump($queryUpdate);

$queryUpdate2 = $pdoCAT->query($queryUpdate);

// var_dump($queryUpdate2);

$_SESSION['msg'] = "Tipo atualizado com sucesso.";

$_SESSION['redirecionar'] = '../cadTipo.php';
$login = $_SESSION['login'];
$tela = 'Tipo';
$acao = 'ATUALIZADO';
$idEvento = $idTipo;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
