<?php

if (!isset($_SESSION)) {
    session_start();
}

// Caso o usuário tente acessar qq trecho do sistema sem login realizado, o sistema direciona para a tela de LOGOUT
if ($_SESSION['admin'] != 5) {
    // $_SESSION['msg'] = 'Usuário não possui permissão para esta tela.';

    $_SESSION['redirecionar'] = 'index.php';
    redirecionar($_SESSION['redirecionar']);
}
