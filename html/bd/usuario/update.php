<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protectAdmin.php');

$matricula = $_POST['matricula'];
$idPerfil = $_POST['perfilUsuario'];
$email = $_POST['email'];
$idUsuario = $_POST['idUsuario'];

// var_dump($idUsuario);
// var_dump($idPerfil);
// var_dump($email);
// exit();


if (isset($_POST['perfilUsuario']) && is_array($_POST['perfilUsuario'])) {
    // Recupere o array de valores selecionados
    $perfilUsuarioSelecionadas = $_POST['perfilUsuario'];
    // var_dump($perfilUsuarioSelecionadas);
    // exit();
    $queryInsertidPerfil = $pdoCAT->query("DELETE FROM [PERFIL_USUARIO] WHERE ID_USUARIO = $idUsuario");
    // echo "<br>";

    foreach ($perfilUsuarioSelecionadas as $idPerfil) {
        $queryInsertidPerfil = $pdoCAT->query("INSERT INTO [PERFIL_USUARIO] VALUES ($idUsuario, $idPerfil)");
        // echo "ID perfil: " . $idPerfil . "<br>";
        // echo "matricula: " . $_SESSION['idUsuario'] . "<br>";
    }


    $querySelectPerfil = "SELECT NM_PERFIL FROM PERFIL WHERE ID_PERFIL = $idPerfil";
    $querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
    while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
        $nmPerfil = $registros['NM_PERFIL'];
    endwhile;
} else {
    $queryInsertidPerfil = $pdoCAT->query("DELETE FROM [PERFIL_USUARIO] WHERE ID_USUARIO = $idUsuario");
    $nmPerfil = '';
}
// exit();


if (isset($matricula)) {
    $queryAdmin = "SELECT MAT_ADM, ID_ADM FROM USUARIO WHERE MAT_ADM = $matricula AND STATUS like 'A'";
} else {
    $queryAdmin = "SELECT EMAIL_ADM, ID_ADM FROM USUARIO WHERE EMAIL_ADM like '$email' AND STATUS like 'A'";
}

$queryDesativar = $pdoCAT->query($queryAdmin);

while ($registros = $queryDesativar->fetch(PDO::FETCH_ASSOC)) :
    if ($registros['MAT_ADM'] != 0) {
        $existeUsuario = $registros['MAT_ADM'];
    } else {
        $existeUsuario = $registros['EMAIL_ADM'];
    }
    $idUsuario = $registros['ID_ADM'];

endwhile;



if (isset($matricula)) {
    $queryAdmin2 = "UPDATE USUARIO SET ID_PERFIL = $idPerfil WHERE MAT_ADM = '$matricula'";
} else {
    $queryAdmin2 = "UPDATE USUARIO SET ID_PERFIL = $idPerfil WHERE EMAIL_ADM LIKE '$email'";
}
$queryDesativar = $pdoCAT->query($queryAdmin2);

$_SESSION['msg'] = "Usuário atualizado com sucesso.";

// header("Location: ../../consultarUsuario.php");


$_SESSION['redirecionar'] = '../consultarUsuario.php';
$login = $_SESSION['login'];
$tela = 'Usuário';
$acao = 'Perfil atualizado para ' . $nmPerfil;
$idEvento = $idUsuario;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
