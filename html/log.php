<?php
session_start();

include_once 'bd/conexao.php';
include_once 'redirecionar.php';

$login = filter_input(INPUT_GET, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
$tela = filter_input(INPUT_GET, 'tela', FILTER_SANITIZE_SPECIAL_CHARS);
$acao = filter_input(INPUT_GET, 'acao', FILTER_SANITIZE_SPECIAL_CHARS);
$idEvento = filter_input(INPUT_GET, 'idEvento', FILTER_SANITIZE_NUMBER_INT);

// Validação de entrada
$login = trim($login ?? '');
$tela = trim($tela ?? '');
$acao = trim($acao ?? '');
$idEvento = intval($idEvento ?? 0);

// Validar tamanhos máximos
if (strlen($login) > 100 || strlen($tela) > 100 || strlen($acao) > 500) {
    // Log inválido, ignorar mas continuar redirecionamento
    $redirecionar = isset($_SESSION['redirecionar']) ? $_SESSION['redirecionar'] : 'index.php';
    redirecionar($redirecionar);
    exit;
}

// Prepared Statement para evitar SQL Injection
try {
    $queryLOG = $pdoCAT->prepare("INSERT INTO auditoria VALUES(:login, GETDATE(), :tela, :acao, :idEvento)");
    $queryLOG->bindParam(':login', $login, PDO::PARAM_STR);
    $queryLOG->bindParam(':tela', $tela, PDO::PARAM_STR);
    $queryLOG->bindParam(':acao', $acao, PDO::PARAM_STR);
    $queryLOG->bindParam(':idEvento', $idEvento, PDO::PARAM_INT);
    $queryLOG->execute();
} catch (PDOException $e) {
    // Log do erro, mas continua o redirecionamento
    error_log('Erro ao registrar log de auditoria: ' . $e->getMessage());
}

$redirecionar = isset($_SESSION['redirecionar']) ? $_SESSION['redirecionar'] : 'index.php';

redirecionar($redirecionar);

exit;
?>