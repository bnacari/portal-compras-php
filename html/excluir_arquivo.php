<?php
session_start();
include_once 'bd/conexao.php';

$file = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_SPECIAL_CHARS);
$directory = filter_input(INPUT_GET, 'directory', FILTER_SANITIZE_SPECIAL_CHARS);
$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);

$fullpath = $directory . '/' . $file;

// Verifique se o arquivo existe no diretório
if (file_exists($fullpath)) {
    // Tente excluir o arquivo
    if (unlink($fullpath)) {
        echo 'Arquivo "' . $file . '" excluído com sucesso.';
    } else {
        echo($fullpath);
        echo 'Erro ao excluir o arquivo.';
    }

    echo "<script>console.log('" . json_encode($idLicitacao) . "');</script>";

    if ($idLicitacao == 'anexos') { 
        $idLicitacao = 0; 
        $tela = 'Anexos';
    } else {
        $tela = 'Licitação';
    }

    $login = $_SESSION['login'];
    $acao = 'Excluir Anexo: ' . $file;
    $idEvento = $idLicitacao;
    $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");

} else {
    echo 'O arquivo não existe.';
}
?> 
