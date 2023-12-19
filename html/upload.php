<?php
$idLicitacao = $_POST['idLicitacao'];

$uploadDir = 'uploads/' . $idLicitacao . "/";

// Verifica se o arquivo foi enviado com sucesso
if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
    $fileName = $_FILES['file']['name'];
    $filePath = $uploadDir . $fileName;

    if (mkdir($uploadDir, 0755, true)) {
    }

    // Move o arquivo para o diretório desejado
    move_uploaded_file($_FILES['file']['tmp_name'], $filePath);
    
} else {
    echo 'Erro no envio do arquivo.';
}


?>
