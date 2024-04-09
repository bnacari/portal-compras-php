<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

// include_once('protect.php');

foreach ($_SESSION['perfil'] as $perfil) {
    $idPerfil[] = $perfil['idPerfil'];

    if ($perfil['idPerfil'] == 9) {
        $isAdmin = 1;
    }
}

$idPerfilFinal = implode(',', $idPerfil);

$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

$querySelect2 = "SELECT L.*, DET.*, TIPO.SGL_TIPO
                    FROM [PortalCompras].[dbo].[LICITACAO] L
                    LEFT JOIN DETALHE_LICITACAO DET ON DET.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN ANEXO A ON A.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN TIPO_LICITACAO TIPO ON TIPO.ID_TIPO = DET.TIPO_LICITACAO
                    WHERE L.ID_LICITACAO = $idLicitacao
                ";

$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idLicitacao = $registros['ID_LICITACAO'];
    $dtLicitacao = $registros['DT_LICITACAO'];
    $tituloLicitacao = $registros['COD_LICITACAO'];
    $tipoLicitacao = $registros['TIPO_LICITACAO'];
    $codLicitacao = $registros['SGL_TIPO'] . ' ' . $registros['COD_LICITACAO'];
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
    $permitirAtualizacao = $registros['ENVIO_ATUALIZACAO_LICITACAO'];
endwhile;

foreach ($_SESSION['perfil'] as $perfil) {
    if ($perfil['idPerfil'] == $tipoLicitacao || isset($_SESSION['isAdmin'])) {
        $isAdminProtect = 1;
    }
}
if ($isAdminProtect != 1) {
    $_SESSION['msg'] = 'Usuário tentando acessar área restrita!';
    header('Location: index.php');
    exit;
}


