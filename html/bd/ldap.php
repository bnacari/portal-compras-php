<?php

session_start();

include_once '../includes/header.inc.php';
include_once 'conexao.php';
include_once '../redirecionar.php';
// include_once '../api.php';

$login      = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
$senha      = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);
$loginADM   = null;

$ldap_server = 'cesan.com.br';
$dominio = '@cesan.com.br'; //Dominio local ou global
$user = $login . $dominio;
$ldap_porta = '389';
$ldap_pass   = $senha;
$ldapcon = ldap_connect($ldap_server, $ldap_porta) or die('Could not connect to LDAP server.');

if ($ldapcon) {

    $bind = ldap_bind($ldapcon, $user, $ldap_pass);

    // verify binding
    if ($bind) {
        $_SESSION['sucesso'] = 1;

        $_SESSION['login'] = $login;

        $querySelect2 = "SELECT A.ID_PERFIL, P.NM_PERFIL 
                            FROM ADMINISTRADOR A
                            LEFT JOIN PERFIL P ON A.ID_PERFIL = P.ID_PERFIL
                            WHERE LGN_ADM = '$login' AND STATUS = 'A'";
        $querySelect = $pdoCAT->query($querySelect2);

        while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
            $idPerfil = $registros['ID_PERFIL'];
            $nmPerfil = $registros['NM_PERFIL'];
        endwhile;

        $_SESSION['admin'] = $idPerfil;
        $_SESSION['nmPerfil'] = $nmPerfil;

        redirecionar("../../api.php?loginEnvolvido=$login");
    } else {

        $_SESSION['sucesso'] = 0;

        $_SESSION['admin'] = 0;

        $_SESSION['login'] = '';

        $_SESSION['idLogin'] = 0;

        $_SESSION['email'] = '';

        $_SESSION['msg'] = 'Usuário ou Senha inválidos.';

        $_SESSION['redirecionar'] = '../login.php';

        $tela = 'login';
        $acao = 'Erro ao logar';
        $evento = 0;
        redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");
    }
}
