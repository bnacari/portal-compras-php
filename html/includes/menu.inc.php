<?php
//ARQUIVO QUE FAZ A VALIDAÇÃO SE O USUÁRIO ESTÁ LOGADO NO SISTEMA
session_start();
include_once '../bd/conexao.php';

$login = $_SESSION['login'] ?? '';
$nomeUsuario = $_SESSION['nomeUsuario'] ?? $login;

// Processa perfis
$idPerfil = [];
$isAdmin = false;

if (!empty($_SESSION['perfil'])) {
    foreach ($_SESSION['perfil'] as $perfil) {
        $idPerfil[] = $perfil['idPerfil'];
        if ($perfil['idPerfil'] == 9) {
            $isAdmin = true;
        }
    }
    $_SESSION['idPerfilFinal'] = implode(',', $idPerfil);
}

// Funções para obter menus do banco
function obterMenusPrincipais($pdoCAT) {
    $query = "SELECT * FROM MENU WHERE DT_EXC_MENU IS NULL ORDER BY NM_MENU";
    $stmt = $pdoCAT->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obterSubMenus($pdoCAT, $menuID) {
    $query = "SELECT * FROM SUBMENU WHERE ID_MENU = ? AND DT_EXC_SUBMENU IS NULL ORDER BY NM_SUBMENU";
    $stmt = $pdoCAT->prepare($query);
    $stmt->execute([$menuID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obterItensMenu($pdoCAT, $submenuID) {
    $query = "SELECT * FROM ITEMMENU WHERE ID_SUBMENU = ? AND DT_EXC_ITEMMENU IS NULL ORDER BY NM_ITEMMENU";
    $stmt = $pdoCAT->prepare($query);
    $stmt->execute([$submenuID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Detectar ambiente
function getAmbiente() {
    if (strpos($_SERVER['HTTP_HOST'], 'vdesk') !== false || strpos($_SERVER['HTTP_HOST'], 'homolog') !== false) {
        return "HOMOLOGAÇÃO";
    }
    return "PRODUÇÃO";
}

// Obter inicial do usuário para avatar
function getInitials($name) {
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
    }
    return strtoupper(substr($name, 0, 2));
}

$userInitials = getInitials($nomeUsuario);
$ambiente = getAmbiente();
$paginaAtual = basename($_SERVER['PHP_SELF'], '.php');

// Mensagem do sistema
$msgSistema = '';
if (isset($_SESSION['msg'])) {
    $msgSistema = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Compras - CESAN</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    
    <style>
        /* ============================================
           RESET & BASE
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 70px 0 0 280px !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            background-color: #f8fafc !important;
            min-height: 100vh;
            transition: padding 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed {
            padding-left: 80px !important;
        }

        /* ============================================
           HEADER PRINCIPAL
           ============================================ */
        .modern-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .modern-header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-toggle-menu {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-toggle-menu:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .btn-toggle-menu ion-icon {
            font-size: 24px;
        }

        .modern-header-left a {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .modern-header-logo {
            width: 40px;
            height: 44px;
            border-radius: 12px;
        }

        .modern-header-title {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.02em;
        }

        .modern-header-title .emoji {
            font-size: 24px;
        }

        .ambiente-badge {
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
            background: #fbbf24;
            padding: 4px 10px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-left: 8px;
        }

        .ambiente-badge.producao {
            background: #22c55e;
            color: #ffffff;
        }

        .modern-header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 100px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .user-avatar.external {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        .user-avatar.admin {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        .admin-badge {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            margin-left: 4px;
        }

        .btn-logout {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.5);
            color: #fef2f2;
            transform: translateY(-2px);
        }

        .btn-logout ion-icon {
            font-size: 22px;
        }

        /* ============================================
           SIDEBAR MODERNA EXPANDÍVEL
           ============================================ */
        .modern-sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 280px;
            height: calc(100vh - 70px);
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            padding: 24px 0;
            z-index: 999;
            overflow-y: auto;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.04);
        }

        .modern-sidebar.collapsed {
            width: 80px;
        }

        .modern-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .modern-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .sidebar-section {
            margin-bottom: 32px;
        }

        .sidebar-section-title {
            padding: 0 20px;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
            transition: opacity 0.2s ease;
        }

        .modern-sidebar.collapsed .sidebar-section-title {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar-nav {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-item {
            position: relative;
            padding: 0 16px;
        }

        .sidebar-link {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 12px 16px;
            border-radius: 12px;
            color: #64748b;
            background: transparent;
            transition: all 0.2s ease;
            text-decoration: none;
            position: relative;
            font-size: 14px;
            font-weight: 600;
            overflow: hidden;
            min-height: 48px;
        }

        .sidebar-link ion-icon {
            font-size: 22px;
            flex-shrink: 0;
            transition: transform 0.2s ease;
            margin-top: 2px;
        }

        .sidebar-link-text {
            flex: 1;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.4;
            transition: opacity 0.2s ease;
        }

        .modern-sidebar.collapsed .sidebar-link-text {
            opacity: 0;
            pointer-events: none;
        }

        .modern-sidebar.collapsed .sidebar-link {
            justify-content: center;
            padding: 12px;
        }

        .sidebar-link:hover {
            background: #f8fafc;
            color: #3b82f6;
        }

        .sidebar-link:hover ion-icon {
            transform: scale(1.1);
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #3b82f6;
            font-weight: 700;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: #3b82f6;
            border-radius: 0 4px 4px 0;
        }

        /* Tooltip para menu colapsado */
        .sidebar-link::after {
            content: attr(data-title);
            position: absolute;
            left: 80px;
            top: 50%;
            transform: translateY(-50%);
            background: #0f172a;
            color: white;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            z-index: 1000;
        }

        .modern-sidebar.collapsed .sidebar-link:hover::after {
            opacity: 1;
            left: 85px;
        }

        /* Badge de contador */
        .sidebar-badge {
            background: #eff6ff;
            color: #3b82f6;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 100px;
            margin-left: auto;
            transition: opacity 0.2s ease;
        }

        .sidebar-badge.external {
            background: #fef3c7;
            color: #b45309;
        }

        .sidebar-badge.admin {
            background: #fee2e2;
            color: #dc2626;
        }

        .modern-sidebar.collapsed .sidebar-badge {
            opacity: 0;
            pointer-events: none;
        }

        /* Divisor */
        .sidebar-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            margin: 20px 16px;
        }

        /* Submenu styles */
        .sidebar-submenu {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sidebar-item.open > .sidebar-submenu {
            max-height: 1000px;
        }

        .sidebar-submenu .sidebar-item {
            padding: 0 16px 0 32px;
        }

        .sidebar-submenu .sidebar-link {
            padding: 10px 16px;
            font-size: 13px;
            min-height: 40px;
        }

        .sidebar-submenu .sidebar-link ion-icon {
            font-size: 18px;
        }

        .sidebar-submenu .sidebar-link-text {
            font-size: 13px;
            line-height: 1.3;
        }

        .sidebar-link .arrow-icon {
            font-size: 16px;
            transition: transform 0.2s ease;
            margin-left: auto;
            flex-shrink: 0;
        }

        .sidebar-item.open > .sidebar-link .arrow-icon {
            transform: rotate(90deg);
        }

        /* Nested submenu */
        .sidebar-submenu .sidebar-submenu .sidebar-item {
            padding-left: 48px;
        }

        .sidebar-submenu .sidebar-submenu .sidebar-link {
            font-size: 12px;
            padding: 8px 16px;
            min-height: 36px;
        }

        .sidebar-submenu .sidebar-submenu .sidebar-link-text {
            font-size: 12px;
        }

        /* ============================================
           MODAL DE MENSAGEM MODERNO
           ============================================ */
        .modern-modal-message {
            display: none;
            position: fixed;
            top: 90px;
            right: 24px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #3b82f6;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            z-index: 10000;
            max-width: 400px;
            animation: slideInRight 0.3s ease;
        }

        .modern-modal-message.success {
            border-left-color: #22c55e;
        }

        .modern-modal-message.error {
            border-left-color: #ef4444;
        }

        .modern-modal-message.warning {
            border-left-color: #f59e0b;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .modal-message-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .modal-message-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 18px;
        }

        .modern-modal-message.success .modal-message-icon {
            background: #dcfce7;
            color: #16a34a;
        }

        .modern-modal-message.error .modal-message-icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .modern-modal-message.warning .modal-message-icon {
            background: #fef3c7;
            color: #d97706;
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .modal-close-btn:hover {
            background: #f1f5f9;
            color: #475569;
        }

        .modal-message-text {
            font-size: 14px;
            color: #475569;
            line-height: 1.5;
            margin: 0;
        }

        /* ============================================
           MODAL CONTATO
           ============================================ */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background: #ffffff;
            border-radius: 20px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalAppear 0.3s ease-out;
        }

        @keyframes modalAppear {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 24px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 20px 20px 0 0;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-title ion-icon {
            font-size: 24px;
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #ffffff;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-close ion-icon {
            font-size: 20px;
        }

        .modal-body {
            padding: 32px;
        }

        .contact-section {
            margin-bottom: 28px;
        }

        .contact-section:last-child {
            margin-bottom: 0;
        }

        .contact-section-title {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .contact-section-subtitle {
            font-size: 14px;
            font-weight: 600;
            color: #3b82f6;
            margin-bottom: 4px;
        }

        .contact-section-address {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 16px;
        }

        .contact-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
        }

        .contact-item ion-icon {
            font-size: 18px;
            color: #3b82f6;
        }

        .contact-item a {
            color: #3b82f6;
            text-decoration: none;
        }

        .contact-item a:hover {
            text-decoration: underline;
        }

        .modal-footer {
            padding: 20px 32px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
        }

        .btn-modal {
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            background: #f1f5f9;
            color: #64748b;
        }

        .btn-modal:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 1024px) {
            .modern-header {
                padding: 0 16px;
            }

            .modern-sidebar {
                width: 240px;
            }

            body {
                padding-left: 240px !important;
            }

            .user-info span {
                display: none;
            }

            .admin-badge {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .modern-header {
                padding: 0 12px;
            }

            .modern-header-title {
                font-size: 16px;
            }

            .modern-header-title .emoji {
                display: none;
            }

            .ambiente-badge {
                display: none;
            }

            .modern-sidebar {
                transform: translateX(-100%);
                width: 280px;
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            }

            .modern-sidebar.mobile-open {
                transform: translateX(0);
            }

            body {
                padding-left: 0 !important;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(15, 23, 42, 0.5);
                z-index: 998;
                backdrop-filter: blur(2px);
            }

            .sidebar-overlay.show {
                display: block;
            }

            /* Botão hambúrguer visível em mobile */
            .btn-toggle-menu {
                display: flex;
            }

            .user-info {
                padding: 8px;
            }

            .btn-logout {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 480px) {
            .modern-header {
                height: 60px;
                padding: 0 10px;
            }

            .modern-header-logo {
                width: 32px;
                height: 36px;
            }

            .modern-header-title {
                font-size: 14px;
            }

            .btn-toggle-menu {
                width: 38px;
                height: 38px;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .btn-logout {
                width: 36px;
                height: 36px;
            }

            .btn-logout ion-icon {
                font-size: 18px;
            }

            .modern-sidebar {
                top: 60px;
                height: calc(100vh - 60px);
                width: 260px;
            }

            body {
                padding-top: 60px !important;
            }

            .sidebar-overlay {
                top: 60px;
            }

            .sidebar-link {
                padding: 10px 14px;
                font-size: 13px;
                min-height: 44px;
            }

            .sidebar-link ion-icon {
                font-size: 20px;
            }

            .sidebar-section-title {
                font-size: 10px;
                padding: 0 16px;
            }

            .sidebar-item {
                padding: 0 12px;
            }
        }

        /* Botão hambúrguer - hidden by default, shown on mobile */
        .btn-toggle-menu {
            display: none;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .btn-toggle-menu:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .btn-toggle-menu ion-icon {
            font-size: 24px;
        }

        @media (max-width: 768px) {
            .btn-toggle-menu {
                display: flex;
            }
        }

        /* Esconder elementos antigos */
        .header2,
        .sidebar2,
        .content2,
        #check {
            display: none !important;
        }
    </style>
</head>
<body>

<!-- Overlay para mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileMenu()"></div>

<!-- Header Moderno -->
<header class="modern-header">
    <div class="modern-header-left">
        <button class="btn-toggle-menu" onclick="toggleSidebar()" title="Expandir/Recolher Menu">
            <ion-icon name="menu-outline"></ion-icon>
        </button>

        <a href="index.php">
            <img src="imagens/logo_icon.png" class="modern-header-logo" alt="Logo">
            <h3 class="modern-header-title">
                Portal de Compras
                <span class="ambiente-badge <?php echo $ambiente === 'PRODUÇÃO' ? 'producao' : ''; ?>"><?php echo $ambiente; ?></span>
            </h3>
        </a>
    </div>

    <div class="modern-header-right">
        <?php if (isset($_SESSION['login'])): ?>
            <div class="user-info">
                <div class="user-avatar <?php echo $isAdmin ? 'admin' : ''; ?>">
                    <?php echo $userInitials; ?>
                </div>
                <span><?php echo htmlspecialchars($login); ?></span>
                <?php if ($isAdmin): ?>
                    <span class="admin-badge">ADMIN</span>
                <?php endif; ?>
            </div>
            <a href="logout.php" class="btn-logout" title="Sair do Sistema">
                <ion-icon name="exit-outline"></ion-icon>
            </a>
        <?php else: ?>
            <a href="login.php" class="btn-logout" style="background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.3); color: #93c5fd;" title="Acessar Sistema">
                <ion-icon name="log-in-outline"></ion-icon>
            </a>
        <?php endif; ?>
    </div>
</header>

<!-- Sidebar Moderna -->
<aside class="modern-sidebar" id="modernSidebar">
    
    <!-- Seção Principal -->
    <div class="sidebar-section">
        <div class="sidebar-section-title">Principal</div>
        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a href="consultarLicitacao.php" class="sidebar-link <?php echo $paginaAtual === 'consultarLicitacao' ? 'active' : ''; ?>" data-title="Licitações">
                    <ion-icon name="document-text-outline"></ion-icon>
                    <span class="sidebar-link-text">Licitações</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Menus Dinâmicos do Banco -->
    <?php
    $menusPrincipais = obterMenusPrincipais($pdoCAT);
    if (!empty($menusPrincipais)):
    ?>
    <div class="sidebar-section">
        <div class="sidebar-section-title">Navegação</div>
        <ul class="sidebar-nav">
            <?php foreach ($menusPrincipais as $menu): 
                $submenus = obterSubMenus($pdoCAT, $menu['ID_MENU']);
                $hasSubmenus = !empty($submenus);
            ?>
            <li class="sidebar-item <?php echo $hasSubmenus ? 'has-submenu' : ''; ?>">
                <?php if ($hasSubmenus): ?>
                    <a href="javascript:void(0)" class="sidebar-link" onclick="toggleSubmenu(this)" data-title="<?php echo htmlspecialchars($menu['NM_MENU']); ?>">
                        <ion-icon name="folder-outline"></ion-icon>
                        <span class="sidebar-link-text"><?php echo htmlspecialchars($menu['NM_MENU']); ?></span>
                        <ion-icon name="chevron-forward-outline" class="arrow-icon"></ion-icon>
                    </a>
                    <ul class="sidebar-submenu">
                        <?php foreach ($submenus as $submenu): 
                            $itens = obterItensMenu($pdoCAT, $submenu['ID_SUBMENU']);
                            $hasItens = !empty($itens);
                        ?>
                        <li class="sidebar-item <?php echo $hasItens ? 'has-submenu' : ''; ?>">
                            <?php if ($hasItens): ?>
                                <a href="javascript:void(0)" class="sidebar-link" onclick="toggleSubmenu(this)" data-title="<?php echo htmlspecialchars($submenu['NM_SUBMENU']); ?>">
                                    <ion-icon name="folder-open-outline"></ion-icon>
                                    <span class="sidebar-link-text"><?php echo htmlspecialchars($submenu['NM_SUBMENU']); ?></span>
                                    <ion-icon name="chevron-forward-outline" class="arrow-icon"></ion-icon>
                                </a>
                                <ul class="sidebar-submenu">
                                    <?php foreach ($itens as $item): ?>
                                    <li class="sidebar-item">
                                        <a href="<?php echo htmlspecialchars($item['LINK_ITEMMENU']); ?>" class="sidebar-link" target="_blank" data-title="<?php echo htmlspecialchars($item['NM_ITEMMENU']); ?>">
                                            <ion-icon name="link-outline"></ion-icon>
                                            <span class="sidebar-link-text"><?php echo htmlspecialchars($item['NM_ITEMMENU']); ?></span>
                                            <span class="sidebar-badge external">Ext</span>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($submenu['LINK_SUBMENU']); ?>" class="sidebar-link" target="_blank" data-title="<?php echo htmlspecialchars($submenu['NM_SUBMENU']); ?>">
                                    <ion-icon name="link-outline"></ion-icon>
                                    <span class="sidebar-link-text"><?php echo htmlspecialchars($submenu['NM_SUBMENU']); ?></span>
                                    <span class="sidebar-badge external">Ext</span>
                                </a>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars($menu['LINK_MENU']); ?>" class="sidebar-link" target="_blank" data-title="<?php echo htmlspecialchars($menu['NM_MENU']); ?>">
                        <ion-icon name="link-outline"></ion-icon>
                        <span class="sidebar-link-text"><?php echo htmlspecialchars($menu['NM_MENU']); ?></span>
                        <span class="sidebar-badge external">Ext</span>
                    </a>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Cadastros (usuários logados) -->
    <?php if (!empty($_SESSION['idPerfilFinal'])): ?>
    <div class="sidebar-divider"></div>
    <div class="sidebar-section">
        <div class="sidebar-section-title">Cadastros</div>
        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a href="cadLicitacao.php" class="sidebar-link <?php echo $paginaAtual === 'cadLicitacao' ? 'active' : ''; ?>" data-title="Criar Licitação">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <span class="sidebar-link-text">Criar Licitação</span>
                </a>
            </li>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Administração -->
    <?php if ($isAdmin): ?>
    <div class="sidebar-divider"></div>
    <div class="sidebar-section">
        <div class="sidebar-section-title">Administração</div>
        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a href="cadCriterio.php" class="sidebar-link <?php echo $paginaAtual === 'cadCriterio' ? 'active' : ''; ?>" data-title="Critérios">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                    <span class="sidebar-link-text">Critérios</span>
                    <span class="sidebar-badge admin">Adm</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="cadForma.php" class="sidebar-link <?php echo $paginaAtual === 'cadForma' ? 'active' : ''; ?>" data-title="Formas">
                    <ion-icon name="git-branch-outline"></ion-icon>
                    <span class="sidebar-link-text">Formas</span>
                    <span class="sidebar-badge admin">Adm</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="cadTipo.php" class="sidebar-link <?php echo $paginaAtual === 'cadTipo' ? 'active' : ''; ?>" data-title="Tipos de Contratação">
                    <ion-icon name="pricetag-outline"></ion-icon>
                    <span class="sidebar-link-text">Tipos de Contratação</span>
                    <span class="sidebar-badge admin">Adm</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="consultarUsuario.php" class="sidebar-link <?php echo $paginaAtual === 'consultarUsuario' ? 'active' : ''; ?>" data-title="Usuários">
                    <ion-icon name="people-outline"></ion-icon>
                    <span class="sidebar-link-text">Usuários</span>
                    <span class="sidebar-badge admin">Adm</span>
                </a>
            </li>
            <li class="sidebar-item has-submenu">
                <a href="javascript:void(0)" class="sidebar-link" onclick="toggleSubmenu(this)" data-title="Menus">
                    <ion-icon name="menu-outline"></ion-icon>
                    <span class="sidebar-link-text">Menus</span>
                    <ion-icon name="chevron-forward-outline" class="arrow-icon"></ion-icon>
                </a>
                <ul class="sidebar-submenu">
                    <li class="sidebar-item">
                        <a href="cadMenu.php" class="sidebar-link <?php echo $paginaAtual === 'cadMenu' ? 'active' : ''; ?>" data-title="Menu Principal">
                            <ion-icon name="list-outline"></ion-icon>
                            <span class="sidebar-link-text">Menu Principal</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="cadSubMenu.php" class="sidebar-link <?php echo $paginaAtual === 'cadSubMenu' ? 'active' : ''; ?>" data-title="SubMenu">
                            <ion-icon name="git-network-outline"></ion-icon>
                            <span class="sidebar-link-text">SubMenu</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="cadItemMenu.php" class="sidebar-link <?php echo $paginaAtual === 'cadItemMenu' ? 'active' : ''; ?>" data-title="Item de Menu">
                            <ion-icon name="link-outline"></ion-icon>
                            <span class="sidebar-link-text">Item de Menu</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a href="cadAnexos.php" class="sidebar-link <?php echo $paginaAtual === 'cadAnexos' ? 'active' : ''; ?>" data-title="Anexos">
                    <ion-icon name="attach-outline"></ion-icon>
                    <span class="sidebar-link-text">Anexos</span>
                    <span class="sidebar-badge admin">Adm</span>
                </a>
            </li>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Configurações -->
    <?php if (isset($_SESSION['login'])): ?>
    <div class="sidebar-divider"></div>
    <div class="sidebar-section">
        <div class="sidebar-section-title">Configurações</div>
        <ul class="sidebar-nav">
            <?php if (strpos($login, '@') !== false): ?>
            <li class="sidebar-item">
                <a href="trocaSenhaUsuario.php" class="sidebar-link <?php echo $paginaAtual === 'trocaSenhaUsuario' ? 'active' : ''; ?>" data-title="Trocar Senha">
                    <ion-icon name="key-outline"></ion-icon>
                    <span class="sidebar-link-text">Trocar Senha</span>
                </a>
            </li>
            <?php endif; ?>
            <li class="sidebar-item">
                <a href="consultarAtualizacao.php" class="sidebar-link <?php echo $paginaAtual === 'consultarAtualizacao' ? 'active' : ''; ?>" data-title="Envio de E-mail">
                    <ion-icon name="mail-outline"></ion-icon>
                    <span class="sidebar-link-text">Envio de E-mail</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="javascript:void(0)" class="sidebar-link" onclick="openModalContato()" data-title="Contatos">
                    <ion-icon name="chatbubble-ellipses-outline"></ion-icon>
                    <span class="sidebar-link-text">Contatos</span>
                </a>
            </li>
        </ul>
    </div>
    <?php endif; ?>

</aside>

<!-- Modal de Contatos -->
<div class="modal-overlay" id="modalContato">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">
                <ion-icon name="chatbubbles-outline"></ion-icon>
                Contatos
            </h3>
            <button class="modal-close" onclick="closeModalContato()">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>
        <div class="modal-body">
            <div class="contact-section">
                <p class="contact-section-title">Informações, dúvidas e esclarecimentos sobre licitação CESAN</p>
                <p class="contact-section-subtitle">Comissão Permanente de Licitações (CPL)</p>
                <p class="contact-section-address">Rua Nelcy Lopes Vieira, S/N, Jardim Limoeiro, Serra, ES, CEP: 29164-018</p>
                <div class="contact-list">
                    <div class="contact-item">
                        <ion-icon name="call-outline"></ion-icon>
                        <span>(27) 2127-5119</span>
                    </div>
                    <div class="contact-item">
                        <ion-icon name="mail-outline"></ion-icon>
                        <a href="mailto:licitacoes@cesan.com.br">licitacoes@cesan.com.br</a>
                    </div>
                </div>
            </div>

            <div class="contact-section">
                <p class="contact-section-title">Informações sobre pregões, dispensas eletrônicas e cadastro de fornecedores</p>
                <p class="contact-section-subtitle">Divisão de Compras e Suprimentos (A-DCS)</p>
                <p class="contact-section-address">Rua Nelcy Lopes Vieira, S/N, Jardim Limoeiro, Serra, ES, CEP: 29164-018</p>
                
                <p style="font-weight: 600; color: #0f172a; margin: 16px 0 12px 0;">Pregoeiros</p>
                <div class="contact-list">
                    <div class="contact-item">
                        <ion-icon name="mail-outline"></ion-icon>
                        <a href="mailto:pregao@cesan.com.br">pregao@cesan.com.br</a>
                    </div>
                    <div class="contact-item">
                        <ion-icon name="call-outline"></ion-icon>
                        <span>Luciana Toledo - (27) 2127-5299</span>
                    </div>
                    <div class="contact-item">
                        <ion-icon name="call-outline"></ion-icon>
                        <span>Fernando Cordeiro - (27) 2127-5418</span>
                    </div>
                    <div class="contact-item">
                        <ion-icon name="call-outline"></ion-icon>
                        <span>Mirelle Ino - (27) 2127-5429</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal" onclick="closeModalContato()">Fechar</button>
        </div>
    </div>
</div>

<!-- Modal de Mensagem Moderno -->
<div id="modalMensagem" class="modern-modal-message">
    <div class="modal-message-header">
        <div class="modal-message-icon">
            <ion-icon name="checkmark-circle"></ion-icon>
        </div>
        <button class="modal-close-btn" onclick="fecharModal()">
            <ion-icon name="close"></ion-icon>
        </button>
    </div>
    <p id="textoMensagem" class="modal-message-text"></p>
</div>

<!-- Scripts -->
<script>
    // Toggle Sidebar (Desktop)
    function toggleSidebar() {
        const sidebar = document.getElementById('modernSidebar');
        const body = document.body;

        if (window.innerWidth <= 768) {
            // Mobile: abrir/fechar completamente
            sidebar.classList.toggle('mobile-open');
            document.getElementById('sidebarOverlay').classList.toggle('show');
            body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        } else {
            // Desktop: expandir/colapsar
            sidebar.classList.toggle('collapsed');
            body.classList.toggle('sidebar-collapsed');

            // Salvar preferência no localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }
    }

    // Toggle Mobile Menu
    function toggleMobileMenu() {
        const sidebar = document.getElementById('modernSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    // Toggle Submenu
    function toggleSubmenu(element) {
        const parent = element.closest('.sidebar-item');
        parent.classList.toggle('open');
    }

    // Restaurar estado do sidebar ao carregar
    window.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth > 768) {
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true') {
                document.getElementById('modernSidebar').classList.add('collapsed');
                document.body.classList.add('sidebar-collapsed');
            }
        }
    });

    // Modal de mensagem
    function abrirModal(mensagem, tipo = 'info') {
        const modal = document.getElementById('modalMensagem');
        const iconEl = modal.querySelector('.modal-message-icon ion-icon');

        // Remover classes anteriores
        modal.classList.remove('success', 'error', 'warning');

        // Definir tipo
        const icons = {
            success: 'checkmark-circle',
            error: 'close-circle',
            warning: 'warning',
            info: 'information-circle'
        };

        modal.classList.add(tipo);
        iconEl.setAttribute('name', icons[tipo] || icons.info);

        document.getElementById('textoMensagem').textContent = mensagem;
        modal.style.display = 'block';

        setTimeout(() => {
            fecharModal();
        }, 5000);
    }

    function fecharModal() {
        const modal = document.getElementById('modalMensagem');
        modal.style.display = 'none';
    }

    // Modal Contato
    function openModalContato() {
        document.getElementById('modalContato').classList.add('active');
    }

    function closeModalContato() {
        document.getElementById('modalContato').classList.remove('active');
    }

    // Fechar modal ao clicar no overlay
    document.getElementById('modalContato').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModalContato();
        }
    });

    // Fechar sidebar ao clicar em link (mobile)
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768 && !link.getAttribute('onclick')) {
                toggleMobileMenu();
            }
        });
    });

    // Fechar com ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            fecharModal();
            closeModalContato();
            if (window.innerWidth <= 768) {
                toggleMobileMenu();
            }
        }
    });
</script>

<?php if (!empty($msgSistema)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        abrirModal(<?php echo json_encode($msgSistema); ?>, 'info');
    });
</script>
<?php endif; ?>