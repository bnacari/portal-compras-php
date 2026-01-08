<?php

session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$nmCriterio = filter_input(INPUT_POST, 'nmCriterio', FILTER_SANITIZE_SPECIAL_CHARS);

$login = $_SESSION['login'];

$queryInsert = $pdoCAT->query("INSERT INTO [portalcompras].[dbo].[CRITERIO_LICITACAO] VALUES ('$nmCriterio', NULL, '$login')");

$querySelectCriterio = "SELECT MAX(ID_CRITERIO) AS ID_CRITERIO FROM CRITERIO_LICITACAO";
$querySelectCriterio2 = $pdoCAT->query($querySelectCriterio);
while ($registros = $querySelectCriterio2->fetch(PDO::FETCH_ASSOC)) :
    $idCriterio = $registros['ID_CRITERIO'];
endwhile;

$_SESSION['msg'] = "Criterio cadastrado com sucesso.";

$_SESSION['redirecionar'] = '../administracao.php?aba=criterios';
$login = $_SESSION['login'];
$tela = 'Criterio';
$acao = 'CRIADO';
$idEvento = $idCriterio;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
