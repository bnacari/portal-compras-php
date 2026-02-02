<?php
//arquivo esqueciSenha.php
session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
// include('protectAdmin.php');

// Sanitização e validação de entrada
$emailUsuario = filter_input(INPUT_POST, 'emailEsqueciSenha', FILTER_SANITIZE_EMAIL);
$emailUsuario = trim($emailUsuario);

// Validar formato de email
if (!filter_var($emailUsuario, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['msg'] = 'E-mail inválido.';
    header('Location: ../../login.php');
    exit();
}

// Validar tamanho máximo
if (strlen($emailUsuario) > 100) {
    $_SESSION['msg'] = 'E-mail inválido.';
    header('Location: ../../login.php');
    exit();
}

// Usar urlencode para passar o email de forma segura na URL
$_SESSION['redirecionar'] = '../../envio.php?emailUsuario=' . urlencode($emailUsuario);
$login = isset($_SESSION['login']) ? $_SESSION['login'] : '';
$tela = 'Login';
$acao = 'Esqueci senha: ' . $emailUsuario;
$idEvento = 0;
redirecionar("../../log.php?login=" . urlencode($login) . "&tela=" . urlencode($tela) . "&acao=" . urlencode($acao) . "&idEvento=$idEvento");
