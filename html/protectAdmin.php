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

$isAdmin = null;
// Caso o usuário tente acessar qq trecho do sistema sem login realizado, o sistema direciona para a tela de LOGOUT
foreach ($_SESSION['perfil'] as $perfil) {
    if ($perfil['idPerfil'] == 9) {
        $isAdmin = 1;
    }
}

if (!isset($isAdmin)){
    
    ?>
    <script>alert('teste');</script>
    <?php
    
    $_SESSION['redirecionar'] = 'index.php';
    redirecionar($_SESSION['redirecionar']);
    exit();
}