<?php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';

if (isset($_GET['path'])) {

    $path = $_GET['path'];

} else {
    redirecionar("licitacao.php");
}

// $path = 'licitacao-cesan-no-039-2023/';

$path = str_replace('/', '', $path);

$explodePath = explode("-", $path);

$ultimas_palavras = array_slice($explodePath, -2);
$primeira_palavra = array_slice($explodePath, 0);

if ($primeira_palavra[0] == 'licitacao') {
    $primeira_palavra[0] = 'licitação';
}

$tipo = $primeira_palavra[0];
$cod = $ultimas_palavras[0];
$ano = $ultimas_palavras[1];

// echo "<br><br>Primeira palavra: " . $tipo . "<br>";
// echo "<br>Penúltima palavra: " . $cod . "<br>";
// echo "<br>Última palavra: " . $ano . "<br><br>";

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

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idLicitacao = $registros['ID_LICITACAO'];

// var_dump($idLicitacao);

endwhile;

redirecionar("viewLicitacao.php?idLicitacao=$idLicitacao");
