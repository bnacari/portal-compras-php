<?php
//itemmenu/read.php

include_once 'bd/conexao.php';
include_once 'redirecionar.php';

include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * 
                FROM [PortalCompras].[dbo].ItemMENU IM
                LEFT JOIN SUBMENU SM ON SM.ID_SUBMENU = IM.ID_SUBMENU
                LEFT JOIN MENU M ON M.ID_MENU = SM.ID_MENU
                ORDER BY [NM_SUBMENU], [NM_ItemMENU]
                    ";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

?>

<style>
    /* Table Styles */
    .itensmenus-table {
        width: 100%;
        border-collapse: collapse;
    }

    .itensmenus-table thead {
        background: #f8fafc;
    }

    .itensmenus-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        letter-spacing: 0.5px;
    }

    .itensmenus-table th:nth-child(5),
    .itensmenus-table th:nth-child(6),
    .itensmenus-table th:nth-child(7) {
        text-align: center;
    }

    .itensmenus-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
        font-size: 14px;
    }

    .itensmenus-table tbody tr {
        transition: background 0.2s ease;
    }

    .itensmenus-table tbody tr:hover {
        background: #f8fafc;
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

    /* Menu/SubMenu Badges */
    .menu-badge,
    .submenu-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid;
    }

    .menu-badge {
        background: #eff6ff;
        color: #1e40af;
        border-color: #bfdbfe;
    }

    .submenu-badge {
        background: #f1f5f9;
        color: #475569;
        border-color: #e2e8f0;
    }

    /* Action Buttons */
    .btn-icon {
        color: #64748b;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
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

    .btn-icon.edit {
        color: #3b82f6;
    }

    .btn-icon.edit:hover {
        color: #2563eb;
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
        .itensmenus-table {
            font-size: 12px;
        }

        .itensmenus-table th,
        .itensmenus-table td {
            padding: 12px 8px;
        }

        .itensmenus-table thead th:nth-child(5) {
            display: none;
        }

        .itensmenus-table tbody td:nth-child(5) {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .itensmenus-table thead th:nth-child(4) {
            display: none;
        }

        .itensmenus-table tbody td:nth-child(4) {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .itensmenus-table thead th:nth-child(3) {
            display: none;
        }

        .itensmenus-table tbody td:nth-child(3) {
            display: none;
        }
    }
</style>

<?php
$count = 0;
$itensmenus = [];

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
    $itensmenus[] = $registros;
    $count++;
}

if ($count === 0) {
    echo '<div class="empty-state">';
    echo '<div class="empty-state-icon">📋</div>';
    echo '<h3>Nenhum item de menu cadastrado</h3>';
    echo '<p>Cadastre um novo item de menu usando o formulário acima.</p>';
    echo '</div>';
} else {
    echo "<table class='itensmenus-table'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th><i class='fas fa-tag'></i> Nome</th>";
    echo "<th><i class='fas fa-sitemap'></i> Menu</th>";
    echo "<th><i class='fas fa-layer-group'></i> SubMenu</th>";
    echo "<th><i class='fas fa-link'></i> Link</th>";
    echo "<th><i class='fas fa-toggle-on'></i> Status</th>";
    echo "<th style='text-align: center;'><i class='fas fa-power-off'></i> Ação</th>";
    echo "<th style='text-align: center;'><i class='fas fa-edit'></i> Editar</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($itensmenus as $registros) {
        $idItemMenu = $registros['ID_ITEMMENU'];
        $nmItemMenu = $registros['NM_ITEMMENU'];
        $linkItemMenu = $registros['LINK_ITEMMENU'];
        $dtExcItemMenu = $registros['DT_EXC_ITEMMENU'];
        $nmSubMenu = $registros['NM_SUBMENU'];
        $nmMenu = $registros['NM_MENU'];

        echo "<tr id='row$idItemMenu'>";

        // Nome do ItemMenu
        echo "<td><label class='nmMenu'>" . htmlspecialchars($nmItemMenu) . "</label></td>";
        
        // Menu relacionado (badge)
        echo "<td>";
        echo "<span class='menu-badge'>" . htmlspecialchars($nmMenu) . "</span>";
        echo "</td>";
        
        // SubMenu relacionado (badge)
        echo "<td>";
        echo "<span class='submenu-badge'>" . htmlspecialchars($nmSubMenu) . "</span>";
        echo "</td>";
        
        // Link
        echo "<td><label class='linkMenu'>" . htmlspecialchars($linkItemMenu) . "</label></td>";
        
        // Status Badge
        echo "<td style='text-align: center;'>";
        if ($dtExcItemMenu == null) {
            echo "<span class='status-badge ativo'>Ativo</span>";
        } else {
            echo "<span class='status-badge inativo'>Inativo</span>";
        }
        echo "</td>";

        // Ativar/Desativar
        echo "<td style='text-align: center;'>";
        if ($dtExcItemMenu == null) {
            echo "<a href='bd/itemmenu/desativa.php?idItemMenu=$idItemMenu' title='Desativar ItemMenu' class='btn-icon deactivate'>";
            echo "<i class='fas fa-times-circle'></i>";
            echo "</a>";
        } else {
            echo "<a href='bd/itemmenu/ativa.php?idItemMenu=$idItemMenu' title='Ativar ItemMenu' class='btn-icon activate'>";
            echo "<i class='fas fa-check-circle'></i>";
            echo "</a>";
        }
        echo "</td>";

        // Editar
        echo "<td style='text-align: center;'>";
        if (isset($_SESSION['isAdmin'])) {
            echo "<a href='editarItemMenu.php?idItemMenu=$idItemMenu' title='Editar ItemMenu' class='btn-icon edit'>";
            echo "<i class='fas fa-edit'></i>";
            echo "</a>";
        }
        echo "</td>";

        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
}
?>