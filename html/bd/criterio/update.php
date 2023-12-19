<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';
include('protectAdmin.php');

$idCriterio = filter_input(INPUT_GET, 'idCriterio', FILTER_SANITIZE_SPECIAL_CHARS);
$nmCriterio = filter_input(INPUT_GET, 'nmCriterio', FILTER_SANITIZE_SPECIAL_CHARS);

// var_dump($idCriterio);
// var_dump($nmCriterio);

$queryUpdate = "UPDATE [portalcompras].[dbo].[CRITERIO_LICITACAO]
                SET [NM_CRITERIO]='$nmCriterio'
                WHERE [ID_CRITERIO]=$idCriterio
                ";

// var_dump($queryUpdate);

$queryUpdate2 = $pdoCAT->query($queryUpdate);

// var_dump($queryUpdate2);

$_SESSION['msg'] = "<p class='center red-text'>".'<strong>Criterio</strong> atualizado com <strong>sucesso</strong>.'."</p>";

$_SESSION['redirecionar'] = '../cadCriterio.php';
$login = $_SESSION['login'];
$tela = 'Criterio';
$acao = 'Criterio ' . $nmCriterio . ' ATUALIZADO';
$idEvento = $idCriterio;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
