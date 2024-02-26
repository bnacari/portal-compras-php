<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

$nmUsuario = $_POST['nomeUsuarioNovo'];
$senhaUsuario = $_POST['senhaUsuarioNovo'];
$senhaUsuario2 = $_POST['senhaUsuarioNovo2'];
$emailUsuario = $_POST['emailUsuarioNovo'];

$senhaHash = password_hash($senhaUsuario, PASSWORD_DEFAULT);

//verifica se o usuário digitou as senhas iguais
if ($senhaUsuario != $senhaUsuario2) {
    echo "<script>alert('Senhas diferentes!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

$querySelectPerfil = "SELECT * FROM USUARIO WHERE EMAIL_ADM LIKE '$emailUsuario'";
$querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
    $email = $registros['EMAIL_ADM'];
endwhile;

//verifica se o e-mail digitado já existe
if ($email == $emailUsuario) {
    echo "<script>alert('E-mail já cadastrado!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

$queryAdmin2 = "INSERT INTO USUARIO VALUES (00000, '$nmUsuario', '$emailUsuario',  GETDATE(), 'A', 'externo', '$emailUsuario', 6, '$senhaHash')";
$querySelectPerfil2 = $pdoCAT->query($queryAdmin2);

$_SESSION['msg'] = "Usuário cadastrado com sucesso.";

$_SESSION['redirecionar'] = '../../login.php';
$login = $_SESSION['login'];
$tela = 'Login';
$acao = 'Usuário cadastrado: ' . $nmUsuario;
$idEvento = 1;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
