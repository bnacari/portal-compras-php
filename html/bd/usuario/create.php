<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include('protectAdmin.php');

$nmUsuario = $_POST['nomeUsuarioNovo'];
$senhaUsuario = $_POST['senhaUsuarioNovo'];
$senhaUsuario2 = $_POST['senhaUsuarioNovo2'];
$emailUsuario = $_POST['emailUsuarioNovo'];

$salt = bin2hex(random_bytes(16)); // Gera um salt de 16 bytes e converte para hexadecimal
$senhaComSalt = $senhaUsuario . $salt;
// Use uma função de hash segura, como bcrypt ou SHA-256
$senhaHash = hash('sha256', $senhaComSalt);

// var_dump($salt);
// var_dump($senhaComSalt);
// var_dump($senhaHash);
// exit();

//verifica se o usuário digitou as senhas iguais
if ($senhaUsuario != $senhaUsuario2) {
    echo "<script>alert('Senhas diferentes!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

$querySelectPerfil = "SELECT * FROM ADMINISTRADOR WHERE EMAIL_ADM LIKE '$emailUsuario'";
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

// $queryAdmin2 = "INSERT INTO ADMINISTRADOR (MAT_ADM, NM_ADM, EMAIL_ADM, DT_CADASTRO, STATUS, LGN_CRIADOR, LGN_ADM, ID_PERFIL, SENHA) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
// $stmt = $pdoCAT->prepare($queryAdmin2);
// $stmt->execute([00000, $nmUsuario, $email,  GETDATE(), 'A', $login, 'externo', 6, "$senhaUsuario"]);

$queryAdmin2 = "INSERT INTO ADMINISTRADOR VALUES (00000, '$nmUsuario', '$emailUsuario',  GETDATE(), 'A', 'externo', '$emailUsuario', 6, '$senhaUsuario')";
$querySelectPerfil2 = $pdoCAT->query($queryAdmin2);

$_SESSION['msg'] = "<p class='center red-text'>" . 'Usuário <strong>cadastrado</strong> com <strong>sucesso</strong>.' . "</p>";

$_SESSION['redirecionar'] = '../login.php';
$login = $_SESSION['login'];
$tela = 'Login';
$acao = 'Usuário cadastrado: ' . $nmUsuario;
$idEvento = 1;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
