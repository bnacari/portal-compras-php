<?php

session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

// if(isset($_POST["descricao"])) {
//     //vem da tela de cadastro de OCORRENCIA
//     $dscParteCorpo = $_POST["descricao"];
// } 

$nmForma = filter_input(INPUT_POST, 'nmForma',   FILTER_SANITIZE_SPECIAL_CHARS) ;

// var_dump($linkOrientacoes);

$queryInsert = $pdoCAT->query("INSERT INTO [portalcompras].[dbo].[FORMA] VALUES ('$nmForma', NULL)");

$querySelectPerfil = "SELECT MAX(ID_FORMA) AS ID_FORMA FROM FORMA";
$querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
    $idForma = $registros['ID_FORMA'];
endwhile;
// var_dump($queryInsert);

$_SESSION['msg'] = "Forma cadastrada com sucesso.";

$_SESSION['redirecionar'] = '../../administracao.php?aba=formas';
$login = $_SESSION['login'];
$tela = 'Forma';
$acao = 'CRIADA';
$idEvento = $idForma;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
