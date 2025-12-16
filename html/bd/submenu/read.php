<?php
//submenu/read.php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';

include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * 
                 FROM [PortalCompras].[dbo].SUBMENU SM
                 LEFT JOIN MENU M ON M.ID_MENU = SM.ID_MENU
                 ORDER BY [NM_MENU], [NM_SUBMENU]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

?>

<style>
    /* Table Styles */
    .submenus-table {
        width: 100%;
        border-collapse: collapse;
    }

    .submenus-table thead {
        background: #f8fafc;
    }

    .submenus-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        letter-spacing: 0.5px;
    }

    .submenus-table th:nth-child(4),
    .submenus-table th:nth-child(5),
    .submenus-table th:nth-child(6) {
        text-align: center;
    }

    .submenus-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
        font-size: 14px;
    }

    .submenus-table tbody tr {
        transition: background 0.2s ease;
    }

    .submenus-table tbody tr:hover {
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

    /* Menu Badge */
    .menu-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
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
    @media (max-width: 768px) {
        .submenus-table {
            font-size: 12px;
        }

        .submenus-table th,
        .submenus-table td {
            padding: 12px 8px;
        }

        .submenus-table thead th:nth-child(4) {
            display: none;
        }

        .submenus-table tbody td:nth-child(4) {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .submenus-table thead th:nth-child(3) {
            display: none;
        }

        .submenus-table tbody td:nth-child(3) {
            display: none;
        }
    }
</style>

<?php
$count = 0;
$submenus = [];

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
    $submenus[] = $registros;
    $count++;
}

if ($count === 0) {
    echo '<div class="empty-state">';
    echo '<div class="empty-state-icon">📋</div>';
    echo '<h3>Nenhum submenu cadastrado</h3>';
    echo '<p>Cadastre um novo submenu usando o formulário acima.</p>';
    echo '</div>';
} else {
    echo "<table class='submenus-table'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th><i class='fas fa-tag'></i> Nome</th>";
    echo "<th><i class='fas fa-sitemap'></i> Menu</th>";
    echo "<th><i class='fas fa-link'></i> Link</th>";
    echo "<th><i class='fas fa-toggle-on'></i> Status</th>";
    echo "<th style='text-align: center;'><i class='fas fa-power-off'></i> Ação</th>";
    echo "<th style='text-align: center;'><i class='fas fa-edit'></i> Editar</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($submenus as $registros) {
        $idSubMenu = $registros['ID_SUBMENU'];
        $nmSubMenu = $registros['NM_SUBMENU'];
        $linkSubMenu = $registros['LINK_SUBMENU'];
        $dtExcSubMenu = $registros['DT_EXC_SUBMENU'];
        $nmMenu = $registros['NM_MENU'];

        echo "<tr id='row$idSubMenu'>";

        // Nome do SubMenu
        echo "<td><label class='nmMenu'>" . htmlspecialchars($nmSubMenu) . "</label></td>";
        
        // Menu relacionado (badge)
        echo "<td>";
        echo "<span class='menu-badge'>" . htmlspecialchars($nmMenu) . "</span>";
        echo "</td>";
        
        // Link
        echo "<td><label class='linkMenu'>" . htmlspecialchars($linkSubMenu) . "</label></td>";
        
        // Status Badge
        echo "<td style='text-align: center;'>";
        if ($dtExcSubMenu == null) {
            echo "<span class='status-badge ativo'>Ativo</span>";
        } else {
            echo "<span class='status-badge inativo'>Inativo</span>";
        }
        echo "</td>";

        // Ativar/Desativar
        echo "<td style='text-align: center;'>";
        if ($dtExcSubMenu == null) {
            echo "<a href='bd/submenu/desativa.php?idSubMenu=$idSubMenu' title='Desativar SubMenu' class='btn-icon deactivate'>";
            echo "<i class='fas fa-times-circle'></i>";
            echo "</a>";
        } else {
            echo "<a href='bd/submenu/ativa.php?idSubMenu=$idSubMenu' title='Ativar SubMenu' class='btn-icon activate'>";
            echo "<i class='fas fa-check-circle'></i>";
            echo "</a>";
        }
        echo "</td>";

        // Editar
        echo "<td style='text-align: center;'>";
        if (isset($_SESSION['isAdmin'])) {
            echo "<a href='editarSubMenu.php?idSubMenu=$idSubMenu' title='Editar SubMenu' class='btn-icon edit'>";
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