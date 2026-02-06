<?php

/**
 * readAjax.php - API AJAX para listagem de licitações
 * VERSÃO ATUALIZADA - Com suporte a notificações
 */

// Capturar todos os erros e convertê-los para JSON
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Forçar saída JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Iniciar sessão se não estiver iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar se o arquivo de conexão existe
    $conexaoPath = __DIR__ . '/../conexao.php';
    if (!file_exists($conexaoPath)) {
        throw new Exception('Arquivo de conexão não encontrado: ' . $conexaoPath);
    }

    // Incluir conexão
    include_once $conexaoPath;

    // Verificar conexão
    if (!isset($pdoCAT)) {
        throw new Exception('Variável $pdoCAT não definida após incluir conexao.php');
    }

    // Variáveis de sessão
    $emailUsuario = isset($_SESSION['email']) ? $_SESSION['email'] : '';
    $idUsuarioLogado = isset($_SESSION['idUsuario']) ? intval($_SESSION['idUsuario']) : 0;

    // Filtros recebidos
    $tituloLicitacaoFilter = isset($_POST['tituloLicitacao']) ? trim($_POST['tituloLicitacao']) : '';
    $statusLicitacaoFilter = isset($_POST['statusLicitacao']) ? trim($_POST['statusLicitacao']) : 'Em Andamento';
    $dtIniLicitacaoFilter = isset($_POST['dtIniLicitacao']) ? trim($_POST['dtIniLicitacao']) : '';
    $dtFimLicitacaoFilter = isset($_POST['dtFimLicitacao']) ? trim($_POST['dtFimLicitacao']) : '';
    $tipoLicitacao = isset($_POST['tipoLicitacao']) ? trim($_POST['tipoLicitacao']) : '';
    $somentesFavoritos = isset($_POST['somenteFavoritos']) ? trim($_POST['somenteFavoritos']) : '0';
    $pagina = isset($_POST['pagina']) ? intval($_POST['pagina']) : 1;
    $limite = isset($_POST['limite']) ? intval($_POST['limite']) : 20;

    // Validar valores
    if ($pagina < 1)
        $pagina = 1;
    if ($limite < 1)
        $limite = 20;
    $offset = ($pagina - 1) * $limite;

    // Construir WHERE
    $whereConditions = array();
    $whereConditions[] = "L.DT_EXC_LICITACAO IS NULL";

    // Tipo
    if (!empty($tipoLicitacao) && $tipoLicitacao !== 'vazio') {
        $whereConditions[] = "D.TIPO_LICITACAO = " . intval($tipoLicitacao);
    }

    // Status
    if (!empty($statusLicitacaoFilter) && $statusLicitacaoFilter !== 'vazio' && $statusLicitacaoFilter !== '') {
        $statusEscaped = str_replace("'", "''", $statusLicitacaoFilter);
        $whereConditions[] = "D.STATUS_LICITACAO = '" . $statusEscaped . "'";
    } else {
        $whereConditions[] = "D.STATUS_LICITACAO <> 'Rascunho'";
    }

    // Título/Código
    if (!empty($tituloLicitacaoFilter)) {
        $tituloEscaped = str_replace("'", "''", $tituloLicitacaoFilter);
        $whereConditions[] = "(D.COD_LICITACAO LIKE '%" . $tituloEscaped . "%' OR D.OBJETO_LICITACAO LIKE '%" . $tituloEscaped . "%')";
    }

    // Data
    if (!empty($dtIniLicitacaoFilter) && !empty($dtFimLicitacaoFilter)) {
        $whereConditions[] = "L.DT_LICITACAO BETWEEN '" . $dtIniLicitacaoFilter . "' AND '" . $dtFimLicitacaoFilter . " 23:59:59'";
    }

    if ($somentesFavoritos === '1' && $idUsuarioLogado > 0) {
        $whereConditions[] = "EXISTS (
        SELECT 1 FROM FAVORITO_LICITACAO F 
        WHERE F.ID_LICITACAO = L.ID_LICITACAO 
        AND F.ID_ADM = $idUsuarioLogado 
        AND F.DT_EXC_FAVORITO IS NULL
    )";
    }


    $whereClause = implode(" AND ", $whereConditions);
    $emailEscaped = str_replace("'", "''", $emailUsuario);

    // Query de contagem
    $queryCount = "SELECT COUNT(DISTINCT L.ID_LICITACAO) as total
                   FROM LICITACAO L
                   LEFT JOIN DETALHE_LICITACAO D ON D.ID_LICITACAO = L.ID_LICITACAO
                   WHERE " . $whereClause;

    $stmtCount = $pdoCAT->query($queryCount);
    if (!$stmtCount) {
        throw new Exception('Erro na query de contagem');
    }

    $resultCount = $stmtCount->fetch(PDO::FETCH_ASSOC);
    $totalRegistros = $resultCount ? intval($resultCount['total']) : 0;

    // ---- Contagem por status para o header ----
    $whereBase = array();
    $whereBase[] = "L.DT_EXC_LICITACAO IS NULL";
    $whereBase[] = "D.STATUS_LICITACAO <> 'Rascunho'";

    if (!empty($tipoLicitacao) && $tipoLicitacao !== 'vazio') {
        $whereBase[] = "D.TIPO_LICITACAO = " . intval($tipoLicitacao);
    }
    if (!empty($tituloLicitacaoFilter)) {
        $tituloEscaped2 = str_replace("'", "''", $tituloLicitacaoFilter);
        $whereBase[] = "(D.COD_LICITACAO LIKE '%" . $tituloEscaped2 . "%' OR D.OBJETO_LICITACAO LIKE '%" . $tituloEscaped2 . "%')";
    }
    if (!empty($dtIniLicitacaoFilter) && !empty($dtFimLicitacaoFilter)) {
        $whereBase[] = "L.DT_LICITACAO BETWEEN '" . $dtIniLicitacaoFilter . "' AND '" . $dtFimLicitacaoFilter . " 23:59:59'";
    }

    $whereBaseClause = implode(" AND ", $whereBase);

    $queryStats = "SELECT 
                   COUNT(DISTINCT L.ID_LICITACAO) as total_geral,
                   COUNT(DISTINCT CASE WHEN D.STATUS_LICITACAO = 'Em Andamento' THEN L.ID_LICITACAO END) as total_andamento,
                   COUNT(DISTINCT CASE WHEN D.STATUS_LICITACAO = 'Encerrado' THEN L.ID_LICITACAO END) as total_encerrado,
                   COUNT(DISTINCT CASE WHEN D.STATUS_LICITACAO = 'Suspenso' THEN L.ID_LICITACAO END) as total_suspenso
               FROM LICITACAO L
               LEFT JOIN DETALHE_LICITACAO D ON D.ID_LICITACAO = L.ID_LICITACAO
               WHERE " . $whereBaseClause;

    $stmtStats = $pdoCAT->query($queryStats);
    $statsResult = $stmtStats ? $stmtStats->fetch(PDO::FETCH_ASSOC) : null;

    $stats = array(
        'total_geral' => $statsResult ? intval($statsResult['total_geral']) : 0,
        'total_andamento' => $statsResult ? intval($statsResult['total_andamento']) : 0,
        'total_encerrado' => $statsResult ? intval($statsResult['total_encerrado']) : 0,
        'total_suspenso' => $statsResult ? intval($statsResult['total_suspenso']) : 0
    );

    // Query principal - ATUALIZADA com campos de notificação
    $querySelect = "SELECT  
                    D.*, 
                    L.ID_LICITACAO, 
                    L.DT_LICITACAO, 
                    TIPO.NM_TIPO, 
                    TIPO.SGL_TIPO,
                    D.ENVIO_ATUALIZACAO_LICITACAO,
                    (SELECT COUNT(*) FROM ATUALIZACAO A 
                     WHERE A.ID_LICITACAO = L.ID_LICITACAO 
                     AND A.ID_ADM = $idUsuarioLogado 
                     AND A.DT_EXC_ATUALIZACAO IS NULL) AS USUARIO_INSCRITO,
                    (SELECT COUNT(*) FROM FAVORITO_LICITACAO F 
                     WHERE F.ID_LICITACAO = L.ID_LICITACAO 
                     AND F.ID_ADM = $idUsuarioLogado 
                     AND F.DT_EXC_FAVORITO IS NULL) AS USUARIO_FAVORITO
                FROM LICITACAO L
                LEFT JOIN DETALHE_LICITACAO D ON D.ID_LICITACAO = L.ID_LICITACAO
                LEFT JOIN TIPO_LICITACAO TIPO ON D.TIPO_LICITACAO = TIPO.ID_TIPO
                WHERE " . $whereClause . "
                ORDER BY L.DT_LICITACAO DESC
                OFFSET " . $offset . " ROWS FETCH NEXT " . $limite . " ROWS ONLY";

    $stmt = $pdoCAT->query($querySelect);
    if (!$stmt) {
        throw new Exception('Erro na query principal');
    }

    $licitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornar sucesso
    echo json_encode(array(
        'success' => true,
        'data' => $licitacoes,
        'total' => $totalRegistros,
        'pagina' => $pagina,
        'limite' => $limite,
        'stats' => $stats
    ));
} catch (Exception $e) {
    // Retornar erro detalhado
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'data' => array(),
        'total' => 0
    ));
}
