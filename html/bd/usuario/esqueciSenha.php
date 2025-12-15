<?php
//arquivo esqueciSenha.php
session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
// include('protectAdmin.php');

$emailUsuario = $_POST['emailEsqueciSenha'];

$_SESSION['redirecionar'] = '../../envio.php?emailUsuario='.$emailUsuario;
$login = $_SESSION['login'];
$tela = 'Login';
$acao = 'Esqueci senha: ' . $emailUsuario;
$idEvento = 0;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
