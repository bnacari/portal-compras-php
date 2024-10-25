<?php
include_once 'redirecionar.php';

$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

if (!isset($_SESSION)) {
    session_start();
}

// echo "<script>alert($idLicitacao);</script>;";

// Caso o usuário tente acessar qq trecho do sistema sem permissão, o sistema direciona para a tela de index
if (empty($_SESSION['perfil']))
{
    ?>
    <script>alert('teste');</script>
    <?php
    
    $_SESSION['redirecionar'] = 'index.php';
    redirecionar($_SESSION['redirecionar']);
}