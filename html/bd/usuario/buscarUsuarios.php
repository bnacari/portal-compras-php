<?php
/**
 * Portal de Compras - API de Busca de Usuários
 * Endpoint AJAX para pesquisa em tempo real
 */

session_start();

// Caminho relativo correto (arquivo está em bd/usuario/)
include_once __DIR__ . '/../conexao.php';

header('Content-Type: application/json');

// Verifica autenticação
if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    exit();
}

// Parâmetros de busca
$nome = filter_input(INPUT_GET, 'nome', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$perfilUsuario = filter_input(INPUT_GET, 'perfilUsuario', FILTER_SANITIZE_SPECIAL_CHARS) ?? '0';
$usuSistema = filter_input(INPUT_GET, 'usuSistema', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'todos';

// Requer pelo menos 2 caracteres para buscar (evita sobrecarga)
if (strlen(trim($nome)) < 2 && $perfilUsuario == '0' && $usuSistema == 'todos') {
    echo json_encode([
        'success' => true,
        'total' => 0,
        'usuarios' => [],
        'message' => 'Digite pelo menos 2 caracteres para pesquisar'
    ]);
    exit();
}

// Monta a query
$queryUsuarios = "WITH UserLicitacao AS (
    SELECT 
        U.mail,
        U.sAMAccountName,
        U.initials,
        U.department,
        U.displayName,
        A.ID_ADM,
        A.NM_ADM,
        A.EMAIL_ADM,
        A.LGN_ADM,
        A.STATUS,
        TL.NM_TIPO,
        TL.ID_TIPO
    FROM [ADCache].[dbo].[Users] U 
    FULL OUTER JOIN portalcompras.dbo.USUARIO A ON A.EMAIL_ADM COLLATE SQL_Latin1_General_CP1_CI_AI = U.mail COLLATE SQL_Latin1_General_CP1_CI_AI 
    LEFT JOIN portalcompras.dbo.PERFIL_USUARIO PU ON PU.ID_USUARIO = A.ID_ADM
    LEFT JOIN portalcompras.dbo.TIPO_LICITACAO TL ON TL.ID_TIPO = PU.ID_TIPO_LICITACAO
    WHERE 1=1";

// Filtro de nome/matrícula (se informado)
if (!empty(trim($nome))) {
    $queryUsuarios .= " AND (U.sAMAccountName LIKE '%$nome%' OR U.displayName LIKE '%$nome%' OR A.NM_ADM LIKE '%$nome%' OR A.EMAIL_ADM LIKE '%$nome%' OR U.initials LIKE '%$nome%')";
}

if ($perfilUsuario != '0' && !empty($perfilUsuario)) {
    $queryUsuarios .= " AND PU.ID_TIPO_LICITACAO = $perfilUsuario";
}

if ($usuSistema == 'sim') {
    $queryUsuarios .= " AND PU.ID_TIPO_LICITACAO IS NOT NULL";
} elseif ($usuSistema == 'nao') {
    $queryUsuarios .= " AND (PU.ID_TIPO_LICITACAO IS NULL)";
}

$queryUsuarios .= ") SELECT * FROM UserLicitacao ORDER BY displayName, NM_ADM";

try {
    $resultUsuarios = $pdoCAT->query($queryUsuarios);
    
    // Agrupa usuários por email (para juntar perfis)
    $usuariosAgrupados = array();
    
    while ($row = $resultUsuarios->fetch(PDO::FETCH_ASSOC)) {
        $matricula = $row['initials'] ?? '';
        $email = isset($row['mail']) ? $row['mail'] : $row['EMAIL_ADM'];
        
        // Pular linhas sem email válido
        if (!$email) continue;
        
        if (!isset($usuariosAgrupados[$email])) {
            $usuariosAgrupados[$email] = array(
                'matricula' => !empty($matricula) && $matricula != '0' ? $matricula : '-',
                'nome' => isset($row['displayName']) ? $row['displayName'] : ($row['NM_ADM'] ?? '-'),
                'unidade' => isset($row['department']) ? $row['department'] : 'Externo',
                'login' => isset($row['sAMAccountName']) ? $row['sAMAccountName'] : ($row['LGN_ADM'] ?? '-'),
                'email' => $email,
                'status' => $row['STATUS'] ?? 'A',
                'perfis' => array(),
                'temMatricula' => !empty($matricula) && $matricula != '0'
            );
        }
        
        // Adicionar perfil se existir
        if (isset($row['NM_TIPO']) && $row['NM_TIPO'] != null && !in_array($row['NM_TIPO'], $usuariosAgrupados[$email]['perfis'])) {
            $usuariosAgrupados[$email]['perfis'][] = $row['NM_TIPO'];
        }
    }
    
    // Converte para array final
    $usuarios = array();
    foreach ($usuariosAgrupados as $email => $u) {
        $usuarios[] = array(
            'matricula' => $u['matricula'],
            'nome' => $u['nome'],
            'unidade' => $u['unidade'],
            'login' => $u['login'],
            'email' => $u['email'],
            'status' => $u['status'],
            'perfil' => count($u['perfis']) > 0 ? implode(', ', $u['perfis']) : '',
            'temMatricula' => $u['temMatricula']
        );
    }

    echo json_encode([
        'success' => true,
        'total' => count($usuarios),
        'usuarios' => $usuarios
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar usuários: ' . $e->getMessage()
    ]);
}