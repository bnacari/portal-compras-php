<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protectAdmin.php');

$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_SPECIAL_CHARS);

$queryAdmin = "SELECT ID_ADM,EMAIL_ADM FROM USUARIO WHERE EMAIL_ADM LIKE '$email' AND STATUS like 'I'";

$queryDesativar = $pdoCAT->query($queryAdmin);

while ($registros = $queryDesativar->fetch(PDO::FETCH_ASSOC)) :
    $existeUsuario = $registros['EMAIL_ADM'];
    $idUsuario = $registros['ID_ADM'];
endwhile;

if (isset($existeUsuario)) {
    $queryUpdate = $pdoCAT->query("UPDATE USUARIO SET STATUS = 'A' WHERE EMAIL_ADM like '$email'");

    $_SESSION['msg'] = "Usuário ativado com sucesso.";

    $_SESSION['redirecionar'] = '../../administracao.php?aba=usuarios';
    $login = $_SESSION['login'];
    $tela = 'Usuario';
    $acao = 'Ativado: '.$email;
    $evento = $idUsuario;
    redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");

} else {

    $_SESSION['msg'] = "Usuário <strong>não</strong> é Administrador.";
    header("Location: ../../administracao.php?aba=usuarios");
}
