<?php
include_once 'redirecionar.php';
include_once 'protectAdmin.php';

// Verifique se os parâmetros necessários estão presentes na URL
if (isset($_GET['rowId'], $_GET['currentName'], $_GET['newName'], $_GET['directory'])) {
    $rowId = $_GET['rowId'];
    $currentName = $_GET['currentName'];
    $newName = $_GET['newName'];
    $directory = $_GET['directory']; // Obter o diretório enviado pela URL

    $lastSlashPos = strrpos($directory, '/');

    // Verificar se a barra foi encontrada e não está no final da string
    if ($lastSlashPos !== false && $lastSlashPos < strlen($directory) - 1) {
        // Obter os caracteres à direita da barra '/'
        $numbers = substr($directory, $lastSlashPos + 1);

        // Remover caracteres não numéricos usando expressão regular
        $idLicitacao = preg_replace("/[^0-9]/", "", $numbers);

    } else {
        echo "A barra '/' não foi encontrada na string ou está no final da string.";
    }

    // echo "<script>alert($idLicitacao);</script>";
    $currentFileName = basename($currentName);
    $newFileName = basename($newName);

    // Caminho completo do arquivo atual e do novo arquivo
    $currentFilePath = $directory . '/' . $currentName;
    $newFilePath = $directory . '/' . $newName;

    // Renomear o arquivo no servidor
    if (rename($currentFilePath, $newFilePath)) {
        $_SESSION['msg'] =  'Arquivo renomeado com sucesso!';

        $login = $_SESSION['login'];
        $tela = 'Licitacao';
        $acao = 'Anexo atualizado de ´' . $currentFileName . '´ para ´' . $newFileName . '´';
        $idEvento = $idLicitacao;
    } else {
        $_SESSION['msg'] =  'Erro ao renomear o arquivo.';
    }
} else {
    $_SESSION['msg'] =  'Parâmetros ausentes na requisição.';
}

$_SESSION['redirecionar'] = 'editarLicitacao.php?idLicitacao=' . $idLicitacao;

redirecionar("log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
