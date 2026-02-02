<?php
/**
 * ============================================
 * Portal de Compras - CESAN
 * Formulário Unificado de Licitação
 * 
 * Cadastro e Edição em uma única tela
 * - Se receber idLicitacao via GET: modo EDIÇÃO
 * - Se não receber: modo CADASTRO
 * ============================================
 */

// Includes necessários
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

// ============================================
// Detecta modo: CADASTRO ou EDIÇÃO
// ============================================
$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);
$modoEdicao = !empty($idLicitacao);

// Verifica perfis do usuário logado
$idPerfil = [];
$isAdmin = 0;
foreach ($_SESSION['perfil'] as $perfil) {
    $idPerfil[] = $perfil['idPerfil'];
    if ($perfil['idPerfil'] == 9) {
        $isAdmin = 1;
    }
}
$idPerfilFinal = implode(',', $idPerfil);

// ============================================
// Inicializa variáveis (vazias para cadastro)
// ============================================
$tituloLicitacao = '';
$tipoLicitacao = '';
$codLicitacao = '';
$statusLicitacao = 'Em Andamento';
$objLicitacao = '';
$respLicitacao = '';
$dtAberLicitacao = '';
$dtIniSessLicitacao = '';
$hrAberLicitacao = '';
$hrIniSessLicitacao = '';
$modoLicitacao = '0';
$criterioLicitacao = '0';
$regimeLicitacao = '';
$formaLicitacao = '0';
$vlLicitacao = '';
$localLicitacao = '';
$identificadorLicitacao = '';
$obsLicitacao = '';
$permitirAtualizacao = 1;
$idTipo = '';
$nmTipo = '';
$idCriterio = '';
$nmCriterio = '';
$idForma = '';
$nmForma = '';

// ============================================
// Modo EDIÇÃO: carrega dados existentes
// ============================================
if ($modoEdicao) {
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
    $isAdminProtect = 0;
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
    if (isset($tipoLicitacao) && $tipoLicitacao != '') {
        $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[TIPO_LICITACAO] WHERE ID_TIPO = $tipoLicitacao";
        $querySelect = $pdoCAT->query($querySelect2);

        while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
            $idTipo = $registros['ID_TIPO'];
            $nmTipo = $registros['NM_TIPO'];
        endwhile;
    }

    // Busca dados do critério
    if ($criterioLicitacao && $criterioLicitacao != '0') {
        $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[CRITERIO_LICITACAO] WHERE ID_CRITERIO = $criterioLicitacao";
        $querySelect = $pdoCAT->query($querySelect2);

        while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
            $idCriterio = $registros['ID_CRITERIO'];
            $nmCriterio = $registros['NM_CRITERIO'];
        endwhile;
    }

    // Busca dados da forma
    if ($formaLicitacao && $formaLicitacao != '0') {
        $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[FORMA] WHERE ID_FORMA = $formaLicitacao";
        $querySelect = $pdoCAT->query($querySelect2);

        while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
            $idForma = $registros['ID_FORMA'];
            $nmForma = $registros['NM_FORMA'];
        endwhile;
    }

    // Corrige datas inválidas (1969)
    if (strpos($dtAberLicitacao, '1969') !== false) {
        $dtAberLicitacao = '';
        $hrAberLicitacao = '';
    }
    if (strpos($dtIniSessLicitacao, '1969') !== false) {
        $dtIniSessLicitacao = '';
        $hrIniSessLicitacao = '';
    }
}

