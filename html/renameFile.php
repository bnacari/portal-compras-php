<?php
//renameFile.php
header('Content-Type: application/json');

include_once 'redirecionar.php';
include_once 'protectAdmin.php';
include_once 'bd/conexao.php';

// Aceitar tanto POST quanto GET
$rowId = $_POST['rowId'] ?? $_GET['rowId'] ?? null;
$currentName = $_POST['currentName'] ?? $_GET['currentName'] ?? null;
$newName = $_POST['newName'] ?? $_GET['newName'] ?? null;
$directory = $_POST['directory'] ?? $_GET['directory'] ?? null;

// Debug: Log dos parâmetros recebidos
error_log("=== RENAME FILE DEBUG ===");
error_log("Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("GET data: " . print_r($_GET, true));
error_log("rowId: " . $rowId);
error_log("currentName: " . $currentName);
error_log("newName: " . $newName);
error_log("directory: " . $directory);

// Validar parâmetros
if (!$currentName || !$newName || !$directory) {
    echo json_encode([
        'success' => false, 
        'message' => 'Parâmetros obrigatórios não fornecidos',
        'debug' => [
            'rowId' => $rowId,
            'currentName' => $currentName,
            'newName' => $newName,
            'directory' => $directory,
            'method' => $_SERVER['REQUEST_METHOD']
        ]
    ]);
    exit();
}

// Extrair ID da licitação do diretório
$lastSlashPos = strrpos($directory, '/');

if ($lastSlashPos !== false && $lastSlashPos < strlen($directory) - 1) {
    $numbers = substr($directory, $lastSlashPos + 1);
    $idLicitacao = preg_replace("/[^0-9]/", "", $numbers);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Diretório inválido'
    ]);
    exit();
}

// Sanitizar nomes de arquivos
$currentFileName = basename($currentName);
$newFileName = basename($newName);

// Validar extensões (manter a mesma extensão)
$currentExt = strtolower(pathinfo($currentFileName, PATHINFO_EXTENSION));
$newExt = strtolower(pathinfo($newFileName, PATHINFO_EXTENSION));

if ($currentExt !== $newExt) {
    echo json_encode([
        'success' => false, 
        'message' => 'Não é permitido alterar a extensão do arquivo'
    ]);
    exit();
}

// Caminhos completos
$currentFilePath = $directory . '/' . $currentFileName;
$newFilePath = $directory . '/' . $newFileName;

// Verificar se arquivo original existe
if (!file_exists($currentFilePath)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Arquivo original não encontrado: ' . $currentFilePath
    ]);
    exit();
}

// Verificar se diretório é gravável
if (!is_writable($directory)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Sem permissão para gravar no diretório'
    ]);
    exit();
}

// Se o nome é o mesmo, não precisa renomear
if ($currentFilePath === $newFilePath) {
    echo json_encode([
        'success' => true, 
        'newFileName' => $newFileName,
        'message' => 'Nome não foi alterado'
    ]);
    exit();
}

// Verificar se novo nome já existe e adicionar sufixo se necessário
if (file_exists($newFilePath)) {
    $pathInfo = pathinfo($newFilePath);
    $filename = $pathInfo['filename'];
    $extension = $pathInfo['extension'];
    
    $i = 1;
    while (file_exists($directory . '/' . $newFileName)) {
        $newFileName = $filename . '_' . $i . '.' . $extension;
        $i++;
    }
    
    $newFilePath = $directory . '/' . $newFileName;
}

// Tentar renomear o arquivo
if (rename($currentFilePath, $newFilePath)) {
    // Registrar na auditoria
    try {
        $login = $_SESSION['login'] ?? 'Sistema';
        $tela = 'Licitacao';
        $acao = 'Anexo atualizado de "' . $currentFileName . '" para "' . $newFileName . '"';
        $idEvento = intval($idLicitacao);
        
        $queryLOG = $pdoCAT->prepare("INSERT INTO auditoria VALUES(?, GETDATE(), ?, ?, ?)");
        $queryLOG->execute([$login, $tela, $acao, $idEvento]);
    } catch (Exception $e) {
        // Log falhou mas arquivo foi renomeado
        error_log("Erro ao registrar auditoria: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true, 
        'newFileName' => $newFileName,
        'message' => 'Arquivo renomeado com sucesso'
    ]);
    
} else {
    // Pegar erro específico do sistema
    $error = error_get_last();
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao renomear arquivo. Verifique as permissões do diretório.',
        'debug' => $error['message'] ?? 'Erro desconhecido'
    ]);
}
?>