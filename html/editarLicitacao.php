<?php
/**
 * ============================================
 * Portal de Compras - CESAN
 * Tela de Edição de Licitação
 * 
 * Layout refatorado baseado em consultarLicitacao.php
 * ============================================
 */

// Includes necessários
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

// Verifica perfis do usuário logado
foreach ($_SESSION['perfil'] as $perfil) {
    $idPerfil[] = $perfil['idPerfil'];

    if ($perfil['idPerfil'] == 9) {
        $isAdmin = 1;
    }
}

$idPerfilFinal = implode(',', $idPerfil);

// Obtém ID da licitação
$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

// Query principal - busca dados da licitação
$querySelect2 = "SELECT L.*, DET.*, TIPO.SGL_TIPO
                    FROM [PortalCompras].[dbo].[LICITACAO] L
                    LEFT JOIN DETALHE_LICITACAO DET ON DET.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN ANEXO A ON A.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN TIPO_LICITACAO TIPO ON TIPO.ID_TIPO = DET.TIPO_LICITACAO
                    WHERE L.ID_LICITACAO = $idLicitacao
                ";

$querySelect = $pdoCAT->query($querySelect2);

// Extrai dados da licitação
while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
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

// Verifica permissão de acesso
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

// Busca dados do tipo de licitação
if (isset($tipoLicitacao)) {
    $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[TIPO_LICITACAO] WHERE ID_TIPO = $tipoLicitacao";
    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
        $idTipo = $registros['ID_TIPO'];
        $nmTipo = $registros['NM_TIPO'];
        $dtExcTipo = $registros['DT_EXC_TIPO'];
    endwhile;
}

// Busca dados do critério
$querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[CRITERIO_LICITACAO] WHERE ID_CRITERIO = $criterioLicitacao";
$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
    $idCriterio = $registros['ID_CRITERIO'];
    $nmCriterio = $registros['NM_CRITERIO'];
    $dtExcCriterio = $registros['DT_EXC_CRITERIO'];
endwhile;

// Busca dados da forma
$querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[FORMA] WHERE ID_FORMA = $formaLicitacao";
$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
    $idForma = $registros['ID_FORMA'];
    $nmForma = $registros['NM_FORMA'];
    $dtExcForma = $registros['DT_EXC_FORMA'];
endwhile;
?>

<!-- jQuery e Plugins -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- CSS da página -->
<link rel="stylesheet" href="style/css/editarLicitacao.css" />

