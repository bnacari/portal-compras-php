<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idForma = filter_input(INPUT_GET, 'idForma', FILTER_SANITIZE_NUMBER_INT);

$queryUpdateLINK = "UPDATE [portalcompras].[dbo].[forma] SET DT_EXC_FORMA = NULL WHERE ID_FORMA = $idForma";

$queryUpdateLINK2 = $pdoCAT->query($queryUpdateLINK);

$_SESSION['msg'] = "<p class='center red-text'>".'<strong>Forma</strong> ativada com <strong>sucesso</strong>.'."</p>";

$_SESSION['redirecionar'] = '../../cadForma.php';
$login = $_SESSION['login'];
$tela = 'Forma';
$acao = 'ATIVADO';
$idEvento = $idForma;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");