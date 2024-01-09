<?php
$idLicitacao = $_POST['idLicitacao'];

$uploadDir = 'uploads/' . $idLicitacao . "/";

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
    }
}

// Retorna os nomes dos arquivos enviados (pode ser processado mais adequadamente conforme necessário)
echo json_encode(['uploadedFiles' => $uploadedFiles]);
?>
