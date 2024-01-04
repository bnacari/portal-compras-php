<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';
include('protectAdmin.php');

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
$regimeLicitacao = $_POST["regimeLicitacao"];
$formaLicitacao = $_POST["formaLicitacao"];
$vlLicitacao = $_POST["vlLicitacao"];
$identificadorLicitacao = $_POST["identificadorLicitacao"];
$localLicitacao = $_POST["localLicitacao"];
$obsLicitacao = $_POST["obsLicitacao"];

// Obtém os valores antigos da tabela DETALHE_LICITACAO
$querySelectOldValues = "SELECT * FROM [portalcompras].[dbo].DETALHE_LICITACAO WHERE [ID_LICITACAO] = $idLicitacao";
$stmt = $pdoCAT->query($querySelectOldValues);
$oldValues = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica as alterações nos campos
$changes = array();
foreach ($oldValues as $field => $oldValue) {
    if ($_POST[$field] != $oldValue) {
        $changes[] = [
            'campo' => $field,
            'valor_antigo' => $oldValue,
            'valor_novo' => $_POST[$field]
        ];
    }
}
if ($oldValues['COD_LICITACAO'] != $codLicitacao) {
    $changes[] = "COD_LICITACAO";
}
if ($oldValues['STATUS_LICITACAO'] != $statusLicitacao) {
    $changes[] = "STATUS_LICITACAO";
}
if ($oldValues['OBJETO_LICITACAO'] != $objLicitacao) {
    $changes[] = "OBJETO_LICITACAO";
}
if ($oldValues['PREG_RESP_LICITACAO'] != $respLicitacao) {
    $changes[] = "PREG_RESP_LICITACAO";
}
if ($oldValues['DT_ABER_LICITACAO'] != $dtAberturaLicitacao) {
    $changes[] = "DT_ABER_LICITACAO";
}
if ($oldValues['DT_INI_SESS_LICITACAO'] != $dtIniSessLicitacao) {
    $changes[] = "DT_INI_SESS_LICITACAO";
}
if ($oldValues['MODO_LICITACAO'] != $modoLicitacao) {
    $changes[] = "MODO_LICITACAO";
}
if ($oldValues['CRITERIO_LICITACAO'] != $criterioLicitacao) {
    $changes[] = "CRITERIO_LICITACAO";
}
if ($oldValues['REGIME_LICITACAO'] != $regimeLicitacao) {
    $changes[] = "REGIME_LICITACAO";
}
if ($oldValues['FORMA_LICITACAO'] != $formaLicitacao) {
    $changes[] = "FORMA_LICITACAO";
}
if ($oldValues['VL_LICITACAO'] != $vlLicitacao) {
    $changes[] = "VL_LICITACAO";
}
if ($oldValues['LOCAL_ABER_LICITACAO'] != $localLicitacao) {
    $changes[] = "LOCAL_ABER_LICITACAO";
}
if ($oldValues['IDENTIFICADOR_LICITACAO'] != $identificadorLicitacao) {
    $changes[] = "IDENTIFICADOR_LICITACAO";
}
if ($oldValues['OBS_LICITACAO'] != $obsLicitacao) {
    $changes[] = "OBS_LICITACAO";
}

// Se houver alterações, execute a atualização
if (!empty($changes)) {
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
                        [REGIME_LICITACAO]='$regimeLicitacao',
                        [FORMA_LICITACAO]=$formaLicitacao,
                        [VL_LICITACAO]='$vlLicitacao',
                        [LOCAL_ABER_LICITACAO]='$localLicitacao',
                        [IDENTIFICADOR_LICITACAO]='$identificadorLicitacao',
                        [OBS_LICITACAO]='$obsLicitacao'
                    WHERE [ID_LICITACAO]=$idLicitacao";
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
    $acao = 'ATUALIZADA';
    $idEvento = $idLicitacao;

    $changesString = json_encode($changes);
    // print_r($changesString);
    // exit();

    redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
} else {
    // Não houve alterações, redirecione sem fazer nada
    $_SESSION['msg'] = "Nenhuma alteração realizada.";
    $_SESSION['redirecionar'] = '../../consultarLicitacao.php';
    redirecionar($_SESSION['redirecionar']);
}
