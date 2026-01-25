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
       Header da Página - Gradiente Padrão
       ============================================ */
    .page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 24px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        position: relative;
        z-index: 1;
    }

    .page-header-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .page-header-icon {
        width: 52px;
        height: 52px;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .page-header h1 {
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 4px 0;
        color: white;
    }

    .page-header-subtitle {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .page-header-subtitle p {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.7);
        margin: 0;
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

        .page-header {
            padding: 20px;
            border-radius: 12px;
        }

        .page-header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-header h1 {
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

        .page-header {
            padding: 16px;
        }

        .page-header-icon {
            width: 44px;
            height: 44px;
            font-size: 20px;
        }

        .page-header h1 {
            font-size: 16px;
        }

        .page-header-subtitle p {
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
         Header da Página
         ============================================ -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-header-info">
                <div class="page-header-icon">
                    <ion-icon name="notifications-outline"></ion-icon>
                </div>
                <div>
                    <h1>Notificações de Licitações</h1>
                    <div class="page-header-subtitle">
                        <p>Gerencie as licitações que você acompanha e receberá atualizações por e-mail</p>
                    </div>
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