<?php
/**
 * ============================================================================
 * ANEXOS.PHP - Administração de Anexos do Sistema
 * ============================================================================
 * 
 * Tela de gerenciamento de arquivos anexos genéricos do sistema.
 * Permite upload, visualização, cópia de link e exclusão de arquivos.
 * 
 * Funcionalidades:
 * - Upload de arquivos via drag-and-drop ou seleção
 * - Listagem de arquivos com download
 * - Cópia de caminho do arquivo para área de transferência
 * - Exclusão de arquivos com confirmação
 * 
 * @author Portal de Compras CESAN
 * @version 2.0 - Layout Modernizado (Padrão consultarLicitacao)
 * ============================================================================
 */

// Includes necessários
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

// Proteção de acesso administrativo
include('protectAdmin.php');

// Conta arquivos para exibir no contador
$directory = "uploads/anexos";
$fileCount = 0;
$files = array();
if (is_dir($directory)) {
    $files = scandir($directory);
    $files = array_diff($files, array('.', '..'));
    $fileCount = count($files);
}
?>

<style>
    /* ============================================
       Page Container - Padrão do Sistema
       ============================================ */
    .page-container {
        padding: 24px;
        max-width: 1400px;
        margin: 0 auto;
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

    /* Decorações */
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

    /* Top Row - Breadcrumb + Data */
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

    .header-breadcrumb>span {
        color: #64748b;
        font-weight: 500;
    }

    .header-date {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 400;
        letter-spacing: 0.02em;
    }

    /* Main Row - Ícone + Título */
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
        margin: 0 0 4px 0;
        color: #1e293b;
        letter-spacing: -0.01em;
        line-height: 1.2;
    }

    .header-subtitle {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #94a3b8;
        margin: 0;
        font-weight: 400;
    }

    .header-subtitle ion-icon {
        font-size: 14px;
        color: #cbd5e1;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Badge no Header */
    .header-stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        padding: 12px 20px;
        border-radius: 12px;
        font-size: 13px;
        color: #166534;
    }

    .header-stat-badge ion-icon {
        font-size: 20px;
        color: #22c55e;
    }

    .header-stat-badge strong {
        font-size: 18px;
        font-weight: 700;
        color: #15803d;
    }

    /* ============================================
       Section Card - Padrão do Sistema
       ============================================ */
    .section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        margin-bottom: 24px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .section-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-header ion-icon {
        font-size: 20px;
        color: #93c5fd;
    }

    .section-header h2 {
        color: #ffffff;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .section-content {
        padding: 24px;
    }

    /* ============================================
       Dropzone - Área de Upload
       ============================================ */
    .dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 48px 32px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dropzone:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .dropzone.dragover {
        border-color: #3b82f6;
        background: #dbeafe;
        transform: scale(1.01);
    }

    .dropzone ion-icon {
        font-size: 48px;
        color: #64748b;
        margin-bottom: 16px;
        display: block;
    }

    .dropzone:hover ion-icon {
        color: #3b82f6;
    }

    .dropzone-text {
        color: #475569;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .dropzone-hint {
        color: #94a3b8;
        font-size: 13px;
    }

    /* ============================================
       Info Alert - Alerta Informativo
       ============================================ */
    .info-alert {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .info-alert ion-icon {
        color: #3b82f6;
        font-size: 20px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .info-alert p {
        margin: 0;
        color: #1e40af;
        font-size: 13px;
        line-height: 1.5;
    }

    /* ============================================
       Tabela de Arquivos - Padrão do Sistema
       ============================================ */
    .file-table-wrapper {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .file-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 500px;
    }

    .file-table thead {
        background: #f8fafc;
    }

    .file-table th {
        padding: 14px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }

    .file-table th ion-icon {
        font-size: 14px;
        margin-right: 6px;
        vertical-align: middle;
    }

    .file-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
    }

    .file-table tbody tr {
        transition: background 0.15s ease;
    }

    .file-table tbody tr:hover {
        background: #f8fafc;
    }

    .file-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ============================================
       Links e Botões - Padrão do Sistema
       ============================================ */
    .file-link {
        color: #3b82f6;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .file-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    .file-link ion-icon {
        font-size: 16px;
    }

    /* Botão Copiar */
    .btn-copy {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #eff6ff;
        color: #1e40af;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #bfdbfe;
        text-decoration: none;
        max-width: 280px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .btn-copy:hover {
        background: #dbeafe;
        border-color: #93c5fd;
        text-decoration: none;
    }

    .btn-copy ion-icon {
        flex-shrink: 0;
        font-size: 14px;
    }

    /* Botões de Ação - Padrão do Sistema */
    .btn-acao {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-acao.excluir {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-acao.excluir:hover {
        background: #fecaca;
        transform: scale(1.05);
    }

    .btn-acao ion-icon {
        font-size: 18px;
    }

    /* ============================================
       Estado Vazio
       ============================================ */
    .empty-state {
        text-align: center;
        padding: 48px 20px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px dashed #cbd5e1;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
        margin: 0 0 6px 0;
    }

    .empty-state p {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }

    /* ============================================
       Toast Notification - Padrão do Sistema
       ============================================ */
    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: white;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s ease;
        max-width: 400px;
        border-left: 4px solid #3b82f6;
    }

    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .toast.success {
        border-left-color: #22c55e;
    }

    .toast.success .toast-icon {
        color: #22c55e;
    }

    .toast.error {
        border-left-color: #ef4444;
    }

    .toast.error .toast-icon {
        color: #ef4444;
    }

    .toast.info {
        border-left-color: #3b82f6;
    }

    .toast.info .toast-icon {
        color: #3b82f6;
    }

    .toast-icon {
        font-size: 22px;
        flex-shrink: 0;
    }

    .toast-message {
        font-size: 14px;
        color: #1e293b;
        margin: 0;
    }

    /* ============================================
       Responsividade
       ============================================ */
    @media (max-width: 768px) {
        .page-container {
            padding: 16px;
        }

        /* Header Profissional Responsivo */
        .page-header-pro {
            border-radius: 16px;
        }

        .header-top-row {
            padding: 12px 20px 0 20px;
            flex-direction: column;
            gap: 8px;
            align-items: flex-start;
        }

        .header-date {
            display: none;
        }

        .header-main-row {
            padding: 16px 20px 20px 20px;
            flex-direction: column;
            align-items: flex-start;
        }

        .header-icon-box {
            width: 48px;
            height: 48px;
            font-size: 22px;
        }

        .header-title-group h1 {
            font-size: 20px;
        }

        .header-subtitle {
            font-size: 12px;
        }

        .header-right {
            width: 100%;
        }

        .header-stat-badge {
            width: 100%;
            justify-content: center;
        }

        .section-content {
            padding: 16px;
        }

        .dropzone {
            padding: 32px 20px;
        }

        .dropzone ion-icon {
            font-size: 40px;
        }

        .file-table th,
        .file-table td {
            padding: 12px 14px;
        }

        /* Oculta coluna de link em mobile */
        .file-table thead th:nth-child(2),
        .file-table tbody td:nth-child(2) {
            display: none;
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
            padding: 12px 16px 16px 16px;
        }

        .header-title-group h1 {
            font-size: 18px;
        }

        .section-header {
            padding: 14px 16px;
        }

        .section-header h2 {
            font-size: 14px;
        }

        .section-content {
            padding: 14px;
        }

        .toast {
            left: 12px;
            right: 12px;
            bottom: 12px;
        }
    }
</style>

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
                <span>Anexos</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <ion-icon name="attach-outline"></ion-icon>
                    <div class="icon-box-pulse"></div>
                </div>
                <div class="header-title-group">
                    <h1>Administrar Anexos</h1>
                    <p class="header-subtitle">
                        <ion-icon name="folder-outline"></ion-icon>
                        Gerencie os arquivos anexos disponíveis no sistema
                    </p>
                </div>
            </div>
            <div class="header-right">
                <div class="header-stat-badge">
                    <ion-icon name="document-outline"></ion-icon>
                    <span><strong><?php echo $fileCount; ?></strong> arquivo<?php echo $fileCount != 1 ? 's' : ''; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         Section Card - Upload de Arquivos
         ============================================ -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="cloud-upload-outline"></ion-icon>
            <h2>Upload de Arquivos</h2>
        </div>
        <div class="section-content">
            <!-- Campo oculto para identificação do diretório -->
            <input type="hidden" id="idLicitacao" value="anexos" />

            <!-- Área de Drag and Drop -->
            <div id="drop-zone" 
                 class="dropzone" 
                 onclick="handleClick(event)" 
                 ondrop="handleDrop(event)"
                 ondragover="handleDragOver(event)"
                 ondragleave="handleDragLeave(event)">
                <ion-icon name="cloud-upload-outline"></ion-icon>
                <div class="dropzone-text">Arraste e solte os arquivos aqui ou clique para selecionar</div>
                <div class="dropzone-hint">Todos os tipos de arquivo são aceitos</div>
            </div>
        </div>
    </div>

    <!-- ============================================
         Section Card - Listagem de Arquivos
         ============================================ -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="folder-open-outline"></ion-icon>
            <h2>Arquivos Anexados</h2>
        </div>
        <div class="section-content">
            <!-- Alerta Informativo -->
            <div class="info-alert">
                <ion-icon name="information-circle-outline"></ion-icon>
                <p>
                    Clique no link do arquivo para copiar o caminho. Use esse caminho para referenciar 
                    o arquivo em outros documentos ou licitações.
                </p>
            </div>

            <!-- Listagem de Arquivos -->
            <div id="filelist">
                <?php
                // Verifica se o diretório existe e tem arquivos
                if (is_dir($directory) && !empty($files)) {
                    echo '<div class="file-table-wrapper">';
                    echo '<table class="file-table">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th><ion-icon name="document-outline"></ion-icon> Nome do Arquivo</th>';
                    echo '<th><ion-icon name="link-outline"></ion-icon> Caminho</th>';
                    echo '<th style="text-align: center; width: 80px;"><ion-icon name="settings-outline"></ion-icon> Ações</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    // Itera sobre cada arquivo
                    foreach ($files as $file) {
                        $filePath = $directory . '/' . $file;

                        echo '<tr>';
                        
                        // Coluna: Nome do arquivo com link de download
                        echo '<td>';
                        echo '<a href="' . htmlspecialchars($filePath) . '" download class="file-link">';
                        echo '<ion-icon name="download-outline"></ion-icon>';
                        echo '<span>' . htmlspecialchars($file) . '</span>';
                        echo '</a>';
                        echo '</td>';
                        
                        // Coluna: Caminho para copiar
                        echo '<td>';
                        echo '<a href="#" class="btn-copy copy-link" data-clipboard-text="' . htmlspecialchars($filePath) . '" title="Clique para copiar">';
                        echo '<ion-icon name="copy-outline"></ion-icon>';
                        echo '<span>' . htmlspecialchars($filePath) . '</span>';
                        echo '</a>';
                        echo '</td>';
                        
                        // Coluna: Botão de exclusão
                        echo '<td style="text-align: center;">';
                        echo '<a href="javascript:void(0);" ';
                        echo 'onclick="confirmDelete(\'' . addslashes($file) . '\', \'' . $directory . '\', \'anexos\')" ';
                        echo 'class="btn-acao excluir" title="Excluir Arquivo">';
                        echo '<ion-icon name="trash-outline"></ion-icon>';
                        echo '</a>';
                        echo '</td>';
                        
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                } elseif (is_dir($directory)) {
                    // Estado vazio - nenhum arquivo
                    echo '<div class="empty-state">';
                    echo '<div class="empty-state-icon">📁</div>';
                    echo '<h3>Nenhum arquivo anexado</h3>';
                    echo '<p>Faça upload de arquivos usando a área acima para começar.</p>';
                    echo '</div>';
                } else {
                    // Diretório não encontrado
                    echo '<div class="empty-state">';
                    echo '<div class="empty-state-icon">⚠️</div>';
                    echo '<h3>Diretório não encontrado</h3>';
                    echo '<p>O diretório de anexos não existe ou não está acessível.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Toast de Notificação -->
<div id="toast" class="toast">
    <ion-icon id="toastIcon" class="toast-icon" name="checkmark-circle-outline"></ion-icon>
    <p id="toastMessage" class="toast-message">Mensagem</p>
</div>

<!-- ============================================
     Scripts JavaScript
     ============================================ -->
<!-- Biblioteca Clipboard.js para copiar texto -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>

<script>
// Exibe data atual no header
document.addEventListener('DOMContentLoaded', function() {
    const hoje = new Date();
    const opcoes = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    const dataFormatada = hoje.toLocaleDateString('pt-BR', opcoes);
    const headerDate = document.getElementById('headerDate');
    if (headerDate) {
        headerDate.textContent = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);
    }
});

/**
 * ============================================
 * FUNÇÕES DE NOTIFICAÇÃO (TOAST)
 * ============================================
 */

/**
 * Exibe uma notificação toast
 * @param {string} message - Mensagem a ser exibida
 * @param {string} type - Tipo: 'success', 'error', 'info'
 */
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    // Remove classes anteriores
    toast.classList.remove('success', 'error', 'info', 'show');
    
    // Define ícone baseado no tipo
    const icons = {
        'success': 'checkmark-circle-outline',
        'error': 'alert-circle-outline',
        'info': 'information-circle-outline'
    };
    
    toastIcon.setAttribute('name', icons[type] || icons['info']);
    toastMessage.textContent = message;
    toast.classList.add(type, 'show');
    
    // Remove após 3 segundos
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

/**
 * ============================================
 * FUNÇÕES DE CLIPBOARD (COPIAR LINK)
 * ============================================
 */

// Inicializa a biblioteca Clipboard.js
var clipboard = new ClipboardJS('.copy-link');

// Evento de sucesso ao copiar
clipboard.on('success', function(e) {
    showToast('Caminho copiado para a área de transferência!', 'success');
    e.clearSelection();
});

// Evento de erro ao copiar
clipboard.on('error', function(e) {
    showToast('Erro ao copiar o caminho do arquivo.', 'error');
});

/**
 * ============================================
 * FUNÇÕES DE EXCLUSÃO DE ARQUIVO
 * ============================================
 */

/**
 * Confirma e executa exclusão de arquivo
 * @param {string} file - Nome do arquivo
 * @param {string} directory - Diretório do arquivo
 * @param {string} idLicitacao - ID da licitação (ou 'anexos')
 */
function confirmDelete(file, directory, idLicitacao) {
    if (confirm('Tem certeza que deseja excluir o arquivo "' + file + '"?\n\nEsta ação não pode ser desfeita.')) {
        $.ajax({
            url: 'excluir_arquivo.php',
            type: 'GET',
            data: {
                file: file,
                directory: directory,
                idLicitacao: idLicitacao
            },
            success: function(response) {
                showToast('Arquivo excluído com sucesso!', 'success');
                // Recarrega a página após breve delay
                setTimeout(function() {
                    location.reload();
                }, 500);
            },
            error: function() {
                showToast('Erro ao excluir o arquivo.', 'error');
            }
        });
    }
}

/**
 * ============================================
 * FUNÇÕES DE UPLOAD DE ARQUIVOS
 * ============================================
 */

// Obtém o ID da licitação do campo oculto
var idLicitacao = document.getElementById('idLicitacao').value;

/**
 * Manipula o evento de soltar arquivos (drop)
 * @param {Event} event - Evento de drop
 */
function handleDrop(event) {
    event.preventDefault();
    document.getElementById('drop-zone').classList.remove('dragover');
    
    var files = event.dataTransfer.files;
    handleFiles(files, idLicitacao);
}

/**
 * Manipula o clique na área de upload
 * @param {Event} event - Evento de clique
 */
function handleClick(event) {
    var inputElement = document.createElement("input");
    inputElement.type = "file";
    inputElement.multiple = true;
    inputElement.addEventListener("change", function() {
        handleFiles(this.files, idLicitacao);
    });
    inputElement.click();
}

/**
 * Processa os arquivos selecionados para upload
 * @param {FileList} files - Lista de arquivos
 * @param {string} idLicitacao - ID da licitação
 */
function handleFiles(files, idLicitacao) {
    if (files.length > 0) {
        var formData = new FormData();

        // Adiciona todos os arquivos ao FormData
        for (var i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        formData.append('idLicitacao', idLicitacao);

        // Envia via AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php', true);
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                showToast('Arquivo(s) enviado(s) com sucesso!', 'success');
                // Recarrega a página para atualizar lista
                setTimeout(function() {
                    location.reload();
                }, 500);
            } else {
                showToast('Erro ao enviar os arquivos.', 'error');
            }
        };
        
        xhr.onerror = function() {
            showToast('Erro de conexão ao enviar arquivos.', 'error');
        };
        
        xhr.send(formData);
    } else {
        showToast('Por favor, selecione um ou mais arquivos.', 'info');
    }
}

/**
 * Manipula o evento de arrastar sobre a área (dragover)
 * @param {Event} event - Evento de dragover
 */
function handleDragOver(event) {
    event.preventDefault();
    document.getElementById('drop-zone').classList.add('dragover');
}

/**
 * Manipula o evento de sair da área de arrastar (dragleave)
 * @param {Event} event - Evento de dragleave
 */
function handleDragLeave(event) {
    event.preventDefault();
    document.getElementById('drop-zone').classList.remove('dragover');
}
</script>