<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

// include('protect.php');

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

    .content-wrapper {
        width: 100%;
    }

    /* Info Alert */
    .info-alert {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .info-alert i {
        color: #3b82f6;
        font-size: 20px;
        margin-top: 2px;
    }

    .info-alert-content {
        flex: 1;
    }

    .info-alert-content p {
        margin: 0;
        color: #1e40af;
        font-size: 14px;
        line-height: 1.6;
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

        .info-alert {
            padding: 14px 16px;
        }
    }

    /* Sobrescrevendo estilos do Materialize se necessário */
    .modern-container * {
        box-sizing: border-box;
    }
</style>

<div class="modern-container">
    <!-- Hero Section -->
    <div class="page-hero">
        <div class="page-hero-content">
            <div class="page-hero-icon">
                <ion-icon name="mail-outline"></ion-icon>
            </div>
            <div class="page-hero-text">
                <h1>Notificações de Licitações</h1>
                <p>Gerencie as licitações que você receberá atualizações por e-mail</p>
            </div>
        </div>
    </div>

    <!-- Card de Conteúdo -->
    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-bell"></i>
                Licitações Acompanhadas
            </h2>
        </div>
        <div class="card-body">
            <!-- Alerta Informativo -->
            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <div class="info-alert-content">
                    <p>
                        Você receberá notificações por e-mail sempre que houver atualizações nas licitações listadas
                        abaixo.
                        Para cancelar o recebimento de notificações, acesse a licitação e desmarque a opção
                        correspondente.
                    </p>
                </div>
            </div>

            <!-- Conteúdo da Listagem -->
            <div class="content-wrapper">
                <?php include_once 'bd/atualizacao/read.php'; ?>
            </div>
        </div>
    </div>
</div>