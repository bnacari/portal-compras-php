<?php
//usuario/read.php

include_once 'bd/conexao.php';
include_once('../../protectAdmin.php');

$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$perfilUsuario = filter_input(INPUT_POST, 'perfilUsuario', FILTER_SANITIZE_SPECIAL_CHARS);

?>

<style>
    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 20px;
    }

    .user-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        transition: all 0.3s ease;
        position: relative;
    }

    .user-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-color: #cbd5e1;
    }

    .user-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 16px 0 0 16px;
    }

    .user-header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .user-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .user-login {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .user-status {
        position: absolute;
        top: 16px;
        right: 16px;
    }

    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .status-dot.active {
        background: #22c55e;
    }

    .status-dot.inactive {
        background: #ef4444;
    }

    .user-details {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 16px;
    }

    .detail-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #475569;
    }

    .detail-row i {
        color: #94a3b8;
        width: 16px;
        text-align: center;
    }

    .detail-value {
        color: #1e293b;
        font-weight: 500;
    }

    .user-perfis {
        margin-bottom: 16px;
    }

    .perfis-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .perfis-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .perfil-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 600;
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .no-perfis {
        font-size: 12px;
        color: #94a3b8;
        font-style: italic;
    }

    .user-actions {
        display: flex;
        gap: 8px;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
    }

    .btn-action-card {
        flex: 1;
        padding: 10px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-edit {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .btn-edit:hover {
        background: #dbeafe;
        border-color: #93c5fd;
    }

    .btn-deactivate {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .btn-deactivate:hover {
        background: #fecaca;
        border-color: #fca5a5;
    }

    .btn-activate {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .btn-activate:hover {
        background: #bbf7d0;
        border-color: #4ade80;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f9fafb;
        border-radius: 12px;
        border: 1px dashed #d1d5db;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.4;
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 600;
        color: #374151;
        margin: 0 0 8px 0;
    }

    .empty-state p {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
    }

    @media (max-width: 768px) {
        .users-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }
</style>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $querySelect2 = "WITH UserLicitacao AS (
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
                        WHERE
                        (U.sAMAccountName LIKE '%$nome%' OR U.displayName LIKE '%$nome%' OR A.NM_ADM like '%$nome%' OR A.EMAIL_ADM LIKE '%$nome%')
                    ";

    if ($perfilUsuario != 0) {
        $querySelect2 .= " AND PU.ID_TIPO_LICITACAO = $perfilUsuario";
    }

    $querySelect2 .= ")
    SELECT *
    FROM UserLicitacao 
    ORDER BY displayName, NM_ADM";

    $querySelect = $pdoCAT->query($querySelect2);

    $usuarios = [];
    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
        $matricula = $registros['initials'];
        $email = isset($registros['mail']) ? $registros['mail'] : $registros['EMAIL_ADM'];
        
        // Pular linhas sem email válido
        if (!$email) continue;
        
        if (!isset($usuarios[$email])) {
            $usuarios[$email] = [
                'matricula' => $matricula,
                'nome' => isset($registros['displayName']) ? $registros['displayName'] : $registros['NM_ADM'],
                'unidade' => isset($registros['department']) ? $registros['department'] : 'externo',
                'login' => isset($registros['sAMAccountName']) ? $registros['sAMAccountName'] : $registros['LGN_ADM'],
                'email' => $email,
                'status' => $registros['STATUS'],
                'perfis' => []
            ];
        }
        
        // Adicionar perfil se existir e não for nulo
        if (isset($registros['NM_TIPO']) && $registros['NM_TIPO'] != null && trim($registros['NM_TIPO']) != '' && !in_array($registros['NM_TIPO'], $usuarios[$email]['perfis'])) {
            $usuarios[$email]['perfis'][] = $registros['NM_TIPO'];
            
            // DEBUG: Descomente a linha abaixo para ver os perfis sendo adicionados
            // echo "<!-- Adicionado perfil '" . $registros['NM_TIPO'] . "' para " . $email . " -->\n";
        }
    }

    if (count($usuarios) === 0) {
        echo '<div class="empty-state">';
        echo '<div class="empty-state-icon">🔍</div>';
        echo '<h3>Nenhum usuário encontrado</h3>';
        echo '<p>Tente ajustar os filtros de busca para encontrar usuários.</p>';
        echo '</div>';
    } else {
        echo '<div class="users-grid">';
        
        foreach ($usuarios as $usuario) {
            $iniciais = '';
            $nomeParts = explode(' ', $usuario['nome']);
            if (count($nomeParts) >= 2) {
                $iniciais = strtoupper(substr($nomeParts[0], 0, 1) . substr($nomeParts[1], 0, 1));
            } else {
                $iniciais = strtoupper(substr($usuario['nome'], 0, 2));
            }
            
            echo '<div class="user-card">';
            
            // Header com avatar e info
            echo '<div class="user-header">';
            echo '<div class="user-avatar">' . $iniciais . '</div>';
            echo '<div class="user-info">';
            echo '<h3 class="user-name">' . htmlspecialchars($usuario['nome']) . '</h3>';
            echo '<div class="user-login">';
            echo '<i class="fas fa-user"></i>';
            echo '<span>' . htmlspecialchars($usuario['login']) . '</span>';
            echo '</div>';
            echo '</div>';
            
            // Status dot
            echo '<div class="user-status">';
            if ($usuario['status'] == 'A') {
                echo '<div class="status-dot active" title="Ativo"></div>';
            } else {
                echo '<div class="status-dot inactive" title="Inativo"></div>';
            }
            echo '</div>';
            
            echo '</div>';
            
            // Detalhes
            echo '<div class="user-details">';
            
            if ($usuario['matricula']) {
                echo '<div class="detail-row">';
                echo '<i class="fas fa-id-card"></i>';
                echo '<span>Matrícula: <span class="detail-value">' . htmlspecialchars($usuario['matricula']) . '</span></span>';
                echo '</div>';
            }
            
            echo '<div class="detail-row">';
            echo '<i class="fas fa-building"></i>';
            echo '<span>Unidade: <span class="detail-value">' . htmlspecialchars($usuario['unidade']) . '</span></span>';
            echo '</div>';
            
            echo '<div class="detail-row">';
            echo '<i class="fas fa-envelope"></i>';
            echo '<span class="detail-value">' . htmlspecialchars($usuario['email']) . '</span>';
            echo '</div>';
            
            echo '</div>';
            
            // Perfis
            echo '<div class="user-perfis">';
            echo '<div class="perfis-label">Perfis de Acesso</div>';
            if (count($usuario['perfis']) > 0) {
                echo '<div class="perfis-list">';
                foreach ($usuario['perfis'] as $perfil) {
                    echo '<span class="perfil-badge">' . htmlspecialchars($perfil) . '</span>';
                }
                echo '</div>';
            } else {
                echo '<div class="no-perfis">Nenhum perfil atribuído</div>';
            }
            echo '</div>';
            
            // Ações
            echo '<div class="user-actions">';
            
            foreach ($_SESSION['perfil'] as $perfil) {
                if ($perfil['idPerfil'] == 9) {
                    // Montar URL com parâmetros de busca para voltar
                    $urlParams = '';
                    if (isset($_POST['nome']) && $_POST['nome'] != '') {
                        $urlParams .= '&nome=' . urlencode($_POST['nome']);
                    }
                    if (isset($_POST['perfilUsuario']) && $_POST['perfilUsuario'] != '0') {
                        $urlParams .= '&perfil=' . urlencode($_POST['perfilUsuario']);
                    }
                    
                    echo '<a href="editarUsuario.php?email=' . urlencode($usuario['email']) . $urlParams . '" class="btn-action-card btn-edit">';
                    echo '<i class="fas fa-edit"></i>';
                    echo '<span>Editar</span>';
                    echo '</a>';
                    
                    if ($usuario['unidade'] == 'externo') {
                        if ($usuario['status'] == 'A') {
                            echo '<a href="bd/usuario/desativa.php?email=' . urlencode($usuario['email']) . '" class="btn-action-card btn-deactivate">';
                            echo '<i class="fas fa-times-circle"></i>';
                            echo '<span>Desativar</span>';
                            echo '</a>';
                        } else {
                            echo '<a href="bd/usuario/ativa.php?email=' . urlencode($usuario['email']) . '" class="btn-action-card btn-activate">';
                            echo '<i class="fas fa-check-circle"></i>';
                            echo '<span>Ativar</span>';
                            echo '</a>';
                        }
                    }
                }
            }
            
            echo '</div>';
            
            echo '</div>';
        }
        
        echo '</div>';
    }
} else {
    echo '<div class="empty-state">';
    echo '<div class="empty-state-icon">👆</div>';
    echo '<h3>Faça uma busca</h3>';
    echo '<p>Use os filtros acima para buscar usuários no sistema.</p>';
    echo '</div>';
}
?>