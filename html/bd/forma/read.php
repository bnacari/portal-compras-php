<?php
//forma/read.php

include_once 'bd/conexao.php';
include_once 'redirecionar.php';

include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] ORDER BY [NM_FORMA]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

?>

<style>
    .formas-table {
        width: 100%;
        border-collapse: collapse;
    }

    .formas-table thead {
        background: #f8fafc;
    }

    .formas-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        letter-spacing: 0.5px;
    }

    .formas-table th:nth-child(2),
    .formas-table th:nth-child(3),
    .formas-table th:nth-child(4) {
        text-align: center;
    }

    .formas-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
        font-size: 14px;
    }

    .formas-table tbody tr {
        transition: background 0.2s ease;
    }

    .formas-table tbody tr:hover {
        background: #f8fafc;
    }

    .formas-table input[type="text"] {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        background: #ffffff;
    }

    .formas-table input[type="text"]:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
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
        .formas-table {
            font-size: 12px;
        }

        .formas-table th,
        .formas-table td {
            padding: 12px 8px;
        }

        .formas-table thead th:nth-child(2) {
            display: none;
        }

        .formas-table tbody td:nth-child(2) {
            display: none;
        }
    }
</style>

<?php
$count = 0;
$formas = [];

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
    $formas[] = $registros;
    $count++;
}

if ($count === 0) {
    echo '<div class="empty-state">';
    echo '<div class="empty-state-icon">📄</div>';
    echo '<h3>Nenhuma forma cadastrada</h3>';
    echo '<p>Cadastre uma nova forma de licitação usando o formulário acima.</p>';
    echo '</div>';
} else {
    echo "<table class='formas-table'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th><i class='fas fa-tag'></i> Nome da Forma</th>";
    echo "<th><i class='fas fa-toggle-on'></i> Status</th>";
    echo "<th style='text-align: center;'><i class='fas fa-power-off'></i> Ação</th>";
    echo "<th style='text-align: center;'><i class='fas fa-edit'></i> Editar</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($formas as $registros) {
        $idForma = $registros['ID_FORMA'];
        $nmForma = $registros['NM_FORMA'];
        $dtExcForma = $registros['DT_EXC_FORMA'];

        echo "<tr id='row$idForma'>";

        echo "<td><label class='nmForma'>" . htmlspecialchars($nmForma) . "</label></td>";
        
        echo "<td style='text-align: center;'>";
        if ($dtExcForma == null) {
            echo "<span class='status-badge ativo'>Ativo</span>";
        } else {
            echo "<span class='status-badge inativo'>Inativo</span>";
        }
        echo "</td>";

        echo "<td style='text-align: center;'>";
        if ($dtExcForma == null) {
            echo "<a href='bd/forma/desativa.php?idForma=$idForma' title='Desativar Forma' class='btn-icon deactivate'>";
            echo "<i class='fas fa-times-circle'></i>";
            echo "</a>";
        } else {
            echo "<a href='bd/forma/ativa.php?idForma=$idForma' title='Ativar Forma' class='btn-icon activate'>";
            echo "<i class='fas fa-check-circle'></i>";
            echo "</a>";
        }
        echo "</td>";

        echo "<td style='text-align: center;'>";
        echo "<button class='btn-action btn-save save-button' data-id='$idForma' hidden>";
        echo "<i class='fas fa-save'></i> Salvar";
        echo "</button>";
        echo "<button class='btn-action btn-edit edit-button' data-id='$idForma'>";
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
            var nmForma = $('#row' + rowId + ' .nmForma').text();

            $('#row' + rowId + ' .nmForma').replaceWith(`<input class='nmForma' type='text' value='${nmForma}' />`);

            $('#row' + rowId + ' .save-button').prop('hidden', false);
            $('#row' + rowId + ' .edit-button').prop('hidden', true);
        });

        $(document).on('click', '.save-button', function(event) {
            event.preventDefault();

            var rowId = $(this).data('id');
            var nmForma = $('#row' + rowId + ' .nmForma').val();

            if (nmForma.trim() === '') {
                alert('O campo nome é obrigatório. Por favor, preencha o campo.');
            } else {
                window.location.href = `bd/forma/update.php?idForma=${rowId}&nmForma=${encodeURIComponent(nmForma)}`;
            }
        });
    });
</script>