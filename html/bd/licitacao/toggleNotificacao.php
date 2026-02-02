<?php
/**
 * toggleNotificacao.php - Toggle de notificação via AJAX
 * Localização: bd/licitacao/toggleNotificacao.php
 */

// Forçar saída JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Iniciar sessão
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar se usuário está logado
    if (!isset($_SESSION['idUsuario']) || !isset($_SESSION['email'])) {
        throw new Exception('Usuário não autenticado');
    }

    // Incluir conexão
    include_once '../conexao.php';
    
    if (!isset($pdoCAT)) {
        throw new Exception('Erro de conexão com banco de dados');
    }

    // Obter dados
    $idLicitacao = filter_input(INPUT_POST, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);
    $acao = filter_input(INPUT_POST, 'acao', FILTER_SANITIZE_SPECIAL_CHARS); // 'inscrever' ou 'cancelar'
    
    if (!$idLicitacao) {
        throw new Exception('ID da licitação não informado');
    }

    $idUsuario = $_SESSION['idUsuario'];
    $emailUsuario = $_SESSION['email'];

    if ($acao === 'inscrever') {
        // Verificar se já está inscrito
        $queryVerifica = "SELECT ID_ATUALIZACAO FROM ATUALIZACAO 
                          WHERE ID_LICITACAO = $idLicitacao 
                          AND ID_ADM = $idUsuario 
                          AND DT_EXC_ATUALIZACAO IS NULL";
        $stmtVerifica = $pdoCAT->query($queryVerifica);
        $jaInscrito = $stmtVerifica->fetch(PDO::FETCH_ASSOC);

        if ($jaInscrito) {
            echo json_encode([
                'success' => true,
                'inscrito' => true,
                'message' => 'Você já está inscrito para receber notificações.'
            ]);
            exit;
        }

        // Inserir inscrição
        $queryInsert = "INSERT INTO [portalcompras].[dbo].ATUALIZACAO 
                        VALUES (getdate(), $idUsuario, '$emailUsuario', $idLicitacao, NULL)";
        $pdoCAT->query($queryInsert);

        echo json_encode([
            'success' => true,
            'inscrito' => true,
            'message' => 'Você receberá notificações desta licitação.'
        ]);

    } else if ($acao === 'cancelar') {
        // Buscar ID da inscrição
        $queryBusca = "SELECT ID_ATUALIZACAO FROM ATUALIZACAO 
                       WHERE ID_LICITACAO = $idLicitacao 
                       AND ID_ADM = $idUsuario 
                       AND DT_EXC_ATUALIZACAO IS NULL";
        $stmtBusca = $pdoCAT->query($queryBusca);
        $inscricao = $stmtBusca->fetch(PDO::FETCH_ASSOC);

        if (!$inscricao) {
            echo json_encode([
                'success' => true,
                'inscrito' => false,
                'message' => 'Você não está inscrito nesta licitação.'
            ]);
            exit;
        }

        // Cancelar inscrição (soft delete)
        $idAtualizacao = $inscricao['ID_ATUALIZACAO'];
        $queryUpdate = "UPDATE ATUALIZACAO SET DT_EXC_ATUALIZACAO = getdate() 
                        WHERE ID_ATUALIZACAO = $idAtualizacao";
        $pdoCAT->query($queryUpdate);

        echo json_encode([
            'success' => true,
            'inscrito' => false,
            'message' => 'Notificações canceladas com sucesso.'
        ]);

    } else {
        throw new Exception('Ação inválida');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}