// Define action do formulário e textos da página
$formAction = $modoEdicao ? 'bd/licitacao/update.php' : 'bd/licitacao/create.php';
$pageTitle = $modoEdicao ? 'Editar Licitação ' . htmlspecialchars($tituloLicitacao) : 'Nova Licitação';
$pageSubtitle = $modoEdicao ? htmlspecialchars($nmTipo . ' ' . $codLicitacao) : 'Preencha os dados para cadastrar uma nova licitação';
$pageIcon = 'document-text-outline';
$btnSubmitText = $modoEdicao ? 'Salvar Alterações' : 'Cadastrar Licitação';
$btnSubmitIcon = $modoEdicao ? 'save-outline' : 'checkmark-circle-outline';
?>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- CSS da página -->
<link rel="stylesheet" href="style/css/licitacaoForm.css" />

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
                <a href="licitacao.php">Licitações</a>
                <ion-icon name="chevron-forward-outline" class="breadcrumb-sep"></ion-icon>
                <span><?php echo $modoEdicao ? 'Editar' : 'Nova'; ?></span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <ion-icon name="<?php echo $pageIcon; ?>"></ion-icon>
                    <div class="icon-box-pulse"></div>
                </div>
                <div class="header-title-group">
                    <h1><?php echo $pageTitle; ?></h1>
                    <p class="header-subtitle">
                        <ion-icon name="document-text-outline"></ion-icon>
                        <?php echo $pageSubtitle; ?>
                    </p>
                </div>
            </div>
            <?php if ($modoEdicao): ?>
                <div class="header-right">
                    <a href="licitacaoView.php?idLicitacao=<?php echo $idLicitacao; ?>" class="btn-header-action">
                        <ion-icon name="eye-outline"></ion-icon>
                        Visualizar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Formulário Principal -->
    <form action="<?php echo $formAction; ?>" method="post" enctype="multipart/form-data"
        onsubmit="return validarFormulario()">

        <?php if ($modoEdicao): ?>
            <!-- Campo oculto com ID da licitação (apenas edição) -->
            <input type="hidden" name="idLicitacao" id="idLicitacao" value="<?php echo $idLicitacao ?>" readonly required>
            <div id="idLicitacaoData" data-id="<?php echo $idLicitacao; ?>"></div>
        <?php else: ?>
            <!-- Campo oculto vazio para cadastro -->
            <input type="hidden" name="idLicitacao" id="idLicitacao" value="" readonly>
            <div id="idLicitacaoData" data-id="0"></div>
        <?php endif; ?>

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
                                <option value='' disabled <?php echo !$modoEdicao ? 'selected' : ''; ?>>Selecione uma
                                    opção</option>
                                <?php
                                if ($isAdmin == 1) {
                                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' ORDER BY NM_TIPO";
                                } else {
                                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' AND ID_TIPO IN ($idPerfilFinal) ORDER BY NM_TIPO";
                                }
                                $querySelect = $pdoCAT->query($querySelect2);

                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                    $selected = ($registros["ID_TIPO"] == $idTipo) ? 'selected' : '';
                                    echo "<option value='" . $registros["ID_TIPO"] . "' $selected>" . htmlspecialchars($registros["NM_TIPO"]) . "</option>";
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
                                class="form-control browser-default" placeholder="000/0000" maxlength="8"
                                autocomplete="off" required>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <ion-icon name="toggle-outline"></ion-icon>
                                Status <span class="required-star">*</span>
                            </label>
                            <?php if ($modoEdicao): ?>
                                <select name="statusLicitacao" id="statusLicitacao" class="form-select" required>
                                    <option value='Em Andamento' <?php echo ($statusLicitacao === 'Em Andamento') ? 'selected' : ''; ?>>Em Andamento</option>
                                    <option value='Encerrado' <?php echo ($statusLicitacao === 'Encerrado') ? 'selected' : ''; ?>>Encerrada</option>
                                    <option value='Suspenso' <?php echo ($statusLicitacao === 'Suspenso') ? 'selected' : ''; ?>>Suspenso</option>
                                    <option value='Rascunho' <?php echo ($statusLicitacao === 'Rascunho') ? 'selected' : ''; ?>>Rascunho</option>
                                </select>
                            <?php else: ?>
                                <input type="text" class="form-control" value="Rascunho" readonly>
                                <input type="hidden" name="statusLicitacao" id="statusLicitacao" value="Rascunho">
                            <?php endif; ?>
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
                                value="<?php echo htmlspecialchars($respLicitacao) ?>" class="form-control"
                                placeholder="Nome do responsável" required>
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
                            <textarea id="objLicitacao" name="objLicitacao" class="form-textarea"
                                placeholder="Descreva o objeto da licitação"
                                required><?php echo htmlspecialchars($objLicitacao) ?></textarea>
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
                                value="<?php echo htmlspecialchars($identificadorLicitacao) ?>" class="form-control"
                                placeholder="Identificador único (opcional)">
                        </div>
                    </div>

                    <div class="form-col-6">
                        <div class="form-group">
                            <label>
                                <ion-icon name="cash-outline"></ion-icon>
                                Valor Estimado
                            </label>
                            <input type="text" id="vlLicitacao" name="vlLicitacao"
                                value="<?php echo htmlspecialchars($vlLicitacao) ?>" class="form-control"
                                placeholder="R$ 0,00">
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
                                value="<?php echo htmlspecialchars($localLicitacao) ?>" class="form-control"
                                placeholder="URL ou endereço do local de abertura">
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
                                <option value='0' <?php echo ($modoLicitacao === '0' || $modoLicitacao === '') ? 'selected' : ''; ?>>Selecione uma opção</option>
                                <option value='Aberta' <?php echo ($modoLicitacao === 'Aberta') ? 'selected' : ''; ?>>
                                    Aberta</option>
                                <option value='Fechada' <?php echo ($modoLicitacao === 'Fechada') ? 'selected' : ''; ?>>
                                    Fechada</option>
                                <option value='Hibrida' <?php echo ($modoLicitacao === 'Hibrida') ? 'selected' : ''; ?>>
                                    Híbrida</option>
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
                            <select name="criterioLicitacao" id="criterioLicitacao"
                                class="form-select select2-criterio">
                                <option value='0' <?php echo (!$idCriterio) ? 'selected' : ''; ?>>Selecione uma opção
                                </option>
                                <?php
                                $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE DT_EXC_CRITERIO IS NULL ORDER BY NM_CRITERIO";
                                $querySelect = $pdoCAT->query($querySelect2);

                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                    $selected = ($registros["ID_CRITERIO"] == $idCriterio) ? 'selected' : '';
                                    echo "<option value='" . $registros["ID_CRITERIO"] . "' $selected>" . htmlspecialchars($registros["NM_CRITERIO"]) . "</option>";
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
                                <option value='0' <?php echo (!$idForma) ? 'selected' : ''; ?>>Selecione uma opção
                                </option>
                                <?php
                                $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] WHERE DT_EXC_FORMA IS NULL ORDER BY NM_FORMA";
                                $querySelect = $pdoCAT->query($querySelect2);

                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                    $selected = ($registros["ID_FORMA"] == $idForma) ? 'selected' : '';
                                    echo "<option value='" . $registros["ID_FORMA"] . "' $selected>" . htmlspecialchars($registros["NM_FORMA"]) . "</option>";
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
                                value="<?php echo htmlspecialchars($regimeLicitacao) ?>" class="form-control"
                                placeholder="Ex: Empreitada por Preço Global">
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
                            <textarea id="obsLicitacao" name="obsLicitacao" class="form-textarea"
                                placeholder="Observações adicionais (opcional)"><?php echo htmlspecialchars($obsLicitacao) ?></textarea>
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
                    <input type="checkbox" name="permitirAtualizacao" id="permitirAtualizacao" <?php echo ($permitirAtualizacao == 1) ? 'checked' : ''; ?>>
                    <label for="permitirAtualizacao">
                        Permitir que os usuários sejam lembrados para futuras atualizações da licitação
                    </label>
                </div>
            </div>
        </div>

        <!-- ============================================
             Seção: Anexos (apenas modo edição)
             ============================================ -->
        <?php if ($modoEdicao): ?>
            <div class="section-card">
                <div class="section-header">
                    <ion-icon name="attach-outline"></ion-icon>
                    <h2>Gerenciar Anexos</h2>
                </div>
                <div class="section-content">
                    <!-- Dropzone para upload -->
                    <div class="form-row">
                        <div class="form-col-12">
                            <div id="drop-zone" class="dropzone" onclick="handleClick(event)" ondrop="handleDrop(event)"
                                ondragover="handleDragOver(event)">
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
                        function getFileIcon($filename)
                        {
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
                            $files = array_diff($files, array('.', '..', '_order.json'));

                            foreach ($files as $file) {
                                $anexos[] = array(
                                    'nmAnexo' => $file,
                                    'linkAnexo' => $directory . '/' . $file,
                                    'timestamp' => filemtime($directory . '/' . $file),
                                );
                            }
                        }

                        // Verifica se existe ordem personalizada salva
                        $orderFile = $directory . '/_order.json';
                        if (file_exists($orderFile)) {
                            $savedOrder = json_decode(file_get_contents($orderFile), true);
                            if (is_array($savedOrder)) {
                                $anexosByName = [];
                                foreach ($anexos as $anexo) {
                                    $anexosByName[$anexo['nmAnexo']] = $anexo;
                                }
                                $orderedAnexos = [];
                                foreach ($savedOrder as $filename) {
                                    if (isset($anexosByName[$filename])) {
                                        $orderedAnexos[] = $anexosByName[$filename];
                                        unset($anexosByName[$filename]);
                                    }
                                }
                                // Arquivos novos (não presentes na ordem salva) vão ao final
                                foreach ($anexosByName as $anexo) {
                                    $orderedAnexos[] = $anexo;
                                }
                                $anexos = $orderedAnexos;
                            }
                        } else {
                            // Sem ordem personalizada: ordena por timestamp (mais recentes primeiro)
                            usort($anexos, function ($a, $b) {
                                return $b['timestamp'] - $a['timestamp'];
                            });
                        }
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

                                echo '<div class="file-card" id="card_row_' . $index . '" data-filename="' . $nomeArquivo . '">';
                                echo '<div class="drag-handle" title="Arrastar para reordenar"><ion-icon name="reorder-three-outline"></ion-icon></div>';
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
                            echo '<thead><tr><th style="width: 40px;"></th><th>Arquivo</th><th>Data</th><th style="text-align: center;">Ações</th></tr></thead>';
                            echo '<tbody>';

                            $index = 0;
                            foreach ($anexos as $anexo) {
                                $nomeArquivo = htmlspecialchars($anexo['nmAnexo']);
                                $linkArquivo = htmlspecialchars($anexo['linkAnexo']);
                                $dataArquivo = date("d/m/Y H:i", $anexo['timestamp']);

                                echo '<tr id="row_' . $index . '" data-filename="' . $nomeArquivo . '">';
                                echo '<td class="drag-handle" title="Arrastar para reordenar"><ion-icon name="reorder-three-outline"></ion-icon></td>';
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
                        } else {
                            // Estado vazio
                            echo '<div class="empty-state">';
                            echo '<ion-icon name="folder-open-outline"></ion-icon>';
                            echo '<p>Nenhum arquivo anexado</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Aviso para cadastro -->
            <div class="section-card">
                <div class="section-header">
                    <ion-icon name="attach-outline"></ion-icon>
                    <h2>Anexos</h2>
                </div>
                <div class="section-content">
                    <div class="info-notice">
                        <ion-icon name="information-circle-outline"></ion-icon>
                        <p>Os anexos poderão ser adicionados após o cadastro da licitação.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- ============================================
             Botões de Ação
             ============================================ -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <ion-icon name="<?php echo $btnSubmitIcon; ?>"></ion-icon>
                <?php echo $btnSubmitText; ?>
            </button>
            <a href="licitacao.php" class="btn btn-secondary">
                <ion-icon name="close-outline"></ion-icon>
                Cancelar
            </a>
            <?php if ($modoEdicao): ?>
                <a href="licitacaoView.php?idLicitacao=<?php echo $idLicitacao; ?>" class="btn btn-outline">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
    /**
     * ============================================
     * JavaScript - Funcionalidades da Página
     * ============================================
     */

    // Variáveis globais
    const modoEdicao = <?php echo $modoEdicao ? 'true' : 'false'; ?>;
    const idLicitacao = <?php echo $idLicitacao ? $idLicitacao : '0'; ?>;
    const directory = '<?php echo $modoEdicao ? $directory : ''; ?>';

    // ============================================
    // Inicialização
    // ============================================
    $(document).ready(function () {
        // Exibe data atual no header
        const hoje = new Date();
        const opcoes = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        const dataFormatada = hoje.toLocaleDateString('pt-BR', opcoes);
        $('#headerDate').text(dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1));

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

        // Aplica máscara no código (JavaScript puro)
        aplicarMascaraCodigo();

        // Validação de código apenas se já tiver valor completo
        if ($('#codLicitacao').val().length === 8) {
            validarCodLicitacao();
        }

        // Restaura preferência de visualização (apenas modo edição)
        if (modoEdicao) {
            const savedView = localStorage.getItem('filesViewEdit');
            if (savedView && (savedView === 'grid' || savedView === 'list')) {
                toggleFilesViewEdit(savedView);
            }
        }
    });

    // ============================================
    // Máscara do Código (000/0000) - JavaScript Puro
    // ============================================
    function aplicarMascaraCodigo() {
        const input = document.getElementById('codLicitacao');
        if (!input) return;

        // Formata valor inicial se existir (com padding de zeros)
        if (input.value) {
            input.value = formatarCodigoCompleto(input.value);
        }

        // Evento de blur - SEMPRE completa com zeros à esquerda
        input.addEventListener('blur', function (e) {
            let valor = e.target.value.replace(/\D/g, '');

            if (valor.length > 0) {
                // Sempre padroniza para 7 dígitos com zeros à esquerda
                valor = valor.padStart(7, '0');
                valor = valor.substring(0, 7);
                valor = valor.substring(0, 3) + '/' + valor.substring(3);
            }

            e.target.value = valor;

            // Dispara validação após formatar
            if (valor.length === 8) {
                validarCodLicitacao();
            }
        });

        // Evento de input - formata enquanto digita
        input.addEventListener('input', function (e) {
            let valor = e.target.value;

            // Remove tudo que não for número
            valor = valor.replace(/\D/g, '');

            // Limita a 7 dígitos
            valor = valor.substring(0, 7);

            // Aplica a máscara 000/0000
            if (valor.length > 3) {
                valor = valor.substring(0, 3) + '/' + valor.substring(3);
            }

            e.target.value = valor;
        });

        // Evento de keydown para impedir caracteres inválidos
        input.addEventListener('keydown', function (e) {
            // Permite: backspace, delete, tab, escape, enter, setas
            if ([8, 9, 27, 13, 46, 37, 38, 39, 40].includes(e.keyCode)) {
                return;
            }
            // Permite Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].includes(e.keyCode)) {
                return;
            }
            // Bloqueia se não for número
            if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        // Evento de paste - cola e formata com padding
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            let texto = (e.clipboardData || window.clipboardData).getData('text');
            texto = texto.replace(/\D/g, '').substring(0, 7);

            if (texto.length > 0) {
                // Aplica padding com zeros à esquerda ao colar
                texto = texto.padStart(7, '0');
                texto = texto.substring(0, 3) + '/' + texto.substring(3);
            }

            e.target.value = texto;
        });
    }

    /**
     * Formata código COM padding de zeros à esquerda
     * Exemplos:
     *   "12026"    → "001/2026"
     *   "1/2026"   → "001/2026"
     *   "0012026"  → "001/2026"
     *   "001/2026" → "001/2026"
     */
    function formatarCodigoCompleto(valor) {
        valor = valor.replace(/\D/g, '');
        if (valor.length > 0) {
            valor = valor.padStart(7, '0');
        }
        valor = valor.substring(0, 7);
        if (valor.length > 3) {
            valor = valor.substring(0, 3) + '/' + valor.substring(3);
        }
        return valor;
    }

    // Mantém compatibilidade
    function formatarCodigo(valor) {
        return formatarCodigoCompleto(valor);
    }

    // ============================================
    // Toggle de Visualização (Grid/Lista) - Apenas edição
    // ============================================
    function toggleFilesViewEdit(view) {
        const grid = document.getElementById('filesGridEdit');
        const list = document.getElementById('filesListEdit');
        const buttons = document.querySelectorAll('.files-view-btn');

        buttons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            }
        });

        if (view === 'grid') {
            if (grid) grid.classList.remove('hidden');
            if (list) list.classList.add('hidden');
        } else {
            if (grid) grid.classList.add('hidden');
            if (list) list.classList.remove('hidden');
        }

        localStorage.setItem('filesViewEdit', view);
    }

    // ============================================
    // Validação do Código da Licitação
    // ============================================
    $('#codLicitacao').on('input blur', function () {
        validarCodLicitacao();
    });

    $('#tipoLicitacao').on('change', function () {
        if ($('#codLicitacao').val().length === 8) {
            validarCodLicitacao();
        }
    });

    function validarCodLicitacao() {
        var codLicitacao = $('#codLicitacao').val();
        var tipoLicitacao = $('#tipoLicitacao').val();
        var idLic = $('#idLicitacao').val() || 0;

        if (codLicitacao.length == 8 && tipoLicitacao) {
            $.ajax({
                url: 'verificaCodLicitacao.php',
                method: 'GET',
                data: {
                    idLicitacao: idLic,
                    codLicitacao: codLicitacao,
                    tipoLicitacao: tipoLicitacao
                },
                dataType: 'json',
                success: function (response) {
                    if (response == 1) {
                        $('#codLicitacao').val('');
                        $('#codLicitacao').focus();
                        alert('Código da Licitação já cadastrado para este tipo.');
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

    <?php if ($modoEdicao): ?>
        // ============================================
        // Upload de Arquivos (Drag & Drop) - Apenas edição
        // ============================================
        function handleClick(event) {
            const input = document.createElement('input');
            input.type = 'file';
            input.multiple = true;
            input.accept = '.pdf,.zip';
            input.onchange = function (e) {
                uploadFiles(e.target.files);
            };
            input.click();
        }

        function handleDrop(event) {
            event.preventDefault();
            event.stopPropagation();
            document.getElementById('drop-zone').classList.remove('dragover');
            uploadFiles(event.dataTransfer.files);
        }

        function handleDragOver(event) {
            event.preventDefault();
            event.stopPropagation();
            document.getElementById('drop-zone').classList.add('dragover');
        }

        function uploadFiles(files) {
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
                success: function (response) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    alert('Erro ao fazer upload: ' + error);
                }
            });
        }

        // ============================================
        // Edição de Nome de Arquivo
        // ============================================
        $(document).on('click', '.edit-button', function () {
            var rowId = $(this).data('id');
            var $rowNmAnexo = $('#row_' + rowId + ' .nmAnexo');
            var $cardNmAnexo = $('#card_row_' + rowId + ' .nmAnexo');

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

            $('#row_' + rowId + ', #card_row_' + rowId).data('currentName', currentName);

            $rowNmAnexo.find('a').hide();
            $rowNmAnexo.find('.edited-name').val(currentName).show();
            $cardNmAnexo.find('a').hide();
            $cardNmAnexo.find('.edited-name').val(currentName).show();

            $('#row_' + rowId + ' .edit-button, #card_row_' + rowId + ' .edit-button').hide();
            $('#row_' + rowId + ' .save-button, #card_row_' + rowId + ' .save-button').addClass('editing');

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

        $(document).on('click', '.save-button', function () {
            var rowId = $(this).data('id');
            var currentName = $('#row_' + rowId + ', #card_row_' + rowId).data('currentName');

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

        function renameFile(rowId, currentName, newName, dir) {
            $.ajax({
                url: 'bd/licitacao/renameAnexo.php',
                method: 'POST',
                data: {
                    directory: dir,
                    currentName: currentName,
                    newName: newName
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Erro ao renomear arquivo: ' + response.message);
                        cancelEdit(rowId);
                    }
                },
                error: function () {
                    alert('Erro ao renomear arquivo');
                    cancelEdit(rowId);
                }
            });
        }

        function cancelEdit(rowId) {
            var currentName = $('#row_' + rowId + ', #card_row_' + rowId).data('currentName');
            $('#row_' + rowId + ' .nmAnexo a, #card_row_' + rowId + ' .nmAnexo a').show();
            $('#row_' + rowId + ' .edited-name, #card_row_' + rowId + ' .edited-name').hide().val(currentName);
            $('#row_' + rowId + ' .edit-button, #card_row_' + rowId + ' .edit-button').show();
            $('#row_' + rowId + ' .save-button, #card_row_' + rowId + ' .save-button').removeClass('editing');
        }

        $(document).on('keydown', '.edited-name', function (e) {
            var $container = $(this).closest('[id^="row_"], [id^="card_row_"]');
            var rowId = $container.attr('id').replace(/\D/g, '');

            if (e.key === 'Escape') {
                cancelEdit(rowId);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                $('#row_' + rowId + ' .save-button, #card_row_' + rowId + ' .save-button').first().click();
            }
        });

        $(document).on('input', '.edited-name', function () {
            var value = $(this).val();
            var sanitized = value.replace(/[<>:"/\\|?*]/g, '');
            if (value !== sanitized) {
                $(this).val(sanitized);
                $(this).css('border-color', '#ef4444');
                setTimeout(() => { $(this).css('border-color', ''); }, 500);
            }
        });

        $(document).on('click', '.delete-button', function () {
            var fileName = $(this).data('file');

            if (confirm('Deseja realmente excluir o arquivo "' + fileName + '"?')) {
                $.ajax({
                    url: 'bd/licitacao/deleteAnexo.php',
                    method: 'POST',
                    data: {
                        directory: directory,
                        fileName: fileName
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Erro ao excluir arquivo: ' + response.message);
                        }
                    },
                    error: function () {
                        alert('Erro ao excluir arquivo');
                    }
                });
            }
        });
    <?php endif; ?>

    // ============================================
    // Reordenação de Arquivos (Drag & Drop)
    // ============================================
    var sortableGrid = null;
    var sortableList = null;

    function initSortable() {
        var gridEl = document.getElementById('filesGridEdit');
        var listBody = document.querySelector('#filesListEdit .files-table tbody');

        if (gridEl) {
            sortableGrid = new Sortable(gridEl, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function (evt) {
                    syncOrderFromGrid();
                    saveOrder();
                }
            });
        }

        if (listBody) {
            sortableList = new Sortable(listBody, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function (evt) {
                    syncOrderFromList();
                    saveOrder();
                }
            });
        }
    }

    function getOrderFromGrid() {
        var items = document.querySelectorAll('#filesGridEdit .file-card');
        var order = [];
        items.forEach(function (item) {
            var filename = item.getAttribute('data-filename');
            if (filename) order.push(filename);
        });
        return order;
    }

    function getOrderFromList() {
        var rows = document.querySelectorAll('#filesListEdit .files-table tbody tr');
        var order = [];
        rows.forEach(function (row) {
            var filename = row.getAttribute('data-filename');
            if (filename) order.push(filename);
        });
        return order;
    }

    function syncOrderFromGrid() {
        var order = getOrderFromGrid();
        var listBody = document.querySelector('#filesListEdit .files-table tbody');
        if (!listBody) return;

        order.forEach(function (filename) {
            var row = listBody.querySelector('tr[data-filename="' + CSS.escape(filename) + '"]');
            if (row) listBody.appendChild(row);
        });
    }

    function syncOrderFromList() {
        var order = getOrderFromList();
        var grid = document.getElementById('filesGridEdit');
        if (!grid) return;

        order.forEach(function (filename) {
            var card = grid.querySelector('.file-card[data-filename="' + CSS.escape(filename) + '"]');
            if (card) grid.appendChild(card);
        });
    }

    function saveOrder() {
        var order = getOrderFromGrid();
        var licId = idLicitacao || document.getElementById('idLicitacao').value || 0;

        if (!licId || licId == 0) {
            console.warn('saveOrder: ID da licitação não disponível (modo cadastro).');
            return;
        }

        fetch('bd/licitacao/reorderAnexo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                idLicitacao: licId,
                order: order
            })
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.success) {
                    console.error('Erro ao salvar ordem:', data.message);
                }
            })
            .catch(function (error) {
                console.error('Erro ao salvar ordem:', error);
            });
    }

    // Inicializa o Sortable quando o DOM estiver pronto
    initSortable();
</script>

<?php include_once 'includes/footer.inc.php'; ?>