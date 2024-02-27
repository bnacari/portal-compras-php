<?php
// Verifica se o parâmetro 'path' foi passado na URL
if(isset($_GET['path'])) {
    // Atribui o valor do parâmetro 'path' a uma variável
    $path = $_GET['path'];
    
    // Use o valor de $path conforme necessário
    echo "O valor do parâmetro 'path' é: " . $path;
} else {
    // Caso o parâmetro 'path' não seja passado, faça algo ou mostre uma mensagem de erro
    echo "O parâmetro 'path' não foi passado na URL.";
}
?>
