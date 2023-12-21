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

$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

$querySelect2 = "SELECT L.*, DET.*
                    FROM [PortalCompras].[dbo].[LICITACAO] L
                    LEFT JOIN DETALHE_LICITACAO DET ON DET.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN ANEXO A ON A.ID_LICITACAO = L.ID_LICITACAO
                    WHERE L.ID_LICITACAO = $idLicitacao
                ";

$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idLicitacao = $registros['ID_LICITACAO'];
    $dtLicitacao = $registros['DT_LICITACAO'];
    $tituloLicitacao = $registros['COD_LICITACAO'];
    $codLicitacao = $registros['COD_LICITACAO'];
    $statusLicitacao = $registros['STATUS_LICITACAO'];
    $objLicitacao = $registros['OBJETO_LICITACAO'];
    $respLicitacao = $registros['PREG_RESP_LICITACAO'];
    $dtAberLicitacao = date('Y-m-d', strtotime($registros['DT_ABER_LICITACAO']));
    $dtIniSessLicitacao = date('Y-m-d', strtotime($registros['DT_INI_SESS_LICITACAO']));
    $hrAberLicitacao = date('H:i', strtotime($registros['DT_ABER_LICITACAO']));
    $hrIniSessLicitacao = date('H:i', strtotime($registros['DT_INI_SESS_LICITACAO']));
    $modoLicitacao = $registros['MODO_LICITACAO'];
    $criterioLicitacao = $registros['CRITERIO_LICITACAO'];
    $regimeLicitacao = $registros['REGIME_LICITACAO'];
    $formaLicitacao = $registros['FORMA_LICITACAO'];
    $vlLicitacao = $registros['VL_LICITACAO'];
    $localLicitacao = $registros['LOCAL_ABER_LICITACAO'];
    $identificadorLicitacao = $registros['IDENTIFICADOR_LICITACAO'];
    $obsLicitacao = $registros['OBS_LICITACAO'];
endwhile;

$querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[CRITERIO_LICITACAO] WHERE ID_CRITERIO = $criterioLicitacao";
$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idCriterio = $registros['ID_CRITERIO'];
    $nmCriterio = $registros['NM_CRITERIO'];
    $dtExcCriterio = $registros['DT_EXC_CRITERIO'];
endwhile;

$querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[FORMA] WHERE ID_FORMA = $formaLicitacao";
$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idForma = $registros['ID_FORMA'];
    $nmForma = $registros['NM_FORMA'];
    $dtExcForma = $registros['DT_EXC_FORMA'];
endwhile;
/////////////////////////////////////////////////////////////////////////

?>

