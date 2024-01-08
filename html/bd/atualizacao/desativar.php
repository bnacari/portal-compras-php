<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protect.php');

$idAtualizacao = filter_input(INPUT_GET, 'idAtualizacao', FILTER_SANITIZE_SPECIAL_CHARS);

$queryUpdate = $pdoCAT->query("UPDATE ATUALIZACAO SET DT_EXC_ATUALIZACAO = getdate() WHERE ID_ATUALIZACAO = $idAtualizacao");

$_SESSION['msg'] = "Você não receberá mais atualizações sobre a licitação.";

$_SESSION['redirecionar'] = '../../consultarAtualizacao.php';
$login = $_SESSION['login'];
$tela = 'Atualizacao';
$acao = 'Desativado';
$evento = $idAtualizacao;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");
