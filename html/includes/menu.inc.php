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
function obterMenusPrincipais($pdoCAT)
{
    $query = "SELECT * FROM MENU WHERE DT_EXC_MENU IS NULL ORDER BY NM_MENU";
    $stmt = $pdoCAT->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obterSubMenus($pdoCAT, $menuID)
{
    $query = "SELECT * FROM SUBMENU WHERE ID_MENU = ? AND DT_EXC_SUBMENU IS NULL ORDER BY NM_SUBMENU";
    $stmt = $pdoCAT->prepare($query);
    $stmt->execute([$menuID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obterItensMenu($pdoCAT, $submenuID)
{
    $query = "SELECT * FROM ITEMMENU WHERE ID_SUBMENU = ? AND DT_EXC_ITEMMENU IS NULL ORDER BY NM_ITEMMENU";
    $stmt = $pdoCAT->prepare($query);
    $stmt->execute([$submenuID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Detectar ambiente
function getAmbiente()
{
    if (
        strpos($_SERVER['HTTP_HOST'], 'vdesk') !== false
        || strpos($_SERVER['HTTP_HOST'], 'hom-') !== false
    ) {
        return "HOMOLOGAÇÃO";
    }
    return "PRODUÇÃO";
}

// Obter inicial do usuário para avatar
function getInitials($name)
{
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons -->
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
            padding: 60px 0 0 220px !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background-color: #f8fafc !important;
            min-height: 100vh;
            transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed {
            padding-left: 70px !important;
        }

        /* ============================================
           HEADER PRINCIPAL - 60px
           ============================================ */
        .modern-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .modern-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-toggle-menu {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
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
            font-size: 20px;
        }

        .modern-header-left a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .modern-header-logo {
            width: 32px;
            height: 38px;
            border-radius: 8px;
            object-fit: contain;
        }

        .modern-header-title {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .modern-header-title .brand-name {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modern-header-title .system-fullname {
            font-size: 10px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 0.02em;
            line-height: 1;
        }

        .ambiente-badge {
            font-size: 8px;
            font-weight: 700;
            color: #0f172a;
            background: #fbbf24;
            padding: 3px 8px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ambiente-badge.producao {
            background: #22c55e;
            color: white;
        }

        .modern-header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 14px 6px 6px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }

        .user-avatar.admin {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .admin-badge {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 2px;
        }

        .btn-logout {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
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
            border-color: rgba(239, 68, 68, 0.4);
            color: #fef2f2;
        }

        .btn-logout ion-icon {
            font-size: 18px;
        }

        .btn-login {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.25);
            color: #93c5fd;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-login:hover {
            background: rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.4);
            color: #bfdbfe;
        }

        .btn-login ion-icon {
            font-size: 18px;
        }

        /* ============================================
           SIDEBAR - 220px (70px collapsed)
           ============================================ */
        .modern-sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 220px;
            height: calc(100vh - 60px);
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            padding: 16px 0;
            z-index: 999;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.04);
        }

        .modern-sidebar.collapsed {
            width: 70px;
        }

        .modern-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .modern-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* Seção do Menu */
        .sidebar-section {
            margin-bottom: 8px;
        }

        /* Título da Seção - Clicável */
        .sidebar-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            margin: 0 12px 4px 12px;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s ease;
            user-select: none;
        }

        .sidebar-section-title:hover {
            background: #f1f5f9;
            color: #475569;
        }

        .sidebar-section-title .section-icon {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-section-title .section-icon ion-icon {
            font-size: 14px;
            opacity: 0.7;
        }

        .sidebar-section-title .toggle-icon {
            font-size: 14px;
            transition: transform 0.3s ease;
            opacity: 0.5;
        }

        .sidebar-section-title.collapsed .toggle-icon {
            transform: rotate(-90deg);
        }

        /* Conteúdo da Seção - Recolhível */
        .sidebar-section-content {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.2s ease;
            opacity: 1;
        }

        .sidebar-section-content.collapsed {
            max-height: 0;
            opacity: 0;
        }

        /* Menu colapsado */
        .modern-sidebar.collapsed .sidebar-section-title {
            justify-content: center;
            padding: 8px;
            margin: 0 8px 4px 8px;
        }

        .modern-sidebar.collapsed .sidebar-section-title span,
        .modern-sidebar.collapsed .sidebar-section-title .toggle-icon {
            display: none;
        }

        .modern-sidebar.collapsed .sidebar-section-title .section-icon ion-icon {
            font-size: 18px;
            opacity: 1;
        }

        .modern-sidebar.collapsed .sidebar-section-content {
            max-height: 1000px !important;
            opacity: 1 !important;
        }

        /* Nav Links */
        .sidebar-nav {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sidebar-item {
            padding: 0 12px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #64748b;
            background: transparent;
            transition: all 0.2s ease;
            text-decoration: none;
            position: relative;
            font-size: 12px;
            font-weight: 500;
        }

        .sidebar-link ion-icon {
            font-size: 18px;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .sidebar-link-text {
            flex: 1;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.4;
            transition: opacity 0.2s ease;
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
            font-weight: 600;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 18px;
            background: #3b82f6;
            border-radius: 0 3px 3px 0;
        }

        /* Badge nos links */
        .sidebar-badge {
            background: #eff6ff;
            color: #3b82f6;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 100px;
            margin-left: auto;
            flex-shrink: 0;
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
            display: none;
        }

        /* Arrow para submenus */
        .sidebar-link .arrow-icon {
            font-size: 14px;
            transition: transform 0.2s ease;
            margin-left: auto;
            flex-shrink: 0;
            opacity: 0.5;
        }

        .sidebar-item.open>.sidebar-link .arrow-icon {
            transform: rotate(90deg);
        }

        /* Menu colapsado */
        .modern-sidebar.collapsed .sidebar-link-text,
        .modern-sidebar.collapsed .arrow-icon {
            opacity: 0;
            width: 0;
            display: none;
        }

        .modern-sidebar.collapsed .sidebar-link {
            justify-content: center;
            padding: 10px;
        }

        .modern-sidebar.collapsed .sidebar-item {
            padding: 0 8px;
        }

        /* Tooltip para menu colapsado */
        .sidebar-link::after {
            content: attr(data-title);
            position: absolute;
            left: 65px;
            top: 50%;
            transform: translateY(-50%);
            background: #0f172a;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            z-index: 1001;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .modern-sidebar.collapsed .sidebar-link:hover::after {
            opacity: 1;
            left: 70px;
        }

        /* Submenus */
        .sidebar-submenu {
            list-style: none;
            margin: 0;
            padding: 4px 0 4px 12px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sidebar-item.open>.sidebar-submenu {
            max-height: 1000px;
        }

        .sidebar-submenu .sidebar-item {
            padding: 0 12px 0 0;
        }

        .sidebar-submenu .sidebar-link {
            padding: 8px 10px;
            font-size: 11px;
            border-radius: 6px;
            margin: 1px 0;
        }

        .sidebar-submenu .sidebar-link ion-icon {
            font-size: 15px;
        }

        .sidebar-submenu .sidebar-link .sidebar-badge {
            font-size: 8px;
            padding: 1px 5px;
        }

        /* Nested submenu (nível 2) */
        .sidebar-submenu .sidebar-submenu {
            margin: 0;
            padding: 4px 0 4px 8px;
        }

        .sidebar-submenu .sidebar-submenu .sidebar-item {
            padding: 0 12px 0 0;
        }

        .sidebar-submenu .sidebar-submenu .sidebar-link {
            font-size: 11px;
            padding: 7px 10px;
        }

        /* Nested submenu (nível 3) */
        .sidebar-submenu .sidebar-submenu .sidebar-submenu {
            padding-left: 8px;
        }

        /* Divisor */
        .sidebar-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            margin: 12px 16px;
        }

        /* ============================================
           TOAST NOTIFICATIONS
           ============================================ */
        .toast-container {
            position: fixed;
            top: 76px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .toast {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.05);
            min-width: 300px;
            max-width: 380px;
            pointer-events: auto;
            animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            border-left: 4px solid #3b82f6;
        }

        .toast.sucesso {
            border-left-color: #22c55e;
        }

        .toast.erro {
            border-left-color: #ef4444;
        }

        .toast.alerta {
            border-left-color: #f59e0b;
        }

        .toast.info {
            border-left-color: #3b82f6;
        }

        .toast-icon {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }

        .toast.sucesso .toast-icon {
            background: #dcfce7;
            color: #15803d;
        }

        .toast.erro .toast-icon {
            background: #fee2e2;
            color: #b91c1c;
        }

        .toast.alerta .toast-icon {
            background: #fef3c7;
            color: #b45309;
        }

        .toast.info .toast-icon {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .toast-content {
            flex: 1;
        }

        .toast-message {
            font-size: 13px;
            color: #475569;
            margin: 0;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 2px;
            font-size: 16px;
            line-height: 1;
            transition: color 0.2s ease;
        }

        .toast-close:hover {
            color: #475569;
        }

        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(100px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .toast.hiding {
            animation: toastSlideOut 0.3s ease forwards;
        }

        @keyframes toastSlideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(100px);
            }
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
            from {
                opacity: 0;
                transform: scale(0.95) translateY(20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 20px 20px 0 0;
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-title ion-icon {
            font-size: 20px;
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #ffffff;
            width: 32px;
            height: 32px;
            border-radius: 8px;
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
            font-size: 18px;
        }

        .modal-body {
            padding: 24px;
        }

        .contact-section {
            margin-bottom: 24px;
        }

        .contact-section:last-child {
            margin-bottom: 0;
        }

        .contact-section-title {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .contact-section-subtitle {
            font-size: 13px;
            font-weight: 600;
            color: #3b82f6;
            margin-bottom: 4px;
        }

        .contact-section-address {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 12px;
        }

        .contact-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: #f8fafc;
            border-radius: 8px;
            font-size: 13px;
            color: #0f172a;
        }

        .contact-item ion-icon {
            font-size: 16px;
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
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
        }

        .btn-modal {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
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
           RESPONSIVIDADE
           ============================================ */
        @media (max-width: 1024px) {
            .modern-header {
                padding: 0 16px;
            }

            .modern-sidebar {
                width: 70px;
            }

            .modern-sidebar .sidebar-section-title span,
            .modern-sidebar .sidebar-section-title .toggle-icon,
            .modern-sidebar .sidebar-link-text,
            .modern-sidebar .sidebar-badge,
            .modern-sidebar .arrow-icon {
                display: none;
                opacity: 0;
            }

            .modern-sidebar .sidebar-section-title {
                justify-content: center;
                padding: 8px;
                margin: 0 8px 4px 8px;
            }

            .modern-sidebar .sidebar-link {
                justify-content: center;
                padding: 10px;
            }

            .modern-sidebar .sidebar-item {
                padding: 0 8px;
            }

            .modern-sidebar .sidebar-section-content {
                max-height: 1000px !important;
                opacity: 1 !important;
            }

            .modern-sidebar .sidebar-submenu {
                display: none;
            }

            body {
                padding-left: 70px !important;
            }

            .user-info span:not(.user-avatar) {
                display: none;
            }

            .user-info {
                padding: 6px;
            }

            .admin-badge {
                display: none;
            }

            .modern-header-title .system-fullname {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .modern-header {
                height: 56px;
                padding: 0 12px;
            }

            .modern-header-title .brand-name {
                font-size: 14px;
            }

            .ambiente-badge {
                display: none;
            }

            .modern-sidebar {
                top: 56px;
                height: calc(100vh - 56px);
                width: 0;
                padding: 0;
                border: none;
            }

            .modern-sidebar.mobile-open {
                width: 260px;
                padding: 16px 0;
                border-right: 1px solid #e2e8f0;
            }

            .modern-sidebar.mobile-open .sidebar-section-title span,
            .modern-sidebar.mobile-open .sidebar-section-title .toggle-icon,
            .modern-sidebar.mobile-open .sidebar-link-text,
            .modern-sidebar.mobile-open .sidebar-badge,
            .modern-sidebar.mobile-open .arrow-icon {
                display: flex;
                opacity: 1;
            }

            .modern-sidebar.mobile-open .sidebar-section-title {
                justify-content: space-between;
                padding: 10px 16px;
                margin: 0 12px 4px 12px;
            }

            .modern-sidebar.mobile-open .sidebar-link {
                justify-content: flex-start;
                padding: 9px 12px;
            }

            .modern-sidebar.mobile-open .sidebar-item {
                padding: 0 12px;
            }

            .modern-sidebar.mobile-open .sidebar-submenu {
                display: block;
            }

            body {
                padding: 56px 0 0 0 !important;
            }

            body.sidebar-collapsed {
                padding-left: 0 !important;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 56px;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 998;
                backdrop-filter: blur(2px);
            }

            .sidebar-overlay.active {
                display: block;
            }
        }

        @media (max-width: 480px) {
            .modern-header-logo {
                width: 28px;
                height: 32px;
            }

            .btn-toggle-menu,
            .btn-logout,
            .btn-login {
                width: 34px;
                height: 34px;
            }

            .user-avatar {
                width: 28px;
                height: 28px;
                font-size: 10px;
            }

            .toast-container {
                top: 68px;
                right: 12px;
                left: 12px;
            }

            .toast {
                min-width: auto;
                max-width: 100%;
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
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

    <!-- Header Moderno -->
    <header class="modern-header">
        <div class="modern-header-left">
            <button class="btn-toggle-menu" onclick="toggleSidebar()" title="Menu">
                <ion-icon name="menu-outline"></ion-icon>
            </button>

            <a href="index.php">
                <img src="imagens/logo_icon.png" class="modern-header-logo" alt="Logo">
                <div class="modern-header-title">
                    <span class="brand-name">
                        Portal de Compras
                        <span
                            class="ambiente-badge <?= $ambiente === 'PRODUÇÃO' ? 'producao' : '' ?>"><?= $ambiente ?></span>
                    </span>
                    <span class="system-fullname">Licitações e Contratos - CESAN</span>
                </div>
            </a>
        </div>

        <div class="modern-header-right">
            <?php if (isset($_SESSION['login'])): ?>
                <div class="user-info">
                    <div class="user-avatar <?= $isAdmin ? 'admin' : '' ?>"><?= $userInitials ?></div>
                    <span><?= htmlspecialchars($login) ?></span>
                    <?php if ($isAdmin): ?><span class="admin-badge">ADMIN</span><?php endif; ?>
                </div>
                <a href="logout.php" class="btn-logout" title="Sair do Sistema">
                    <ion-icon name="exit-outline"></ion-icon>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-login" title="Acessar Sistema">
                    <ion-icon name="log-in-outline"></ion-icon>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Sidebar Moderna -->
    <aside class="modern-sidebar" id="modernSidebar">
        <!-- Seção: Principal -->
        <div class="sidebar-section">
            <div class="sidebar-section-title" onclick="toggleSection(this)" data-section="principal">
                <span class="section-icon">
                    <ion-icon name="home-outline"></ion-icon>
                    <span>Principal</span>
                </span>
                <ion-icon name="chevron-down-outline" class="toggle-icon"></ion-icon>
            </div>
            <div class="sidebar-section-content" id="section-principal">
                <ul class="sidebar-nav">
                    <li class="sidebar-item">
                        <a href="consultarLicitacao.php"
                            class="sidebar-link <?= $paginaAtual === 'consultarLicitacao' ? 'active' : '' ?>"
                            data-title="Licitações">
                            <ion-icon name="document-text-outline"></ion-icon>
                            <span class="sidebar-link-text">Licitações</span>
                        </a>
                    </li>

                    <?php if (!empty($_SESSION['idPerfilFinal'])): ?>
                        <div class="sidebar-section-content" id="section-cadastros">
                            <li class="sidebar-item">
                                <a href="licitacaoForm.php"
                                    class="sidebar-link <?= $paginaAtual === 'licitacaoForm' ? 'active' : '' ?>"
                                    data-title="Criar Licitação">
                                    <ion-icon name="create-outline"></ion-icon>
                                    <span class="sidebar-link-text">Criar Licitação</span>
                                </a>
                            </li>
                        </div>
                    <?php endif; ?>


                </ul>
            </div>
        </div>

        <?php
        $menusPrincipais = obterMenusPrincipais($pdoCAT);
        if (!empty($menusPrincipais)):
            ?>
            <div class="sidebar-divider"></div>
            <div class="sidebar-section">
                <div class="sidebar-section-title" onclick="toggleSection(this)" data-section="navegacao">
                    <span class="section-icon">
                        <ion-icon name="compass-outline"></ion-icon>
                        <span>Navegação</span>
                    </span>
                    <ion-icon name="chevron-down-outline" class="toggle-icon"></ion-icon>
                </div>
                <div class="sidebar-section-content" id="section-navegacao">
                    <ul class="sidebar-nav">
                        <?php foreach ($menusPrincipais as $menu):
                            $submenus = obterSubMenus($pdoCAT, $menu['ID_MENU']);
                            $hasSubmenus = !empty($submenus);
                            ?>
                            <li class="sidebar-item <?= $hasSubmenus ? 'has-submenu' : '' ?>">
                                <?php if ($hasSubmenus): ?>
                                    <a href="javascript:void(0)" class="sidebar-link" onclick="toggleSubmenu(this)"
                                        data-title="<?= htmlspecialchars($menu['NM_MENU']) ?>"
                                        title="<?= htmlspecialchars($menu['NM_MENU']) ?>">
                                        <ion-icon name="folder-outline"></ion-icon>
                                        <span class="sidebar-link-text"
                                            data-fullname="<?= htmlspecialchars($menu['NM_MENU']) ?>"><?= htmlspecialchars($menu['NM_MENU']) ?></span>
                                        <ion-icon name="chevron-forward-outline" class="arrow-icon"></ion-icon>
                                    </a>
                                    <ul class="sidebar-submenu">
                                        <?php foreach ($submenus as $submenu):
                                            $itens = obterItensMenu($pdoCAT, $submenu['ID_SUBMENU']);
                                            $hasItens = !empty($itens);
                                            ?>
                                            <li class="sidebar-item <?= $hasItens ? 'has-submenu' : '' ?>">
                                                <?php if ($hasItens): ?>
                                                    <a href="javascript:void(0)" class="sidebar-link" onclick="toggleSubmenu(this)"
                                                        data-title="<?= htmlspecialchars($submenu['NM_SUBMENU']) ?>"
                                                        title="<?= htmlspecialchars($submenu['NM_SUBMENU']) ?>">
                                                        <ion-icon name="folder-open-outline"></ion-icon>
                                                        <span class="sidebar-link-text"
                                                            data-fullname="<?= htmlspecialchars($submenu['NM_SUBMENU']) ?>"><?= htmlspecialchars($submenu['NM_SUBMENU']) ?></span>
                                                        <ion-icon name="chevron-forward-outline" class="arrow-icon"></ion-icon>
                                                    </a>
                                                    <ul class="sidebar-submenu">
                                                        <?php foreach ($itens as $item): ?>
                                                            <li class="sidebar-item">
                                                                <a href="<?= htmlspecialchars($item['LINK_ITEMMENU']) ?>" class="sidebar-link"
                                                                    target="_blank" data-title="<?= htmlspecialchars($item['NM_ITEMMENU']) ?>"
                                                                    title="<?= htmlspecialchars($item['NM_ITEMMENU']) ?>">
                                                                    <ion-icon name="link-outline"></ion-icon>
                                                                    <span class="sidebar-link-text"
                                                                        data-fullname="<?= htmlspecialchars($item['NM_ITEMMENU']) ?>"><?= htmlspecialchars($item['NM_ITEMMENU']) ?></span>
                                                                    <span class="sidebar-badge external">Ext</span>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php else: ?>
                                                    <a href="<?= htmlspecialchars($submenu['LINK_SUBMENU']) ?>" class="sidebar-link"
                                                        target="_blank" data-title="<?= htmlspecialchars($submenu['NM_SUBMENU']) ?>"
                                                        title="<?= htmlspecialchars($submenu['NM_SUBMENU']) ?>">
                                                        <ion-icon name="link-outline"></ion-icon>
                                                        <span class="sidebar-link-text"
                                                            data-fullname="<?= htmlspecialchars($submenu['NM_SUBMENU']) ?>"><?= htmlspecialchars($submenu['NM_SUBMENU']) ?></span>
                                                        <span class="sidebar-badge external">Ext</span>
                                                    </a>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars($menu['LINK_MENU']) ?>" class="sidebar-link" target="_blank"
                                        data-title="<?= htmlspecialchars($menu['NM_MENU']) ?>"
                                        title="<?= htmlspecialchars($menu['NM_MENU']) ?>">
                                        <ion-icon name="link-outline"></ion-icon>
                                        <span class="sidebar-link-text"
                                            data-fullname="<?= htmlspecialchars($menu['NM_MENU']) ?>"><?= htmlspecialchars($menu['NM_MENU']) ?></span>
                                        <span class="sidebar-badge external">Ext</span>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>



        <?php if (isset($_SESSION['login'])): ?>
            <div class="sidebar-divider"></div>
            <div class="sidebar-section">
                <div class="sidebar-section-title" onclick="toggleSection(this)" data-section="config">
                    <span class="section-icon">
                        <ion-icon name="cog-outline"></ion-icon>
                        <span>Configurações</span>
                    </span>
                    <ion-icon name="chevron-down-outline" class="toggle-icon"></ion-icon>
                </div>
                <div class="sidebar-section-content" id="section-config">
                    <ul class="sidebar-nav">
                        <?php if (strpos($login, '@') !== false): ?>
                            <li class="sidebar-item">
                                <a href="trocaSenhaUsuario.php"
                                    class="sidebar-link <?= $paginaAtual === 'trocaSenhaUsuario' ? 'active' : '' ?>"
                                    data-title="Trocar Senha">
                                    <ion-icon name="key-outline"></ion-icon>
                                    <span class="sidebar-link-text">Trocar Senha</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="sidebar-item">
                            <a href="consultarAtualizacao.php"
                                class="sidebar-link <?= $paginaAtual === 'consultarAtualizacao' ? 'active' : '' ?>"
                                data-title="Envio de E-mail">
                                <ion-icon name="mail-outline"></ion-icon>
                                <span class="sidebar-link-text">Envio de E-mail</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="javascript:void(0)" class="sidebar-link" onclick="openModalContato()"
                                data-title="Contatos">
                                <ion-icon name="chatbubble-ellipses-outline"></ion-icon>
                                <span class="sidebar-link-text">Contatos</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
            <div class="sidebar-divider"></div>
            <div class="sidebar-section">
                <div class="sidebar-section-title" onclick="toggleSection(this)" data-section="admin">
                    <span class="section-icon">
                        <ion-icon name="settings-outline"></ion-icon>
                        <span>Administração</span>
                    </span>
                    <ion-icon name="chevron-down-outline" class="toggle-icon"></ion-icon>
                </div>
                <div class="sidebar-section-content" id="section-admin">
                    <ul class="sidebar-nav">
                        <li class="sidebar-item">
                            <a href="administracao.php"
                                class="sidebar-link <?= $paginaAtual === 'administracao' ? 'active' : '' ?>"
                                data-title="Cadastros">
                                <ion-icon name="settings-outline"></ion-icon>
                                <span class="sidebar-link-text">Cadastros</span>
                                <span class="sidebar-badge admin">Adm</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="cadAnexos.php" class="sidebar-link <?= $paginaAtual === 'cadAnexos' ? 'active' : '' ?>"
                                data-title="Anexos">
                                <ion-icon name="attach-outline"></ion-icon>
                                <span class="sidebar-link-text">Anexos</span>
                                <span class="sidebar-badge admin">Adm</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </aside>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

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
                    <p class="contact-section-address">Rua Nelcy Lopes Vieira, S/N, Jardim Limoeiro, Serra, ES, CEP:
                        29164-018</p>
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
                    <p class="contact-section-title">Informações sobre pregões, dispensas eletrônicas e cadastro de
                        fornecedores</p>
                    <p class="contact-section-subtitle">Divisão de Compras e Suprimentos (A-DCS)</p>
                    <p class="contact-section-address">Rua Nelcy Lopes Vieira, S/N, Jardim Limoeiro, Serra, ES, CEP:
                        29164-018</p>
                    <p style="font-weight: 600; color: #0f172a; margin: 12px 0 8px 0; font-size: 13px;">Pregoeiros</p>
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

    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('modernSidebar');
            const body = document.body;
            const overlay = document.getElementById('sidebarOverlay');

            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
                return;
            }

            sidebar.classList.toggle('collapsed');
            body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }

        function closeMobileSidebar() {
            document.getElementById('modernSidebar').classList.remove('mobile-open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }

        // Toggle Section
        function toggleSection(element) {
            const sidebar = document.getElementById('modernSidebar');
            if (sidebar.classList.contains('collapsed') && window.innerWidth > 768) return;

            const sectionId = element.getAttribute('data-section');
            const content = document.getElementById('section-' + sectionId);

            element.classList.toggle('collapsed');
            content.classList.toggle('collapsed');

            const states = JSON.parse(localStorage.getItem('sidebarSections') || '{}');
            states[sectionId] = content.classList.contains('collapsed');
            localStorage.setItem('sidebarSections', JSON.stringify(states));
        }

        // Toggle Submenu
        function toggleSubmenu(element) {
            element.closest('.sidebar-item').classList.toggle('open');
        }

        // Restore States
        document.addEventListener('DOMContentLoaded', function () {
            if (window.innerWidth > 768) {
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    document.getElementById('modernSidebar').classList.add('collapsed');
                    document.body.classList.add('sidebar-collapsed');
                }
            }

            const sectionStates = JSON.parse(localStorage.getItem('sidebarSections') || '{}');
            for (const [section, isCollapsed] of Object.entries(sectionStates)) {
                if (isCollapsed) {
                    const title = document.querySelector(`[data-section="${section}"]`);
                    const content = document.getElementById('section-' + section);
                    if (title && content) {
                        title.classList.add('collapsed');
                        content.classList.add('collapsed');
                    }
                }
            }
        });

        // Handle Resize
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                document.getElementById('modernSidebar').classList.remove('mobile-open');
                document.getElementById('sidebarOverlay').classList.remove('active');
            }
        });

        // Toast System
        function showToast(message, type = 'info', duration = 5000) {
            const container = document.getElementById('toastContainer');
            const icons = { sucesso: 'checkmark-circle', erro: 'close-circle', alerta: 'warning', info: 'information-circle' };
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
            <div class="toast-icon"><ion-icon name="${icons[type] || icons.info}"></ion-icon></div>
            <div class="toast-content"><p class="toast-message">${message}</p></div>
            <button class="toast-close" onclick="closeToast(this)"><ion-icon name="close"></ion-icon></button>
        `;
            container.appendChild(toast);
            if (duration > 0) setTimeout(() => { if (toast.parentNode) closeToast(toast.querySelector('.toast-close')); }, duration);
        }

        function closeToast(button) {
            const toast = button.closest('.toast');
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 300);
        }

        // Modal Contato
        function openModalContato() { document.getElementById('modalContato').classList.add('active'); }
        function closeModalContato() { document.getElementById('modalContato').classList.remove('active'); }

        document.getElementById('modalContato').addEventListener('click', function (e) { if (e.target === this) closeModalContato(); });
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => { if (window.innerWidth <= 768 && !link.getAttribute('onclick')) closeMobileSidebar(); });
        });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeModalContato(); if (window.innerWidth <= 768) closeMobileSidebar(); } });
    </script>

    <?php if (!empty($msgSistema)): ?>
        <script>document.addEventListener('DOMContentLoaded', function () { showToast(<?= json_encode($msgSistema) ?>, 'info'); });</script>
    <?php endif; ?>