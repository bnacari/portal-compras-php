<?php
include_once 'redirecionar.php';

if (!isset($_SESSION)) {
    session_start();
}

// if ($_SESSION['sucesso'] != 1) {
//     $_SESSION['redirecionar'] = 'login.php';

//     redirecionar($_SESSION['redirecionar']);
// }

$pagina_atual = basename($_SERVER['PHP_SELF']);

// var_dump($pagina_atual);

// Caso o usuário tente acessar qq trecho do sistema sem login realizado, o sistema direciona para a tela de LOGOUT
if (($_SESSION['admin'] != 5 && $_SESSION['admin'] != 4)) {

    if ($pagina_atual != 'cadLicitacao.php') {
        $_SESSION['redirecionar'] = 'index.php';
        redirecionar($_SESSION['redirecionar']);
    } 
}

// if (($_SESSION['admin'] == 4 && $pagina_atual != 'cadLicitacao.php')) {
//     // $_SESSION['msg'] = 'Usuário não possui permissão para esta tela.';

//     $_SESSION['redirecionar'] = 'index.php';
//     redirecionar($_SESSION['redirecionar']);
// }
