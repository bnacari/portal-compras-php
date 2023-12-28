<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include('protectAdmin.php');

$idAtualizacao = filter_input(INPUT_GET, 'idAtualizacao', FILTER_SANITIZE_SPECIAL_CHARS);

// $queryAdmin = "SELECT ID_ADM,EMAIL_ADM FROM ADMINISTRADOR WHERE EMAIL_ADM LIKE '$email' AND STATUS like 'A'";

// $queryDesativar = $pdoCAT->query($queryAdmin);

// while ($registros = $queryDesativar->fetch(PDO::FETCH_ASSOC)) :
//     $existeUsuario = $registros['EMAIL_ADM'];
//     $idUsuario = $registros['ID_ADM'];
// endwhile;

// if (isset($existeUsuario)) {
$queryUpdate = $pdoCAT->query("UPDATE ATUALIZACAO SET DT_EXC_ATUALIZACAO = getdate() WHERE ID_ATUALIZACAO = $idAtualizacao");

$_SESSION['msg'] = "Você não receberá mais atualizações sobre a licitação.";

$_SESSION['redirecionar'] = '../../consultarAtualizacao.php';
$login = $_SESSION['login'];
$tela = 'Atualizacao';
$acao = 'Desativado: ' . $email;
$evento = $idUsuario;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");

// } else {

//     $_SESSION['msg'] = "Usuário <strong>não</strong> é Administrador.";
//     header("Location: ../../consultarUsuario.php");
// }
