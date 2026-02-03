<?php

session_start();
include_once '../conexao.php';

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

// Verifica se o usuário digitou as senhas iguais
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
while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)):
    $email = $registros['EMAIL_ADM'];
endwhile;

// Verifica se o e-mail digitado já existe
if ($email == $emailUsuario) {
    echo "<script>alert('E-mail já cadastrado!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

try {
    // INSERT do novo usuário
    $queryAdmin2 = "INSERT INTO USUARIO (MAT_ADM, NM_ADM, EMAIL_ADM, DT_CADASTRO, STATUS, LGN_CRIADOR, LGN_ADM, ID_PERFIL, SENHA) 
                    VALUES (0, :nmUsuario, :emailUsuario, GETDATE(), 'A', 'externo', :emailUsuario2, 6, :senhaHash)";
    
    $stmt = $pdoCAT->prepare($queryAdmin2);
    $stmt->bindParam(':nmUsuario', $nmUsuario, PDO::PARAM_STR);
    $stmt->bindParam(':emailUsuario', $emailUsuario, PDO::PARAM_STR);
    $stmt->bindParam(':emailUsuario2', $emailUsuario, PDO::PARAM_STR);
    $stmt->bindParam(':senhaHash', $senhaHash, PDO::PARAM_STR);
    $stmt->execute();

    // Registrar no LOG - nomes corretos das colunas
    $loginAuditoria = 'registro_externo';
    $tela = 'Login';
    $acao = 'Usuario externo cadastrado: ' . $nmUsuario;
    
    $queryLOG = "INSERT INTO AUDITORIA (LGN_AUDITORIA, DT_AUDITORIA, TELA_AUDITORIA, ACAO_AUDITORIA, ID_EVENTO) 
                 VALUES (:login, GETDATE(), :tela, :acao, 1)";
    $stmtLog = $pdoCAT->prepare($queryLOG);
    $stmtLog->bindParam(':login', $loginAuditoria, PDO::PARAM_STR);
    $stmtLog->bindParam(':tela', $tela, PDO::PARAM_STR);
    $stmtLog->bindParam(':acao', $acao, PDO::PARAM_STR);
    $stmtLog->execute();

    // Define mensagem de sucesso e redireciona para login
    $_SESSION['msg'] = "Usuário cadastrado com sucesso! Faça login para continuar.";
    header("Location: ../../login.php");
    exit();

} catch (PDOException $e) {
    error_log("Erro ao cadastrar usuário: " . $e->getMessage());
    echo "<script>alert('Erro ao cadastrar usuário. Tente novamente.');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}