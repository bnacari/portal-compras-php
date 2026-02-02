<?php
/**
 * ============================================================================
 * REORDERANEXO.PHP - Reordenar Anexos via AJAX (Tela de Edição de Licitação)
 * ============================================================================
 * 
 * Endpoint para salvar a ordem dos arquivos anexos na tela licitacaoForm.php.
 * Recebe um array JSON com os nomes dos arquivos na nova ordem e salva
 * em um arquivo _order.json dentro do diretório de uploads da licitação.
 * 
 * @author Portal de Compras CESAN
 * ============================================================================
 */

session_start();
header('Content-Type: application/json');
include_once '../conexao.php';

// Obtém os parâmetros via POST (JSON)
$input = json_decode(file_get_contents('php://input'), true);

$idLicitacao = isset($input['idLicitacao']) ? $input['idLicitacao'] : null;
$order = isset($input['order']) ? $input['order'] : null;

// Validação dos parâmetros
if (!$idLicitacao || !is_array($order)) {
    echo json_encode([
        'success' => false,
        'message' => 'Parâmetros obrigatórios não fornecidos.'
    ]);
    exit;
}

// Sanitiza o ID da licitação
$idLicitacao = intval($idLicitacao);

// Diretório de uploads (relativo à raiz html/)
$uploadDir = '../../uploads/' . $idLicitacao;

// Verifica se o diretório existe
if (!is_dir($uploadDir)) {
    echo json_encode([
        'success' => false,
        'message' => 'Diretório de uploads não encontrado.'
    ]);
    exit;
}

// Sanitiza os nomes dos arquivos no array de ordem
$sanitizedOrder = [];
foreach ($order as $filename) {
    $sanitized = basename($filename);
    if ($sanitized && $sanitized !== '_order.json') {
        $sanitizedOrder[] = $sanitized;
    }
}

// Salva o arquivo de ordem
$orderFile = $uploadDir . '/_order.json';

if (file_put_contents($orderFile, json_encode($sanitizedOrder, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    // Registra no log de auditoria
    try {
        $login = $_SESSION['login'] ?? 'Sistema';
        $tela = 'Licitação';
        $acao = 'Reordenar Anexos';
        $idEvento = $idLicitacao;
        $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");
    } catch (Exception $e) {
        error_log("Erro ao registrar auditoria: " . $e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'message' => 'Ordem salva com sucesso.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao salvar arquivo de ordem. Verifique as permissões.'
    ]);
}