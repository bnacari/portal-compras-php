<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include('protect.php');

$senhaAtual = $_POST['senhaAtual'];
$senhaNova = $_POST['senhaNova'];
$senhaNova2 = $_POST['senhaNova2'];

// var_dump();

if ($_SESSION['sucesso'] == 1) {
    $emailUsuario = $_SESSION['login'];
} else {
    $_SESSION['msg'] = "E-mail NÃO cadastrado.";
    echo "<script>location.href='login.php';</script>";
    exit();
}

$querySelectPerfil = "SELECT * FROM ADMINISTRADOR WHERE EMAIL_ADM LIKE '$emailUsuario'";
$querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
    $nmUsuario = $registros['NM_ADM'];
    $email = $registros['EMAIL_ADM'];
    $senha = $registros['SENHA'];
endwhile;

if (!isset($email)) {
    $_SESSION['msg'] = "E-mail NÃO cadastrado.";
    echo "<script>location.href='login.php';</script>";
    exit();
} else if ($senha != $senhaAtual) {
    $_SESSION['msg'] = "Senha atual não confere.";
    echo "<script>location.href='../../trocaSenhaUsuario.php';</script>";
    exit();
} else if ($senhaNova != $senhaNova2) {
    $_SESSION['msg'] = "As senhas não são iguais.";
    echo "<script>location.href='../../trocaSenhaUsuario.php';</script>";
    exit();
}

$queryAdmin2 = "UPDATE ADMINISTRADOR SET SENHA = '$senhaNova' WHERE EMAIL_ADM LIKE '$emailUsuario'";

$queryDesativar = $pdoCAT->query($queryAdmin2);

$_SESSION['msg'] = "Senha atualizada com <strong>sucesso</strong>.";

$_SESSION['redirecionar'] = '../trocaSenhaUsuario.php';
$login = $_SESSION['login'];
$tela = 'Troca Senha';
$acao = 'Senha atualizada';
$idEvento = 0;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
