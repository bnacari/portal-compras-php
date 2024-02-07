<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

?>

<!-- FORMULÁRIOS DE CADASTRO -->
<div class="row container">
    <fieldset class="formulario col s12">
        <form class="col s12 formulario">

            <h5 class="light center">Administrar Anexos</h5>
            <input type="hidden" id="idLicitacao" value="anexos" />

            <div class="input-field col s12">
                <div id="drop-zone" class="dropzone" onclick="handleClick(event)" ondrop="handleDrop(event)" ondragover="handleDragOver(event)">
                    <i class="bi bi-upload"></i> <br>Arraste e solte os arquivos aqui ou clique para selecionar.
                </div>
            </div>
        </form>

    </fieldset>
    <p>&nbsp;</p>

    <fieldset class="formulario col s12">
        <div id="filelist">
            <?php
            $directory = "uploads/anexos";

            // Array para armazenar os anexos
            $anexos = array();

            // Verifique se o diretório existe
            if (is_dir($directory)) {
                // Liste os arquivos no diretório
                $files = scandir($directory);

                // Exclua . e ..
                $files = array_diff($files, array('.', '..'));

                if (!empty($files)) {
                    echo '</br>';
                    // Crie a estrutura HTML para exibir os arquivos em uma grid
                    echo '<div class="grid">';

                    echo '<table><thead><tr><th><h6><strong>Anexos</strong></h6></th><th>Link (clique para copiar)</th><th>Excluir</th></tr></thead><tbody>';

                    foreach ($files as $file) {
                        $filePath = $directory . '/' . $file;

                        echo '<tr>';
                        echo '<td><a href="' . $filePath . '" download>' . $file . '</a></td>';
                        echo '<td><a href="#" class="copy-link" data-clipboard-text="' . $filePath . '">' . $filePath . '</a></td>';
                        echo '<td><a href="javascript:void(0);" onclick="confirmDelete(\'' . $file . '\', \'' . $directory . '\', \'' . 'anexos' . '\')" style="color:red" title="Excluir Arquivo"><i class="bi bi-x-circle"></i></a></td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </fieldset>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

<script>
    // Inicializa a biblioteca Clipboard.js
    var clipboard = new ClipboardJS('.copy-link');

    clipboard.on('success', function(e) {
        // alert('Caminho copiado: ' + e.text);
        alert('Copiado!');
        e.clearSelection();
    });

    clipboard.on('error', function(e) {
        alert('Erro ao copiar o caminho do arquivo.');
    });

    function confirmDelete(file, directory, idLicitacao) {
        if (confirm('Tem certeza que deseja excluir o arquivo?')) {
            // Use AJAX para excluir o arquivo
            $.ajax({
                url: 'excluir_arquivo.php',
                type: 'GET',
                data: {
                    file: file,
                    directory: directory,
                    idLicitacao: idLicitacao
                },
                success: function(response) {
                    // Se a exclusão for bem-sucedida, recarregue a lista de arquivos
                    $('#filelist').load(window.location.href + ' #filelist');
                },
                error: function() {
                    alert('Erro ao excluir o arquivo.');
                }
            });
        } else {
            // O usuário cancelou a exclusão
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

            // Use AJAX para enviar os arquivos
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Adicione aqui qualquer ação adicional após o upload bem-sucedido
                    updateFileList();
                    // Se a inclusão for bem-sucedida, recarregue a lista de arquivos
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
        // Adicione a chamada para uploadFile se necessário
        document.getElementById('drop-zone').classList.add('dragover');
    }

    function updateFileList() {
        // Atualiza a lista de arquivos
        var filelistElement = document.getElementById('filelist');
        if (filelistElement) {
            filelistElement.innerHTML = ''; // Limpa a lista atual

            // Adicione código para obter e exibir a nova lista de arquivos
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_file_list.php?idLicitacao=' + idLicitacao, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var files = response.files;

                    // Exibe a nova lista de arquivos
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


    // Adicione um evento de dragleave para remover a classe 'dragover' quando o mouse sai da área de drop-zone
    document.getElementById('drop-zone').addEventListener('dragleave', function(event) {
        event.preventDefault();
        document.getElementById('drop-zone').classList.remove('dragover');
    });
</script>