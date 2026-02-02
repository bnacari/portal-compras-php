<?php
/**
 * ============================================================================
 * UPLOADANEXO.PHP - Upload de Anexos via AJAX (Tela de Edição de Licitação)
 * ============================================================================
 * 
 * Endpoint para upload de arquivos anexos na tela licitacaoForm.php (modo edição).
 * Recebe arquivos via AJAX (drag-and-drop ou seleção) e salva no diretório
 * uploads/{idLicitacao}/.
 * 
 * Aceita apenas arquivos PDF e ZIP conforme validação do frontend.
 * 
 * @author Portal de Compras CESAN
 * ============================================================================
 */

session_start();
include_once '../conexao.php';

// Obtém o ID da licitação
$idLicitacao = isset($_POST['idLicitacao']) ? $_POST['idLicitacao'] : null;

if (!$idLicitacao) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da licitação não informado.']);
    exit;
}

// Diretório de upload (relativo à raiz html/)
$uploadDir = '../../uploads/' . $idLicitacao . '/';

// Cria o diretório se não existir
if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Array para armazenar os nomes dos arquivos enviados
$uploadedFiles = [];

// Verifica se há arquivos enviados
if (!empty($_FILES['files']['name'])) {
    // Loop através de cada arquivo
    for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
        $fileName = $_FILES['files']['name'][$i];
        $filePath = $uploadDir . $fileName;

        // Verifica se o arquivo já existe no diretório
        $fileCount = 1;
        while (file_exists($filePath)) {
            // Renomeia o arquivo adicionando um número ao final
            $fileName = pathinfo($_FILES['files']['name'][$i], PATHINFO_FILENAME) . '_' . $fileCount . '.' . pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
            $filePath = $uploadDir . $fileName;
            $fileCount++;
        }

        // Move o arquivo para o diretório desejado
        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $filePath)) {
            $uploadedFiles[] = $fileName;
        }

        // Registra no log de auditoria
        $login = $_SESSION['login'];
        $tela = 'Licitação';
        $acao = 'Inserir Anexo: ' . $fileName;
        $idEvento = $idLicitacao;
        $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");
    }
}

// Retorna os nomes dos arquivos enviados
echo json_encode(['uploadedFiles' => $uploadedFiles]);