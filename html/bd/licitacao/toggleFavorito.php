<?php
/**
 * toggleFavorito.php - Toggle de favorito via AJAX
 * Localização: bd/licitacao/toggleFavorito.php
 */

// Forçar saída JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Iniciar sessão
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar se usuário está logado
    if (!isset($_SESSION['idUsuario'])) {
        throw new Exception('Usuário não autenticado');
    }

    // Incluir conexão
    include_once '../conexao.php';
    
    if (!isset($pdoCAT)) {
        throw new Exception('Erro de conexão com banco de dados');
    }

    // Obter dados
    $idLicitacao = filter_input(INPUT_POST, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);
    
    if (!$idLicitacao) {
        throw new Exception('ID da licitação não informado');
    }

    $idUsuario = intval($_SESSION['idUsuario']);

    // Verificar se já é favorito ativo
    $queryVerifica = "SELECT ID_FAVORITO FROM FAVORITO_LICITACAO 
                      WHERE ID_LICITACAO = $idLicitacao 
                      AND ID_ADM = $idUsuario 
                      AND DT_EXC_FAVORITO IS NULL";
    $stmtVerifica = $pdoCAT->query($queryVerifica);
    $jaFavorito = $stmtVerifica->fetch(PDO::FETCH_ASSOC);

    if ($jaFavorito) {
        // Remover favorito (soft delete)
        $idFavorito = $jaFavorito['ID_FAVORITO'];
        $queryUpdate = "UPDATE FAVORITO_LICITACAO 
                        SET DT_EXC_FAVORITO = GETDATE() 
                        WHERE ID_FAVORITO = $idFavorito";
        $pdoCAT->query($queryUpdate);

        echo json_encode(array(
            'success' => true,
            'favorito' => false,
            'message' => 'Licitação removida dos favoritos.'
        ));
    } else {
        // Verificar se existe registro excluído para reutilizar
        $queryExcluido = "SELECT ID_FAVORITO FROM FAVORITO_LICITACAO 
                          WHERE ID_LICITACAO = $idLicitacao 
                          AND ID_ADM = $idUsuario 
                          AND DT_EXC_FAVORITO IS NOT NULL";
        $stmtExcluido = $pdoCAT->query($queryExcluido);
        $registroExcluido = $stmtExcluido->fetch(PDO::FETCH_ASSOC);

        if ($registroExcluido) {
            // Reativar registro existente
            $idFavorito = $registroExcluido['ID_FAVORITO'];
            $queryReativar = "UPDATE FAVORITO_LICITACAO 
                              SET DT_EXC_FAVORITO = NULL, DT_FAVORITO = GETDATE() 
                              WHERE ID_FAVORITO = $idFavorito";
            $pdoCAT->query($queryReativar);
        } else {
            // Inserir novo favorito
            $queryInsert = "INSERT INTO [portalcompras].[dbo].FAVORITO_LICITACAO 
                            (ID_ADM, ID_LICITACAO, DT_FAVORITO, DT_EXC_FAVORITO) 
                            VALUES ($idUsuario, $idLicitacao, GETDATE(), NULL)";
            $pdoCAT->query($queryInsert);
        }

        echo json_encode(array(
            'success' => true,
            'favorito' => true,
            'message' => 'Licitação adicionada aos favoritos.'
        ));
    }

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}