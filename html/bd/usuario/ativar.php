<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protectAdmin.php');

$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_SPECIAL_CHARS);

$queryAdmin = "SELECT ID_ADM,EMAIL_ADM FROM ADMINISTRADOR WHERE EMAIL_ADM LIKE '$email' AND STATUS like 'I'";

$queryDesativar = $pdoCAT->query($queryAdmin);

while ($registros = $queryDesativar->fetch(PDO::FETCH_ASSOC)) :
    $existeUsuario = $registros['EMAIL_ADM'];
    $idUsuario = $registros['ID_ADM'];
endwhile;

if (isset($existeUsuario)) {
    $queryUpdate = $pdoCAT->query("UPDATE ADMINISTRADOR SET STATUS = 'A' WHERE EMAIL_ADM like '$email'");

    $_SESSION['msg'] = "Usuário <strong>ativado</strong> com <strong>sucesso.</strong>";

    $_SESSION['redirecionar'] = '../../consultarUsuario.php';
    $login = $_SESSION['login'];
    $tela = 'Usuario';
    $acao = 'Ativado: '.$email;
    $evento = $idUsuario;
    redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");

} else {

    $_SESSION['msg'] = "Usuário <strong>não</strong> é Administrador.";
    header("Location: ../../consultarUsuario.php");
}
