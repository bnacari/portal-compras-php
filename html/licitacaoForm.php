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
$pageTitle = $modoEdicao ? 'Editar ' . htmlspecialchars($codLicitacao) : 'Nova Licitação';
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
             COM ARQUIVOS EXTERNOS EXCLUÍDOS PARA RESTAURAÇÃO
             ============================================ -->
        <?php if ($modoEdicao): ?>
            <div class="section-card">
                <div class="section-header">
                    <ion-icon name="attach-outline"></ion-icon>
                    <h2>Gerenciar Anexos</h2>
                </div>
                <div class="section-content anexos-section">
                    <!-- Dropzone para upload -->
                    <div class="form-row dropzone-wrapper">
                        <div class="form-col-12">
                            <div id="drop-zone" class="dropzone" onclick="handleClick(event)" ondrop="handleDrop(event)"
                                ondragover="handleDragOver(event)">
                                <ion-icon name="cloud-upload-outline"></ion-icon>
                                <p>Arraste e solte os arquivos aqui ou clique para selecionar</p>
                                <p>Apenas arquivos PDF ou ZIP</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de arquivos UNIFICADA -->
                    <div id="filelist">
                        <?php
                        $directory = "uploads" . '/' . $idLicitacao;
                        $anexos = array();

                        // ============================================
                        // 1. Busca anexos EXTERNOS do banco (ATIVOS E EXCLUÍDOS)
                        // ============================================
                        if ($idLicitacao > 2000) {
                            // Licitações 13.303 - Query com CTE (inclui excluídos)
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
                                                MAX(DT_EXC_ANEXO) AS DT_EXC_ANEXO
                                            FROM RankedAnexos
                                            GROUP BY ID_LICITACAO, rn";
                        } else {
                            // Licitações legadas (inclui excluídos)
                            $queryAnexo = "SELECT ID_LICITACAO, NM_ANEXO, LINK_ANEXO, DT_EXC_ANEXO 
                                           FROM ANEXO 
                                           WHERE ID_LICITACAO = $idLicitacao";
                        }

                        $queryAnexo2 = $pdoCAT->query($queryAnexo);

                        while ($registros = $queryAnexo2->fetch(PDO::FETCH_ASSOC)) {
                            if (!empty($registros['LINK_ANEXO'])) {
                                $anexos[] = array(
                                    'nmAnexo' => $registros['NM_ANEXO'] ?? basename($registros['LINK_ANEXO']),
                                    'linkAnexo' => $registros['LINK_ANEXO'],
                                    'timestamp' => null,
                                    'isExternal' => true,
                                    'isDeleted' => !empty($registros['DT_EXC_ANEXO']),
                                    'dtExcAnexo' => $registros['DT_EXC_ANEXO'],
                                    'orderKey' => 'ext:' . $registros['LINK_ANEXO']
                                );
                            }
                        }

                        // ============================================
                        // 2. Busca arquivos FÍSICOS do diretório
                        // ============================================
                        if (is_dir($directory)) {
                            $files = scandir($directory);
                            $files = array_diff($files, array('.', '..', '_order.json'));

                            foreach ($files as $file) {
                                $anexos[] = array(
                                    'nmAnexo' => $file,
                                    'linkAnexo' => $directory . '/' . $file,
                                    'timestamp' => filemtime($directory . '/' . $file),
                                    'isExternal' => false,
                                    'isDeleted' => false,
                                    'dtExcAnexo' => null,
                                    'orderKey' => $file
                                );
                            }
                        }

                        // ============================================
                        // 3. Aplica ordenação salva (se existir)
                        // ============================================
                        $orderFile = $directory . '/_order.json';
                        if (file_exists($orderFile)) {
                            $savedOrder = json_decode(file_get_contents($orderFile), true);
                            if (is_array($savedOrder)) {
                                $anexosByKey = [];
                                foreach ($anexos as $anexo) {
                                    $anexosByKey[$anexo['orderKey']] = $anexo;
                                }
                                $orderedAnexos = [];
                                foreach ($savedOrder as $key) {
                                    if (isset($anexosByKey[$key])) {
                                        $orderedAnexos[] = $anexosByKey[$key];
                                        unset($anexosByKey[$key]);
                                    }
                                }
                                foreach ($anexosByKey as $anexo) {
                                    $orderedAnexos[] = $anexo;
                                }
                                $anexos = $orderedAnexos;
                            }
                        } else {
                            // Ordena: ativos primeiro, depois excluídos
                            usort($anexos, function ($a, $b) {
                                // Excluídos vão para o final
                                if ($a['isDeleted'] && !$b['isDeleted'])
                                    return 1;
                                if (!$a['isDeleted'] && $b['isDeleted'])
                                    return -1;
                                // Entre ativos, ordena por timestamp
                                if ($a['timestamp'] === null && $b['timestamp'] === null)
                                    return 0;
                                if ($a['timestamp'] === null)
                                    return 1;
                                if ($b['timestamp'] === null)
                                    return -1;
                                return $b['timestamp'] - $a['timestamp'];
                            });
                        }

                        // ============================================
                        // 4. Renderiza a tabela
                        // ============================================
                        if (!empty($anexos)) {
                            // Conta ativos e excluídos
                            foreach ($anexos as $a) {
                                if ($a['isDeleted'])
                                    $countExcluidos++;
                                else
                                    $countAtivos++;
                            }
                            $nmAnexo = isset($registros['NM_ANEXO']) && $registros['NM_ANEXO']
                                ? $registros['NM_ANEXO']
                                : basename($registros['LINK_ANEXO']);
                                
                            // Header
                            echo '<div class="files-section-header">';
                            echo '<div class="files-section-title">';
                            echo '<ion-icon name="folder-open-outline"></ion-icon>';
                            echo '<span>Arquivos (' . $countAtivos . ')</span>';
                            if ($countExcluidos > 0) {
                                echo '<span class="deleted-count">+ ' . $countExcluidos . ' excluído(s)</span>';
                            }
                            echo '</div>';
                            echo '</div>';

                            // Tabela
                            echo '<div class="files-table-wrapper">';
                            echo '<table class="files-table">';
                            echo '<thead><tr>';
                            echo '<th style="width: 40px;"></th>';
                            echo '<th>Arquivo</th>';
                            echo '<th style="width: 150px;">Data</th>';
                            echo '<th style="width: 100px; text-align: center;">Ações</th>';
                            echo '</tr></thead>';
                            echo '<tbody id="filesTableBody">';

                            $index = 0;
                            foreach ($anexos as $anexo) {
                                $nomeArquivo = htmlspecialchars($anexo['nmAnexo']);
                                $linkArquivo = htmlspecialchars($anexo['linkAnexo']);
                                $dataArquivo = $anexo['timestamp'] ? date("d/m/Y H:i", $anexo['timestamp']) : '-';
                                $isExternal = $anexo['isExternal'];
                                $isDeleted = $anexo['isDeleted'];
                                $dtExcAnexo = $anexo['dtExcAnexo'] ?? '';
                                $orderKey = htmlspecialchars($anexo['orderKey']);

                                // Classes CSS
                                $rowClasses = [];
                                if ($isExternal)
                                    $rowClasses[] = 'external-row';
                                if ($isDeleted)
                                    $rowClasses[] = 'deleted-row';
                                $rowClass = implode(' ', $rowClasses);

                                $iconName = $isExternal ? 'link' : 'document';

                                echo '<tr id="row_' . $index . '" class="' . $rowClass . '" data-order-key="' . $orderKey . '" data-is-external="' . ($isExternal ? '1' : '0') . '">';

                                // Coluna: Drag handle
                                echo '<td class="drag-handle-cell">';
                                if (!$isDeleted) {
                                    echo '<div class="drag-handle"><ion-icon name="menu-outline"></ion-icon></div>';
                                }
                                echo '</td>';

                                // Coluna: Nome do arquivo
                                echo '<td class="nmAnexo">';
                                if ($isDeleted) {
                                    echo '<span class="deleted-file-name">';
                                    echo '<ion-icon name="' . $iconName . '-outline"></ion-icon> ' . $nomeArquivo;
                                    echo '</span>';
                                } else {
                                    echo '<a href="' . $linkArquivo . '" target="_blank">';
                                    echo '<ion-icon name="' . $iconName . '-outline"></ion-icon> ' . $nomeArquivo;
                                    echo '</a>';
                                }
                                echo '<input type="text" class="edited-name" style="display:none;" />';
                                if ($isExternal && !$isDeleted) {
                                    echo ' <span class="external-badge-inline">Externo</span>';
                                }
                                if ($isDeleted) {
                                    echo ' <span class="deleted-badge-inline">Excluído</span>';
                                }
                                echo '</td>';

                                // Coluna: Data
                                echo '<td class="file-date">' . $dataArquivo . '</td>';

                                // Coluna: Ações
                                echo '<td class="file-actions">';
                                if ($isDeleted) {
                                    // Botão Restaurar (para excluídos)
                                    echo '<button type="button" class="action-btn restore-button" data-id="' . $index . '" data-file="' . $nomeArquivo . '" data-link="' . $linkArquivo . '" data-dt-exc="' . htmlspecialchars($dtExcAnexo) . '" title="Restaurar">';
                                    echo '<ion-icon name="refresh-outline"></ion-icon>';
                                    echo '</button>';
                                } else {
                                    if (!$isExternal) {
                                        // Botões de edição apenas para arquivos físicos
                                        echo '<button type="button" class="action-btn edit-button" data-id="' . $index . '" title="Editar">';
                                        echo '<ion-icon name="create-outline"></ion-icon>';
                                        echo '</button>';
                                        echo '<button type="button" class="action-btn save-button" data-id="' . $index . '" title="Salvar">';
                                        echo '<ion-icon name="checkmark-outline"></ion-icon>';
                                        echo '</button>';
                                    }
                                    // Botão Excluir
                                    echo '<button type="button" class="action-btn delete-button" data-id="' . $index . '" data-file="' . $nomeArquivo . '" data-is-external="' . ($isExternal ? '1' : '0') . '" data-link="' . $linkArquivo . '" title="Excluir">';
                                    echo '<ion-icon name="trash-outline"></ion-icon>';
                                    echo '</button>';
                                }
                                echo '</td>';

                                echo '</tr>';
                                $index++;
                            }

                            echo '</tbody></table>';
                            echo '</div>';
                        } else {
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
    // Função para Excluir/Restaurar Anexos Externos (BD)
    // ============================================
    function confirmDelete(file, directory, idLicitacao, dtExcAnexo) {
        var mensagem = dtExcAnexo
            ? 'Deseja restaurar este anexo externo?'
            : 'Tem certeza que deseja excluir este anexo externo?';

        if (confirm(mensagem)) {
            $.ajax({
                url: 'excluir_arquivo.php',
                type: 'GET',
                data: {
                    file: file,
                    directory: directory,
                    idLicitacao: idLicitacao,
                    dtExcAnexo: dtExcAnexo || ''
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Erro AJAX:', status, error);
                    alert('Erro ao processar a solicitação.');
                }
            });
        }
    }
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


    <?php endif; ?>



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



    /**
  * Salva a ordem dos arquivos
  */
    function saveOrder() {
        var rows = document.querySelectorAll('#filesTableBody tr:not(.deleted-row)');
        var order = [];

        rows.forEach(function (row) {
            var orderKey = row.getAttribute('data-order-key');
            if (orderKey) {
                order.push(orderKey);
            }
        });

        // Salva via AJAX
        fetch('bd/licitacao/reorderAnexo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                idLicitacao: idLicitacao,
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
                console.error('Erro ao salvar ordem dos arquivos:', error);
            });
    }

    // Inicializa o Sortable quando o DOM estiver pronto
    $(document).ready(function () {
        if (modoEdicao) {
            initSortable();
        }
    });

    // Inicializa o Sortable quando o DOM estiver pronto
    initSortable();

    /**
     * Função para confirmar exclusão/restauração de anexos externos (banco de dados)
     * 
     * @param {string} file - Nome do arquivo
     * @param {string} directory - Link/caminho do arquivo
     * @param {string} idLicitacao - ID da licitação
     * @param {string} dtExcAnexo - Data de exclusão (se existir, é para restaurar)
     */
    function confirmDeleteExternal(file, directory, idLicitacao, dtExcAnexo) {
        var acao = dtExcAnexo ? 'restaurar' : 'excluir';
        var mensagem = dtExcAnexo
            ? 'Deseja restaurar este anexo externo?'
            : 'Tem certeza que deseja excluir este anexo externo?';

        if (confirm(mensagem)) {
            $.ajax({
                url: 'excluir_arquivo.php',
                type: 'GET',
                data: {
                    file: file,
                    directory: directory,
                    idLicitacao: idLicitacao,
                    dtExcAnexo: dtExcAnexo || ''
                },
                success: function (response) {
                    // Recarrega a página para atualizar a lista
                    location.reload();
                },
                error: function () {
                    alert('Erro ao ' + acao + ' o anexo externo.');
                }
            });
        }
    }


    // ============================================
    // Exclusão de Arquivos (físicos e externos)
    // ============================================
    $(document).on('click', '.delete-button', function () {
        var $btn = $(this);
        var fileName = $btn.data('file');
        var isExternal = $btn.data('is-external') == '1';
        var link = $btn.data('link');

        var mensagem = isExternal
            ? 'Deseja excluir este link externo?\n\n"' + fileName + '"'
            : 'Deseja realmente excluir o arquivo "' + fileName + '"?';

        if (confirm(mensagem)) {
            if (isExternal) {
                // Exclusão de anexo externo (banco de dados)
                $.ajax({
                    url: 'excluir_arquivo.php',
                    type: 'GET',
                    data: {
                        file: fileName,
                        directory: link,
                        idLicitacao: idLicitacao
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        console.error('Erro ao excluir anexo externo:', error);
                        alert('Erro ao excluir o anexo externo.');
                    }
                });
            } else {
                // Exclusão de arquivo físico (diretório)
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
        }
    });

    // ============================================
    // Restauração de Arquivos Externos Excluídos
    // ============================================
    $(document).on('click', '.restore-button', function () {
        var $btn = $(this);
        var fileName = $btn.data('file');
        var link = $btn.data('link');
        var dtExc = $btn.data('dt-exc');

        if (confirm('Deseja restaurar este anexo?\n\n"' + fileName + '"')) {
            $.ajax({
                url: 'excluir_arquivo.php',
                type: 'GET',
                data: {
                    file: fileName,
                    directory: link,
                    idLicitacao: idLicitacao,
                    dtExcAnexo: dtExc  // Passa a data de exclusão para indicar restauração
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Erro ao restaurar anexo:', error);
                    alert('Erro ao restaurar o anexo.');
                }
            });
        }
    });

    // ============================================
    // Reordenação de Arquivos (Drag & Drop) - Tabela
    // ============================================
    function initSortable() {
        var tableBody = document.getElementById('filesTableBody');

        if (tableBody) {
            new Sortable(tableBody, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                filter: '.deleted-row', // Não permite arrastar linhas excluídas
                onEnd: function () {
                    saveOrder();
                }
            });
        }
    }

    /**
     * Sincroniza a ordem da lista com o grid
     */
    function syncOrderFromList() {
        var listRows = document.querySelectorAll('#filesListEdit tbody tr');
        var gridContainer = document.getElementById('filesGridEdit');

        if (!gridContainer) return;

        var newOrder = [];
        listRows.forEach(function (row) {
            var orderKey = row.getAttribute('data-order-key');
            newOrder.push(orderKey);
        });

        // Reordena o grid baseado na nova ordem
        newOrder.forEach(function (key) {
            var card = gridContainer.querySelector('[data-order-key="' + key + '"]');
            if (card) {
                gridContainer.appendChild(card);
            }
        });
    }

    /**
     * Sincroniza a ordem do grid com a lista
     */
    function syncOrderFromGrid() {
        var gridCards = document.querySelectorAll('#filesGridEdit .file-card-edit');
        var listBody = document.querySelector('#filesListEdit tbody');

        if (!listBody) return;

        var newOrder = [];
        gridCards.forEach(function (card) {
            var orderKey = card.getAttribute('data-order-key');
            newOrder.push(orderKey);
        });

        // Reordena a lista baseado na nova ordem
        newOrder.forEach(function (key) {
            var row = listBody.querySelector('[data-order-key="' + key + '"]');
            if (row) {
                listBody.appendChild(row);
            }
        });
    }

    /**
     * Salva a ordem dos arquivos (físicos e externos)
     * Usa o atributo data-order-key que inclui prefixo "ext:" para externos
     */
    function saveFileOrder() {
        var gridCards = document.querySelectorAll('#filesGridEdit .file-card-edit');
        var order = [];

        gridCards.forEach(function (card) {
            var orderKey = card.getAttribute('data-order-key');
            if (orderKey) {
                order.push(orderKey);
            }
        });

        // Salva via AJAX (usando fetch com JSON)
        fetch('bd/licitacao/reorderAnexo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                idLicitacao: idLicitacao,
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
                console.error('Erro ao salvar ordem dos arquivos:', error);
            });
    }

    // Inicializa o Sortable quando o DOM estiver pronto
    $(document).ready(function () {
        if (modoEdicao) {
            initSortable();
        }
    });
</script>

<?php include_once 'includes/footer.inc.php'; ?>