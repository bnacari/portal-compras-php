<?php
/**
 * ============================================================================
 * EXCLUIR_ARQUIVO.PHP - Exclusão/Restauração de Anexos
 * ============================================================================
 * 
 * Endpoint para exclusão de arquivos físicos do diretório OU
 * exclusão/restauração lógica de anexos externos na tabela ANEXO.
 * 
 * Para arquivos físicos: exclui o arquivo do servidor
 * Para anexos do BD: atualiza DT_EXC_ANEXO (GETDATE para excluir, NULL para restaurar)
 * 
 * @author Portal de Compras CESAN
 * ============================================================================
 */

session_start();
include_once 'bd/conexao.php';

// Obtém os parâmetros via GET
$file = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_SPECIAL_CHARS);
$directory = filter_input(INPUT_GET, 'directory', FILTER_SANITIZE_SPECIAL_CHARS);
$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$dtExcAnexo = filter_input(INPUT_GET, 'dtExcAnexo', FILTER_SANITIZE_SPECIAL_CHARS);

$fullpath = $directory . '/' . $file;

// Verifique se o arquivo existe no diretório físico
if (file_exists($fullpath)) {
    // Tente excluir o arquivo físico
    if (unlink($fullpath)) {
        echo 'Arquivo "' . $file . '" excluído com sucesso.';
    } else {
        echo 'Erro ao excluir o arquivo.';
    }

    if ($idLicitacao == 'anexos') {
        $idLicitacao = 0;
        $tela = 'Anexos';
    } else {
        $tela = 'Licitação';
    }

    // Log de auditoria para arquivo físico
    $login = $_SESSION['login'] ?? 'Sistema';
    $acao = 'Excluir Anexo: ' . $file;
    $idEvento = intval($idLicitacao);
    $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");

} else {
    // Arquivo não existe fisicamente - é um anexo externo no banco de dados
    $tela = 'Licitação';

    // CORREÇÃO: usar empty() em vez de !isset()
    // empty() retorna true para: null, '', 0, '0', false, array()
    // Isso garante que string vazia '' seja tratada como "não tem valor"
    if (empty($dtExcAnexo)) {
        // EXCLUIR: preencher DT_EXC_ANEXO com GETDATE()
        $queryUpdateAnexo = "UPDATE [portalcompras].[dbo].ANEXO 
                             SET DT_EXC_ANEXO = GETDATE()
                             WHERE ID_LICITACAO = $idLicitacao 
                             AND (LINK_ANEXO LIKE '$file' OR LINK_ANEXO LIKE '$directory')";

        $queryUpdateAnexo2 = $pdoCAT->query($queryUpdateAnexo);

        // Log de auditoria
        $login = $_SESSION['login'] ?? 'Sistema';
        $acao = 'Excluir Anexo Externo: ' . $file;
        $idEvento = intval($idLicitacao);
        $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");

    } else {
        // RESTAURAR: preencher DT_EXC_ANEXO com NULL
        $queryUpdateAnexo = "UPDATE [portalcompras].[dbo].ANEXO 
                             SET DT_EXC_ANEXO = NULL
                             WHERE ID_LICITACAO = $idLicitacao 
                             AND (LINK_ANEXO LIKE '$file' OR LINK_ANEXO LIKE '$directory')";

        $queryUpdateAnexo2 = $pdoCAT->query($queryUpdateAnexo);

        // Log de auditoria
        $login = $_SESSION['login'] ?? 'Sistema';
        $acao = 'Restaurar Anexo Externo: ' . $file;
        $idEvento = intval($idLicitacao);
        $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");
    }
}