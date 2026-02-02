<?php
/**
 * ============================================================================
 * RENAMEANEXO.PHP - Renomear Anexos via AJAX (Tela de Edição de Licitação)
 * ============================================================================
 * 
 * Endpoint para renomear arquivos anexos na tela licitacaoForm.php.
 * Recebe directory, currentName e newName via POST.
 * 
 * @author Portal de Compras CESAN
 * ============================================================================
 */

session_start();
header('Content-Type: application/json');
include_once '../conexao.php';

// Obtém os parâmetros
$directory = isset($_POST['directory']) ? $_POST['directory'] : null;
$currentName = isset($_POST['currentName']) ? $_POST['currentName'] : null;
$newName = isset($_POST['newName']) ? $_POST['newName'] : null;

// Validação dos parâmetros
if (!$directory || !$currentName || !$newName) {
    echo json_encode([
        'success' => false,
        'message' => 'Parâmetros obrigatórios não fornecidos.'
    ]);
    exit;
}

// Sanitiza os nomes de arquivo (previne path traversal)
$currentFileName = basename($currentName);
$newFileName = basename($newName);

// Validar extensões (manter a mesma extensão)
$currentExt = strtolower(pathinfo($currentFileName, PATHINFO_EXTENSION));
$newExt = strtolower(pathinfo($newFileName, PATHINFO_EXTENSION));

if ($currentExt !== $newExt) {
    echo json_encode([
        'success' => false,
        'message' => 'Não é permitido alterar a extensão do arquivo.'
    ]);
    exit;
}

// Caminhos completos (relativo à raiz html/)
// O directory vem do JS como "uploads/{idLicitacao}", relativo à raiz html/
// Como estamos em bd/licitacao/, precisamos subir dois níveis
$currentFilePath = '../../' . $directory . '/' . $currentFileName;
$newFilePath = '../../' . $directory . '/' . $newFileName;

// Extrai o ID da licitação do diretório
$parts = explode('/', $directory);
$idLicitacao = end($parts);

// Se o nome é o mesmo, não precisa renomear
if ($currentFileName === $newFileName) {
    echo json_encode([
        'success' => true,
        'newFileName' => $newFileName,
        'message' => 'Nome não foi alterado.'
    ]);
    exit;
}

// Verificar se arquivo original existe
if (!file_exists($currentFilePath)) {
    echo json_encode([
        'success' => false,
        'message' => 'Arquivo original não encontrado.'
    ]);
    exit;
}

// Verificar se novo nome já existe e adicionar sufixo se necessário
if (file_exists($newFilePath)) {
    $pathInfo = pathinfo($newFilePath);
    $filename = $pathInfo['filename'];
    $extension = $pathInfo['extension'];

    $i = 1;
    while (file_exists('../../' . $directory . '/' . $newFileName)) {
        $newFileName = $filename . '_' . $i . '.' . $extension;
        $i++;
    }

    $newFilePath = '../../' . $directory . '/' . $newFileName;
}

// Tentar renomear o arquivo
if (rename($currentFilePath, $newFilePath)) {
    // Registra no log de auditoria
    $login = $_SESSION['login'] ?? 'Sistema';
    $tela = 'Licitação';
    $acao = 'Anexo renomeado de "' . $currentFileName . '" para "' . $newFileName . '"';
    $idEvento = intval($idLicitacao);

    try {
        $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");
    } catch (Exception $e) {
        error_log("Erro ao registrar auditoria: " . $e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'newFileName' => $newFileName,
        'message' => 'Arquivo renomeado com sucesso.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao renomear arquivo. Verifique as permissões do diretório.'
    ]);
}