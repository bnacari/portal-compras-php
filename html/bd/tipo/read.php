<?php
//tipo/read.php

include_once 'bd/conexao.php';
include_once 'redirecionar.php';

include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[TIPO_LICITACAO] ORDER BY [NM_TIPO]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

?>

<style>
    /* Table Styles */
    .tipos-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tipos-table thead {
        background: #f8fafc;
    }

    .tipos-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        letter-spacing: 0.5px;
    }

    .tipos-table th:nth-child(4),
    .tipos-table th:nth-child(5),
    .tipos-table th:nth-child(6) {
        text-align: center;
    }

    .tipos-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
        font-size: 14px;
    }

    .tipos-table tbody tr {
        transition: background 0.2s ease;
    }

    .tipos-table tbody tr:hover {
        background: #f8fafc;
    }

    /* Inputs inline na tabela */
    .tipos-table input[type="text"] {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        background: #ffffff;
    }

    .tipos-table input[type="text"]:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    /* Sigla Badge */
    .sigla-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        font-family: 'Courier New', monospace;
        letter-spacing: 0.5px;
    }

    /* User Badge */
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

    /* Status Badge */
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

    /* Action Buttons */
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
    @media (max-width: 1024px) {
        .tipos-table {
            font-size: 12px;
        }

        .tipos-table th,
        .tipos-table td {
            padding: 12px 8px;
        }

        .tipos-table thead th:nth-child(4) {
            display: none;
        }

        .tipos-table tbody td:nth-child(4) {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .tipos-table thead th:nth-child(3) {
            display: none;
        }

        .tipos-table tbody td:nth-child(3) {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .tipos-table thead th:nth-child(2) {
            display: none;
        }

        .tipos-table tbody td:nth-child(2) {
            display: none;
        }
    }
</style>

<?php
$count = 0;
$tipos = [];

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
    $tipos[] = $registros;
    $count++;
}

if ($count === 0) {
    echo '<div class="empty-state">';
    echo '<div class="empty-state-icon">📋</div>';
    echo '<h3>Nenhum tipo cadastrado</h3>';
    echo '<p>Cadastre um novo tipo de licitação usando o formulário acima.</p>';
    echo '</div>';
} else {
    echo "<table class='tipos-table'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th><i class='fas fa-tag'></i> Nome do Tipo</th>";
    echo "<th><i class='fas fa-code'></i> Sigla</th>";
    echo "<th><i class='fas fa-user'></i> Login Criador</th>";
    echo "<th><i class='fas fa-toggle-on'></i> Status</th>";
    echo "<th style='text-align: center;'><i class='fas fa-power-off'></i> Ação</th>";
    echo "<th style='text-align: center;'><i class='fas fa-edit'></i> Editar</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($tipos as $registros) {
        $idTipo = $registros['ID_TIPO'];
        $nmTipo = $registros['NM_TIPO'];
        $sglTipo = $registros['SGL_TIPO'];
        $dtExcTipo = $registros['DT_EXC_TIPO'];
        $lgnCriadorTipo = $registros['LGN_CRIADOR_TIPO'];

        echo "<tr id='row$idTipo'>";

        // Nome do Tipo
        echo "<td><label class='pubnmTipo'>" . htmlspecialchars($nmTipo) . "</label></td>";
        
        // Sigla (badge)
        echo "<td>";
        echo "<span class='sigla-badge'><label class='pubsglTipo'>" . htmlspecialchars($sglTipo) . "</label></span>";
        echo "</td>";
        
        // Login Criador
        echo "<td>";
        if ($lgnCriadorTipo) {
            echo "<span class='user-badge'><i class='fas fa-user'></i> " . htmlspecialchars($lgnCriadorTipo) . "</span>";
        } else {
            echo "<span style='color: #94a3b8;'>-</span>";
        }
        echo "</td>";
        
        // Status Badge
        echo "<td style='text-align: center;'>";
        if ($dtExcTipo == null) {
            echo "<span class='status-badge ativo'>Ativo</span>";
        } else {
            echo "<span class='status-badge inativo'>Inativo</span>";
        }
        echo "</td>";

        // Ativar/Desativar
        echo "<td style='text-align: center;'>";
        if ($dtExcTipo == null) {
            echo "<a href='bd/tipo/desativa.php?idTipo=$idTipo' title='Desativar Tipo' class='btn-icon deactivate'>";
            echo "<i class='fas fa-times-circle'></i>";
            echo "</a>";
        } else {
            echo "<a href='bd/tipo/ativa.php?idTipo=$idTipo' title='Ativar Tipo' class='btn-icon activate'>";
            echo "<i class='fas fa-check-circle'></i>";
            echo "</a>";
        }
        echo "</td>";

        // Editar
        echo "<td style='text-align: center;'>";
        echo "<button class='btn-action btn-save save-button' data-id='$idTipo' hidden>";
        echo "<i class='fas fa-save'></i> Salvar";
        echo "</button>";
        echo "<button class='btn-action btn-edit edit-button' data-id='$idTipo'>";
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
            $('#row' + rowId + ' .pubnmTipo').replaceWith(`<input class='pubnmTipo' type='text' value='${$('#row' + rowId + ' .pubnmTipo').text()}' />`);
            $('#row' + rowId + ' .pubsglTipo').replaceWith(`<input class='pubsglTipo' type='text' value='${$('#row' + rowId + ' .pubsglTipo').text()}' />`);

            $('#row' + rowId + ' .save-button').prop('hidden', false);
            $('#row' + rowId + ' .edit-button').prop('hidden', true);
        });

        $(document).on('click', '.save-button', function(event) {
            event.preventDefault();

            var rowId = $(this).data('id');
            var nmTipo = $('#row' + rowId + ' .pubnmTipo').val();
            var sglTipo = $('#row' + rowId + ' .pubsglTipo').val();

            if (nmTipo.trim() === '') {
                alert('Os campos são obrigatórios. Por favor, preencha todos os campos.');
            } else {
                window.location.href = `bd/tipo/update.php?idTipo=${rowId}&nmTipo=${encodeURIComponent(nmTipo)}&sglTipo=${encodeURIComponent(sglTipo)}`;
            }
        });
    });
</script>