

<?php
// GAMBIARRA FEITA PARA REDIRECIONAR PARA A 
// PÁGINA ABAIXO CASO O USUÁRIO TENTE ACESSAR "http://portal-de-compras.sistemas.cesan.com.br/licitacao/972/"
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$porta = $_SERVER['SERVER_PORT'];

$caminho_do_servidor = "$protocolo://$host/viewLicitacao.php?idLicitacao=39783";

header("Location: $caminho_do_servidor");
exit();


?>