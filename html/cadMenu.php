<?php
//cadMenu.php
// session_start();
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

?>

<style>
    .page-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 20px;
        padding: 40px 48px;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
    }

    .page-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-hero-content {
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        z-index: 1;
    }

    .page-hero-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 28px;
    }

    .page-hero-text h1 {
        color: #ffffff;
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px 0;
        letter-spacing: -0.02em;
    }

    .page-hero-text p {
        color: #94a3b8;
        font-size: 16px;
        margin: 0;
    }

    .modern-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px;
    }

    .modern-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 32px;
    }

    .card-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 24px 32px;
        border-bottom: 1px solid #e2e8f0;
    }

    .card-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-header i {
        font-size: 20px;
    }

    .card-body {
        padding: 32px;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.2s ease;
        background-color: #ffffff;
        color: #1e293b;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-submit {
        padding: 14px 32px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #0f172a;
        color: white;
    }

    .btn-submit:hover {
        background: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Table Styles */
    .menus-table {
        width: 100%;
        border-collapse: collapse;
    }

    .menus-table thead {
        background: #f8fafc;
    }

    .menus-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        letter-spacing: 0.5px;
    }

    .menus-table th:nth-child(3),
    .menus-table th:nth-child(4),
    .menus-table th:nth-child(5) {
        text-align: center;
    }

    .menus-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
        font-size: 14px;
    }

    .menus-table tbody tr {
        transition: background 0.2s ease;
    }

    .menus-table tbody tr:hover {
        background: #f8fafc;
    }

    /* Inputs inline na tabela */
    .menus-table input[type="text"] {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        background: #ffffff;
    }

    .menus-table input[type="text"]:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
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
    @media (max-width: 768px) {
        .modern-container {
            padding: 24px 16px;
        }

        .page-hero {
            padding: 28px;
        }

        .page-hero-content {
            flex-direction: column;
            text-align: center;
        }

        .page-hero-text h1 {
            font-size: 24px;
        }

        .card-body {
            padding: 24px 16px;
        }

        .menus-table {
            font-size: 12px;
        }

        .menus-table th,
        .menus-table td {
            padding: 12px 8px;
        }

        .menus-table thead th:nth-child(3) {
            display: none;
        }

        .menus-table tbody td:nth-child(3) {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .modern-container {
            padding: 16px 12px;
        }

        .page-hero {
            padding: 24px 16px;
        }

        .page-hero-text h1 {
            font-size: 20px;
        }

        .page-hero-text p {
            font-size: 14px;
        }

        .card-header {
            padding: 20px 24px;
        }

        .card-header h2 {
            font-size: 18px;
        }

        .card-body {
            padding: 20px 16px;
        }

        .btn-submit {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="modern-container">
    <!-- Hero Section -->
    <div class="page-hero">
        <div class="page-hero-content">
            <div class="page-hero-icon">
                <ion-icon name="list-outline"></ion-icon>
            </div>
            <div class="page-hero-text">
                <h1>Administrar Menus</h1>
                <p>Gerencie os itens de menu do sistema</p>
            </div>
        </div>
    </div>

    <!-- Card de Cadastro -->
    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-plus-circle"></i>
                Cadastrar Novo Menu
            </h2>
        </div>
        <div class="card-body">
            <form action="bd/menus/create.php" method="post" id="formFiltrar">
                <div class="form-group">
                    <label>
                        <i class="fas fa-tag"></i>
                        Nome do Menu *
                    </label>
                    <input type="text" id="nmMenu" name="nmMenu" class="form-control" required autofocus
                        placeholder="Digite o nome do menu">
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-link"></i>
                        Link do Menu
                    </label>
                    <input type="text" id="linkMenu" name="linkMenu" class="form-control"
                        placeholder="Digite o link do menu (ex: pagina.php)">
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-check"></i>
                    <span>Cadastrar Menu</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Card de Listagem -->
    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-list"></i>
                Menus Cadastrados
            </h2>
        </div>
        <div class="card-body">
            <div class="content3">
                <?php include_once 'bd/menus/read.php'; ?>
            </div>
        </div>
    </div>
</div>