<?php

session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$nmTipo = filter_input(INPUT_POST, 'nmTipo', FILTER_SANITIZE_SPECIAL_CHARS);

$login = $_SESSION['login'];

$queryInsert = $pdoCAT->query("INSERT INTO [portalcompras].[dbo].[TIPO_LICITACAO] VALUES ('$nmTipo', NULL, '$login')");

$querySelectTipo = "SELECT MAX(ID_TIPO) AS ID_TIPO FROM TIPO_LICITACAO";
$querySelectTipo2 = $pdoCAT->query($querySelectTipo);
while ($registros = $querySelectTipo2->fetch(PDO::FETCH_ASSOC)) :
    $idTipo = $registros['ID_TIPO'];
endwhile;

$_SESSION['msg'] = "Tipo cadastrado com sucesso.";

$_SESSION['redirecionar'] = '../cadTipo.php';
$login = $_SESSION['login'];
$tela = 'Tipo';
$acao = 'CRIADO';
$idEvento = $idTipo;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
