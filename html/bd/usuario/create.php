<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

// Sanitização e validação de entrada
$nmUsuario = filter_input(INPUT_POST, 'nomeUsuarioNovo', FILTER_SANITIZE_SPECIAL_CHARS);
$senhaUsuario = filter_input(INPUT_POST, 'senhaUsuarioNovo', FILTER_UNSAFE_RAW);
$senhaUsuario2 = filter_input(INPUT_POST, 'senhaUsuarioNovo2', FILTER_UNSAFE_RAW);
$emailUsuario = filter_input(INPUT_POST, 'emailUsuarioNovo', FILTER_SANITIZE_EMAIL);

// Validações de entrada
$nmUsuario = trim($nmUsuario);
$emailUsuario = trim($emailUsuario);

// Validar tamanho máximo
if (strlen($nmUsuario) > 100 || strlen($emailUsuario) > 100 || strlen($senhaUsuario) > 255) {
    echo "<script>alert('Dados de entrada inválidos!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

// Validar formato de email
if (!filter_var($emailUsuario, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('E-mail inválido!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

// Validar senha mínima
if (strlen($senhaUsuario) < 6) {
    echo "<script>alert('A senha deve ter no mínimo 6 caracteres!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

$senhaHash = password_hash($senhaUsuario, PASSWORD_DEFAULT);

//verifica se o usuário digitou as senhas iguais
if ($senhaUsuario != $senhaUsuario2) {
    echo "<script>alert('Senhas diferentes!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

// Prepared Statement para verificar e-mail existente
$querySelectPerfil = "SELECT EMAIL_ADM FROM USUARIO WHERE EMAIL_ADM = :email";
$querySelectPerfil2 = $pdoCAT->prepare($querySelectPerfil);
$querySelectPerfil2->bindParam(':email', $emailUsuario, PDO::PARAM_STR);
$querySelectPerfil2->execute();

$email = null;
while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
    $email = $registros['EMAIL_ADM'];
endwhile;

//verifica se o e-mail digitado já existe
if ($email == $emailUsuario) {
    echo "<script>alert('E-mail já cadastrado!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

// Prepared Statement para INSERT - evita SQL Injection
$queryAdmin2 = "INSERT INTO USUARIO (ID_ADM, NM_ADM, EMAIL_ADM, DT_CADASTRO, STATUS, LGN_ADM, EMAIL_ADM, ID_PERFIL, SENHA) 
                VALUES (00000, :nmUsuario, :emailUsuario, GETDATE(), 'A', 'externo', :emailUsuario2, 6, :senhaHash)";
$stmt = $pdoCAT->prepare($queryAdmin2);
$stmt->bindParam(':nmUsuario', $nmUsuario, PDO::PARAM_STR);
$stmt->bindParam(':emailUsuario', $emailUsuario, PDO::PARAM_STR);
$stmt->bindParam(':emailUsuario2', $emailUsuario, PDO::PARAM_STR);
$stmt->bindParam(':senhaHash', $senhaHash, PDO::PARAM_STR);
$stmt->execute();

$_SESSION['msg'] = "Usuário cadastrado com sucesso.";

$_SESSION['redirecionar'] = '../../login.php';
$login = $_SESSION['login'];
$tela = 'Login';
$acao = 'Usuário cadastrado: ' . $nmUsuario;
$idEvento = 1;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
