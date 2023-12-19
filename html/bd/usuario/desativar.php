<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include('protectAdmin.php');

$matricula = filter_input(INPUT_GET, 'matricula', FILTER_SANITIZE_NUMBER_INT);

$queryAdmin = "SELECT MAT_ADM FROM ADMINISTRADOR WHERE MAT_ADM = $matricula AND STATUS like 'A'";

$queryDesativar = $pdoCAT->query($queryAdmin);

while($registros = $queryDesativar->fetch(PDO::FETCH_ASSOC)):
    $existeUsuario = $registros['MAT_ADM'];
endwhile; 

if ($existeUsuario != null)
{
    $queryUpdate = $pdoCAT->query("UPDATE ADMINISTRADOR SET STATUS = 'I' WHERE MAT_ADM = $matricula");

    $_SESSION['msg'] = "<p class='center red-text'>".'Usuário <strong>desativado</strong> com <strong>sucesso</strong>.'."</p>";

    header("Location: ../../consultarUsuario.php");

} else {
    
    $_SESSION['msg'] = "<p class='center red-text'>".'Usuário <strong>não</strong> é Administrador .'."</p>";
    
}

// $_SESSION['redirecionar'] = '../consultarUsuario.php';
// $login = $_SESSION['login'];
// $tela = 'Desativa ADM';
// $acao = 'ADM Descadastrado: '.$matricula;
// $evento = 0;
// redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");

