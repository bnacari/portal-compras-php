<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include('protectAdmin.php');

$matricula = $_POST['matricula'];
$idPerfil = $_POST['perfilUsuario'];

$querySelectPerfil = "SELECT NM_PERFIL FROM PERFIL WHERE ID_PERFIL = $idPerfil";
$querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
while($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)):
    $nmPerfil = $registros['NM_PERFIL'];
endwhile;
// var_dump($perfilUsuario);

$queryAdmin = "SELECT MAT_ADM FROM ADMINISTRADOR WHERE MAT_ADM = $matricula AND STATUS like 'A'";
// var_dump($queryAdmin);
// exit();
$queryDesativar = $pdoCAT->query($queryAdmin);

while ($registros = $queryDesativar->fetch(PDO::FETCH_ASSOC)) :
    $existeUsuario = $registros['MAT_ADM'];
endwhile;

if ($existeUsuario != null) {
    $queryAdmin2 = "UPDATE ADMINISTRADOR SET ID_PERFIL = $idPerfil WHERE MAT_ADM = $matricula";
    // var_dump($queryAdmin2);
    // exit();
    $queryDesativar = $pdoCAT->query($queryAdmin2);

    $_SESSION['msg'] = "<p class='center red-text'>" . 'Usuário <strong>alterado</strong> com <strong>sucesso</strong>.' . "</p>";

    // header("Location: ../../consultarUsuario.php");
}

$_SESSION['redirecionar'] = '../consultarUsuario.php';
$login = $_SESSION['login'];
$tela = 'Usuário';
$acao = 'Perfil atualizado para ' . $nmPerfil;
$idEvento = $matricula;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
