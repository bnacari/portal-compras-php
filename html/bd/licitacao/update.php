<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protectAdmin.php');

$pattern = "/^\d{4}-\d{2}-\d{2}$/";
$patternTime = "/^\d{2}:\d{2}$/";

$login = $_SESSION['login'];
$_SESSION['msg'] = '';

$idLicitacao = $_POST['idLicitacao'];
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
$permitirAtualizacao = $_POST["permitirAtualizacao"];

// Remove os espaços em branco do início e do final da string
$str_sem_espacos = trim($obsLicitacao);

// Verifica se a string resultante é vazia
if (empty($str_sem_espacos)) {
    $obsLicitacao = '';
}

if ($permitirAtualizacao == 'on') {
    $permitirAtualizacao = 1;
} else {
    $permitirAtualizacao = 0;
}

$dataAberturaFormatada = date("Y-m-d", strtotime($dtAbertura));
$hrAberturaFormatada = date("H:i", strtotime($hrAbertura));
$dtAberturaLicitacao = $dataAberturaFormatada . ' ' . $hrAberturaFormatada;

$dataInicioFormatada = date("Y-m-d", strtotime($dtIniSessao));
$hrInicioSessaoFormatada = date("H:i", strtotime($hrIniSessao));
$dtIniSessLicitacao = $dataInicioFormatada . ' ' . $hrInicioSessaoFormatada;

// ATUALIZA TABELA LICITAÇÃO
$queryUpdateLicitacao = "UPDATE [portalcompras].[dbo].LICITACAO 
                SET [COD_LICITACAO]='$codLicitacao', 
                    [OBS_LICITACAO]='$obsLicitacao'
                WHERE [ID_LICITACAO]=$idLicitacao";

$queryUpdateLici2 = $pdoCAT->query($queryUpdateLicitacao);

// ATUALIZA TABELA DETALHE_LICITAÇÃO
$queryUpdate = "UPDATE [portalcompras].[dbo].DETALHE_LICITACAO 
                SET [COD_LICITACAO]='$codLicitacao', 
                    [STATUS_LICITACAO]='$statusLicitacao', 
                    [OBJETO_LICITACAO]='$objLicitacao', 
                    [PREG_RESP_LICITACAO]='$respLicitacao', 
                    [DT_ABER_LICITACAO]='$dtAberturaLicitacao', 
                    [DT_INI_SESS_LICITACAO]='$dtIniSessLicitacao', 
                    [MODO_LICITACAO]='$modoLicitacao', 
                    [CRITERIO_LICITACAO]=$criterioLicitacao,
                    [TIPO_LICITACAO]=$tipoLicitacao, 
                    [REGIME_LICITACAO]='$regimeLicitacao',
                    [FORMA_LICITACAO]=$formaLicitacao,
                    [VL_LICITACAO]='$vlLicitacao',
                    [LOCAL_ABER_LICITACAO]='$localLicitacao',
                    [IDENTIFICADOR_LICITACAO]='$identificadorLicitacao',
                    [OBS_LICITACAO]='$obsLicitacao',
                    [ENVIO_ATUALIZACAO_LICITACAO]=$permitirAtualizacao
                WHERE [ID_LICITACAO]=$idLicitacao";

// var_dump($queryUpdate);
// exit();

$queryUpdate2 = $pdoCAT->query($queryUpdate);

// Caminho para a pasta de uploads
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

// $_SESSION['msg'] = "Licitação atualizada com sucesso!";

$_SESSION['redirecionar'] = '../../envio.php?idLicitacao=' . $idLicitacao;
// $_SESSION['redirecionar'] = '../../consultarLicitacao.php';
$login = $_SESSION['login'];
$tela = 'Licitacao';
$acao = 'Licitacao ' . $idLicitacao . ' ATUALIZADA';
$idEvento = $idLicitacao;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
