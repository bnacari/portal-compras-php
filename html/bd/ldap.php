<?php

session_start();

include_once '../includes/header.inc.php';
include_once 'conexao.php';
include_once '../redirecionar.php';

// include_once '../api.php';

$login      = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
$senha      = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);
$loginADM   = null;

if (strpos($login, '@') !== false) {

    $querySelect2 = "SELECT * FROM USUARIO WHERE EMAIL_ADM LIKE '$login' AND STATUS LIKE 'A'";
    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
        $idUsuario = $registros['ID_ADM'];
        $emailUsuario = $registros['EMAIL_ADM'];
        $nmUsuario = $registros['NM_ADM'];
        $senhaBanco  = $registros['SENHA'];
    endwhile;

    // var_dump($senha);
    // var_dump($senhaBanco);
    // exit();

    // Comparar a senha calculada com a senha armazenada no banco de dados
    if (password_verify($senha, $senhaBanco)) {

        $_SESSION['sucesso'] = 1;
        $_SESSION['login'] = $login;
        $_SESSION['perfil'] = 0;
        $_SESSION['email'] = $emailUsuario;
        $_SESSION['idUsuario'] = $idUsuario;

        $_SESSION['redirecionar'] = '../index.php';
        $login = $_SESSION['login'];
        $tela = 'Login';
        $acao = 'Login';
        $idEvento = 0;
        redirecionar("../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");

        exit();
    }
}

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

        $querySelect2 = "SELECT U.ID_ADM, U.EMAIL_ADM, PU.ID_TIPO_LICITACAO, TL.NM_TIPO
                            FROM USUARIO U 
                            LEFT JOIN PERFIL_USUARIO PU ON U.ID_ADM = PU.ID_USUARIO
                            LEFT JOIN TIPO_LICITACAO TL ON TL.ID_TIPO = PU.ID_TIPO_LICITACAO 
                         WHERE U.LGN_ADM = '$login' AND U.STATUS = 'A'";

        $querySelect = $pdoCAT->query($querySelect2);

        $perfilUsuario = array();
        $emailUsuario = null;
        $idUsuario = null;

        while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
            $idUsuario = $registros['ID_ADM'];
            $emailUsuario = $registros['EMAIL_ADM'];
            // Adicione cada ID_TIPO_LICITACAO ao array $perfilUsuario
            $perfilUsuario[] = array(
                'idPerfil' => $registros['ID_TIPO_LICITACAO'],
                'nmPerfil' => $registros['NM_TIPO']
            );
        }

        $_SESSION['perfil'] = $perfilUsuario;
        $_SESSION['email'] = $emailUsuario;
        $_SESSION['idUsuario'] = $idUsuario;

        foreach ($_SESSION['perfil'] as $perfil) {
            if ($perfil['idPerfil'] == 9) {
                $_SESSION['isAdmin'] = 1;
            }
        }

        $_SESSION['redirecionar'] = '../index.php';
        $login = $_SESSION['login'];
        $tela = 'Login';
        $acao = 'Login';
        $idEvento = 0;
        redirecionar("../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
    } else {

        $_SESSION['sucesso'] = 0;

        $_SESSION['perfil'] = 0;

        $_SESSION['login'] = '';

        $_SESSION['idLogin'] = 0;

        $_SESSION['email'] = '';

        $_SESSION['msg'] = 'Usuário ou Senha inválidos.';

        $_SESSION['redirecionar'] = '../login.php';

        $tela = 'Login';
        $acao = 'Erro ao logar';
        $evento = 0;
        redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");
    }
}
