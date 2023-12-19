<?php
session_start();

include_once 'bd/conexao.php';
include_once 'redirecionar.php';

$login = filter_input(INPUT_GET, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
$tela = filter_input(INPUT_GET, 'tela', FILTER_SANITIZE_SPECIAL_CHARS);
$acao = filter_input(INPUT_GET, 'acao', FILTER_SANITIZE_SPECIAL_CHARS);
$idEvento = filter_input(INPUT_GET, 'idEvento', FILTER_SANITIZE_SPECIAL_CHARS);

$queryLOG = $pdoCAT->query("INSERT INTO auditoria VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");

$redirecionar = $_SESSION['redirecionar'];

redirecionar($redirecionar);

exit;
?>