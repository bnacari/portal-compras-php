<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protectAdmin.php');

$matricula = $_POST['matricula'];
$idPerfil = $_POST['perfilUsuario'];
$email = $_POST['email'];

// var_dump($matricula);
// var_dump($idPerfil);
// var_dump($email);
// exit();

$querySelectPerfil = "SELECT NM_PERFIL FROM PERFIL WHERE ID_PERFIL = $idPerfil";
$querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
    $nmPerfil = $registros['NM_PERFIL'];
endwhile;

if (isset($matricula)) {
    $queryAdmin = "SELECT MAT_ADM, ID_ADM FROM ADMINISTRADOR WHERE MAT_ADM = $matricula AND STATUS like 'A'";
} else {
    $queryAdmin = "SELECT EMAIL_ADM, ID_ADM FROM ADMINISTRADOR WHERE EMAIL_ADM like '$email' AND STATUS like 'A'";
}

$queryDesativar = $pdoCAT->query($queryAdmin);

while ($registros = $queryDesativar->fetch(PDO::FETCH_ASSOC)) :
    if ($registros['MAT_ADM'] != 0) {
        $existeUsuario = $registros['MAT_ADM'];
    } else {
        $existeUsuario = $registros['EMAIL_ADM'];
    }
    $idUsuario = $registros['ID_ADM'];

endwhile;

if (isset($matricula)) {
    $queryAdmin2 = "UPDATE ADMINISTRADOR SET ID_PERFIL = $idPerfil WHERE MAT_ADM = '$matricula'";
} else {
    $queryAdmin2 = "UPDATE ADMINISTRADOR SET ID_PERFIL = $idPerfil WHERE EMAIL_ADM LIKE '$email'";
}
    $queryDesativar = $pdoCAT->query($queryAdmin2);

    $_SESSION['msg'] = "<p class='center red-text'>" . 'Usuário <strong>alterado</strong> com <strong>sucesso</strong>.' . "</p>";

    // header("Location: ../../consultarUsuario.php");


$_SESSION['redirecionar'] = '../consultarUsuario.php';
$login = $_SESSION['login'];
$tela = 'Usuário';
$acao = 'Perfil atualizado para ' . $nmPerfil;
$idEvento = $idUsuario;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
