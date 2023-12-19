<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include('protectAdmin.php');

$matricula = filter_input(INPUT_GET, 'matricula', FILTER_SANITIZE_NUMBER_INT);

$queryAdmin = "SELECT MAT_ADM FROM ADMINISTRADOR WHERE MAT_ADM = $matricula";

$queryDelete = $pdoCAT->query($queryAdmin);

$existeUsuario = 0 ;

while($registros = $queryDelete->fetch(PDO::FETCH_ASSOC)):
    $existeUsuario = $registros['MAT_ADM'];
endwhile; 

if ($existeUsuario != 0)
{
    $queryUpdate = $pdoCAT->query("UPDATE ADMINISTRADOR SET STATUS = 'A' WHERE MAT_ADM = $matricula");
} else {
    $queryInsert = "SELECT [ID]
                    ,[sAMAccountName]
                    ,[initials]
                    ,[department]
                    ,[physicalDeliveryOfficeName]
                    ,[displayName]
                    ,[telephoneNumber]
                    ,[mobile]
                    ,[mail]
                    ,[accountExpires]
                    ,[IsEnabled]
                    ,[objectCategory]
                FROM [ADCache].[dbo].[Users]
                where initials = $matricula";

    $queryInsert2 = $pdoCAT->query($queryInsert);
        
    while($registros = $queryInsert2->fetch(PDO::FETCH_ASSOC)):
        $nome = $registros['displayName'];
        $mail = $registros['mail'];
        $login = $registros['sAMAccountName'];
    endwhile;

    $loginCriador = $_SESSION['login'];

    $queryInsert3 = $pdoCAT->query("INSERT INTO ADMINISTRADOR VALUES ($matricula, '$nome', '$mail', GETDATE(), 'A', '$loginCriador', '$login')");

}

$_SESSION['msg'] = "<p class='center red-text'>".'<strong>Usuário</strong> ativado com <strong>sucesso</strong>.'."</p>";

header("Location: ../../consultarUsuario.php");

// $_SESSION['redirecionar'] = '../consultarUsuario.php';
// $login = $_SESSION['login'];
// $tela = 'Ativa ADM';
// $acao = 'ADM Cadastrado: '.$matricula;
// $evento = 0;
// redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");