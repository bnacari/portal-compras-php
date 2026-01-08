<?php
/**
 * Portal de Compras - Administração Unificada
 * Tela com abas: Tipos, Critérios, Formas, Estrutura de Menus, Usuários, Perfis
 */

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

// Determina aba ativa
$abaAtiva = $_GET['aba'] ?? 'tipos';

// Se for aba de estrutura, buscar dados hierárquicos
if ($abaAtiva == 'estrutura') {
    $queryMenus = $pdoCAT->query("
        SELECT 
            M.ID_MENU, M.NM_MENU, M.LINK_MENU, M.DT_EXC_MENU,
            SM.ID_SUBMENU, SM.NM_SUBMENU, SM.LINK_SUBMENU, SM.DT_EXC_SUBMENU,
            IM.ID_ITEMMENU, IM.NM_ITEMMENU, IM.LINK_ITEMMENU, IM.DT_EXC_ITEMMENU
        FROM [PortalCompras].[dbo].[MENU] M
        LEFT JOIN [PortalCompras].[dbo].[SUBMENU] SM ON SM.ID_MENU = M.ID_MENU
        LEFT JOIN [PortalCompras].[dbo].[ITEMMENU] IM ON IM.ID_SUBMENU = SM.ID_SUBMENU
        ORDER BY M.NM_MENU, SM.NM_SUBMENU, IM.NM_ITEMMENU
    ");

    $menus = [];
    while ($row = $queryMenus->fetch(PDO::FETCH_ASSOC)) {
        $menuId = $row['ID_MENU'];
        $subMenuId = $row['ID_SUBMENU'];
        
        if (!isset($menus[$menuId])) {
            $menus[$menuId] = [
                'id' => $menuId,
                'nome' => $row['NM_MENU'],
                'link' => $row['LINK_MENU'],
                'inativo' => !empty($row['DT_EXC_MENU']),
                'submenus' => []
            ];
        }
        
        if ($subMenuId && !isset($menus[$menuId]['submenus'][$subMenuId])) {
            $menus[$menuId]['submenus'][$subMenuId] = [
                'id' => $subMenuId,
                'nome' => $row['NM_SUBMENU'],
                'link' => trim($row['LINK_SUBMENU'] ?? ''),
                'inativo' => !empty($row['DT_EXC_SUBMENU']),
                'itens' => []
            ];
        }
        
        if ($row['ID_ITEMMENU']) {
            $menus[$menuId]['submenus'][$subMenuId]['itens'][] = [
                'id' => $row['ID_ITEMMENU'],
                'nome' => $row['NM_ITEMMENU'],
                'link' => trim($row['LINK_ITEMMENU'] ?? ''),
                'inativo' => !empty($row['DT_EXC_ITEMMENU'])
            ];
        }
    }
    
    $totalMenus = 0;
    $totalSubMenus = 0;
    $totalItens = 0;
    foreach ($menus as $m) {
        if (!$m['inativo']) {
            $totalMenus++;
        }
        foreach ($m['submenus'] as $s) {
            if (!$s['inativo']) {
                $totalSubMenus++;
            }
            foreach ($s['itens'] as $i) {
                if (!$i['inativo']) {
                    $totalItens++;
                }
            }
        }
    }
}
?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* ============================================
       CSS Variables
       ============================================ */
    :root {
        --primary-50: #eff6ff;
        --primary-100: #dbeafe;
        --primary-500: #3b82f6;
        --primary-600: #2563eb;
        --primary-700: #1d4ed8;
        --success-50: #f0fdf4;
        --success-100: #dcfce7;
        --success-500: #22c55e;
        --success-600: #16a34a;
        --success-700: #15803d;
        --warning-50: #fffbeb;
        --warning-100: #fef3c7;
        --warning-500: #f59e0b;
        --warning-600: #d97706;
        --error-50: #fef2f2;
        --error-100: #fee2e2;
        --error-500: #ef4444;
        --error-600: #dc2626;
        --error-700: #b91c1c;
        --purple-50: #faf5ff;
        --purple-100: #f3e8ff;
        --purple-500: #a855f7;
        --purple-600: #9333ea;
        --purple-700: #7c3aed;
        --dark-50: #f8fafc;
        --dark-100: #f1f5f9;
        --dark-200: #e2e8f0;
        --dark-300: #cbd5e1;
        --dark-400: #94a3b8;
        --dark-500: #64748b;
        --dark-600: #475569;
        --dark-700: #334155;
        --dark-800: #1e293b;
        --dark-900: #0f172a;
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --radius-2xl: 20px;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --transition-fast: 0.15s ease;
        --transition-normal: 0.25s ease;
    }

    /* ============================================
       Page Container
       ============================================ */
    .admin-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 32px 24px;
    }

    /* ============================================
       Page Header
       ============================================ */
    .page-header {
        background: linear-gradient(135deg, var(--dark-900) 0%, var(--dark-800) 100%);
        border-radius: var(--radius-2xl);
        padding: 40px 48px;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header-content {
        display: flex;
        align-items: center;
        gap: 24px;
        position: relative;
        z-index: 1;
    }

    .page-header-icon {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 32px;
        backdrop-filter: blur(10px);
    }

    .page-header h1 {
        color: #ffffff;
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px 0;
        letter-spacing: -0.02em;
    }

    .page-header p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 16px;
        margin: 0;
    }

    /* ============================================
       Tabs Navigation
       ============================================ */
    .tabs-nav {
        display: flex;
        gap: 6px;
        background: #ffffff;
        padding: 8px;
        border-radius: var(--radius-xl);
        margin-bottom: 28px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--dark-200);
    }

    .tabs-nav::-webkit-scrollbar {
        height: 4px;
    }

    .tabs-nav::-webkit-scrollbar-track {
        background: var(--dark-100);
        border-radius: 4px;
    }

    .tabs-nav::-webkit-scrollbar-thumb {
        background: var(--dark-300);
        border-radius: 4px;
    }

    .tab-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 22px;
        font-size: 14px;
        font-weight: 500;
        color: var(--dark-500);
        text-decoration: none;
        border-radius: var(--radius-lg);
        transition: all var(--transition-normal);
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .tab-link:hover {
        color: var(--dark-700);
        background: var(--dark-50);
    }

    .tab-link.active {
        background: linear-gradient(135deg, var(--dark-900) 0%, var(--dark-800) 100%);
        color: #ffffff;
        font-weight: 600;
        box-shadow: var(--shadow-md);
    }

    .tab-link ion-icon {
        font-size: 20px;
    }

    /* ============================================
       Section Card
       ============================================ */
    .section-card {
        background: #ffffff;
        border: 1px solid var(--dark-200);
        border-radius: var(--radius-2xl);
        margin-bottom: 24px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: box-shadow var(--transition-normal);
    }

    .section-card:hover {
        box-shadow: var(--shadow-md);
    }

    .section-card-header {
        background: linear-gradient(135deg, var(--dark-900) 0%, var(--dark-800) 100%);
        padding: 20px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-card-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .section-card-header-left ion-icon {
        font-size: 22px;
        color: rgba(255, 255, 255, 0.9);
    }

    .section-card-header h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
        color: #ffffff;
        letter-spacing: -0.01em;
    }

    .section-card-body {
        padding: 28px;
    }

    /* ============================================
       Forms
       ============================================ */
    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-col-2 { grid-column: span 2; }
    .form-col-3 { grid-column: span 3; }
    .form-col-4 { grid-column: span 4; }
    .form-col-5 { grid-column: span 5; }
    .form-col-6 { grid-column: span 6; }
    .form-col-8 { grid-column: span 8; }
    .form-col-12 { grid-column: span 12; }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
        color: var(--dark-600);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 10px;
    }

    .form-label ion-icon {
        font-size: 15px;
        color: var(--dark-400);
    }

    .form-label .required {
        color: var(--error-500);
    }

    .form-control {
        height: 48px;
        padding: 0 16px;
        border: 2px solid var(--dark-200);
        border-radius: var(--radius-lg);
        font-size: 14px;
        color: var(--dark-800);
        background: #ffffff;
        transition: all var(--transition-fast);
    }

    .form-control:hover {
        border-color: var(--dark-300);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 4px var(--primary-100);
    }

    .form-control::placeholder {
        color: var(--dark-400);
    }

    .form-select {
        height: 48px;
        padding: 0 44px 0 16px;
        border: 2px solid var(--dark-200);
        border-radius: var(--radius-lg);
        font-size: 14px;
        color: var(--dark-800);
        background: #ffffff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 16px center;
        appearance: none;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .form-select:hover {
        border-color: var(--dark-300);
    }

    .form-select:focus {
        outline: none;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 4px var(--primary-100);
    }

    .inline-form {
        display: flex;
        gap: 20px;
        align-items: flex-end;
    }

    .inline-form .form-group {
        flex: 1;
    }

    .inline-form .form-group.auto-width {
        flex: 0 0 auto;
    }

    /* ============================================
       Select2 Custom Styles
       ============================================ */
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 48px !important;
        padding: 0 16px !important;
        border: 2px solid var(--dark-200) !important;
        border-radius: var(--radius-lg) !important;
        background: #ffffff !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default .select2-selection--single:hover {
        border-color: var(--dark-300) !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--primary-500) !important;
        box-shadow: 0 0 0 4px var(--primary-100) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 44px !important;
        padding-left: 0 !important;
        padding-right: 30px !important;
        color: var(--dark-800) !important;
        font-size: 14px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: var(--dark-400) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        right: 12px !important;
    }

    .select2-dropdown {
        border: 2px solid var(--dark-200) !important;
        border-radius: var(--radius-lg) !important;
        box-shadow: var(--shadow-lg) !important;
        margin-top: 4px !important;
        overflow: hidden !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        padding: 12px 16px !important;
        border: 2px solid var(--dark-200) !important;
        border-radius: var(--radius-md) !important;
        font-size: 14px !important;
        margin: 8px !important;
        width: calc(100% - 16px) !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--primary-500) !important;
        outline: none !important;
    }

    .select2-container--default .select2-results__option {
        padding: 12px 16px !important;
        font-size: 14px !important;
        transition: background var(--transition-fast) !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--primary-50) !important;
        color: var(--primary-700) !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: var(--primary-100) !important;
        color: var(--primary-700) !important;
    }

    /* ============================================
       Buttons
       ============================================ */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 48px;
        padding: 0 28px;
        font-size: 14px;
        font-weight: 600;
        border-radius: var(--radius-lg);
        border: none;
        cursor: pointer;
        transition: all var(--transition-normal);
        text-decoration: none;
    }

    .btn ion-icon {
        font-size: 20px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--dark-900) 0%, var(--dark-800) 100%);
        color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background: var(--dark-100);
        color: var(--dark-700);
        border: 2px solid var(--dark-200);
    }

    .btn-secondary:hover {
        background: var(--dark-200);
        border-color: var(--dark-300);
    }

    /* ============================================
       Radio Button Group
       ============================================ */
    .radio-group {
        display: flex;
        align-items: center;
        gap: 0;
        background: var(--dark-100);
        border-radius: var(--radius-lg);
        padding: 5px;
        border: 2px solid var(--dark-200);
    }

    .radio-group-label {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 11px 18px;
        font-size: 13px;
        font-weight: 500;
        color: var(--dark-500);
        cursor: pointer;
        border-radius: var(--radius-md);
        transition: all var(--transition-fast);
        white-space: nowrap;
    }

    .radio-group-label:hover {
        color: var(--dark-700);
    }

    .radio-group input[type="radio"] {
        display: none;
    }

    .radio-group input[type="radio"]:checked + .radio-group-label {
        background: #ffffff;
        color: var(--dark-800);
        font-weight: 600;
        box-shadow: var(--shadow-sm);
    }

    /* ============================================
       Filter Grid (Users Tab)
       ============================================ */
    .filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 20px;
        align-items: end;
    }

    /* ============================================
       Table Styles
       ============================================ */
    .table-container {
        overflow-x: auto;
        background: #ffffff;
        border-radius: var(--radius-lg);
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .modern-table thead {
        background: var(--dark-50);
        border-bottom: 2px solid var(--dark-200);
    }

    .modern-table thead th {
        padding: 16px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--dark-500);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        white-space: nowrap;
    }

    .modern-table tbody tr {
        border-bottom: 1px solid var(--dark-100);
        transition: background var(--transition-fast);
    }

    .modern-table tbody tr:hover {
        background: var(--dark-50);
    }

    .modern-table tbody tr:last-child {
        border-bottom: none;
    }

    .modern-table tbody td {
        padding: 16px 20px;
        color: var(--dark-700);
        vertical-align: middle;
    }

    .modern-table .cell-name {
        font-weight: 600;
        color: var(--dark-800);
    }

    .modern-table .cell-secondary {
        color: var(--dark-500);
        font-size: 13px;
    }

    /* ============================================
       Status Badge
       ============================================ */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .status-badge.active {
        background: var(--success-100);
        color: var(--success-700);
    }

    .status-badge.inactive {
        background: var(--error-100);
        color: var(--error-700);
    }

    /* Sigla Badge */
    .sigla-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 700;
        background: var(--primary-50);
        color: var(--primary-700);
        border: 1px solid var(--primary-100);
        font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
        letter-spacing: 0.5px;
    }

    /* Menu Badge */
    .menu-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 500;
        background: var(--dark-100);
        color: var(--dark-600);
        border: 1px solid var(--dark-200);
    }

    /* ============================================
       Action Buttons
       ============================================ */
    .action-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--transition-fast);
        text-decoration: none;
    }

    .btn-action ion-icon {
        font-size: 18px;
    }

    .btn-action.edit {
        background: var(--primary-50);
        color: var(--primary-600);
    }

    .btn-action.edit:hover {
        background: var(--primary-500);
        color: white;
        transform: scale(1.05);
    }

    .btn-action.activate {
        background: var(--success-100);
        color: var(--success-700);
    }

    .btn-action.activate:hover {
        background: var(--success-500);
        color: white;
        transform: scale(1.05);
    }

    .btn-action.deactivate {
        background: var(--error-100);
        color: var(--error-700);
    }

    .btn-action.deactivate:hover {
        background: var(--error-500);
        color: white;
        transform: scale(1.05);
    }

    .btn-action.save {
        background: var(--success-100);
        color: var(--success-700);
    }

    .btn-action.save:hover {
        background: var(--success-500);
        color: white;
        transform: scale(1.05);
    }

    .btn-action.cancel {
        background: var(--dark-100);
        color: var(--dark-500);
    }

    .btn-action.cancel:hover {
        background: var(--dark-300);
        color: var(--dark-700);
        transform: scale(1.05);
    }

    /* ============================================
       Inline Edit Input
       ============================================ */
    .inline-edit-input {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--primary-500);
        border-radius: var(--radius-md);
        font-size: 14px;
        background: #ffffff;
        color: var(--dark-800);
    }

    .inline-edit-input:focus {
        outline: none;
        box-shadow: 0 0 0 4px var(--primary-100);
    }

    /* ============================================
       Empty State
       ============================================ */
    .empty-state {
        text-align: center;
        padding: 56px 24px;
        color: var(--dark-400);
    }

    .empty-state ion-icon {
        font-size: 56px;
        margin-bottom: 16px;
        opacity: 0.4;
    }

    .empty-state h3 {
        font-size: 17px;
        font-weight: 600;
        color: var(--dark-600);
        margin: 0 0 8px 0;
    }

    .empty-state p {
        font-size: 14px;
        color: var(--dark-500);
        margin: 0;
    }

    /* ============================================
       Loading State
       ============================================ */
    .table-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 56px 24px;
        color: var(--dark-500);
    }

    .spinner {
        width: 44px;
        height: 44px;
        border: 3px solid var(--dark-200);
        border-top-color: var(--primary-500);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-bottom: 16px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Results Counter */
    .results-counter {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: var(--primary-100);
        color: var(--primary-700);
        border-radius: 100px;
        font-size: 12px;
        font-weight: 700;
        margin-left: 14px;
    }

    /* ============================================
       ESTRUTURA DE MENUS - Minimalista
       ============================================ */
    
    /* Stats Bar */
    .stats-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }

    .stat-card {
        flex: 1;
        background: #ffffff;
        border: 1px solid var(--dark-200);
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: var(--dark-100);
        color: var(--dark-600);
    }

    .stat-info h3 {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark-800);
        margin: 0;
        line-height: 1;
    }

    .stat-info p {
        font-size: 12px;
        color: var(--dark-500);
        margin: 2px 0 0 0;
    }

    /* Add Menu Button */
    .add-menu-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px;
        background: #ffffff;
        border: 1px dashed var(--dark-300);
        border-radius: var(--radius-lg);
        color: var(--dark-500);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition-fast);
        margin-bottom: 16px;
    }

    .add-menu-btn:hover {
        background: var(--dark-50);
        border-color: var(--dark-400);
        color: var(--dark-700);
    }

    .add-menu-btn ion-icon {
        font-size: 18px;
    }

    /* Menu Card (Level 1) */
    .menu-card {
        background: #ffffff;
        border: 1px solid var(--dark-200);
        border-radius: var(--radius-lg);
        margin-bottom: 8px;
        overflow: hidden;
    }

    .menu-card.inactive .menu-card-header {
        background: #969EA9;
    }

    .menu-card.inactive .menu-card-header:hover {
        background: #858d97;
    }

    .menu-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: var(--dark-800);
        cursor: pointer;
        transition: background var(--transition-fast);
    }

    .menu-card-header:hover {
        background: var(--dark-700);
    }

    .menu-card-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }

    .menu-icon {
        width: 32px;
        height: 32px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 16px;
        flex-shrink: 0;
    }

    .menu-info {
        min-width: 0;
        flex: 1;
    }

    .menu-info h3 {
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        margin: 0;
    }

    .menu-info span {
        color: var(--dark-400);
        font-size: 12px;
        display: block;
        margin-top: 2px;
        word-break: break-all;
        overflow-wrap: break-word;
    }

    .menu-card-header-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        margin-left: 12px;
    }

    .menu-badge-count {
        padding: 4px 10px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 100px;
        color: var(--dark-300);
        font-size: 11px;
        font-weight: 500;
    }

    /* Header Action Buttons */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .header-actions .btn-action {
        width: 28px;
        height: 28px;
        background: rgba(255, 255, 255, 0.1);
        color: var(--dark-300);
        border: none;
    }

    .header-actions .btn-action:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        transform: none;
    }

    .header-actions .btn-action.deactivate:hover {
        background: var(--error-500);
        color: #ffffff;
    }

    .header-actions .btn-action.activate:hover {
        background: var(--success-500);
        color: #ffffff;
    }

    .header-actions .btn-action ion-icon {
        font-size: 14px;
    }

    .expand-icon {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark-400);
        font-size: 18px;
        transition: transform var(--transition-fast);
    }

    .menu-card.expanded .expand-icon {
        transform: rotate(180deg);
    }

    .menu-card-body {
        display: none;
        padding: 16px;
        background: var(--dark-50);
    }

    .menu-card.expanded .menu-card-body {
        display: block;
    }

    /* SubMenu Card (Level 2) */
    .submenu-card {
        background: #ffffff;
        border: 1px solid var(--dark-200);
        border-radius: var(--radius-md);
        margin-bottom: 8px;
        overflow: hidden;
    }

    .submenu-card.inactive .submenu-card-header {
        background: #969EA9;
    }

    .submenu-card.inactive .submenu-card-header:hover {
        background: #858d97;
    }

    .submenu-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: #005596;
        cursor: pointer;
        transition: background var(--transition-fast);
    }

    .submenu-card-header:hover {
        background: #3a3b3b;
    }

    .submenu-card-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }

    .submenu-icon {
        width: 28px;
        height: 28px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 14px;
        flex-shrink: 0;
    }

    .submenu-info {
        min-width: 0;
        flex: 1;
    }

    .submenu-info h4 {
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        margin: 0;
    }

    .submenu-info span {
        color: var(--dark-300);
        font-size: 11px;
        display: block;
        margin-top: 1px;
        word-break: break-all;
        overflow-wrap: break-word;
    }

    .submenu-card-header-right {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
        margin-left: 10px;
    }

    .submenu-badge-count {
        padding: 3px 8px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 100px;
        color: var(--dark-300);
        font-size: 10px;
        font-weight: 500;
    }

    .submenu-expand-icon {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark-400);
        font-size: 16px;
        transition: transform var(--transition-fast);
    }

    .submenu-card.expanded .submenu-expand-icon {
        transform: rotate(180deg);
    }

    .submenu-card-body {
        display: none;
        padding: 12px;
        background: #ffffff;
    }

    .submenu-card.expanded .submenu-card-body {
        display: block;
    }

    /* Item Row (Level 3) */
    .item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        background: #ffffff;
        border: 1px solid var(--dark-200);
        border-radius: var(--radius-sm);
        margin-bottom: 6px;
        transition: all var(--transition-fast);
    }

    .item-row:hover {
        border-color: var(--dark-300);
        background: var(--dark-50);
    }

    .item-row.inactive {
        background: #f0f1f2;
        border-color: #d0d3d6;
    }

    .item-row.inactive .item-icon {
        background: #969EA9;
        color: #ffffff;
    }

    .item-row.inactive .item-info h5,
    .item-row.inactive .item-info span {
        color: #969EA9;
    }

    .item-row-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }

    .item-icon {
        width: 28px;
        height: 28px;
        background: var(--dark-100);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark-500);
        font-size: 14px;
        flex-shrink: 0;
    }

    .item-info {
        min-width: 0;
        flex: 1;
    }

    .item-info h5 {
        font-size: 13px;
        font-weight: 600;
        color: var(--dark-800);
        margin: 0;
    }

    .item-info span {
        font-size: 11px;
        color: var(--dark-500);
        display: block;
        margin-top: 1px;
        word-break: break-all;
        overflow-wrap: break-word;
    }

    .item-row-right {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
        margin-left: 10px;
    }

    /* Smaller action buttons for items */
    .item-row-right .btn-action {
        width: 28px;
        height: 28px;
    }

    .item-row-right .btn-action ion-icon {
        font-size: 14px;
    }

    /* Add Buttons (Inside Cards) */
    .add-submenu-btn,
    .add-item-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 10px;
        background: transparent;
        border: 1px dashed var(--dark-300);
        border-radius: var(--radius-sm);
        color: var(--dark-500);
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition-fast);
        margin-top: 6px;
    }

    .add-submenu-btn:hover,
    .add-item-btn:hover {
        background: var(--dark-100);
        border-color: var(--dark-400);
        color: var(--dark-600);
    }

    .add-submenu-btn ion-icon,
    .add-item-btn ion-icon {
        font-size: 14px;
    }

    /* Status Inactive Badge */
    .status-inactive-badge {
        padding: 2px 8px;
        background: var(--error-100);
        color: var(--error-700);
        border-radius: 100px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        margin-left: 6px;
    }

    /* ============================================
       Modal
       ============================================ */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal {
        background: #ffffff;
        border-radius: var(--radius-2xl);
        width: 100%;
        max-width: 500px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: modalIn 0.25s ease;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        background: linear-gradient(135deg, var(--dark-900) 0%, var(--dark-800) 100%);
    }

    .modal-header h3 {
        color: #ffffff;
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-header h3 ion-icon {
        font-size: 22px;
    }

    .modal-close {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        border-radius: var(--radius-md);
        color: #ffffff;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition-fast);
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .modal-body {
        padding: 24px;
    }

    .modal-body .form-group {
        margin-bottom: 20px;
    }

    .modal-body .form-group:last-child {
        margin-bottom: 0;
    }

    .modal-footer {
        display: flex;
        gap: 12px;
        padding: 20px 24px;
        background: var(--dark-50);
        border-top: 1px solid var(--dark-200);
    }

    .modal-footer .btn {
        flex: 1;
    }

    .modal-footer .btn-secondary {
        flex: 0 0 auto;
    }

    /* ============================================
       Responsive
       ============================================ */
    @media (max-width: 1024px) {
        .filter-grid {
            grid-template-columns: 1fr 1fr;
        }
        
        .form-col-3, .form-col-4 {
            grid-column: span 6;
        }

        .stats-bar {
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1 1 calc(50% - 8px);
        }
    }

    @media (max-width: 768px) {
        .admin-container {
            padding: 20px 16px;
        }

        .page-header {
            padding: 28px;
        }

        .page-header h1 {
            font-size: 24px;
        }

        .page-header-content {
            flex-direction: column;
            text-align: center;
        }

        .tabs-nav {
            gap: 4px;
            padding: 6px;
        }

        .tab-link {
            padding: 12px 16px;
            font-size: 13px;
        }

        .tab-link span {
            display: none;
        }

        .section-card-body {
            padding: 20px;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .form-row {
            gap: 16px;
        }

        .form-col-2, .form-col-3, .form-col-4, .form-col-5, .form-col-6, .form-col-8 {
            grid-column: span 12;
        }

        .inline-form {
            flex-direction: column;
        }

        .inline-form .form-group.auto-width {
            width: 100%;
        }

        .btn {
            width: 100%;
        }

        .stats-bar {
            flex-direction: column;
        }

        .stat-card {
            flex: 1 1 100%;
        }

        .modal {
            margin: 16px;
        }
    }
</style>

<div class="admin-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <ion-icon name="settings-outline"></ion-icon>
            </div>
            <div>
                <h1>Administração do Sistema</h1>
                <p>Gerencie cadastros, menus, usuários e configurações</p>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <nav class="tabs-nav">
        <a href="?aba=tipos" class="tab-link <?= $abaAtiva == 'tipos' ? 'active' : '' ?>">
            <ion-icon name="pricetag-outline"></ion-icon>
            <span>Tipos</span>
        </a>
        <a href="?aba=criterios" class="tab-link <?= $abaAtiva == 'criterios' ? 'active' : '' ?>">
            <ion-icon name="checkmark-circle-outline"></ion-icon>
            <span>Critérios</span>
        </a>
        <a href="?aba=formas" class="tab-link <?= $abaAtiva == 'formas' ? 'active' : '' ?>">
            <ion-icon name="git-branch-outline"></ion-icon>
            <span>Formas</span>
        </a>
        <a href="?aba=estrutura" class="tab-link <?= $abaAtiva == 'estrutura' ? 'active' : '' ?>">
            <ion-icon name="git-network-outline"></ion-icon>
            <span>Estrutura de Menus</span>
        </a>
        <a href="?aba=usuarios" class="tab-link <?= $abaAtiva == 'usuarios' ? 'active' : '' ?>">
            <ion-icon name="people-outline"></ion-icon>
            <span>Usuários</span>
        </a>
        <a href="?aba=perfis" class="tab-link <?= $abaAtiva == 'perfis' ? 'active' : '' ?>">
            <ion-icon name="shield-checkmark-outline"></ion-icon>
            <span>Perfis</span>
        </a>
    </nav>

    <!-- ============================================
         ABA: TIPOS DE LICITAÇÃO
         ============================================ -->
    <?php if ($abaAtiva == 'tipos') : ?>
    <div class="tab-pane active" id="tab-tipos">
        <!-- Cadastro -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <h3>Novo Tipo de Licitação</h3>
                </div>
            </div>
            <div class="section-card-body">
                <form action="bd/tipo/create.php" method="post">
                    <div class="form-row">
                        <div class="form-col-8">
                            <div class="form-group">
                                <label class="form-label">
                                    <ion-icon name="text-outline"></ion-icon>
                                    Nome do Tipo <span class="required">*</span>
                                </label>
                                <input type="text" name="nmTipo" class="form-control" placeholder="Ex: Pregão Eletrônico" required autofocus>
                            </div>
                        </div>
                        <div class="form-col-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <ion-icon name="code-outline"></ion-icon>
                                    Sigla <span class="required">*</span>
                                </label>
                                <input type="text" name="sglTipo" class="form-control" placeholder="Ex: PE" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col-12">
                            <button type="submit" class="btn btn-primary">
                                <ion-icon name="add-outline"></ion-icon>
                                Cadastrar Tipo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listagem -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="list-outline"></ion-icon>
                    <h3>Tipos Cadastrados</h3>
                </div>
            </div>
            <div class="section-card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Nome do Tipo</th>
                                <th>Sigla</th>
                                <th>Criado por</th>
                                <th>Status</th>
                                <th style="width: 140px; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $queryTipos = $pdoCAT->query("SELECT * FROM [PortalCompras].[dbo].[TIPO_LICITACAO] ORDER BY [NM_TIPO]");
                            $totalTipos = 0;
                            while ($row = $queryTipos->fetch(PDO::FETCH_ASSOC)) : $totalTipos++;
                            ?>
                            <tr id="rowTipo<?= $row['ID_TIPO'] ?>">
                                <td>
                                    <span class="cell-name pubnmTipo"><?= htmlspecialchars($row['NM_TIPO']) ?></span>
                                </td>
                                <td>
                                    <span class="sigla-badge pubsglTipo"><?= htmlspecialchars($row['SGL_TIPO']) ?></span>
                                </td>
                                <td class="cell-secondary"><?= htmlspecialchars($row['LGN_CRIADOR_TIPO'] ?? '-') ?></td>
                                <td>
                                    <?php if (empty($row['DT_EXC_TIPO'])) : ?>
                                        <span class="status-badge active">Ativo</span>
                                    <?php else : ?>
                                        <span class="status-badge inactive">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons" style="justify-content: center;">
                                        <?php if (empty($row['DT_EXC_TIPO'])) : ?>
                                            <a href="bd/tipo/desativa.php?idTipo=<?= $row['ID_TIPO'] ?>" class="btn-action deactivate" title="Desativar">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </a>
                                        <?php else : ?>
                                            <a href="bd/tipo/ativa.php?idTipo=<?= $row['ID_TIPO'] ?>" class="btn-action activate" title="Ativar">
                                                <ion-icon name="checkmark-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn-action edit edit-btn-tipo" data-id="<?= $row['ID_TIPO'] ?>" title="Editar">
                                            <ion-icon name="pencil-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action save save-btn-tipo" data-id="<?= $row['ID_TIPO'] ?>" title="Salvar" style="display: none;">
                                            <ion-icon name="checkmark-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action cancel cancel-btn-tipo" data-id="<?= $row['ID_TIPO'] ?>" title="Cancelar" style="display: none;">
                                            <ion-icon name="close-outline"></ion-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($totalTipos == 0) : ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <ion-icon name="pricetag-outline"></ion-icon>
                                        <h3>Nenhum tipo cadastrado</h3>
                                        <p>Adicione um novo tipo acima</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================
         ABA: CRITÉRIOS
         ============================================ -->
    <?php if ($abaAtiva == 'criterios') : ?>
    <div class="tab-pane active" id="tab-criterios">
        <!-- Cadastro -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <h3>Novo Critério de Licitação</h3>
                </div>
            </div>
            <div class="section-card-body">
                <form action="bd/criterio/create.php" method="post" class="inline-form">
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="checkmark-circle-outline"></ion-icon>
                            Nome do Critério <span class="required">*</span>
                        </label>
                        <input type="text" name="nmCriterio" class="form-control" placeholder="Ex: Menor Preço, Melhor Técnica" required autofocus>
                    </div>
                    <div class="form-group auto-width">
                        <button type="submit" class="btn btn-primary">
                            <ion-icon name="add-outline"></ion-icon>
                            Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listagem -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="list-outline"></ion-icon>
                    <h3>Critérios Cadastrados</h3>
                </div>
            </div>
            <div class="section-card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Nome do Critério</th>
                                <th>Criado por</th>
                                <th>Status</th>
                                <th style="width: 140px; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $queryCriterios = $pdoCAT->query("SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] ORDER BY [NM_CRITERIO]");
                            $totalCriterios = 0;
                            while ($row = $queryCriterios->fetch(PDO::FETCH_ASSOC)) : $totalCriterios++;
                            ?>
                            <tr id="rowCriterio<?= $row['ID_CRITERIO'] ?>">
                                <td>
                                    <span class="cell-name pubnmCriterio"><?= htmlspecialchars($row['NM_CRITERIO']) ?></span>
                                </td>
                                <td class="cell-secondary"><?= htmlspecialchars($row['LGN_CRIADOR_CRITERIO'] ?? '-') ?></td>
                                <td>
                                    <?php if (empty($row['DT_EXC_CRITERIO'])) : ?>
                                        <span class="status-badge active">Ativo</span>
                                    <?php else : ?>
                                        <span class="status-badge inactive">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons" style="justify-content: center;">
                                        <?php if (empty($row['DT_EXC_CRITERIO'])) : ?>
                                            <a href="bd/criterio/desativa.php?idCriterio=<?= $row['ID_CRITERIO'] ?>" class="btn-action deactivate" title="Desativar">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </a>
                                        <?php else : ?>
                                            <a href="bd/criterio/ativa.php?idCriterio=<?= $row['ID_CRITERIO'] ?>" class="btn-action activate" title="Ativar">
                                                <ion-icon name="checkmark-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn-action edit edit-btn-criterio" data-id="<?= $row['ID_CRITERIO'] ?>" title="Editar">
                                            <ion-icon name="pencil-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action save save-btn-criterio" data-id="<?= $row['ID_CRITERIO'] ?>" title="Salvar" style="display: none;">
                                            <ion-icon name="checkmark-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action cancel cancel-btn-criterio" data-id="<?= $row['ID_CRITERIO'] ?>" title="Cancelar" style="display: none;">
                                            <ion-icon name="close-outline"></ion-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($totalCriterios == 0) : ?>
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                                        <h3>Nenhum critério cadastrado</h3>
                                        <p>Adicione um novo critério acima</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================
         ABA: FORMAS
         ============================================ -->
    <?php if ($abaAtiva == 'formas') : ?>
    <div class="tab-pane active" id="tab-formas">
        <!-- Cadastro -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <h3>Nova Forma de Licitação</h3>
                </div>
            </div>
            <div class="section-card-body">
                <form action="bd/forma/create.php" method="post" class="inline-form">
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="git-branch-outline"></ion-icon>
                            Nome da Forma <span class="required">*</span>
                        </label>
                        <input type="text" name="nmForma" class="form-control" placeholder="Ex: Presencial, Eletrônica" required autofocus>
                    </div>
                    <div class="form-group auto-width">
                        <button type="submit" class="btn btn-primary">
                            <ion-icon name="add-outline"></ion-icon>
                            Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listagem -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="list-outline"></ion-icon>
                    <h3>Formas Cadastradas</h3>
                </div>
            </div>
            <div class="section-card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Nome da Forma</th>
                                <th>Status</th>
                                <th style="width: 140px; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $queryFormas = $pdoCAT->query("SELECT * FROM [portalcompras].[dbo].[FORMA] ORDER BY [NM_FORMA]");
                            $totalFormas = 0;
                            while ($row = $queryFormas->fetch(PDO::FETCH_ASSOC)) : $totalFormas++;
                            ?>
                            <tr id="rowForma<?= $row['ID_FORMA'] ?>">
                                <td>
                                    <span class="cell-name nmForma"><?= htmlspecialchars($row['NM_FORMA']) ?></span>
                                </td>
                                <td>
                                    <?php if (empty($row['DT_EXC_FORMA'])) : ?>
                                        <span class="status-badge active">Ativo</span>
                                    <?php else : ?>
                                        <span class="status-badge inactive">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons" style="justify-content: center;">
                                        <?php if (empty($row['DT_EXC_FORMA'])) : ?>
                                            <a href="bd/forma/desativa.php?idForma=<?= $row['ID_FORMA'] ?>" class="btn-action deactivate" title="Desativar">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </a>
                                        <?php else : ?>
                                            <a href="bd/forma/ativa.php?idForma=<?= $row['ID_FORMA'] ?>" class="btn-action activate" title="Ativar">
                                                <ion-icon name="checkmark-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn-action edit edit-btn-forma" data-id="<?= $row['ID_FORMA'] ?>" title="Editar">
                                            <ion-icon name="pencil-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action save save-btn-forma" data-id="<?= $row['ID_FORMA'] ?>" title="Salvar" style="display: none;">
                                            <ion-icon name="checkmark-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action cancel cancel-btn-forma" data-id="<?= $row['ID_FORMA'] ?>" title="Cancelar" style="display: none;">
                                            <ion-icon name="close-outline"></ion-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($totalFormas == 0) : ?>
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <ion-icon name="git-branch-outline"></ion-icon>
                                        <h3>Nenhuma forma cadastrada</h3>
                                        <p>Adicione uma nova forma acima</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================
         ABA: ESTRUTURA DE MENUS (Acordeão)
         ============================================ -->
    <?php if ($abaAtiva == 'estrutura') : ?>
    <div class="tab-pane active" id="tab-estrutura">
        
        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-icon menu">
                    <ion-icon name="folder-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <h3><?= $totalMenus ?></h3>
                    <p>Menus</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon submenu">
                    <ion-icon name="folder-open-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <h3><?= $totalSubMenus ?></h3>
                    <p>SubMenus</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon item">
                    <ion-icon name="document-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <h3><?= $totalItens ?></h3>
                    <p>Itens de Menu</p>
                </div>
            </div>
        </div>

        <!-- Add Menu Button -->
        <button class="add-menu-btn" onclick="abrirModalMenu()">
            <ion-icon name="add-circle-outline"></ion-icon>
            Adicionar Novo Menu
        </button>

        <!-- Menu Cards -->
        <?php if (empty($menus)) : ?>
            <div class="empty-state">
                <ion-icon name="folder-open-outline"></ion-icon>
                <h3>Nenhum menu cadastrado</h3>
                <p>Clique no botão acima para adicionar o primeiro menu</p>
            </div>
        <?php else : ?>
            <?php foreach ($menus as $menu) : ?>
            <div class="menu-card <?= $menu['inativo'] ? 'inactive' : '' ?>" id="menu-<?= $menu['id'] ?>">
                <!-- Menu Header -->
                <div class="menu-card-header" onclick="toggleMenu(<?= $menu['id'] ?>)">
                    <div class="menu-card-header-left">
                        <div class="menu-icon">
                            <ion-icon name="folder-outline"></ion-icon>
                        </div>
                        <div class="menu-info">
                            <h3>
                                <?= htmlspecialchars($menu['nome']) ?>
                                <?php if ($menu['inativo']) : ?>
                                    <span class="status-inactive-badge">Inativo</span>
                                <?php endif; ?>
                            </h3>
                            <span><?= $menu['link'] ? htmlspecialchars($menu['link']) : 'Sem link direto' ?></span>
                        </div>
                    </div>
                    <div class="menu-card-header-right">
                        <?php $submenusAtivos = count(array_filter($menu['submenus'], function($s) { return !$s['inativo']; })); ?>
                        <span class="menu-badge-count"><?= $submenusAtivos ?> submenus</span>
                        <div class="header-actions" onclick="event.stopPropagation();">
                            <?php if ($menu['inativo']) : ?>
                                <a href="bd/menus/ativa.php?idMenu=<?= $menu['id'] ?>&redirect=administracao&aba=estrutura" 
                                   class="btn-action activate" title="Ativar">
                                    <ion-icon name="checkmark-outline"></ion-icon>
                                </a>
                            <?php else : ?>
                                <a href="bd/menus/desativa.php?idMenu=<?= $menu['id'] ?>&redirect=administracao&aba=estrutura" 
                                   class="btn-action deactivate" title="Desativar">
                                    <ion-icon name="close-outline"></ion-icon>
                                </a>
                            <?php endif; ?>
                            <button class="btn-action edit" onclick="abrirModalMenu(<?= $menu['id'] ?>, <?= htmlspecialchars(json_encode($menu['nome']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($menu['link'] ?? ''), ENT_QUOTES) ?>)" title="Editar">
                                <ion-icon name="pencil-outline"></ion-icon>
                            </button>
                        </div>
                        <div class="expand-icon">
                            <ion-icon name="chevron-down-outline"></ion-icon>
                        </div>
                    </div>
                </div>

                <!-- Menu Body (SubMenus) -->
                <div class="menu-card-body">
                    <?php if (empty($menu['submenus'])) : ?>
                        <div class="empty-state" style="padding: 32px;">
                            <ion-icon name="folder-open-outline"></ion-icon>
                            <h3>Nenhum submenu</h3>
                            <p>Adicione submenus a este menu</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($menu['submenus'] as $submenu) : ?>
                        <div class="submenu-card <?= $submenu['inativo'] ? 'inactive' : '' ?>" id="submenu-<?= $submenu['id'] ?>">
                            <!-- SubMenu Header -->
                            <div class="submenu-card-header" onclick="toggleSubMenu(<?= $submenu['id'] ?>)">
                                <div class="submenu-card-header-left">
                                    <div class="submenu-icon">
                                        <ion-icon name="folder-open-outline"></ion-icon>
                                    </div>
                                    <div class="submenu-info">
                                        <h4>
                                            <?= htmlspecialchars($submenu['nome']) ?>
                                            <?php if ($submenu['inativo']) : ?>
                                                <span class="status-inactive-badge">Inativo</span>
                                            <?php endif; ?>
                                        </h4>
                                        <span><?= $submenu['link'] ? htmlspecialchars($submenu['link']) : 'Sem link' ?></span>
                                    </div>
                                </div>
                                <div class="submenu-card-header-right">
                                    <?php $itensAtivos = count(array_filter($submenu['itens'], function($i) { return !$i['inativo']; })); ?>
                                    <span class="submenu-badge-count"><?= $itensAtivos ?> itens</span>
                                    <div class="header-actions" onclick="event.stopPropagation();">
                                        <?php if ($submenu['inativo']) : ?>
                                            <a href="bd/submenu/ativa.php?idSubMenu=<?= $submenu['id'] ?>&redirect=administracao&aba=estrutura" 
                                               class="btn-action activate" title="Ativar">
                                                <ion-icon name="checkmark-outline"></ion-icon>
                                            </a>
                                        <?php else : ?>
                                            <a href="bd/submenu/desativa.php?idSubMenu=<?= $submenu['id'] ?>&redirect=administracao&aba=estrutura" 
                                               class="btn-action deactivate" title="Desativar">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn-action edit" onclick="abrirModalSubMenu(<?= $submenu['id'] ?>, <?= htmlspecialchars(json_encode($submenu['nome']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($submenu['link'] ?? ''), ENT_QUOTES) ?>, <?= $menu['id'] ?>)" title="Editar">
                                            <ion-icon name="pencil-outline"></ion-icon>
                                        </button>
                                    </div>
                                    <div class="submenu-expand-icon">
                                        <ion-icon name="chevron-down-outline"></ion-icon>
                                    </div>
                                </div>
                            </div>

                            <!-- SubMenu Body (Items) -->
                            <div class="submenu-card-body">
                                <?php if (empty($submenu['itens'])) : ?>
                                    <div class="empty-state" style="padding: 24px;">
                                        <ion-icon name="document-outline"></ion-icon>
                                        <h3>Nenhum item</h3>
                                        <p>Adicione itens a este submenu</p>
                                    </div>
                                <?php else : ?>
                                    <?php foreach ($submenu['itens'] as $item) : ?>
                                    <div class="item-row <?= $item['inativo'] ? 'inactive' : '' ?>">
                                        <div class="item-row-left">
                                            <div class="item-icon">
                                                <ion-icon name="document-text-outline"></ion-icon>
                                            </div>
                                            <div class="item-info">
                                                <h5><?= htmlspecialchars($item['nome']) ?></h5>
                                                <span><?= $item['link'] ? htmlspecialchars($item['link']) : 'Sem link' ?></span>
                                            </div>
                                        </div>
                                        <div class="item-row-right">
                                            <?php if ($item['inativo']) : ?>
                                                <a href="bd/itemmenu/ativa.php?idItemMenu=<?= $item['id'] ?>&redirect=administracao&aba=estrutura" 
                                                   class="btn-action activate" title="Ativar">
                                                    <ion-icon name="checkmark-outline"></ion-icon>
                                                </a>
                                            <?php else : ?>
                                                <a href="bd/itemmenu/desativa.php?idItemMenu=<?= $item['id'] ?>&redirect=administracao&aba=estrutura" 
                                                   class="btn-action deactivate" title="Desativar">
                                                    <ion-icon name="close-outline"></ion-icon>
                                                </a>
                                            <?php endif; ?>
                                            <button class="btn-action edit" onclick="abrirModalItem(<?= $item['id'] ?>, <?= htmlspecialchars(json_encode($item['nome']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($item['link'] ?? ''), ENT_QUOTES) ?>, <?= $submenu['id'] ?>)" title="Editar">
                                                <ion-icon name="pencil-outline"></ion-icon>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Add Item Button -->
                                <button class="add-item-btn" onclick="abrirModalItem(null, '', '', <?= $submenu['id'] ?>)">
                                    <ion-icon name="add-outline"></ion-icon>
                                    Adicionar Item
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Add SubMenu Button -->
                    <button class="add-submenu-btn" onclick="abrirModalSubMenu(null, '', '', <?= $menu['id'] ?>)">
                        <ion-icon name="add-outline"></ion-icon>
                        Adicionar SubMenu
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal: Menu -->
    <div class="modal-overlay" id="modalMenu">
        <div class="modal">
            <div class="modal-header">
                <h3>
                    <ion-icon name="folder-outline"></ion-icon>
                    <span id="modalMenuTitle">Novo Menu</span>
                </h3>
                <button class="modal-close" onclick="fecharModal('modalMenu')">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            <form id="formMenu" action="bd/menus/create.php" method="post">
                <input type="hidden" name="idMenu" id="inputMenuId">
                <input type="hidden" name="redirect" value="administracao">
                <input type="hidden" name="aba" value="estrutura">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="text-outline"></ion-icon>
                            Nome do Menu <span class="required">*</span>
                        </label>
                        <input type="text" name="nmMenu" id="inputMenuNome" class="form-control" placeholder="Ex: Cadastros" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="link-outline"></ion-icon>
                            Link (opcional)
                        </label>
                        <input type="text" name="linkMenu" id="inputMenuLink" class="form-control" placeholder="Ex: pagina.php">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modalMenu')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <ion-icon name="checkmark-outline"></ion-icon>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: SubMenu -->
    <div class="modal-overlay" id="modalSubMenu">
        <div class="modal">
            <div class="modal-header">
                <h3>
                    <ion-icon name="folder-open-outline"></ion-icon>
                    <span id="modalSubMenuTitle">Novo SubMenu</span>
                </h3>
                <button class="modal-close" onclick="fecharModal('modalSubMenu')">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            <form id="formSubMenu" action="bd/submenu/create.php" method="post">
                <input type="hidden" name="idSubMenu" id="inputSubMenuId">
                <input type="hidden" name="redirect" value="administracao">
                <input type="hidden" name="aba" value="estrutura">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="folder-outline"></ion-icon>
                            Menu Pai <span class="required">*</span>
                        </label>
                        <select name="idMenu" id="inputSubMenuPai" class="form-select" required>
                            <option value="">Selecione o menu</option>
                            <?php foreach ($menus as $m) : ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="text-outline"></ion-icon>
                            Nome do SubMenu <span class="required">*</span>
                        </label>
                        <input type="text" name="nmSubMenu" id="inputSubMenuNome" class="form-control" placeholder="Ex: Fornecedores" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="link-outline"></ion-icon>
                            Link (opcional)
                        </label>
                        <input type="text" name="linkSubMenu" id="inputSubMenuLink" class="form-control" placeholder="Ex: fornecedores.php">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modalSubMenu')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <ion-icon name="checkmark-outline"></ion-icon>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Item Menu -->
    <div class="modal-overlay" id="modalItem">
        <div class="modal">
            <div class="modal-header">
                <h3>
                    <ion-icon name="document-text-outline"></ion-icon>
                    <span id="modalItemTitle">Novo Item</span>
                </h3>
                <button class="modal-close" onclick="fecharModal('modalItem')">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            <form id="formItem" action="bd/itemmenu/create.php" method="post">
                <input type="hidden" name="idItemMenu" id="inputItemId">
                <input type="hidden" name="redirect" value="administracao">
                <input type="hidden" name="aba" value="estrutura">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="folder-open-outline"></ion-icon>
                            SubMenu Pai <span class="required">*</span>
                        </label>
                        <select name="idSubMenu" id="inputItemPai" class="form-select" required>
                            <option value="">Selecione o submenu</option>
                            <?php foreach ($menus as $m) : ?>
                                <?php foreach ($m['submenus'] as $s) : ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($m['nome']) ?> → <?= htmlspecialchars($s['nome']) ?></option>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="text-outline"></ion-icon>
                            Nome do Item <span class="required">*</span>
                        </label>
                        <input type="text" name="nmItemMenu" id="inputItemNome" class="form-control" placeholder="Ex: Novo Fornecedor" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="link-outline"></ion-icon>
                            Link (opcional)
                        </label>
                        <input type="text" name="linkItemMenu" id="inputItemLink" class="form-control" placeholder="Ex: novoFornecedor.php">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modalItem')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <ion-icon name="checkmark-outline"></ion-icon>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================
         ABA: USUÁRIOS
         ============================================ -->
    <?php if ($abaAtiva == 'usuarios') : ?>
    <div class="tab-pane active" id="tab-usuarios">
        <!-- Filtros -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="search-outline"></ion-icon>
                    <h3>Pesquisar Usuários</h3>
                </div>
            </div>
            <div class="section-card-body">
                <div class="filter-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="person-outline"></ion-icon>
                            Nome, Login ou Matrícula
                        </label>
                        <input type="text" id="filtroNome" class="form-control" placeholder="Digite para pesquisar..." 
                            style="text-transform: uppercase;" autofocus>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="shield-outline"></ion-icon>
                            Perfil
                        </label>
                        <select id="filtroPerfil" class="form-select">
                            <option value="0">Todos os perfis</option>
                            <?php
                            $queryPerfis = $pdoCAT->query("SELECT * FROM TIPO_LICITACAO WHERE DT_EXC_TIPO IS NULL ORDER BY NM_TIPO");
                            while ($p = $queryPerfis->fetch(PDO::FETCH_ASSOC)) :
                                echo "<option value='{$p['ID_TIPO']}'>" . htmlspecialchars($p['NM_TIPO']) . "</option>";
                            endwhile;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="checkbox-outline"></ion-icon>
                            Cadastrado no sistema?
                        </label>
                        <div class="radio-group">
                            <input type="radio" name="usuSistema" id="usuTodos" value="todos" checked>
                            <label for="usuTodos" class="radio-group-label">Todos</label>
                            <input type="radio" name="usuSistema" id="usuSim" value="sim">
                            <label for="usuSim" class="radio-group-label">Sim</label>
                            <input type="radio" name="usuSistema" id="usuNao" value="nao">
                            <label for="usuNao" class="radio-group-label">Não</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listagem -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="list-outline"></ion-icon>
                    <h3>Usuários</h3>
                    <span class="results-counter" id="resultsCounter" style="display: none;">
                        <span id="totalUsuarios">0</span> encontrados
                    </span>
                </div>
            </div>
            <div class="section-card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="modern-table" id="tabelaUsuarios">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Login</th>
                                <th>Nome</th>
                                <th>Unidade</th>
                                <th>E-mail</th>
                                <th>Perfil</th>
                                <th style="width: 120px; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyUsuarios">
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <ion-icon name="search-outline"></ion-icon>
                                        <h3>Digite para pesquisar</h3>
                                        <p>Informe nome, login ou matrícula (mín. 2 caracteres)</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================
         ABA: PERFIS
         ============================================ -->
    <?php if ($abaAtiva == 'perfis') : ?>
    <div class="tab-pane active" id="tab-perfis">
        <!-- Cadastro -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <h3>Novo Perfil</h3>
                </div>
            </div>
            <div class="section-card-body">
                <form action="bd/perfil/create.php" method="post" class="inline-form">
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="shield-checkmark-outline"></ion-icon>
                            Nome do Perfil <span class="required">*</span>
                        </label>
                        <input type="text" name="nmPerfil" class="form-control" placeholder="Digite o nome do perfil" required autofocus>
                    </div>
                    <div class="form-group auto-width">
                        <button type="submit" class="btn btn-primary">
                            <ion-icon name="add-outline"></ion-icon>
                            Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listagem -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="list-outline"></ion-icon>
                    <h3>Perfis Cadastrados</h3>
                </div>
            </div>
            <div class="section-card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Nome do Perfil</th>
                                <th>Status</th>
                                <th style="width: 140px; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $queryPerfisLista = $pdoCAT->query("SELECT * FROM [portalcompras].[dbo].[PERFIL] ORDER BY [NM_PERFIL]");
                            $totalPerfis = 0;
                            while ($row = $queryPerfisLista->fetch(PDO::FETCH_ASSOC)) : $totalPerfis++;
                            ?>
                            <tr id="rowPerfil<?= $row['ID_PERFIL'] ?>">
                                <td>
                                    <span class="cell-name pubnmPerfil"><?= htmlspecialchars($row['NM_PERFIL']) ?></span>
                                </td>
                                <td>
                                    <?php if (empty($row['DT_EXC_PERFIL'])) : ?>
                                        <span class="status-badge active">Ativo</span>
                                    <?php else : ?>
                                        <span class="status-badge inactive">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons" style="justify-content: center;">
                                        <?php if (empty($row['DT_EXC_PERFIL'])) : ?>
                                            <a href="bd/perfil/desativa.php?idPerfil=<?= $row['ID_PERFIL'] ?>" class="btn-action deactivate" title="Desativar">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </a>
                                        <?php else : ?>
                                            <a href="bd/perfil/ativa.php?idPerfil=<?= $row['ID_PERFIL'] ?>" class="btn-action activate" title="Ativar">
                                                <ion-icon name="checkmark-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn-action edit edit-btn-perfil" data-id="<?= $row['ID_PERFIL'] ?>" title="Editar">
                                            <ion-icon name="pencil-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action save save-btn-perfil" data-id="<?= $row['ID_PERFIL'] ?>" title="Salvar" style="display: none;">
                                            <ion-icon name="checkmark-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action cancel cancel-btn-perfil" data-id="<?= $row['ID_PERFIL'] ?>" title="Cancelar" style="display: none;">
                                            <ion-icon name="close-outline"></ion-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($totalPerfis == 0) : ?>
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <ion-icon name="shield-checkmark-outline"></ion-icon>
                                        <h3>Nenhum perfil cadastrado</h3>
                                        <p>Adicione um novo perfil acima</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    // ============================================
    // Busca AJAX de Usuários
    // ============================================
    var timeoutBusca;
    
    function buscarUsuarios() {
        var nome = $('#filtroNome').val().trim();
        var perfil = $('#filtroPerfil').val();
        var usuSistema = $('input[name="usuSistema"]:checked').val();
        
        if (nome.length < 2 && perfil == '0' && usuSistema == 'todos') {
            $('#tbodyUsuarios').html(`
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <ion-icon name="search-outline"></ion-icon>
                            <h3>Digite para pesquisar</h3>
                            <p>Informe nome, login ou matrícula (mín. 2 caracteres)</p>
                        </div>
                    </td>
                </tr>
            `);
            $('#resultsCounter').hide();
            return;
        }
        
        $('#tbodyUsuarios').html(`
            <tr>
                <td colspan="7">
                    <div class="table-loading">
                        <div class="spinner"></div>
                        <p>Buscando usuários...</p>
                    </div>
                </td>
            </tr>
        `);
        $('#resultsCounter').hide();
        
        $.ajax({
            url: 'bd/usuario/buscarUsuarios.php',
            type: 'GET',
            data: {
                nome: nome,
                perfilUsuario: perfil,
                usuSistema: usuSistema
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderizarUsuarios(response.usuarios);
                    $('#totalUsuarios').text(response.total);
                    if (response.total > 0) {
                        $('#resultsCounter').show();
                    }
                } else {
                    $('#tbodyUsuarios').html(`
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <ion-icon name="alert-circle-outline"></ion-icon>
                                    <h3>Erro ao buscar</h3>
                                    <p>${response.error || 'Tente novamente'}</p>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            },
            error: function() {
                $('#tbodyUsuarios').html(`
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <ion-icon name="alert-circle-outline"></ion-icon>
                                <h3>Erro de conexão</h3>
                                <p>Não foi possível conectar ao servidor</p>
                            </div>
                        </td>
                    </tr>
                `);
            }
        });
    }
    
    function renderizarUsuarios(usuarios) {
        if (usuarios.length === 0) {
            $('#tbodyUsuarios').html(`
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <ion-icon name="people-outline"></ion-icon>
                            <h3>Nenhum usuário encontrado</h3>
                            <p>Tente outros filtros</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }
        
        var html = '';
        usuarios.forEach(function(u) {
            var perfilBadge = u.perfil 
                ? `<span class="status-badge active">${escapeHtml(u.perfil)}</span>` 
                : '<span class="cell-secondary">-</span>';
            
            var acoes = '';
            if (!u.temMatricula) {
                if (u.status === 'A') {
                    acoes += `<a href="bd/usuario/desativa.php?email=${encodeURIComponent(u.email)}" class="btn-action deactivate" title="Desativar">
                        <ion-icon name="close-outline"></ion-icon>
                    </a>`;
                } else {
                    acoes += `<a href="bd/usuario/ativa.php?email=${encodeURIComponent(u.email)}" class="btn-action activate" title="Ativar">
                        <ion-icon name="checkmark-outline"></ion-icon>
                    </a>`;
                }
            }
            acoes += `<a href="editarUsuario.php?email=${encodeURIComponent(u.email)}&login=${encodeURIComponent(u.login)}" class="btn-action edit" title="Editar Perfil">
                <ion-icon name="pencil-outline"></ion-icon>
            </a>`;
            
            html += `
                <tr>
                    <td class="cell-secondary">${escapeHtml(u.matricula)}</td>
                    <td><span class="cell-name">${escapeHtml(u.login)}</span></td>
                    <td>${escapeHtml(u.nome)}</td>
                    <td class="cell-secondary">${escapeHtml(u.unidade)}</td>
                    <td class="cell-secondary">${escapeHtml(u.email)}</td>
                    <td>${perfilBadge}</td>
                    <td>
                        <div class="action-buttons" style="justify-content: center;">
                            ${acoes}
                        </div>
                    </td>
                </tr>
            `;
        });
        
        $('#tbodyUsuarios').html(html);
    }
    
    function escapeHtml(text) {
        if (!text) return '-';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
    
    $('#filtroNome').on('keyup', function() {
        clearTimeout(timeoutBusca);
        timeoutBusca = setTimeout(function() {
            buscarUsuarios();
        }, 400);
    });
    
    $('#filtroPerfil').on('change', function() {
        buscarUsuarios();
    });
    
    $('input[name="usuSistema"]').on('change', function() {
        buscarUsuarios();
    });

    // ============================================
    // Edição Inline - TIPOS
    // ============================================
    $('.edit-btn-tipo').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowTipo' + id);
        var nmTipo = row.find('.pubnmTipo').text();
        var sglTipo = row.find('.pubsglTipo').text();
        
        row.find('.pubnmTipo').replaceWith(`<input type="text" class="inline-edit-input pubnmTipo" value="${nmTipo}" />`);
        row.find('.pubsglTipo').replaceWith(`<input type="text" class="inline-edit-input pubsglTipo" value="${sglTipo}" style="width: 80px;" />`);
        
        row.find('.edit-btn-tipo').hide();
        row.find('.save-btn-tipo, .cancel-btn-tipo').show();
        row.find('.deactivate, .activate').hide();
    });

    $('.save-btn-tipo').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowTipo' + id);
        var nmTipo = row.find('input.pubnmTipo').val().trim();
        var sglTipo = row.find('input.pubsglTipo').val().trim();
        
        if (nmTipo === '' || sglTipo === '') {
            alert('Preencha todos os campos');
            return;
        }
        
        window.location.href = `bd/tipo/update.php?idTipo=${id}&nmTipo=${encodeURIComponent(nmTipo)}&sglTipo=${encodeURIComponent(sglTipo)}`;
    });

    $('.cancel-btn-tipo').on('click', function() {
        location.reload();
    });

    // ============================================
    // Edição Inline - CRITÉRIOS
    // ============================================
    $('.edit-btn-criterio').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowCriterio' + id);
        var currentName = row.find('.pubnmCriterio').text();
        
        row.find('.pubnmCriterio').replaceWith(`<input type="text" class="inline-edit-input pubnmCriterio" value="${currentName}" />`);
        
        row.find('.edit-btn-criterio').hide();
        row.find('.save-btn-criterio, .cancel-btn-criterio').show();
        row.find('.deactivate, .activate').hide();
    });

    $('.save-btn-criterio').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowCriterio' + id);
        var nmCriterio = row.find('input.pubnmCriterio').val().trim();
        
        if (nmCriterio === '') {
            alert('O nome é obrigatório');
            return;
        }
        
        window.location.href = `bd/criterio/update.php?idCriterio=${id}&nmCriterio=${encodeURIComponent(nmCriterio)}`;
    });

    $('.cancel-btn-criterio').on('click', function() {
        location.reload();
    });

    // ============================================
    // Edição Inline - FORMAS
    // ============================================
    $('.edit-btn-forma').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowForma' + id);
        var currentName = row.find('.nmForma').text();
        
        row.find('.nmForma').replaceWith(`<input type="text" class="inline-edit-input nmForma" value="${currentName}" />`);
        
        row.find('.edit-btn-forma').hide();
        row.find('.save-btn-forma, .cancel-btn-forma').show();
        row.find('.deactivate, .activate').hide();
    });

    $('.save-btn-forma').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowForma' + id);
        var nmForma = row.find('input.nmForma').val().trim();
        
        if (nmForma === '') {
            alert('O nome é obrigatório');
            return;
        }
        
        window.location.href = `bd/forma/update.php?idForma=${id}&nmForma=${encodeURIComponent(nmForma)}`;
    });

    $('.cancel-btn-forma').on('click', function() {
        location.reload();
    });

    // ============================================
    // Edição Inline - PERFIS
    // ============================================
    $('.edit-btn-perfil').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowPerfil' + id);
        var currentName = row.find('.pubnmPerfil').text();
        
        row.find('.pubnmPerfil').replaceWith(`<input type="text" class="inline-edit-input pubnmPerfil" value="${currentName}" />`);
        
        row.find('.edit-btn-perfil').hide();
        row.find('.save-btn-perfil, .cancel-btn-perfil').show();
        row.find('.deactivate, .activate').hide();
    });

    $('.save-btn-perfil').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowPerfil' + id);
        var nmPerfil = row.find('input.pubnmPerfil').val().trim();
        
        if (nmPerfil === '') {
            alert('O nome é obrigatório');
            return;
        }
        
        window.location.href = `bd/perfil/update.php?idPerfil=${id}&nmPerfil=${encodeURIComponent(nmPerfil)}`;
    });

    $('.cancel-btn-perfil').on('click', function() {
        location.reload();
    });
});

