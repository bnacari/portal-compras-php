<?php
include_once 'redirecionar.php';

if (!isset($_SESSION)) {
    session_start();
}

if ($_SESSION['sucesso'] != 1) {
    $_SESSION['redirecionar'] = 'login.php';
    // $_SESSION['msg'] = 'Usuário não está logado, <a href="login.php" style="color:#fefefe">Clique Aqui</a> para realizar login no sistema.';
    
    // echo "<script>window.location.href = 'login.php';</script>";

    redirecionar($_SESSION['redirecionar']);
}

$pagina_atual = basename($_SERVER['PHP_SELF']);

// var_dump($pagina_atual);

// Caso o usuário tente acessar qq trecho do sistema sem login realizado, o sistema direciona para a tela de LOGOUT
if (($_SESSION['admin'] != 5 && $_SESSION['admin'] != 4) && $_SESSION['sucesso'] != 1) {
    // $_SESSION['msg'] = 'Usuário não possui permissão para esta tela.';

    $_SESSION['redirecionar'] = 'index.php';
    redirecionar($_SESSION['redirecionar']);
}

if (($_SESSION['admin'] == 4 && $pagina_atual != 'cadLicitacao.php') && $_SESSION['sucesso'] != 1) {
    // $_SESSION['msg'] = 'Usuário não possui permissão para esta tela.';

    $_SESSION['redirecionar'] = 'index.php';
    redirecionar($_SESSION['redirecionar']);
}


