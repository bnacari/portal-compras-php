<?php

$file = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_SPECIAL_CHARS);
$directory = filter_input(INPUT_GET, 'directory', FILTER_SANITIZE_SPECIAL_CHARS);

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
} else {
    echo 'O arquivo não existe.';
}
?>
