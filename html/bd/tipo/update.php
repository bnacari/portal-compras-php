<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';
include('protectAdmin.php');

$idTipo = filter_input(INPUT_GET, 'idTipo', FILTER_SANITIZE_SPECIAL_CHARS);
$nmTipo = filter_input(INPUT_GET, 'nmTipo', FILTER_SANITIZE_SPECIAL_CHARS);

// var_dump($idTipo);
// var_dump($nmTipo);

$queryUpdate = "UPDATE [portalcompras].[dbo].[TIPO_LICITACAO]
                SET [NM_TIPO]='$nmTipo'
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
