<?php
include_once 'redirecionar.php';

$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

if (!isset($_SESSION)) {
    session_start();
}

// echo "<script>alert($idLicitacao);</script>;";

// Caso o usuário tente acessar qq trecho do sistema sem login realizado, o sistema direciona para a tela de LOGOUT
if (empty($_SESSION['perfil']))
{
    $_SESSION['redirecionar'] = 'index.php';
    redirecionar($_SESSION['redirecionar']);
}