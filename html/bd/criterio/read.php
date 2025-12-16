<?php
//criterio/read.php

include_once 'bd/conexao.php';
include_once 'redirecionar.php';
include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] ORDER BY [NM_CRITERIO]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

?>

<style>
    .criterios-table {
        width: 100%;
        border-collapse: collapse;
    }

    .criterios-table thead {
        background: #f8fafc;
    }

    .criterios-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        letter-spacing: 0.5px;
    }

    .criterios-table th:nth-child(2),
    .criterios-table th:nth-child(3),
    .criterios-table th:nth-child(4) {
        text-align: center;
    }

    .criterios-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
        font-size: 14px;
    }

    .criterios-table tbody tr {
        transition: background 0.2s ease;
    }

    .criterios-table tbody tr:hover {
        background: #f8fafc;
    }

    .criterios-table input[type="text"] {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        background: #ffffff;
    }

    .criterios-table input[type="text"]:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .user-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.ativo {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .status-badge.inativo {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .btn-action {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
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

    .btn-save {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .btn-save:hover {
        background: #bbf7d0;
        border-color: #4ade80;
    }

    .btn-icon {
        color: #64748b;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-icon:hover {
        transform: scale(1.1);
    }

    .btn-icon.deactivate {
        color: #dc2626;
    }

    .btn-icon.deactivate:hover {
        color: #ef4444;
    }

    .btn-icon.activate {
        color: #16a34a;
    }

    .btn-icon.activate:hover {
        color: #22c55e;
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
        .criterios-table {
            font-size: 12px;
        }

        .criterios-table th,
        .criterios-table td {
            padding: 12px 8px;
        }

        .criterios-table thead th:nth-child(2) {
            display: none;
        }

        .criterios-table tbody td:nth-child(2) {
            display: none;
        }
    }
</style>

<?php
$count = 0;
$criterios = [];

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
    $criterios[] = $registros;
    $count++;
}

if ($count === 0) {
    echo '<div class="empty-state">';
    echo '<div class="empty-state-icon">⚖️</div>';
    echo '<h3>Nenhum critério cadastrado</h3>';
    echo '<p>Cadastre um novo critério de licitação usando o formulário acima.</p>';
    echo '</div>';
} else {
    echo "<table class='criterios-table'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th><i class='fas fa-gavel'></i> Nome do Critério</th>";
    echo "<th><i class='fas fa-user'></i> Login Criador</th>";
    echo "<th><i class='fas fa-toggle-on'></i> Status</th>";
    echo "<th style='text-align: center;'><i class='fas fa-power-off'></i> Ação</th>";
    echo "<th style='text-align: center;'><i class='fas fa-edit'></i> Editar</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($criterios as $registros) {
        $idCriterio = $registros['ID_CRITERIO'];
        $nmCriterio = $registros['NM_CRITERIO'];
        $dtExcCriterio = $registros['DT_EXC_CRITERIO'];
        $lgnCriadorCriterio = $registros['LGN_CRIADOR_CRITERIO'];

        echo "<tr id='row$idCriterio'>";

        echo "<td><label class='pubnmCriterio'>" . htmlspecialchars($nmCriterio) . "</label></td>";
        
        echo "<td style='text-align: center;'>";
        if ($lgnCriadorCriterio) {
            echo "<span class='user-badge'><i class='fas fa-user'></i> " . htmlspecialchars($lgnCriadorCriterio) . "</span>";
        } else {
            echo "<span style='color: #94a3b8;'>-</span>";
        }
        echo "</td>";
        
        echo "<td style='text-align: center;'>";
        if ($dtExcCriterio == null) {
            echo "<span class='status-badge ativo'>Ativo</span>";
        } else {
            echo "<span class='status-badge inativo'>Inativo</span>";
        }
        echo "</td>";

        echo "<td style='text-align: center;'>";
        if ($dtExcCriterio == null) {
            echo "<a href='bd/criterio/desativa.php?idCriterio=$idCriterio' title='Desativar Critério' class='btn-icon deactivate'>";
            echo "<i class='fas fa-times-circle'></i>";
            echo "</a>";
        } else {
            echo "<a href='bd/criterio/ativa.php?idCriterio=$idCriterio' title='Ativar Critério' class='btn-icon activate'>";
            echo "<i class='fas fa-check-circle'></i>";
            echo "</a>";
        }
        echo "</td>";

        echo "<td style='text-align: center;'>";
        echo "<button class='btn-action btn-save save-button' data-id='$idCriterio' hidden>";
        echo "<i class='fas fa-save'></i> Salvar";
        echo "</button>";
        echo "<button class='btn-action btn-edit edit-button' data-id='$idCriterio'>";
        echo "<i class='fas fa-edit'></i> Editar";
        echo "</button>";
        echo "</td>";

        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
}
?>

<script>
    $(document).ready(function() {
        $('.edit-button').on('click', function() {
            var rowId = $(this).data('id');
            $('#row' + rowId + ' .pubnmCriterio').replaceWith(`<input class='pubnmCriterio' type='text' value='${$('#row' + rowId + ' .pubnmCriterio').text()}' />`);
            $('#row' + rowId + ' .save-button').prop('hidden', false);
            $('#row' + rowId + ' .edit-button').prop('hidden', true);
        });

        $(document).on('click', '.save-button', function(event) {
            event.preventDefault();

            var rowId = $(this).data('id');
            var nmCriterio = $('#row' + rowId + ' .pubnmCriterio').val();

            if (nmCriterio.trim() === '') {
                alert('O campo nome é obrigatório. Por favor, preencha o campo.');
            } else {
                window.location.href = `bd/criterio/update.php?idCriterio=${rowId}&nmCriterio=${encodeURIComponent(nmCriterio)}`;
            }
        });
    });
</script>