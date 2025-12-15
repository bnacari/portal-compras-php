<?php

include_once 'bd/conexao.php';
include_once 'redirecionar.php';
include_once('../../protect.php');

$login = $_SESSION['login'];

$querySelect2 = "SELECT A.ID_ATUALIZACAO, ADM.NM_ADM, A.EMAIL_ADM, A.DT_EXC_ATUALIZACAO, DL.*, L.DT_LICITACAO
                    FROM ATUALIZACAO A 
                    LEFT JOIN USUARIO ADM ON ADM.ID_ADM = A.ID_ADM
                    LEFT JOIN DETALHE_LICITACAO DL ON A.ID_LICITACAO = DL.ID_LICITACAO
                    LEFT JOIN LICITACAO L ON L.ID_LICITACAO = DL.ID_LICITACAO
                    WHERE ADM.STATUS LIKE 'A'
                    AND A.DT_EXC_ATUALIZACAO IS NULL
                    AND ADM.LGN_ADM LIKE '$login'
                    ORDER BY L.DT_LICITACAO DESC
                ";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

?>

<style>
    /* Cards Container */
    .licitacoes-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Card Individual */
    .licitacao-item {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px 24px;
        transition: all 0.2s ease;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        position: relative;
    }

    .licitacao-item:hover {
        border-color: #d1d5db;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Barra lateral colorida */
    .licitacao-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #3b82f6;
        border-radius: 12px 0 0 12px;
    }

    /* Conteúdo principal */
    .item-content {
        flex: 1;
        min-width: 0;
    }

    /* Código da Licitação */
    .item-code {
        font-size: 16px;
        font-weight: 700;
        color: #1e40af;
        text-decoration: none;
        margin-bottom: 8px;
        display: inline-block;
        transition: color 0.2s ease;
    }

    .item-code:hover {
        color: #3b82f6;
        text-decoration: underline;
    }

    /* Objeto */
    .item-objeto {
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 12px;
    }

    /* Footer com Status e Data */
    .item-footer {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid;
    }

    .status-badge.andamento {
        background: #dbeafe;
        color: #1e40af;
        border-color: #93c5fd;
    }

    .status-badge.suspenso {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }

    .status-badge.rascunho {
        background: #f3f4f6;
        color: #374151;
        border-color: #d1d5db;
    }

    .status-badge.encerrado {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    /* Data */
    .item-date {
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    /* Botão de Ação */
    .item-action {
        display: flex;
        align-items: flex-start;
        padding-top: 2px;
    }

    .btn-remove {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: transparent;
        border: none;
        color: #dc2626;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-remove:hover {
        background: #fee2e2;
        transform: scale(1.1);
    }

    .btn-remove i {
        font-size: 20px;
    }

    /* Empty State */
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

    /* Responsividade */
    @media (max-width: 768px) {
        .licitacao-item {
            padding: 16px 20px;
            flex-direction: column;
            gap: 12px;
        }

        .item-action {
            width: 100%;
            justify-content: flex-end;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
        }

        .item-footer {
            gap: 8px;
        }
    }

    @media (max-width: 480px) {
        .licitacao-item {
            padding: 14px 16px;
        }

        .item-code {
            font-size: 14px;
        }

        .item-objeto {
            font-size: 13px;
        }

        .status-badge {
            font-size: 11px;
            padding: 3px 10px;
        }

        .item-date {
            font-size: 12px;
        }
    }
</style>

<?php
$count = 0;
$licitacoes = [];

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
    $licitacoes[] = $registros;
    $count++;
}

if ($count === 0) {
    // Estado vazio
    echo '<div class="empty-state">';
    echo '<div class="empty-state-icon">🔕</div>';
    echo '<h3>Nenhuma licitação acompanhada</h3>';
    echo '<p>Você não está recebendo notificações de nenhuma licitação no momento.</p>';
    echo '</div>';
} else {
    // Lista de cards
    echo '<div class="licitacoes-list">';
    
    foreach ($licitacoes as $registros) {
        $idAtualizacao = $registros['ID_ATUALIZACAO'];
        $dtExcAtualizacao = $registros['DT_EXC_ATUALIZACAO'];
        $nmUsuario = $registros['NM_ADM'];
        $email = $registros['EMAIL_ADM'];
        $idLicitacao = $registros['ID_LICITACAO'];
        $codLicitacao = $registros['COD_LICITACAO'];
        $statusLicitacao = $registros['STATUS_LICITACAO'];
        $objLicitacao = $registros['OBJETO_LICITACAO'];
        $dtLicitacao = isset($registros['DT_LICITACAO']) ? date('d/m/Y', strtotime($registros['DT_LICITACAO'])) : '';
        
        // Determinar classe do status
        $statusClass = 'andamento';
        if ($statusLicitacao == 'Suspenso') {
            $statusClass = 'suspenso';
        } else if ($statusLicitacao == 'Rascunho') {
            $statusClass = 'rascunho';
        } else if ($statusLicitacao == 'Encerrado') {
            $statusClass = 'encerrado';
        }
        
        echo '<div class="licitacao-item">';
        
        // Conteúdo principal
        echo '<div class="item-content">';
        
        // Código da Licitação
        echo '<a href="viewLicitacao.php?idLicitacao=' . $idLicitacao . '" class="item-code">';
        echo htmlspecialchars($codLicitacao);
        echo '</a>';
        
        // Objeto da Licitação
        echo '<div class="item-objeto">';
        echo htmlspecialchars($objLicitacao);
        echo '</div>';
        
        // Footer com Status e Data
        echo '<div class="item-footer">';
        
        // Status Badge
        echo '<span class="status-badge ' . $statusClass . '">';
        echo $statusLicitacao;
        echo '</span>';
        
        // Data
        if ($dtLicitacao) {
            echo '<span class="item-date">' . $dtLicitacao . '</span>';
        }
        
        echo '</div>'; // fim item-footer
        
        echo '</div>'; // fim item-content
        
        // Ação de remover
        echo '<div class="item-action">';
        if (!isset($dtExcAtualizacao)) {
            echo '<a href="bd/atualizacao/desativar.php?idAtualizacao=' . $idAtualizacao . '" class="btn-remove" title="Não desejo ser lembrado sobre futuras atualizações">';
            echo '<i class="fas fa-times-circle"></i>';
            echo '</a>';
        }
        echo '</div>';
        
        echo '</div>'; // fim licitacao-item
    }
    
    echo '</div>'; // fim licitacoes-list
}
?>