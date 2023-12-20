<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

if ($_SESSION['admin'] == 5) {
    // Não faça nada ou redirecione para onde for necessário se essas condições forem atendidas.
} else {
    header('Location: index.php');
}

$idLicitacao = 'anexos';

?>

<!-- FORMULÁRIOS DE CADASTRO -->
<div class="row container">
    <form class="col s12 formulario">

        <fieldset class="formulario col s12">
            <h5 class="light center">Adm Anexos</h5>

            <input type="hidden" id="idLicitacao" value="anexos" />

            <div class="input-field col s12">
                <input type="file" id="fileInput" />
            </div>
            <div class="input-field col s2">
                <button type="button" class="btn green" onclick="uploadFile()">Salvar Arquivo</button>
            </div>
            <!-- </form> -->
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
                            echo '<td><a href="javascript:void(0);" onclick="confirmDelete(\'' . $file . '\', \'' . $directory . '\')" style="color:red" title="Excluir Arquivo"><i class="material-icons">remove</i></a></td>';
                            echo '</tr>';
                        }

                        echo '</tbody></table>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </fieldset>
    </form>
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

    function confirmDelete(file, directory) {
        if (confirm('Tem certeza que deseja excluir o arquivo?')) {
            // Use AJAX para excluir o arquivo
            $.ajax({
                url: 'excluir_arquivo.php',
                type: 'GET',
                data: {
                    file: file,
                    directory: directory
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

    function uploadFile() {
        var fileInput = document.getElementById('fileInput');
        var idLicitacao = document.getElementById('idLicitacao').value;

        // Verifica se algum arquivo foi selecionado
        if (fileInput.files.length > 0) {
            var file = fileInput.files[0];
            var formData = new FormData();

            // Adiciona o arquivo ao objeto FormData
            formData.append('file', file);
            formData.append('idLicitacao', idLicitacao);
            // Use AJAX para enviar o arquivo
            $.ajax({
                url: 'upload.php', // Substitua pelo seu script de upload
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Adicione aqui qualquer ação adicional após o upload bem-sucedido
                    $('#filelist').load(window.location.href + ' #filelist');

                },
                error: function() {
                    alert('Erro ao enviar o arquivo.');
                }
            });
        } else {
            alert('Por favor, selecione um arquivo.');
        }
    }
</script>