<?php
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
        font-size: 48px;
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

    /* Dropzone para upload de arquivos */
    .dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 60px 40px;
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
        transform: scale(1.02);
    }

    .dropzone i {
        font-size: 64px;
        color: #64748b;
        margin-bottom: 20px;
        display: block;
    }

    .dropzone-text {
        color: #475569;
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .dropzone-hint {
        color: #94a3b8;
        font-size: 14px;
    }

    /* Tabela de arquivos */
    .files-table {
        width: 100%;
        border-collapse: collapse;
    }

    .files-table thead {
        background: #f8fafc;
    }

    .files-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        letter-spacing: 0.5px;
    }

    .files-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
        font-size: 14px;
    }

    .files-table tbody tr {
        transition: background 0.2s ease;
    }

    .files-table tbody tr:hover {
        background: #f8fafc;
    }

    .files-table a {
        color: #3b82f6;
        text-decoration: none;
        transition: color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .files-table a:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    .files-table a i {
        font-size: 16px;
    }

    /* Botões de ação */
    .btn-copy {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #eff6ff;
        color: #1e40af;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #bfdbfe;
    }

    .btn-copy:hover {
        background: #dbeafe;
        border-color: #93c5fd;
        text-decoration: none;
    }

    .btn-delete {
        color: #dc2626;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
    }

    .btn-delete:hover {
        color: #ef4444;
        transform: scale(1.1);
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

        .dropzone {
            padding: 40px 24px;
        }

        .files-table {
            font-size: 12px;
        }

        .files-table th,
        .files-table td {
            padding: 12px 8px;
        }

        .files-table thead th:nth-child(2) {
            display: none;
        }

        .files-table tbody td:nth-child(2) {
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

        .dropzone {
            padding: 32px 20px;
        }

        .dropzone i {
            font-size: 48px;
        }

        .dropzone-text {
            font-size: 14px;
        }

        .dropzone-hint {
            font-size: 13px;
        }
    }
</style>

<div class="modern-container">
    <!-- Hero Section -->
    <div class="page-hero">
        <div class="page-hero-content">
            <span class="page-hero-icon">📎</span>
            <div class="page-hero-text">
                <h1>Administrar Anexos</h1>
                <p>Gerencie os arquivos anexos disponíveis no sistema</p>
            </div>
        </div>
    </div>

    <!-- Card de Upload -->
    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-cloud-upload-alt"></i>
                Upload de Arquivos
            </h2>
        </div>
        <div class="card-body">
            <input type="hidden" id="idLicitacao" value="anexos" />
            
            <div id="drop-zone" class="dropzone" onclick="handleClick(event)" ondrop="handleDrop(event)" ondragover="handleDragOver(event)">
                <i class="fas fa-cloud-upload-alt"></i>
                <div class="dropzone-text">Arraste e solte os arquivos aqui ou clique para selecionar</div>
                <div class="dropzone-hint">Todos os tipos de arquivo são aceitos</div>
            </div>
        </div>
    </div>

    <!-- Card de Arquivos -->
    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-folder-open"></i>
                Arquivos Anexados
            </h2>
        </div>
        <div class="card-body">
            <!-- Alerta Informativo -->
            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <div class="info-alert-content">
                    <p>
                        Clique no link do arquivo para copiar o caminho. Use esse caminho para referenciar o arquivo em outros documentos ou licitações.
                    </p>
                </div>
            </div>

            <div id="filelist">
                <?php
                $directory = "uploads/anexos";
                $anexos = array();

                if (is_dir($directory)) {
                    $files = scandir($directory);
                    $files = array_diff($files, array('.', '..'));

                    if (!empty($files)) {
                        echo '<table class="files-table">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th><i class="fas fa-file"></i> Nome do Arquivo</th>';
                        echo '<th><i class="fas fa-link"></i> Caminho do Link</th>';
                        echo '<th style="text-align: center;"><i class="fas fa-trash"></i> Ações</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        foreach ($files as $file) {
                            $filePath = $directory . '/' . $file;

                            echo '<tr>';
                            echo '<td>';
                            echo '<a href="' . $filePath . '" download>';
                            echo '<i class="fas fa-download"></i>';
                            echo htmlspecialchars($file);
                            echo '</a>';
                            echo '</td>';
                            echo '<td>';
                            echo '<a href="#" class="btn-copy copy-link" data-clipboard-text="' . $filePath . '">';
                            echo '<i class="fas fa-copy"></i>';
                            echo htmlspecialchars($filePath);
                            echo '</a>';
                            echo '</td>';
                            echo '<td style="text-align: center;">';
                            echo '<a href="javascript:void(0);" onclick="confirmDelete(\'' . addslashes($file) . '\', \'' . $directory . '\', \'anexos\')" class="btn-delete" title="Excluir Arquivo">';
                            echo '<i class="fas fa-times-circle"></i>';
                            echo '</a>';
                            echo '</td>';
                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<div class="empty-state">';
                        echo '<div class="empty-state-icon">📁</div>';
                        echo '<h3>Nenhum arquivo anexado</h3>';
                        echo '<p>Faça upload de arquivos usando a área acima para começar.</p>';
                        echo '</div>';
                    }
                } else {
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

<script>
    // Inicializa a biblioteca Clipboard.js
    var clipboard = new ClipboardJS('.copy-link');

    clipboard.on('success', function(e) {
        alert('Caminho copiado!');
        e.clearSelection();
    });

    clipboard.on('error', function(e) {
        alert('Erro ao copiar o caminho do arquivo.');
    });

    function confirmDelete(file, directory, idLicitacao) {
        if (confirm('Tem certeza que deseja excluir o arquivo?')) {
            $.ajax({
                url: 'excluir_arquivo.php',
                type: 'GET',
                data: {
                    file: file,
                    directory: directory,
                    idLicitacao: idLicitacao
                },
                success: function(response) {
                    $('#filelist').load(window.location.href + ' #filelist');
                },
                error: function() {
                    alert('Erro ao excluir o arquivo.');
                }
            });
        }
    }

    var idLicitacao = document.getElementById('idLicitacao').value;

    function handleDrop(event) {
        event.preventDefault();
        var files = event.dataTransfer.files;
        handleFiles(files, idLicitacao);
    }

    function handleClick(event) {
        var inputElement = document.createElement("input");
        inputElement.type = "file";
        inputElement.multiple = true;
        inputElement.addEventListener("change", function() {
            handleFiles(this.files, idLicitacao);
        });
        inputElement.click();
    }

    function handleFiles(files, idLicitacao) {
        if (files.length > 0) {
            var formData = new FormData();

            for (var i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }

            formData.append('idLicitacao', idLicitacao);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    updateFileList();
                    $('#filelist').load(window.location.href + ' #filelist');
                } else {
                    alert('Erro ao enviar os arquivos.');
                }
            };
            xhr.send(formData);
        } else {
            alert('Por favor, selecione um ou mais arquivos.');
        }
    }

    function handleDragOver(event) {
        event.preventDefault();
        document.getElementById('drop-zone').classList.add('dragover');
    }

    function updateFileList() {
        var filelistElement = document.getElementById('filelist');
        if (filelistElement) {
            filelistElement.innerHTML = '';

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_file_list.php?idLicitacao=' + idLicitacao, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var files = response.files;

                    if (files.length > 0) {
                        var fileListHTML = '<ul>';
                        for (var i = 0; i < files.length; i++) {
                            fileListHTML += '<li><a href="' + uploadDir + files[i] + '" download>' + files[i] + '</a></li>';
                        }
                        fileListHTML += '</ul>';
                        filelistElement.innerHTML = fileListHTML;
                    } else {
                        filelistElement.innerHTML = 'Nenhum arquivo disponível.';
                    }
                } else {
                    alert('Erro ao obter a lista de arquivos.');
                }
            };
            xhr.send();
        }
    }

    document.getElementById('drop-zone').addEventListener('dragleave', function(event) {
        event.preventDefault();
        document.getElementById('drop-zone').classList.remove('dragover');
    });
</script>