// ============================================
// Estrutura de Menus - Acordeão
// ============================================
function toggleMenu(id) {
    const card = document.getElementById('menu-' + id);
    card.classList.toggle('expanded');
}

function toggleSubMenu(id) {
    event.stopPropagation();
    const card = document.getElementById('submenu-' + id);
    card.classList.toggle('expanded');
}

// ============================================
// Modal Functions
// ============================================
function fecharModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function abrirModalMenu(id = null, nome = '', link = '') {
    const modal = document.getElementById('modalMenu');
    const form = document.getElementById('formMenu');
    
    document.getElementById('modalMenuTitle').textContent = id ? 'Editar Menu' : 'Novo Menu';
    document.getElementById('inputMenuId').value = id || '';
    document.getElementById('inputMenuNome').value = nome;
    document.getElementById('inputMenuLink').value = link;
    
    form.action = id ? 'bd/menus/update.php' : 'bd/menus/create.php';
    
    modal.classList.add('active');
    document.getElementById('inputMenuNome').focus();
}

function abrirModalSubMenu(id = null, nome = '', link = '', menuPai = null) {
    event.stopPropagation();
    const modal = document.getElementById('modalSubMenu');
    const form = document.getElementById('formSubMenu');
    
    document.getElementById('modalSubMenuTitle').textContent = id ? 'Editar SubMenu' : 'Novo SubMenu';
    document.getElementById('inputSubMenuId').value = id || '';
    document.getElementById('inputSubMenuNome').value = nome;
    document.getElementById('inputSubMenuLink').value = link;
    document.getElementById('inputSubMenuPai').value = menuPai || '';
    
    form.action = id ? 'bd/submenu/update.php' : 'bd/submenu/create.php';
    
    modal.classList.add('active');
    document.getElementById('inputSubMenuNome').focus();
}

function abrirModalItem(id = null, nome = '', link = '', subMenuPai = null) {
    event.stopPropagation();
    const modal = document.getElementById('modalItem');
    const form = document.getElementById('formItem');
    
    document.getElementById('modalItemTitle').textContent = id ? 'Editar Item' : 'Novo Item';
    document.getElementById('inputItemId').value = id || '';
    document.getElementById('inputItemNome').value = nome;
    document.getElementById('inputItemLink').value = link;
    document.getElementById('inputItemPai').value = subMenuPai || '';
    
    form.action = id ? 'bd/itemmenu/update.php' : 'bd/itemmenu/create.php';
    
    modal.classList.add('active');
    document.getElementById('inputItemNome').focus();
}

// Fechar modal ao clicar fora
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(modal => {
            modal.classList.remove('active');
        });
    }
});
</script>

<?php include_once 'includes/footer.inc.php'; ?>