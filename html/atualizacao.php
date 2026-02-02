<?php
/**
 * ============================================
 * ATUALIZACAO.PHP - Notificações de Licitações
 * ============================================
 * 
 * Tela para gerenciamento de notificações de licitações
 * O usuário pode visualizar e cancelar as licitações que acompanha
 * 
 * @author Portal de Compras CESAN
 * @version 2.0 - Layout refatorado
 */

// ============================================
// INCLUDES E INICIALIZAÇÃO
// ============================================
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

// Proteção de acesso - usuário deve estar logado
include('protect.php');
?>

<!-- ============================================
     CSS - Estilos da Página
     ============================================ -->
<style>
    /* ============================================
       Container Principal
       ============================================ */
    .page-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
    }

    /* ============================================
       Page Header Profissional (Padrão Administração)
       ============================================ */
    .page-header-pro {
        background: #ffffff;
        border-radius: 20px;
        padding: 0;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow:
            0 1px 3px rgba(0, 0, 0, 0.04),
            0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .page-header-pro::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg,
                #1e3a5f 0%,
                #3b82f6 40%,
                #60a5fa 60%,
                #2d5a87 100%);
        z-index: 2;
    }

    .header-decoration {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
    }

    .decoration-circle-1 {
        position: absolute;
        width: 300px;
        height: 300px;
        top: -140px;
        right: -40px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.04) 0%, transparent 70%);
        border-radius: 50%;
    }

    .decoration-circle-2 {
        position: absolute;
        width: 200px;
        height: 200px;
        bottom: -100px;
        left: 5%;
        background: radial-gradient(circle, rgba(30, 58, 95, 0.03) 0%, transparent 70%);
        border-radius: 50%;
    }

    .header-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 32px 0 32px;
        position: relative;
        z-index: 1;
    }

    .header-breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #94a3b8;
    }

    .header-breadcrumb a {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .header-breadcrumb a:hover {
        color: #3b82f6;
    }

    .header-breadcrumb a ion-icon {
        font-size: 14px;
    }

    .breadcrumb-sep {
        font-size: 10px;
        color: #cbd5e1;
    }

    .header-breadcrumb > span {
        color: #64748b;
        font-weight: 500;
    }

    .header-date {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 400;
        letter-spacing: 0.02em;
    }

    .header-main-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px 32px 24px 32px;
        position: relative;
        z-index: 1;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .header-icon-box {
        position: relative;
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: #2563eb;
        flex-shrink: 0;
    }

    .icon-box-pulse {
        position: absolute;
        inset: -3px;
        border-radius: 16px;
        border: 2px solid rgba(59, 130, 246, 0.15);
        animation: iconPulse 3s ease-in-out infinite;
    }

    @keyframes iconPulse {
        0%, 100% {
            opacity: 0;
            transform: scale(1);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
    }

    .header-title-group h1 {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .header-subtitle {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        color: #64748b;
        margin: 6px 0 0 0;
    }

    .header-subtitle ion-icon {
        font-size: 16px;
        color: #94a3b8;
    }

    /* ============================================
       Card Principal
       ============================================ */
    .content-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .card-header-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-header-title i {
        font-size: 18px;
        color: #3b82f6;
    }

    .card-header-title h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
    }

    .card-body {
        padding: 24px;
    }

    /* ============================================
       Alerta Informativo
       ============================================ */
    .info-alert {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .info-alert-icon {
        width: 36px;
        height: 36px;
        background: #3b82f6;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-alert-icon i {
        color: #ffffff;
        font-size: 16px;
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

    /* ============================================
       Responsividade
       ============================================ */
    @media (max-width: 768px) {
        .page-container {
            padding: 16px;
        }

        .page-header-pro {
            border-radius: 16px;
        }

        .header-top-row {
            padding: 12px 20px 0 20px;
        }

        .header-main-row {
            padding: 16px 20px 20px 20px;
        }

        .header-date {
            display: none;
        }

        .header-title-group h1 {
            font-size: 18px;
        }

        .card-header {
            padding: 16px 20px;
            flex-direction: column;
            align-items: flex-start;
        }

        .card-body {
            padding: 16px;
        }

        .info-alert {
            padding: 14px 16px;
        }
    }

    @media (max-width: 480px) {
        .page-container {
            padding: 12px;
        }

        .header-top-row {
            padding: 10px 16px 0 16px;
        }

        .header-main-row {
            padding: 14px 16px 16px 16px;
        }

        .header-icon-box {
            width: 44px;
            height: 44px;
            font-size: 20px;
        }

        .header-title-group h1 {
            font-size: 16px;
        }

        .header-subtitle {
            font-size: 12px;
        }

        .info-alert {
            flex-direction: column;
            gap: 12px;
        }

        .info-alert-content p {
            font-size: 13px;
        }
    }
</style>

<!-- ============================================
     ESTRUTURA HTML DA PÁGINA
     ============================================ -->
<div class="page-container">
    
    <!-- ============================================
         Header Profissional - Padrão Administração
         ============================================ -->
    <div class="page-header-pro">
        <div class="header-decoration">
            <div class="decoration-circle-1"></div>
            <div class="decoration-circle-2"></div>
        </div>

        <div class="header-top-row">
            <div class="header-breadcrumb">
                <a href="index.php"><ion-icon name="home-outline"></ion-icon> Início</a>
                <ion-icon name="chevron-forward-outline" class="breadcrumb-sep"></ion-icon>
                <span>Notificações</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <ion-icon name="notifications-outline"></ion-icon>
                    <div class="icon-box-pulse"></div>
                </div>
                <div class="header-title-group">
                    <h1>Notificações de Licitações</h1>
                    <p class="header-subtitle">
                        <ion-icon name="bell-outline"></ion-icon>
                        Gerencie as licitações que você acompanha e receberá atualizações por e-mail
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         Card de Conteúdo Principal
         ============================================ -->
    <div class="content-card">
        <div class="card-header">
            <div class="card-header-title">
                <i class="fas fa-bell"></i>
                <h2>Licitações Acompanhadas</h2>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Alerta Informativo -->
            <div class="info-alert">
                <div class="info-alert-icon">
                    <i class="fas fa-info"></i>
                </div>
                <div class="info-alert-content">
                    <p>
                        Você receberá notificações por e-mail sempre que houver atualizações nas licitações listadas abaixo.
                        Para cancelar o recebimento, clique no botão <strong>×</strong> ao lado da licitação desejada.
                    </p>
                </div>
            </div>

            <!-- ============================================
                 Listagem de Licitações Acompanhadas
                 ============================================ -->
            <?php include_once 'bd/atualizacao/read.php'; ?>
        </div>
    </div>
</div>

<!-- ============================================
     Scripts - Exibe data e hora no header
     ============================================ -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hoje = new Date();
        const opcoes = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        const opcoesHora = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const dataFormatada = hoje.toLocaleDateString('pt-BR', opcoes);
        const horaFormatada = hoje.toLocaleTimeString('pt-BR', opcoesHora);
        const dataHora = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1) + ' - ' + horaFormatada;
        const headerDate = document.getElementById('headerDate');
        if (headerDate) {
            headerDate.textContent = dataHora;
        }
    });
</script>