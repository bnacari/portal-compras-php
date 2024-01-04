<?php

session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include('protectAdmin.php');

$pattern = "/^\d{4}-\d{2}-\d{2}$/";
$patternTime = "/^\d{2}:\d{2}$/";

$login = $_SESSION['login'];
$_SESSION['msg'] = '';

//vem da tela de cadastro da LICITAÇÃO
$codLicitacao = $_POST["codLicitacao"];
$statusLicitacao = $_POST["statusLicitacao"];
$respLicitacao = $_POST["respLicitacao"];
$objLicitacao = $_POST["objLicitacao"];
$dtAbertura = $_POST["dtAberLicitacao"];
$dtIniSessao = $_POST["dtIniSessLicitacao"];
$hrAbertura = $_POST["hrAberLicitacao"];
$hrIniSessao = $_POST["hrIniSessLicitacao"];
$modoLicitacao = $_POST["modoLicitacao"];
$criterioLicitacao = $_POST["criterioLicitacao"];
$tipoLicitacao = $_POST["tipoLicitacao"];
$regimeLicitacao = $_POST["regimeLicitacao"];
$formaLicitacao = $_POST["formaLicitacao"];
$vlLicitacao = $_POST["vlLicitacao"];
$identificadorLicitacao = $_POST["identificadorLicitacao"];
$localLicitacao = $_POST["localLicitacao"];
$obsLicitacao = $_POST["obsLicitacao"];
$dataAberturaFormatada = date("Y-m-d", strtotime($dtAbertura));
$hrAberturaFormatada = date("H:i", strtotime($hrAbertura));
$dtAberturaLicitacao = $dataAberturaFormatada . ' ' . $hrAberturaFormatada;

$dataInicioFormatada = date("Y-m-d", strtotime($dtIniSessao));
$hrInicioSessaoFormatada = date("H:i", strtotime($hrIniSessao));
$dtIniSessLicitacao = $dataInicioFormatada . ' ' . $hrInicioSessaoFormatada;

$query = "INSERT INTO [portalcompras].[dbo].[licitacao] VALUES (0, getdate(), '$codLicitacao', '', NULL, '$obsLicitacao', '$login', NULL)";
// var_dump($query);
// exit;
$queryInsertVisita = $pdoCAT->query($query);

$queryID = "SELECT MAX(ID_LICITACAO) AS ID_LICITACAO FROM LICITACAO";
$stmt = $pdoCAT->query($queryID);

if ($stmt) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $idLicitacao = $row['ID_LICITACAO'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Diretório de destino dos arquivos
        $uploadDirectory = "../../uploads"; // Diretório principal
    
        $fullPath = $uploadDirectory . '/' . $idLicitacao;
    
        if (mkdir($fullPath, 0755, true)) {
        }
    
        foreach ($_FILES["anexos"]["error"] as $key => $error) {
    
            $nomeArquivo = $_FILES["anexos"]["name"][$key];
            $caminhoTemporario = $_FILES["anexos"]["tmp_name"][$key];
            $caminhoDestino = $fullPath . "/" . $nomeArquivo;
    
            // Move o arquivo para o destino
            if (move_uploaded_file($caminhoTemporario, $caminhoDestino)) {
                echo "O arquivo $nomeArquivo foi enviado com sucesso.<br>";
            } else {
                echo "Ocorreu um erro ao enviar o arquivo $caminhoDestino.<br>";
            }
        }
    }
}

$query = "INSERT INTO [portalcompras].[dbo].[DETALHE_LICITACAO] VALUES ($idLicitacao, '$codLicitacao', '$statusLicitacao', '$objLicitacao', '$respLicitacao', '$dtAberturaLicitacao', '$dtIniSessLicitacao', '$modoLicitacao', $tipoLicitacao, $criterioLicitacao, '$regimeLicitacao', $formaLicitacao, '$vlLicitacao', '$localLicitacao', '$identificadorLicitacao', '$obsLicitacao', NULL)";

// var_dump($query);

$queryInsertDetalheLicitacao = $pdoCAT->query($query);

// exit();

$_SESSION['msg'] = "Licitação de código ' $idLicitacao ' cadastrada com sucesso.";

$_SESSION['redirecionar'] = '../../editarLicitacao.php?idLicitacao=' . $idLicitacao;
$login = $_SESSION['login'];
$tela = 'Licitacao';
$acao = 'CRIADA';
$idEvento = $idLicitacao;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");