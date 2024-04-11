<?php
session_start(); // Inicia a sessão (se ainda não estiver iniciada)

include_once 'bd/conexao.php'; // Inclui o arquivo de conexão com o banco de dados

if (isset($_GET['codLicitacao'], $_GET['tipoLicitacao'])) {
    $codLicitacao = $_GET['codLicitacao'];
    $tipoLicitacao = $_GET['tipoLicitacao'];

    $querySelect2 = "SELECT COUNT(*) AS numRows
                    FROM DETALHE_LICITACAO
                    WHERE COD_LICITACAO = '$codLicitacao'
                    AND TIPO_LICITACAO = $tipoLicitacao
                    AND DT_EXC_LICITACAO IS NULL";

    $querySelect = $pdoCAT->query($querySelect2);
    $result = $querySelect->fetch(PDO::FETCH_ASSOC);

    if ($result['numRows'] > 0) {
        // O código da licitação já existe
        echo json_encode(1); // Retorna a resposta como JSON
    } else {
        // O código da licitação não existe
        echo json_encode(0); // Retorna a resposta como JSON
    }
}
?>