if (isset($tipoLicitacao)) {
    $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[TIPO_LICITACAO] WHERE ID_TIPO = $tipoLicitacao";
    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
        $idTipo = $registros['ID_TIPO'];
        $nmTipo = $registros['NM_TIPO'];
        $dtExcTipo = $registros['DT_EXC_TIPO'];
    endwhile;
}
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
    <form action="bd/licitacao/update.php" method="post" class="col s12 formulario" enctype="multipart/form-data" onsubmit="return validarFormulario()">

        <fieldset class="formulario col s12">
            <h5 class="light" style="color: #404040">Editar Licitação <?php echo $nmTipo ?> <?php echo $codLicitacao ?></h5>
        </fieldset>

        <input type="text" name="idLicitacao" id="idLicitacao" value="<?php echo $idLicitacao ?>" style="display:none" readonly required>

        <div id="idLicitacao" data-id="<?php echo $idLicitacao; ?>"></div>

        <p>&nbsp;</p>
        <fieldset class="formulario" style="padding:15px; border-color:#eee; border-radius:10px">

            <!-- <div id="perfilAdmin">
                <p>TESTE USUARIO</p>
            </div>
            <div id="perfilContador">
                <p>TESTE CONTADOR</p>
            </div> -->

            <div class="input-field col s3">
                <select name="tipoLicitacao" id="tipoLicitacao">
                    <option value='' disabled>Selecione uma opção</option>
                    <?php
                    if (isset($isAdmin)) {
                        $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' ORDER BY NM_TIPO";
                        $querySelect = $pdoCAT->query($querySelect2);
                    } else {
                        $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' AND ID_TIPO IN ($idPerfilFinal) ORDER BY NM_TIPO";
                        $querySelect = $pdoCAT->query($querySelect2);
                    }

                    echo "<option value='" . $idTipo . "' selected>" . $nmTipo . "</option>";
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        // Verifica se o ID é diferente do ID já selecionado
                        if ($registros["ID_TIPO"] != $idTipo) {
                            echo "<option value='" . $registros["ID_TIPO"] . "'>" . $registros["NM_TIPO"] . "</option>";
                        }
                    endwhile;
                    ?>
                </select>
                <label>Tipo de Contratação</label>
            </div>

            <div class="input-field col s3" id="perfilAdmin">
                <input type="text" name="codLicitacao" id="codLicitacao" value="<?php echo $codLicitacao ?>">
                <label>Código</label>
            </div>
            <div class="input-field col s3" id="perfilContador">
                <select name="statusLicitacao" id="statusLicitacao" required>
                    <option value='Em Andamento' <?php echo ($statusLicitacao === 'Em Andamento') ? 'selected' : ''; ?>>Em Andamento</option>
                    <option value='Encerrado' <?php echo ($statusLicitacao === 'Encerrado') ? 'selected' : ''; ?>>Encerrada</option>
                    <option value='Suspenso' <?php echo ($statusLicitacao === 'Suspenso') ? 'selected' : ''; ?>>Suspensa</option>
                    <option value='Rascunho' <?php echo ($statusLicitacao === 'Rascunho') ? 'selected' : ''; ?>>Rascunho</option>
                </select>
                <label>Status</label>
            </div>

            <div class="input-field col s3">
                <input type="text" name="respLicitacao" id="respLicitacao" value="<?php echo $respLicitacao ?>" required>
                <label>Responsável</label>
            </div>

            <div class="input-field col s12">
                <textarea type="text" name="objLicitacao" id="objLicitacao" required><?php echo $objLicitacao ?> </textarea>
                <label>Objeto</label>
            </div>

            <?php
            if (date('Y', strtotime($dtAberLicitacao)) == 1969) {
                $dtAberLicitacao = '';
                $hrAberLicitacao = '';
            }
            ?>
            <div class="input-field col s2">
                <input type="date" name="dtAberLicitacao" id="dtAberLicitacao" value="<?php echo $dtAberLicitacao ?>">
                <label>Data de Abertura</label>
            </div>
            <?php
            if (date('Y', strtotime($dtIniSessLicitacao)) == 1969) {
                $dtIniSessLicitacao = '';
                $hrIniSessLicitacao = '';
            }
            ?>
            <div class="input-field col s2">
                <input type="time" name="hrAberLicitacao" id="hrAberLicitacao" value="<?php echo $hrAberLicitacao ?>">
                <label>Horário de Abertura</label>
            </div>

            <div class="input-field col s2">
                <input type="date" name="dtIniSessLicitacao" id="dtIniSessLicitacao" value="<?php echo $dtIniSessLicitacao ?>">
                <label>Início da Sessão de Disputa de Preços</label>
            </div>
            <div class="input-field col s2">
                <input type="time" name="hrIniSessLicitacao" id="hrIniSessLicitacao" value="<?php echo $hrIniSessLicitacao ?>">
                <label>Horário</label>
            </div>

            <div class="input-field col s4">
                <select name="modoLicitacao" id="modoLicitacao">
                    <option value='0' <?php echo ($modoLicitacao === '0') ? 'selected' : '0'; ?>>Selecione uma opção</option>
                    <option value='Aberta' <?php echo ($modoLicitacao === 'Aberta') ? 'selected' : ''; ?>>Aberta</option>
                    <option value='Fechada' <?php echo ($modoLicitacao === 'Fechada') ? 'selected' : ''; ?>>Fechada</option>
                    <option value='Hibrida' <?php echo ($modoLicitacao === 'Hibrida') ? 'selected' : ''; ?>>Híbrida</option>
                </select>
                <label>Modo de Disputa</label>
            </div>

            <div class="input-field col s4">
                <select name="criterioLicitacao" id="criterioLicitacao">
                    <!-- <option value='' disabled>Selecione uma opção</option> -->
                    <?php
                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE DT_EXC_CRITERIO IS NULL";
                    $querySelect = $pdoCAT->query($querySelect2);
                    if ($idCriterio != 0) {
                        echo "<option value='" . $idCriterio . "' selected>" . $nmCriterio . "</option>";
                    } else {
                        echo "<option value='0' selected>Selecione uma opção</option>";
                    }
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
                <input type="text" name="regimeLicitacao" id="regimeLicitacao" value="<?php echo $regimeLicitacao ?>">
                <label>Regime de Execução</label>
            </div>
            <div class="input-field col s4">
                <select name="formaLicitacao" id="formaLicitacao">
                    <!-- <option value='' disabled>Selecione uma opção</option> -->
                    <?php
                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] WHERE DT_EXC_FORMA IS NULL";
                    $querySelect = $pdoCAT->query($querySelect2);

                    if ($idForma != 0) {
                        echo "<option value='" . $idForma . "' selected>" . $nmForma . "</option>";
                    } else {
                        echo "<option value='0' selected>Selecione uma opção</option>";
                    }
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
                <input type="text" name="vlLicitacao" id="vlLicitacao" value="<?php echo $vlLicitacao ?>">
                <label>Valor Estimado</label>
            </div>

            <div class="input-field col s4">
                <input type="text" name="identificadorLicitacao" id="identificadorLicitacao" value="<?php echo $identificadorLicitacao ?>">
                <label>Identificador</label>
            </div>

            <div class="input-field col s4">
                <input type="text" name="localLicitacao" id="localLicitacao" value="<?php echo $localLicitacao ?>">
                <label>Local de Abertura</label>
            </div>
            <div class="input-field col s12">
                <textarea type="text" name="obsLicitacao" id="obsLicitacao"><?php echo $obsLicitacao ?> </textarea>
                <label>Observação</label>
            </div>

            <div class="input-field col s12">
                <input type="checkbox" name="permitirAtualizacao" id="permitirAtualizacao" <?php echo ($permitirAtualizacao == 1) ? 'checked' : ''; ?>>
                <label for="permitirAtualizacao">Permitir que os usuários sejam lembrados para futuras atualizações da licitação?</label>
            </div>

        </fieldset>

        <p>&nbsp;</p>

        <!-- ============================================================================================== -->

        <fieldset class="formulario">

            <div class="input-field col s12">
                <div id="drop-zone" class="dropzone" onclick="handleClick(event)" ondrop="handleDrop(event)" ondragover="handleDragOver(event)">
                    <i class="bi bi-upload"></i> <br>Arraste e solte os arquivos aqui ou clique para selecionar.
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

                    $anexosDiretorio = array();
                    foreach ($files as $file) {
                        $anexosDiretorio[] = array(
                            'nmAnexo' => $file,
                            'linkAnexo' => $directory . '/' . $file,
                            'timestamp' => filemtime($directory . '/' . $file), // Obtém o timestamp do arquivo
                        );
                    }

                    usort($anexosDiretorio, function ($a, $b) {
                        return $b['timestamp'] - $a['timestamp'];
                    });

                    if (!empty($anexosDiretorio)) {
                        echo '</br>';
                        // Crie a estrutura HTML para exibir os arquivos em uma grid
                        echo '<div class="grid">';

                        echo '<table><thead>
                                        <tr>
                                            <th><h6><strong>Lista de Documentos</strong></h6></th>
                                            <th><h6><strong>Data Inclusão</strong></h6></th>
                                            <th><h6><strong>Excluir</strong></h6></th>
                                            <th><h6><strong>Editar</strong></h6></th>

                                        </tr>
                                    </thead>
                                    <tbody>';

                        foreach ($anexosDiretorio as $index => $anexo) {
                            echo '<tr id="row_' . $index . '">';

                            // Exibir o nome do arquivo como um campo de entrada quando o botão de edição é clicado
                            echo '<td class="nmAnexo">';
                            echo '<span>' . $anexo['nmAnexo'] . '</span>';
                            echo '<input type="text" class="edited-name" value="' . $anexo['nmAnexo'] . '" style="display:none;">'; // Campo de entrada oculto
                            echo '</td>';

                            echo '<td>' . date("d/m/y H:i:s", $anexo['timestamp']) . '</td>';
                            echo '<td><a href="javascript:void(0);" onclick="confirmDelete(\'' . $anexo['nmAnexo'] . '\', \'' . $directory . '\', \'' . $idLicitacao . '\')" style="color:red;" title="Excluir Arquivo"><i class="bi bi-x-circle"></i></a></td>';

                            echo '<td><a href="javascript:void(0);" class="edit-button" data-id="' . $index . '" title="Editar"><i class="material-icons">tune</i></a></td>';
                            echo '<td><a href="javascript:void(0);" class="save-button" data-id="' . $index . '" style="color:green;" title="Salvar" hidden><i class="bi bi-check-circle"></i></a></td>';

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
                    DT_EXC_ANEXO, -- Adicionando a coluna DT_EXC_ANEXO
                    ROW_NUMBER() OVER (PARTITION BY ID_LICITACAO, CASE WHEN NM_ANEXO LIKE '%_descricao' THEN 1 ELSE 2 END ORDER BY NM_ANEXO) AS rn
                FROM ANEXO
                WHERE ID_LICITACAO = $idLicitacao
                -- AND DT_EXC_ANEXO IS NULL

            )
            SELECT
                ID_LICITACAO,
                MAX(CASE WHEN NM_ANEXO like '%_descricao' THEN LINK_ANEXO END) AS NM_ANEXO,
                MAX(CASE WHEN NM_ANEXO like '%_arquivo' THEN LINK_ANEXO END) AS LINK_ANEXO,
                MAX(CASE WHEN NM_ANEXO like '%_descricao' THEN DT_EXC_ANEXO END) AS DT_EXC_ANEXO -- Nova coluna DT_EXC_ANEXO

            FROM RankedAnexos
            GROUP BY ID_LICITACAO, rn;";

                    // TRECHO PARA LICITAÇÕES TACLACODE
                } else {
                    $queryAnexo = "SELECT ID_LICITACAO, NM_ANEXO, LINK_ANEXO, DT_EXC_ANEXO FROM ANEXO WHERE ID_LICITACAO = $idLicitacao";
                }

                $queryAnexo2 = $pdoCAT->query($queryAnexo);

                // Obtenha anexos do banco de dados
                while ($registros = $queryAnexo2->fetch(PDO::FETCH_ASSOC)) {
                    $anexos[] = array(
                        'nmAnexo' => $registros['NM_ANEXO'],
                        'linkAnexo' => $registros['LINK_ANEXO'],
                        'dtExcAnexo' => $registros['DT_EXC_ANEXO'],
                    );
                }

                // Exiba os anexos
                if (!empty($anexos)) {
                    echo '<div class="grid">';
                    echo '<table><thead><tr><th><h6><strong>Anexos</strong></h6></th><th>Excluído?</th><th>Excluir</th></tr></thead><tbody>';

                    foreach ($anexos as $anexo) {
                        echo '<tr>';
                        echo '<td><a href="' . $anexo['linkAnexo'] . '" target="_blank">' . $anexo['nmAnexo'] . '</a></td>';
                        echo '<td>' . $anexo['dtExcAnexo'] . '</td>';

                        if (!isset($anexo['dtExcAnexo'])) {
                            echo '<td><a href="javascript:void(0);" onclick="confirmDelete(\'' . $anexo['nmAnexo'] . '\', \'' . $anexo['linkAnexo'] . '\', \'' . $idLicitacao . '\')" style="color:red;" title="Excluir Arquivo 3"><i class="bi bi-x-circle"></i></a></td>';
                        } else {
                            echo '<td><a href="javascript:void(0);" onclick="confirmDelete(\'' . $anexo['nmAnexo'] . '\', \'' . $anexo['linkAnexo'] . '\', \'' . $idLicitacao . '\', \'' . $anexo['dtExcAnexo'] . '\')" title="Restaurar Arquivo"><i class="bi bi-check-lg"></i></a></td>';
                        }
                        echo '</tr>';
                    }

                    echo '</tbody></table>';
                    echo '</div>';
                }
                ?>
            </div>
        </fieldset>

        <p>&nbsp;</p>

        <div class="input-field col s12">
            <button type="submit" class="btn blue">Salvar</button>
        </div>
    </form>