<!-- FORMULÁRIOS DE CADASTRO -->
<div class="row container">
    <!-- <form action="bd/licitacao/update.php" method="post" class="col s12 formulario"> -->
    <form action="bd/licitacao/update.php" method="post" class="col s12 formulario" enctype="multipart/form-data">

        <fieldset class="formulario col s12">
            <h5 class="light center">Editar Licitação <?php echo $codLicitacao ?></h5>
        </fieldset>

        <input type="text" name="idLicitacao" id="idLicitacao" value="<?php echo $idLicitacao ?>" style="display:none" readonly required>

        <div id="idLicitacao" data-id="<?php echo $idLicitacao; ?>"></div>

        <p>&nbsp;</p>
        <fieldset class="formulario" style="padding:15px; border-color:#eee; border-radius:10px">
            <!-- <h6><strong>Local a Visitar</strong></h6> -->
            <div class="input-field col s4">
                <input type="text" name="codLicitacao" id="codLicitacao" value="<?php echo $codLicitacao ?>">
                <label>Código</label>
            </div>

            <div class="input-field col s4">
                <select name="statusLicitacao" id="statusLicitacao" required>
                    <option value='Em Andamento' <?php echo ($statusLicitacao === 'Em Andamento') ? 'selected' : ''; ?>>Em Andamento</option>
                    <option value='Encerrado' <?php echo ($statusLicitacao === 'Encerrado') ? 'selected' : ''; ?>>Encerrada</option>
                    <option value='Suspenso' <?php echo ($statusLicitacao === 'Suspenso') ? 'selected' : ''; ?>>Suspensa</option>
                    <option value='Rascunho' <?php echo ($statusLicitacao === 'Rascunho') ? 'selected' : ''; ?>>Rascunho</option>
                </select>
                <label>Status</label>
            </div>

            <div class="input-field col s4">
                <input type="text" name="respLicitacao" id="respLicitacao" value="<?php echo $respLicitacao ?>" required>
                <label>Responsável</label>
            </div>

            <div class="input-field col s12">
                <textarea type="text" name="objLicitacao" id="objLicitacao" required><?php echo $objLicitacao ?> </textarea>
                <label>Objeto</label>
            </div>
            <div class="input-field col s2">
                <input type="date" name="dtAberLicitacao" id="dtAberLicitacao" required value="<?php echo $dtAberLicitacao ?>">
                <label>Data de Abertura</label>
            </div>
            <div class="input-field col s2">
                <input type="time" name="hrAberLicitacao" id="hrAberLicitacao" required value="<?php echo $hrAberLicitacao ?>">
                <label>Horário de Abertura</label>
            </div>

            <div class="input-field col s2">
                <input type="date" name="dtIniSessLicitacao" id="dtIniSessLicitacao" required value="<?php echo $dtIniSessLicitacao ?>">
                <label>Início da Sessão de Disputa de Preços</label>
            </div>
            <div class="input-field col s2">
                <input type="time" name="hrIniSessLicitacao" id="hrIniSessLicitacao" required value="<?php echo $hrIniSessLicitacao ?>">
                <label>Horário</label>
            </div>

            <div class="input-field col s4">
                <select name="modoLicitacao" id="modoLicitacao" required>
                    <option value='Aberta' <?php echo ($modoLicitacao === 'Aberta') ? 'selected' : ''; ?>>Aberta</option>
                    <option value='Fechada' <?php echo ($modoLicitacao === 'Fechada') ? 'selected' : ''; ?>>Fechada</option>
                </select>
                <label>Modo de Disputa</label>
            </div>

            <div class="input-field col s4">
                <select name="criterioLicitacao" id="criterioLicitacao" required>
                    <option value='' disabled>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE DT_EXC_CRITERIO IS NULL";
                    $querySelect = $pdoCAT->query($querySelect2);

                    echo "<option value='" . $idCriterio . "' selected>" . $nmCriterio . "</option>";
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        // Verifica se o ID é diferente do ID já selecionado
                        if ($registros["ID_CRITERIO"] != $idCriterio) {
                            echo "<option value='" . $registros["ID_CRITERIO"] . "'>" . $registros["NM_CRITERIO"] . "</option>";
                        }
                    endwhile;
                    ?>
                </select>
                <label>Critério de Julgamento</label>
            </div>

            <div class="input-field col s4">
                <input type="text" name="regimeLicitacao" id="regimeLicitacao" required value="<?php echo $regimeLicitacao ?>">
                <label>Regime de Execução</label>
            </div>
            <div class="input-field col s4">
                <select name="formaLicitacao" id="formaLicitacao" required>
                    <option value='' disabled>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] WHERE DT_EXC_FORMA IS NULL";
                    $querySelect = $pdoCAT->query($querySelect2);

                    echo "<option value='" . $idForma . "' selected>" . $nmForma . "</option>";
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        // Verifica se o ID é diferente do ID já selecionado
                        if ($registros["ID_FORMA"] != $idForma) {
                            echo "<option value='" . $registros["ID_FORMA"] . "'>" . $registros["NM_FORMA"] . "</option>";
                        }
                    endwhile;
                    ?>
                </select>
                <label>Forma</label>
            </div>
            <div class="input-field col s4">
                <input type="text" name="vlLicitacao" id="vlLicitacao" required value="<?php echo $vlLicitacao ?>">
                <label>Valor Estimado</label>
            </div>

            <div class="input-field col s4">
                <input type="text" name="identificadorLicitacao" id="identificadorLicitacao" required value="<?php echo $identificadorLicitacao ?>">
                <label>Identificador</label>
            </div>

            <div class="input-field col s4">
                <input type="text" name="localLicitacao" id="localLicitacao" required value="<?php echo $localLicitacao ?>">
                <label>Local de Abertura</label>
            </div>
            <div class="input-field col s12">
                <textarea type="text" name="obsLicitacao" id="obsLicitacao"><?php echo $obsLicitacao ?> </textarea>
                <label>Observação</label>
            </div>
        </fieldset>

        <p>&nbsp;</p>

        <!-- ============================================================================================== -->

        <fieldset class="formulario">

            <div class="input-field col s12">
                <div id="drop-zone" class="dropzone" onclick="handleClick(event)" ondrop="handleDrop(event)" ondragover="handleDragOver(event)">
                    Arraste e solte os arquivos aqui ou clique para selecionar.
                </div>
            </div>

            <div id="filelist">
                <?php
                $directory = "uploads" . '/' . $idLicitacao;

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

                        echo '<table><thead><tr><th><h6><strong>Anexos</strong></h6></th><th>Excluir</th></tr></thead><tbody>';

                        foreach ($files as $file) {
                            echo '<tr>';
                            echo '<td><a href="' . $directory . '/' . $file . '" download>' . $file . '</a></td>';
                            echo '<td><a href="javascript:void(0);" onclick="confirmDelete(\'' . $file . '\', \'' . $directory . '\')" style="color:red" title="Excluir Arquivo"><i class="material-icons">remove</i></a></td>';
                            echo '</tr>';
                        }

                        echo '</tbody></table>';
                        echo '</div>';
                    }
                }

                // TRECHO PARA LICITAÇÕES 13.303
                if ($idLicitacao > 2000) {
                    $queryAnexo = "WITH RankedAnexos AS (
                SELECT
                    ID_LICITACAO,
                    NM_ANEXO,
                    LINK_ANEXO,
                    ROW_NUMBER() OVER (PARTITION BY ID_LICITACAO, CASE WHEN NM_ANEXO LIKE '%_descricao' THEN 1 ELSE 2 END ORDER BY NM_ANEXO) AS rn
                FROM ANEXO
                WHERE ID_LICITACAO = $idLicitacao
            )
            SELECT
                ID_LICITACAO,
                MAX(CASE WHEN NM_ANEXO like '%_descricao' THEN LINK_ANEXO END) AS NM_ANEXO,
                MAX(CASE WHEN NM_ANEXO like '%_arquivo' THEN LINK_ANEXO END) AS LINK_ANEXO
            FROM RankedAnexos
            GROUP BY ID_LICITACAO, rn;";

                    // TRECHO PARA LICITAÇÕES TACLACODE
                } else {
                    $queryAnexo = "SELECT ID_LICITACAO, NM_ANEXO, LINK_ANEXO FROM ANEXO WHERE ID_LICITACAO = $idLicitacao";
                }

                $queryAnexo2 = $pdoCAT->query($queryAnexo);

                // Obtenha anexos do banco de dados
                while ($registros = $queryAnexo2->fetch(PDO::FETCH_ASSOC)) {
                    $anexos[] = array(
                        'nmAnexo' => $registros['NM_ANEXO'],
                        'linkAnexo' => $registros['LINK_ANEXO'],
                    );
                }

                // Exiba os anexos
                if (!empty($anexos)) {
                    echo '<div class="grid">';
                    echo '<table><thead><tr><th><h6><strong>Anexos</strong></h6></th><th>Excluir</th></tr></thead><tbody>';

                    foreach ($anexos as $anexo) {
                        echo '<tr>';
                        echo '<td><a href="' . $anexo['linkAnexo'] . '" download>' . $anexo['nmAnexo'] . '</a></td>';
                        echo '<td><a href="javascript:void(0);" onclick="confirmDelete(\'' . $anexo['nmAnexo'] . '\', \'' . $anexo['linkAnexo'] . '\')" style="color:red" title="Excluir Arquivo"><i class="material-icons">remove</i></a></td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>';
                    echo '</div>';
                }
                ?>
            </div>
        </fieldset>


        <p>&nbsp;</p>

        <div class="input-field col s2">
            <button type="submit" class="btn blue">Salvar</button>
        </div>
    </form>
</div>

<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->

<script>
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