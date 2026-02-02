<?php
/**
 * ============================================================================
 * DELETEANEXO.PHP - Exclusão de Anexos via AJAX (Tela de Edição de Licitação)
 * ============================================================================
 * 
 * Endpoint para exclusão de arquivos anexos na tela licitacaoForm.php.
 * Recebe directory e fileName via POST e exclui o arquivo físico do servidor.
 * 
 * @author Portal de Compras CESAN
 * ============================================================================
 */

session_start();
header('Content-Type: application/json');
include_once '../conexao.php';

// Obtém os parâmetros
$directory = isset($_POST['directory']) ? $_POST['directory'] : null;
$fileName = isset($_POST['fileName']) ? $_POST['fileName'] : null;

// Validação dos parâmetros
if (!$directory || !$fileName) {
    echo json_encode([
        'success' => false,
        'message' => 'Parâmetros obrigatórios não fornecidos.'
    ]);
    exit;
}

// Sanitiza o nome do arquivo (previne path traversal)
$fileName = basename($fileName);

// Caminho completo (relativo à raiz html/)
// O directory vem do JS como "uploads/{idLicitacao}", relativo à raiz html/
// Como estamos em bd/licitacao/, precisamos subir dois níveis
$fullPath = '../../' . $directory . '/' . $fileName;

// Extrai o ID da licitação do diretório
$parts = explode('/', $directory);
$idLicitacao = end($parts);

// Verifica se o arquivo existe
if (file_exists($fullPath)) {
    // Tenta excluir o arquivo
    if (unlink($fullPath)) {
        // Registra no log de auditoria
        $login = $_SESSION['login'] ?? 'Sistema';
        $tela = 'Licitação';
        $acao = 'Excluir Anexo: ' . $fileName;
        $idEvento = intval($idLicitacao);
        
        try {
            $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");
        } catch (Exception $e) {
            error_log("Erro ao registrar auditoria: " . $e->getMessage());
        }

        echo json_encode([
            'success' => true,
            'message' => 'Arquivo excluído com sucesso.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao excluir o arquivo. Verifique as permissões.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Arquivo não encontrado: ' . $fileName
    ]);
}