</div>

<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->

<script>
    $(document).ready(function() {
        $('#codLicitacao').mask('000/0000');

        $(document).on('click', '.edit-button', function() {
            var rowId = $(this).data('id');
            var $nmAnexoCell = $('#row_' + rowId + ' .nmAnexo');
            var currentName = $nmAnexoCell.find('span').text(); // Obter o nome original do arquivo

            // Armazenar o nome original do arquivo como um atributo de dados (data attribute) na linha da tabela
            $('#row_' + rowId).data('currentName', currentName);

            // Substituir o texto por um campo de entrada
            $nmAnexoCell.html('<input type="text" class="edited-name" value="' + currentName + '">');

            // Esconder o botão de editar e mostrar o botão de salvar
            $('#row_' + rowId + ' .edit-button').hide();
            $('#row_' + rowId + ' .save-button').show();
        });


        // Ao clicar no botão de salvar
        $(document).on('click', '.save-button', function() {
            var rowId = $(this).data('id');
            var newName = $('#row_' + rowId + ' .edited-name').val(); // Obter o novo nome do campo de entrada
            var directory = '<?php echo $directory; ?>'; // Obtém o diretório do PHP
            var currentName = $('#row_' + rowId).data('currentName'); // Obter o nome original do arquivo

            // Chamar renameFile para renomear o arquivo
            renameFile(rowId, currentName, newName, directory);
        });


    });

    function renameFile(rowId, currentName, newName, directory) {
        if (currentName === '') {
            newName = prompt("Novo nome do arquivo:", currentName); // Prompt para o novo nome
            if (!newName) return; // Se o usuário cancelar, saia da função
        }

        // console.log('Row ID:', rowId);
        // console.log('CurrentName:', currentName);
        // console.log('New Name:', newName);
        // console.log('Directory:', directory);

        // Construa a URL com os parâmetros necessários
        var url = `renameFile.php?rowId=${rowId}&currentName=${currentName}&newName=${newName}&directory=${directory}`;

        // Redirecione para a nova URL
        window.location.href = url;
    }


    function validarFormulario() {
        var tipoLicitacao = document.getElementById('tipoLicitacao').value;

        if (tipoLicitacao === '') {
            alert('Por favor, selecione uma opção para o Tipo de Contratação.');
            return false; // Evita o envio do formulário se a validação falhar
        }

        // Continue com o envio do formulário se a validação passar
        return true;
    }

    // Função para mostrar/ocultar campos com base no perfil do usuário

    function confirmDelete(file, directory, idLicitacao, dtExcAnexo) {
        if (confirm('Tem certeza que deseja excluir o arquivo?')) {
            // Use AJAX para excluir o arquivo
            $.ajax({
                url: 'excluir_arquivo.php',
                type: 'GET',
                data: {
                    file: file,
                    directory: directory,
                    idLicitacao: idLicitacao,
                    dtExcAnexo: dtExcAnexo
                },
                success: function(response) {
                    // alert(dtExcAnexo);
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
                // Verifica se o arquivo é do tipo PDF ou se a extensão é ZIP
                if (files[i].type === 'application/pdf' || files[i].name.endsWith('.zip')) {
                    formData.append('files[]', files[i]);
                } else {
                    alert('O arquivo "' + files[i].name + '" não é um PDF ou ZIP. Por favor, selecione apenas arquivos PDF ou ZIP.');
                    return; // Encerra a função se encontrar um arquivo que não é PDF ou ZIP
                }
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
                            fileListHTML += '<li><a href="' + uploadDir + files[i] + '">' + files[i] + '</a></li>';
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