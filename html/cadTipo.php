<?php

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
    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-col-8 {
        grid-column: span 8;
    }

    .form-col-4 {
        grid-column: span 4;
    }

    .form-col-12 {
        grid-column: span 12;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-group label i {
        font-size: 16px;
    }

    .form-group label .required-star {
        color: #ef4444;
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

        .form-row {
            gap: 16px;
        }

        .form-col-8,
        .form-col-4 {
            grid-column: span 12;
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
                <ion-icon name="pricetag-outline"></ion-icon>
            </div>
            <div class="page-hero-text">
                <h1>Administrar Tipos de Licitação</h1>
                <p>Gerencie os tipos de contratação disponíveis no sistema</p>
            </div>
        </div>
    </div>

    <!-- Card de Cadastro -->
    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-plus-circle"></i>
                Cadastrar Novo Tipo de Licitação
            </h2>
        </div>
        <div class="card-body">
            <form action="bd/tipo/create.php" method="post" id="formFiltrar">
                <div class="form-row">
                    <div class="form-col-8">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-tag"></i>
                                Nome do Tipo <span class="required-star">*</span>
                            </label>
                            <input type="text" id="nmTipo" name="nmTipo" class="form-control" required autofocus
                                placeholder="Digite o nome do tipo (ex: Pregão Eletrônico)">
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-code"></i>
                                Sigla <span class="required-star">*</span>
                            </label>
                            <input type="text" id="sglTipo" name="sglTipo" class="form-control" required
                                placeholder="Ex: PE">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col-12">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check"></i>
                            <span>Cadastrar Tipo</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card de Listagem -->
    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-list"></i>
                Tipos de Contratação Cadastrados
            </h2>
        </div>
        <div class="card-body">
            <div class="content3">
                <?php include_once 'bd/tipo/read.php'; ?>
            </div>
        </div>
    </div>
</div>