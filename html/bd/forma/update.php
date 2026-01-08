<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idForma = filter_input(INPUT_GET, 'idForma', FILTER_SANITIZE_SPECIAL_CHARS);
$nmForma = filter_input(INPUT_GET, 'nmForma', FILTER_SANITIZE_SPECIAL_CHARS);

$queryUpdate = "UPDATE [portalcompras].[dbo].[FORMA]
                SET [NM_FORMA] = '$nmForma'
                WHERE [ID_FORMA]=$idForma
                ";

$queryUpdate2 = $pdoCAT->query($queryUpdate);

$_SESSION['msg'] = "Forma atualizada com sucesso.";

$_SESSION['redirecionar'] = '../../administracao.php?aba=formas';
$login = $_SESSION['login'];
$tela = 'Forma';
$acao = 'ATUALIZADA';
$idEvento = $idForma;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