<div class="page-container">

    <!-- ============================================
         Header da Página
         ============================================ -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-header-info">
                <div class="page-header-icon">
                    <ion-icon name="create-outline"></ion-icon>
                </div>
                <div>
                    <h1>Editar Licitação <?php echo htmlspecialchars($tituloLicitacao); ?></h1>
                    <p class="page-header-subtitle"><?php echo htmlspecialchars($nmTipo . ' ' . $codLicitacao); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário Principal -->
    <form action="bd/licitacao/update.php" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario()">
        
        <!-- Campo oculto com ID da licitação -->
        <input type="hidden" name="idLicitacao" id="idLicitacao" value="<?php echo $idLicitacao ?>" readonly required>
        <div id="idLicitacaoData" data-id="<?php echo $idLicitacao; ?>"></div>

        <!-- ============================================
             Seção: Informações Básicas
             ============================================ -->
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="information-circle-outline"></ion-icon>
                <h2>Informações Básicas</h2>
            </div>
            <div class="section-content">
                <div class="form-row">
                    <!-- Tipo de Contratação -->
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <ion-icon name="pricetag-outline"></ion-icon>
                                Tipo de Contratação <span class="required-star">*</span>
                            </label>
                            <select name="tipoLicitacao" id="tipoLicitacao" class="form-select select2-tipo" required>
                                <option value='' disabled>Selecione uma opção</option>
                                <?php
                                if ($isAdmin == 1) {
                                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' ORDER BY NM_TIPO";
                                    $querySelect = $pdoCAT->query($querySelect2);
                                } else {
                                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' AND ID_TIPO IN ($idPerfilFinal) ORDER BY NM_TIPO";
                                    $querySelect = $pdoCAT->query($querySelect2);
                                }

                                echo "<option value='" . $idTipo . "' selected>" . htmlspecialchars($nmTipo) . "</option>";
                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                    if ($registros["ID_TIPO"] != $idTipo) {
                                        echo "<option value='" . $registros["ID_TIPO"] . "'>" . htmlspecialchars($registros["NM_TIPO"]) . "</option>";
                                    }
                                endwhile;
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Código -->
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <ion-icon name="barcode-outline"></ion-icon>
                                Código <span class="required-star">*</span>
                            </label>
                            <input type="text" id="codLicitacao" name="codLicitacao" 
                                value="<?php echo htmlspecialchars($tituloLicitacao) ?>" 
                                class="form-control" required>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <ion-icon name="toggle-outline"></ion-icon>
                                Status <span class="required-star">*</span>
                            </label>
                            <select name="statusLicitacao" id="statusLicitacao" class="form-select" required>
                                <option value='Em Andamento' <?php echo ($statusLicitacao === 'Em Andamento') ? 'selected' : ''; ?>>Em Andamento</option>
                                <option value='Encerrado' <?php echo ($statusLicitacao === 'Encerrado') ? 'selected' : ''; ?>>Encerrada</option>
                                <option value='Suspenso' <?php echo ($statusLicitacao === 'Suspenso') ? 'selected' : ''; ?>>Suspensa</option>
                                <option value='Rascunho' <?php echo ($statusLicitacao === 'Rascunho') ? 'selected' : ''; ?>>Rascunho</option>
                            </select>
                        </div>
                    </div>

                    <!-- Responsável -->
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <ion-icon name="person-outline"></ion-icon>
                                Responsável <span class="required-star">*</span>
                            </label>
                            <input type="text" id="respLicitacao" name="respLicitacao"
                                value="<?php echo htmlspecialchars($respLicitacao) ?>" 
                                class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Objeto -->
                <div class="form-row">
                    <div class="form-col-12">
                        <div class="form-group">
                            <label>
                                <ion-icon name="document-text-outline"></ion-icon>
                                Objeto <span class="required-star">*</span>
                            </label>
                            <textarea id="objLicitacao" name="objLicitacao" class="form-textarea" required><?php echo htmlspecialchars($objLicitacao) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Identificador e Valor -->
                <div class="form-row">
                    <div class="form-col-6">
                        <div class="form-group">
                            <label>
                                <ion-icon name="finger-print-outline"></ion-icon>
                                Identificador
                            </label>
                            <input type="text" id="identificadorLicitacao" name="identificadorLicitacao"
                                value="<?php echo htmlspecialchars($identificadorLicitacao) ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-6">
                        <div class="form-group">
                            <label>
                                <ion-icon name="cash-outline"></ion-icon>
                                Valor Estimado
                            </label>
                            <input type="text" id="vlLicitacao" name="vlLicitacao" 
                                value="<?php echo htmlspecialchars($vlLicitacao) ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Local de Abertura -->
                <div class="form-row">
                    <div class="form-col-12">
                        <div class="form-group">
                            <label>
                                <ion-icon name="location-outline"></ion-icon>
                                Local de Abertura
                            </label>
                            <input type="text" id="localLicitacao" name="localLicitacao"
                                value="<?php echo htmlspecialchars($localLicitacao) ?>" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================
             Seção: Datas e Horários
             ============================================ -->
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="calendar-outline"></ion-icon>
                <h2>Datas e Horários</h2>
            </div>
            <div class="section-content">
                <?php
                // Corrige datas inválidas (1969)
                if (strpos($dtAberLicitacao, '1969') !== false) {
                    $dtAberLicitacao = '';
                    $hrAberLicitacao = '';
                }
                if (strpos($dtIniSessLicitacao, '1969') !== false) {
                    $dtIniSessLicitacao = '';
                    $hrIniSessLicitacao = '';
                }
                ?>
                <div class="form-row">
                    <!-- Data de Abertura -->
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <ion-icon name="calendar-number-outline"></ion-icon>
                                Data de Abertura
                            </label>
                            <input type="date" id="dtAberLicitacao" name="dtAberLicitacao"
                                value="<?php echo $dtAberLicitacao ?>" class="form-control">
                        </div>
                    </div>

                    <!-- Horário de Abertura -->
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <ion-icon name="time-outline"></ion-icon>
                                Horário de Abertura
                            </label>
                            <input type="time" id="hrAberLicitacao" name="hrAberLicitacao"
                                value="<?php echo $hrAberLicitacao ?>" class="form-control">
                        </div>
                    </div>

                    <!-- Data da Sessão -->
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <ion-icon name="calendar-number-outline"></ion-icon>
                                Data da Sessão de Disputa
                            </label>
                            <input type="date" id="dtIniSessLicitacao" name="dtIniSessLicitacao"
                                value="<?php echo $dtIniSessLicitacao ?>" class="form-control">
                        </div>
                    </div>

                    <!-- Horário da Sessão -->
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <ion-icon name="time-outline"></ion-icon>
                                Horário da Sessão de Disputa
                            </label>
                            <input type="time" id="hrIniSessLicitacao" name="hrIniSessLicitacao"
                                value="<?php echo $hrIniSessLicitacao ?>" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================
             Seção: Detalhes da Licitação
             ============================================ -->
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="settings-outline"></ion-icon>
                <h2>Detalhes da Licitação</h2>
            </div>
            <div class="section-content">
                <div class="form-row">
                    <!-- Modo de Disputa -->
                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <ion-icon name="swap-horizontal-outline"></ion-icon>
                                Modo de Disputa
                            </label>
                            <select name="modoLicitacao" id="modoLicitacao" class="form-select">
                                <option value='0' <?php echo ($modoLicitacao === '0') ? 'selected' : ''; ?>>Selecione uma opção</option>
                                <option value='Aberta' <?php echo ($modoLicitacao === 'Aberta') ? 'selected' : ''; ?>>Aberta</option>
                                <option value='Fechada' <?php echo ($modoLicitacao === 'Fechada') ? 'selected' : ''; ?>>Fechada</option>
                                <option value='Hibrida' <?php echo ($modoLicitacao === 'Hibrida') ? 'selected' : ''; ?>>Híbrida</option>
                            </select>
                        </div>
                    </div>

                    <!-- Critério de Julgamento -->
                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <ion-icon name="options-outline"></ion-icon>
                                Critério de Julgamento
                            </label>
                            <select name="criterioLicitacao" id="criterioLicitacao" class="form-select select2-criterio">
                                <?php
                                $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE DT_EXC_CRITERIO IS NULL ORDER BY NM_CRITERIO";
                                $querySelect = $pdoCAT->query($querySelect2);

                                if (isset($idCriterio)) {
                                    echo "<option value='" . $idCriterio . "' selected>" . htmlspecialchars($nmCriterio) . "</option>";
                                } else {
                                    echo "<option value='0' selected>Selecione uma opção</option>";
                                }

                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                    if ($registros["ID_CRITERIO"] != $idCriterio) {
                                        echo "<option value='" . $registros["ID_CRITERIO"] . "'>" . htmlspecialchars($registros["NM_CRITERIO"]) . "</option>";
                                    }
                                endwhile;
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Forma -->
                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <ion-icon name="layers-outline"></ion-icon>
                                Forma
                            </label>
                            <select name="formaLicitacao" id="formaLicitacao" class="form-select select2-forma">
                                <?php
                                $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] WHERE DT_EXC_FORMA IS NULL ORDER BY NM_FORMA";
                                $querySelect = $pdoCAT->query($querySelect2);

                                if (isset($idForma)) {
                                    echo "<option value='" . $idForma . "' selected>" . htmlspecialchars($nmForma) . "</option>";
                                } else {
                                    echo "<option value='0' selected>Selecione uma opção</option>";
                                }

                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                    if ($registros["ID_FORMA"] != $idForma) {
                                        echo "<option value='" . $registros["ID_FORMA"] . "'>" . htmlspecialchars($registros["NM_FORMA"]) . "</option>";
                                    }
                                endwhile;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Regime de Execução -->
                <div class="form-row">
                    <div class="form-col-12">
                        <div class="form-group">
                            <label>
                                <ion-icon name="construct-outline"></ion-icon>
                                Regime de Execução
                            </label>
                            <input type="text" id="regimeLicitacao" name="regimeLicitacao"
                                value="<?php echo htmlspecialchars($regimeLicitacao) ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Observação -->
                <div class="form-row">
                    <div class="form-col-12">
                        <div class="form-group">
                            <label>
                                <ion-icon name="chatbubble-outline"></ion-icon>
                                Observação
                            </label>
                            <textarea id="obsLicitacao" name="obsLicitacao" class="form-textarea"><?php echo htmlspecialchars($obsLicitacao) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================
             Seção: Configurações
             ============================================ -->
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="cog-outline"></ion-icon>
                <h2>Configurações</h2>
            </div>
            <div class="section-content">
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="permitirAtualizacao" id="permitirAtualizacao" 
                        <?php echo ($permitirAtualizacao == 1) ? 'checked' : ''; ?>>
                    <label for="permitirAtualizacao">
                        Permitir que os usuários sejam lembrados para futuras atualizações da licitação
                    </label>
                </div>
            </div>
        </div>

        <!-- ============================================
             Seção: Anexos
             ============================================ -->
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="attach-outline"></ion-icon>
                <h2>Gerenciar Anexos</h2>
            </div>
            <div class="section-content">
                <!-- Dropzone para upload -->
                <div class="form-row">
                    <div class="form-col-12">
                        <div id="drop-zone" class="dropzone" onclick="handleClick(event)" 
                            ondrop="handleDrop(event)" ondragover="handleDragOver(event)">
                            <ion-icon name="cloud-upload-outline"></ion-icon>
                            <p>Arraste e solte os arquivos aqui ou clique para selecionar</p>
                            <p>Apenas arquivos PDF ou ZIP</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de arquivos -->
                <div id="filelist">
                    <?php
                    $directory = "uploads" . '/' . $idLicitacao;
                    $anexos = array();

                    /**
                     * Função auxiliar para determinar ícone do arquivo
                     */
                    function getFileIcon($filename) {
                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        $icons = [
                            'pdf' => ['icon' => 'document-text', 'class' => 'pdf'],
                            'doc' => ['icon' => 'document', 'class' => 'doc'],
                            'docx' => ['icon' => 'document', 'class' => 'doc'],
                            'xls' => ['icon' => 'grid', 'class' => 'xls'],
                            'xlsx' => ['icon' => 'grid', 'class' => 'xls'],
                            'zip' => ['icon' => 'archive', 'class' => 'zip'],
                            'rar' => ['icon' => 'archive', 'class' => 'zip'],
                            'jpg' => ['icon' => 'image', 'class' => 'img'],
                            'jpeg' => ['icon' => 'image', 'class' => 'img'],
                            'png' => ['icon' => 'image', 'class' => 'img'],
                        ];
                        return $icons[$ext] ?? ['icon' => 'document', 'class' => 'default'];
                    }

                    // Arquivos do diretório físico
                    if (is_dir($directory)) {
                        $files = scandir($directory);
                        $files = array_diff($files, array('.', '..'));

                        foreach ($files as $file) {
                            $anexos[] = array(
                                'nmAnexo' => $file,
                                'linkAnexo' => $directory . '/' . $file,
                                'timestamp' => filemtime($directory . '/' . $file),
                            );
                        }
                    }

                    // Ordena por timestamp (mais recentes primeiro)
                    usort($anexos, function ($a, $b) {
                        return $b['timestamp'] - $a['timestamp'];
                    });

                    if (!empty($anexos)) {
                        // Header com toggle de visualização
                        echo '<div class="files-section-header">';
                        echo '<div class="files-section-title">';
                        echo '<ion-icon name="folder-open-outline"></ion-icon>';
                        echo '<span>Arquivos do Diretório (' . count($anexos) . ')</span>';
                        echo '</div>';
                        echo '<div class="files-view-toggle">';
                        echo '<button type="button" class="files-view-btn active" data-view="grid" onclick="toggleFilesViewEdit(\'grid\')">';
                        echo '<ion-icon name="grid-outline"></ion-icon>';
                        echo '<span>Cards</span>';
                        echo '</button>';
                        echo '<button type="button" class="files-view-btn" data-view="list" onclick="toggleFilesViewEdit(\'list\')">';
                        echo '<ion-icon name="list-outline"></ion-icon>';
                        echo '<span>Lista</span>';
                        echo '</button>';
                        echo '</div>';
                        echo '</div>';

                        // GRID VIEW
                        echo '<div class="files-grid" id="filesGridEdit">';
                        $index = 0;
                        foreach ($anexos as $anexo) {
                            $fileInfo = getFileIcon($anexo['nmAnexo']);
                            $nomeArquivo = htmlspecialchars($anexo['nmAnexo']);
                            $linkArquivo = htmlspecialchars($anexo['linkAnexo']);

                            echo '<div class="file-card" id="card_row_' . $index . '">';
                            echo '<div class="file-card-icon ' . $fileInfo['class'] . '">';
                            echo '<ion-icon name="' . $fileInfo['icon'] . '-outline"></ion-icon>';
                            echo '</div>';
                            echo '<div class="file-card-info">';
                            echo '<div class="file-card-name nmAnexo">';
                            echo '<a href="' . $linkArquivo . '" target="_blank">' . $nomeArquivo . '</a>';
                            echo '<input type="text" class="edited-name" style="display:none;" value="' . $nomeArquivo . '">';
                            echo '</div>';
                            echo '<div class="file-card-actions">';
                            echo '<button type="button" class="action-btn edit-button" data-id="' . $index . '" title="Editar nome">';
                            echo '<ion-icon name="create-outline"></ion-icon>';
                            echo '</button>';
                            echo '<button type="button" class="action-btn save-button" data-id="' . $index . '" title="Salvar">';
                            echo '<ion-icon name="checkmark-outline"></ion-icon>';
                            echo '</button>';
                            echo '<button type="button" class="action-btn delete-button" data-id="' . $index . '" data-file="' . $nomeArquivo . '" title="Excluir">';
                            echo '<ion-icon name="trash-outline"></ion-icon>';
                            echo '</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            $index++;
                        }
                        echo '</div>';

                        // LIST VIEW
                        echo '<div class="files-list hidden" id="filesListEdit">';
                        echo '<div class="files-table-wrapper">';
                        echo '<table class="files-table">';
                        echo '<thead><tr><th>Arquivo</th><th>Data</th><th style="text-align: center;">Ações</th></tr></thead>';
                        echo '<tbody>';

                        $index = 0;
                        foreach ($anexos as $anexo) {
                            $nomeArquivo = htmlspecialchars($anexo['nmAnexo']);
                            $linkArquivo = htmlspecialchars($anexo['linkAnexo']);
                            $dataArquivo = date("d/m/Y H:i", $anexo['timestamp']);

                            echo '<tr id="row_' . $index . '">';
                            echo '<td class="nmAnexo">';
                            echo '<a href="' . $linkArquivo . '" target="_blank">';
                            echo '<ion-icon name="document-outline"></ion-icon> ' . $nomeArquivo;
                            echo '</a>';
                            echo '<input type="text" class="edited-name" style="display:none;" value="' . $nomeArquivo . '">';
                            echo '</td>';
                            echo '<td>' . $dataArquivo . '</td>';
                            echo '<td style="text-align: center;">';
                            echo '<button type="button" class="action-btn edit-button" data-id="' . $index . '" title="Editar">';
                            echo '<ion-icon name="create-outline"></ion-icon>';
                            echo '</button>';
                            echo '<button type="button" class="action-btn save-button" data-id="' . $index . '" title="Salvar">';
                            echo '<ion-icon name="checkmark-outline"></ion-icon>';
                            echo '</button>';
                            echo '<button type="button" class="action-btn delete-button" data-id="' . $index . '" data-file="' . $nomeArquivo . '" title="Excluir">';
                            echo '<ion-icon name="trash-outline"></ion-icon>';
                            echo '</button>';
                            echo '</td>';
                            echo '</tr>';
                            $index++;
                        }

                        echo '</tbody></table>';
                        echo '</div>';
                        echo '</div>';
                    }

                    // Anexos do banco de dados (para licitações 13.303)
                    $anexosBD = array();
                    if ($idLicitacao > 2000) {
                        $queryAnexo = "WITH RankedAnexos AS (
                            SELECT
                                ID_LICITACAO,
                                NM_ANEXO,
                                LINK_ANEXO,
                                DT_EXC_ANEXO,
                                ROW_NUMBER() OVER (PARTITION BY ID_LICITACAO, CASE WHEN NM_ANEXO LIKE '%_descricao' THEN 1 ELSE 2 END ORDER BY NM_ANEXO) AS rn
                            FROM ANEXO
                            WHERE ID_LICITACAO = $idLicitacao
                        )
                        SELECT
                            ID_LICITACAO,
                            MAX(CASE WHEN NM_ANEXO like '%_descricao' THEN LINK_ANEXO END) AS NM_ANEXO,
                            MAX(CASE WHEN NM_ANEXO like '%_arquivo' THEN LINK_ANEXO END) AS LINK_ANEXO,
                            MAX(CASE WHEN NM_ANEXO like '%_descricao' THEN DT_EXC_ANEXO END) AS DT_EXC_ANEXO
                        FROM RankedAnexos
                        GROUP BY ID_LICITACAO, rn;";
                    } else {
                        $queryAnexo = "SELECT ID_LICITACAO, NM_ANEXO, LINK_ANEXO, DT_EXC_ANEXO FROM ANEXO WHERE ID_LICITACAO = $idLicitacao";
                    }

                    $queryAnexo2 = $pdoCAT->query($queryAnexo);

                    while ($registros = $queryAnexo2->fetch(PDO::FETCH_ASSOC)) {
                        $anexosBD[] = array(
                            'nmAnexo' => $registros['NM_ANEXO'],
                            'linkAnexo' => $registros['LINK_ANEXO'],
                            'dtExcAnexo' => $registros['DT_EXC_ANEXO'],
                        );
                    }

                    if (!empty($anexosBD)) {
                        echo '<div class="files-section-header" style="margin-top: 32px;">';
                        echo '<div class="files-section-title">';
                        echo '<ion-icon name="server-outline"></ion-icon>';
                        echo '<span>Anexos do Banco de Dados (' . count($anexosBD) . ')</span>';
                        echo '</div>';
                        echo '</div>';

                        echo '<div class="files-table-wrapper">';
                        echo '<table class="files-table">';
                        echo '<thead><tr><th>Arquivo</th><th>Status</th><th style="text-align: center;">Ação</th></tr></thead>';
                        echo '<tbody>';

                        foreach ($anexosBD as $anexo) {
                            $nomeArquivo = htmlspecialchars($anexo['nmAnexo']);
                            $linkArquivo = htmlspecialchars($anexo['linkAnexo']);
                            $isExcluido = !empty($anexo['dtExcAnexo']);

                            echo '<tr>';
                            echo '<td>';
                            echo '<a href="' . $linkArquivo . '" target="_blank">';
                            echo '<ion-icon name="document-outline"></ion-icon> ' . $nomeArquivo;
                            echo '</a>';
                            echo '</td>';
                            echo '<td>';
                            if ($isExcluido) {
                                echo '<span style="color: #dc2626;">Excluído</span>';
                            } else {
                                echo '<span style="color: #16a34a;">Ativo</span>';
                            }
                            echo '</td>';
                            echo '<td style="text-align: center;">';
                            echo '<button type="button" class="action-btn delete-button" data-file="' . $nomeArquivo . '" title="Excluir">';
                            echo '<ion-icon name="trash-outline"></ion-icon>';
                            echo '</button>';
                            echo '</td>';
                            echo '</tr>';
                        }

                        echo '</tbody></table>';
                        echo '</div>';
                    }

                    // Estado vazio
                    if (empty($anexos) && empty($anexosBD)) {
                        echo '<div class="empty-state">';
                        echo '<ion-icon name="folder-open-outline"></ion-icon>';
                        echo '<p>Nenhum arquivo anexado</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- ============================================
             Botões de Ação
             ============================================ -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <ion-icon name="save-outline"></ion-icon>
                Salvar Alterações
            </button>
            <a href="viewLicitacao.php?idLicitacao=<?php echo $idLicitacao; ?>" class="btn btn-secondary">
                <ion-icon name="close-outline"></ion-icon>
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
    /**
     * ============================================
     * JavaScript - Funcionalidades da Página
     * ============================================
     */

    // ============================================
    // Inicialização
    // ============================================
    $(document).ready(function() {
        // Destrói Materialize Select (evita conflitos)
        if (typeof M !== 'undefined') {
            $('form select').formSelect('destroy');
        }

        // Inicializa Select2 nos dropdowns
        $('.select2-tipo, .select2-criterio, .select2-forma').select2({
            placeholder: 'Selecione uma opção',
            allowClear: true,
            width: '100%'
        });

        // Máscara no código da licitação
        $('#codLicitacao').mask('000/0000');
        validarCodLicitacao();

        // Restaura preferência de visualização
        const savedView = localStorage.getItem('filesViewEdit');
        if (savedView && (savedView === 'grid' || savedView === 'list')) {
            toggleFilesViewEdit(savedView);
        }
    });

    // ============================================
    // Toggle de Visualização (Grid/Lista)
    // ============================================
    function toggleFilesViewEdit(view) {
        const grid = document.getElementById('filesGridEdit');
        const list = document.getElementById('filesListEdit');
        const buttons = document.querySelectorAll('.files-view-btn');

        // Atualiza botões
        buttons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            }
        });

        // Alterna visualização
        if (view === 'grid') {
            if (grid) grid.classList.remove('hidden');
            if (list) list.classList.add('hidden');
        } else {
            if (grid) grid.classList.add('hidden');
            if (list) list.classList.remove('hidden');
        }

        // Salva preferência
        localStorage.setItem('filesViewEdit', view);
    }

    // ============================================
    // Validação do Código da Licitação
    // ============================================
    $('#codLicitacao').on('input blur', function() {
        validarCodLicitacao();
    });

    $('#tipoLicitacao').on('change', function() {
        if ($('#codLicitacao').val().length === 8) {
            validarCodLicitacao();
        }
    });

    function validarCodLicitacao() {
        var codLicitacao = $('#codLicitacao').val();
        var tipoLicitacao = $('#tipoLicitacao').val();
        var idLicitacao = $('#idLicitacao').val();

        if (codLicitacao.length == 8) {
            $.ajax({
                url: 'verificaCodLicitacao.php',
                method: 'GET',
                data: {
                    idLicitacao: idLicitacao,
                    codLicitacao: codLicitacao,
                    tipoLicitacao: tipoLicitacao
                },
                dataType: 'json',
                success: function(response) {
                    if (response == 1) {
                        $('#codLicitacao').val('');
                        $('#codLicitacao').focus();
                        alert('Código da Licitação já cadastrado.');
                    }
                }
            });
        }
    }

    // ============================================
    // Validação do Formulário
    // ============================================
    function validarFormulario() {
        var tipoLicitacao = $('#tipoLicitacao').val();
        var codLicitacao = $('#codLicitacao').val();
        var statusLicitacao = $('#statusLicitacao').val();
        var respLicitacao = $('#respLicitacao').val();
        var objLicitacao = $('#objLicitacao').val();

        if (!tipoLicitacao || tipoLicitacao === '') {
            alert('Selecione o Tipo de Contratação.');
            $('#tipoLicitacao').focus();
            return false;
        }

        if (!codLicitacao || codLicitacao.length < 8) {
            alert('Informe o Código da Licitação no formato 000/0000.');
            $('#codLicitacao').focus();
            return false;
        }

        if (!statusLicitacao) {
            alert('Selecione o Status da Licitação.');
            $('#statusLicitacao').focus();
            return false;
        }

        if (!respLicitacao || respLicitacao.trim() === '') {
            alert('Informe o Responsável pela Licitação.');
            $('#respLicitacao').focus();
            return false;
        }

        if (!objLicitacao || objLicitacao.trim() === '') {
            alert('Informe o Objeto da Licitação.');
            $('#objLicitacao').focus();
            return false;
        }

        return true;
    }

    // ============================================
    // Upload de Arquivos (Drag & Drop)
    // ============================================
    function handleClick(event) {
        const input = document.createElement('input');
        input.type = 'file';
        input.multiple = true;
        input.accept = '.pdf,.zip';
        input.onchange = function(e) {
            uploadFiles(e.target.files);
        };
        input.click();
    }

    function handleDrop(event) {
        event.preventDefault();
        event.stopPropagation();
        document.getElementById('drop-zone').classList.remove('dragover');

        const files = event.dataTransfer.files;
        uploadFiles(files);
    }

    function handleDragOver(event) {
        event.preventDefault();
        event.stopPropagation();
        document.getElementById('drop-zone').classList.add('dragover');
    }

    function uploadFiles(files) {
        const idLicitacao = <?php echo $idLicitacao; ?>;
        const formData = new FormData();

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const ext = file.name.split('.').pop().toLowerCase();

            if (ext !== 'pdf' && ext !== 'zip') {
                alert('Apenas arquivos PDF ou ZIP são permitidos: ' + file.name);
                continue;
            }

            formData.append('files[]', file);
        }

        formData.append('idLicitacao', idLicitacao);

        $.ajax({
            url: 'bd/licitacao/uploadAnexo.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('Erro ao fazer upload: ' + error);
            }
        });
    }

    // ============================================
    // Edição de Nome de Arquivo
    // ============================================
    $(document).on('click', '.edit-button', function() {
        var rowId = $(this).data('id');

        // Seleciona elementos em ambas as views
        var $rowNmAnexo = $('#row_' + rowId + ' .nmAnexo');
        var $cardNmAnexo = $('#card_row_' + rowId + ' .nmAnexo');

        // Pega o nome atual do arquivo
        var currentName = '';
        if ($rowNmAnexo.length > 0 && $rowNmAnexo.find('a').length > 0) {
            currentName = $rowNmAnexo.find('a').text().trim();
        } else if ($cardNmAnexo.length > 0 && $cardNmAnexo.find('a').length > 0) {
            currentName = $cardNmAnexo.find('a').text().trim();
        }

        if (!currentName) {
            alert('Erro ao obter nome do arquivo');
            return;
        }

        // Salva nome atual
        $('#row_' + rowId + ', #card_row_' + rowId).data('currentName', currentName);

        // Esconde link e mostra input em AMBAS as views
        $rowNmAnexo.find('a').hide();
        $rowNmAnexo.find('.edited-name').val(currentName).show();

        $cardNmAnexo.find('a').hide();
        $cardNmAnexo.find('.edited-name').val(currentName).show();

        // Esconde botão editar e mostra botão salvar
        $('#row_' + rowId + ' .edit-button, #card_row_' + rowId + ' .edit-button').hide();
        $('#row_' + rowId + ' .save-button, #card_row_' + rowId + ' .save-button').css('display', 'flex').show();

        // Foca e seleciona texto até a extensão
        var $input = $rowNmAnexo.find('.edited-name').is(':visible') ? 
            $rowNmAnexo.find('.edited-name') : $cardNmAnexo.find('.edited-name');
        
        if ($input.length > 0) {
            $input.focus();
            var dotIndex = currentName.lastIndexOf('.');
            if (dotIndex > 0) {
                $input[0].setSelectionRange(0, dotIndex);
            }
        }
    });

    // ============================================
    // Salvar Nome do Arquivo
    // ============================================
    $(document).on('click', '.save-button', function() {
        var rowId = $(this).data('id');
        var directory = '<?php echo $directory; ?>';
        var currentName = $('#row_' + rowId + ', #card_row_' + rowId).data('currentName');

        // Pega novo nome
        var newName = '';
        var $rowInput = $('#row_' + rowId + ' .edited-name');
        var $cardInput = $('#card_row_' + rowId + ' .edited-name');

        if ($rowInput.is(':visible')) {
            newName = $rowInput.val().trim();
        } else if ($cardInput.is(':visible')) {
            newName = $cardInput.val().trim();
        }

        if (!newName) {
            alert('Nome do arquivo não pode ser vazio');
            return;
        }

        renameFile(rowId, currentName, newName, directory);
    });

    function renameFile(rowId, currentName, newName, directory) {
        $.ajax({
            url: 'bd/licitacao/renameAnexo.php',
            method: 'POST',
            data: {
                directory: directory,
                currentName: currentName,
                newName: newName
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Erro ao renomear arquivo: ' + response.message);
                    cancelEdit(rowId);
                }
            },
            error: function() {
                alert('Erro ao renomear arquivo');
                cancelEdit(rowId);
            }
        });
    }

    function cancelEdit(rowId) {
        var currentName = $('#row_' + rowId + ', #card_row_' + rowId).data('currentName');

        // Restaura estado original
        $('#row_' + rowId + ' .nmAnexo a, #card_row_' + rowId + ' .nmAnexo a').show();
        $('#row_' + rowId + ' .edited-name, #card_row_' + rowId + ' .edited-name').hide().val(currentName);
        $('#row_' + rowId + ' .edit-button, #card_row_' + rowId + ' .edit-button').show();
        $('#row_' + rowId + ' .save-button, #card_row_' + rowId + ' .save-button').hide();
    }

    // Cancelar edição com ESC, salvar com Enter
    $(document).on('keydown', '.edited-name', function(e) {
        var $container = $(this).closest('[id^="row_"], [id^="card_row_"]');
        var rowId = $container.attr('id').replace(/\D/g, '');

        if (e.key === 'Escape') {
            cancelEdit(rowId);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            $('#row_' + rowId + ' .save-button, #card_row_' + rowId + ' .save-button').first().click();
        }
    });

    // Validar caracteres inválidos
    $(document).on('input', '.edited-name', function() {
        var value = $(this).val();
        var sanitized = value.replace(/[<>:"/\\|?*]/g, '');
        if (value !== sanitized) {
            $(this).val(sanitized);
            $(this).css('border-color', '#ef4444');
            setTimeout(() => {
                $(this).css('border-color', '');
            }, 500);
        }
    });

    // ============================================
    // Excluir Arquivo
    // ============================================
    $(document).on('click', '.delete-button', function() {
        var fileName = $(this).data('file');
        var directory = '<?php echo $directory; ?>';

        if (confirm('Deseja realmente excluir o arquivo "' + fileName + '"?')) {
            $.ajax({
                url: 'bd/licitacao/deleteAnexo.php',
                method: 'POST',
                data: {
                    directory: directory,
                    fileName: fileName
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Erro ao excluir arquivo: ' + response.message);
                    }
                },
                error: function() {
                    alert('Erro ao excluir arquivo');
                }
            });
        }
    });
</script>

<?php include_once 'includes/footer.inc.php'; ?>