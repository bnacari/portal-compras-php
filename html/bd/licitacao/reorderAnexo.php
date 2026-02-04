<?php
/**
 * ============================================================================
 * REORDERANEXO.PHP - Reordenar Anexos via AJAX (Versão Unificada)
 * ============================================================================
 * 
 * Endpoint para salvar a ordem dos arquivos anexos na tela licitacaoForm.php.
 * Suporta tanto arquivos físicos quanto links externos do banco de dados.
 * 
 * Formato das chaves de ordenação:
 * - Arquivos físicos: nome do arquivo (ex: "documento.pdf")
 * - Links externos: prefixo "ext:" + URL (ex: "ext:http://servidor/arquivo.pdf")
 * 
 * @author Portal de Compras CESAN
 * ============================================================================
 */

session_start();
header('Content-Type: application/json');
include_once '../conexao.php';

// Obtém os parâmetros via POST
// Suporta tanto JSON quanto form data
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if (stripos($contentType, 'application/json') !== false) {
    // Dados JSON
    $input = json_decode(file_get_contents('php://input'), true);
    $idLicitacao = isset($input['idLicitacao']) ? $input['idLicitacao'] : null;
    $order = isset($input['order']) ? $input['order'] : null;
} else {
    // Form data
    $idLicitacao = isset($_POST['idLicitacao']) ? $_POST['idLicitacao'] : null;
    $order = isset($_POST['order']) ? $_POST['order'] : null;
    
    // Se order veio como string JSON, decodifica
    if (is_string($order)) {
        $order = json_decode($order, true);
    }
}

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

// Cria o diretório se não existir (para poder salvar o _order.json)
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'Não foi possível criar o diretório de uploads.'
        ]);
        exit;
    }
}

// Processa o array de ordem
// Mantém as chaves como estão (incluindo prefixo "ext:" para links externos)
$sanitizedOrder = [];
foreach ($order as $key) {
    if (empty($key)) continue;
    
    // Se começa com "ext:", é um link externo - mantém como está
    if (strpos($key, 'ext:') === 0) {
        $sanitizedOrder[] = $key;
    } else {
        // É um arquivo físico - sanitiza o nome
        $sanitized = basename($key);
        if ($sanitized && $sanitized !== '_order.json') {
            $sanitizedOrder[] = $sanitized;
        }
    }
}

// Salva o arquivo de ordem
$orderFile = $uploadDir . '/_order.json';

if (file_put_contents($orderFile, json_encode($sanitizedOrder, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))) {
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
        'message' => 'Ordem salva com sucesso.',
        'order' => $sanitizedOrder
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao salvar arquivo de ordem. Verifique as permissões.'
    ]);
}