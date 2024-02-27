<?php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';

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

// $path = 'credenciamento-of-proposal-cesan-105-2023';

$explodePath = explode("-", $path);

$ultimas_palavras = array_slice($explodePath, -2);
$primeira_palavra = array_slice($explodePath, 0);

// Atribuindo as duas últimas palavras a variáveis
$tipo = $primeira_palavra[0];
$cod = $ultimas_palavras[0];
$ano = $ultimas_palavras[1];

echo "<br><br>Primeira palavra: " . $tipo . "<br>";
echo "<br>Penúltima palavra: " . $cod . "<br>";
echo "<br>Última palavra: " . $ano . "<br><br>";

// // Atribuindo cada palavra a uma variável individualmente
// foreach ($array_palavras as $i => $palavra) {
//     ${"palavra" . ($i + 1)} = $palavra;
// }

// // Exibindo as variáveis
// for ($i = 1; $i <= count($array_palavras); $i++) {
//     echo "Palavra $i: " . ${"palavra$i"} . "<br>";
// }

$querySelect2 = "SELECT 
                DISTINCT D.*, L.ID_LICITACAO, L.DT_LICITACAO, TIPO.NM_TIPO AS NM_TIPO
                FROM
                LICITACAO L
                LEFT JOIN ANEXO A ON L.ID_LICITACAO = A.ID_LICITACAO
                LEFT JOIN DETALHE_LICITACAO D ON D.ID_LICITACAO = L.ID_LICITACAO
                LEFT JOIN TIPO_LICITACAO TIPO ON D.TIPO_LICITACAO = TIPO.ID_TIPO
                WHERE 
                D.COD_LICITACAO like '%$cod%' and D.COD_LICITACAO like '%$ano%' AND NM_TIPO LIKE '%$tipo%'
                ";

$querySelect = $pdoCAT->query($querySelect2);

// var_dump($querySelect2);
// exit();

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idLicitacao = $registros['ID_LICITACAO'];
    
    var_dump($idLicitacao);

endwhile;

redirecionar("viewLicitacao.php?idLicitacao=$idLicitacao");

?>
