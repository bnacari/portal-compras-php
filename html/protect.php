<?php

if(!isset($_SESSION)) {
    session_start();
}
// Caso o usuário tente acessar qq trecho do sistema sem login realizado, o sistema direciona para a tela de LOGOUT
if($_SESSION['sucesso'] != 1) {
    $_SESSION['msg'] = 'Usuário não está logado, <a href="login.php" style="color:#fefefe">Clique Aqui</a> para realizar login no sistema.';
    header('Location: login.php');
}

?>