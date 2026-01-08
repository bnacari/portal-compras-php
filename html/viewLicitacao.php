<?php
//viewLicitacao.php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';
include_once 'redirecionar.php';

// include('protect.php');

$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

$querySelect2 = "SELECT TIPO.SGL_TIPO AS SGL_TIPO, L.*, DET.COD_LICITACAO AS COD_LIC, DET.*
                    FROM [PortalCompras].[dbo].[LICITACAO] L
                    LEFT JOIN DETALHE_LICITACAO DET ON DET.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN ANEXO A ON A.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN TIPO_LICITACAO TIPO ON TIPO.ID_TIPO = DET.TIPO_LICITACAO
                    WHERE L.ID_LICITACAO = $idLicitacao
                ";

$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
    $idLicitacao = $registros['ID_LICITACAO'];
    $dtLicitacao = $registros['DT_LICITACAO'];
    $codLicitacao = $registros['SGL_TIPO'] . ' ' . $registros['COD_LIC'];
    $statusLicitacao = $registros['STATUS_LICITACAO'];
    $objLicitacao = $registros['OBJETO_LICITACAO'];
    $respLicitacao = $registros['PREG_RESP_LICITACAO'];
    $dtAberLicitacao = date('d/m/Y H:i', strtotime($registros['DT_ABER_LICITACAO']));
    $dtIniSessLicitacao = date('d/m/Y H:i', strtotime($registros['DT_INI_SESS_LICITACAO']));
    $modoLicitacao = $registros['MODO_LICITACAO'];
    $criterioLicitacao = $registros['CRITERIO_LICITACAO'];
    $tipoLicitacao = $registros['TIPO_LICITACAO'];
    $regimeLicitacao = $registros['REGIME_LICITACAO'];
    $formaLicitacao = $registros['FORMA_LICITACAO'];
    $vlLicitacao = $registros['VL_LICITACAO'];
    $localLicitacao = $registros['LOCAL_ABER_LICITACAO'];
    $identificadorLicitacao = $registros['IDENTIFICADOR_LICITACAO'];
    $obsLicitacao = $registros['OBS_LICITACAO'];
    $dtExcLicitacao = $registros['DT_EXC_LICITACAO'];
endwhile;

//evito que usuários que não estejam LOGADOS no sistema acessem licitações com status "RASCUNHO" com links diretos
if (($_SESSION['sucesso'] != 1 && $statusLicitacao == 'Rascunho') || ($_SESSION['sucesso'] != 1 && $dtExcLicitacao != null)) {
    $_SESSION['redirecionar'] = 'index.php';
    redirecionar($_SESSION['redirecionar']);
}

if (isset($tipoLicitacao)) {
    $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[TIPO_LICITACAO] WHERE ID_TIPO = $tipoLicitacao";
    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
        $idTipo = $registros['ID_TIPO'];
        $nmTipo = $registros['NM_TIPO'];
    endwhile;

    $tituloLicitacao = $nmTipo . ' - ' . $codLicitacao;
} else {
    $tituloLicitacao = $codLicitacao;
}

// Buscar nome do critério
$nmCriterio = '';
if (isset($criterioLicitacao) && $criterioLicitacao != 0) {
    $queryCriterio = $pdoCAT->query("SELECT NM_CRITERIO FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE ID_CRITERIO = $criterioLicitacao");
    $regCriterio = $queryCriterio->fetch(PDO::FETCH_ASSOC);
    $nmCriterio = $regCriterio['NM_CRITERIO'] ?? '';
}

// Buscar nome da forma
$nmForma = '';
if (isset($formaLicitacao) && $formaLicitacao != 0) {
    $queryForma = $pdoCAT->query("SELECT NM_FORMA FROM [portalcompras].[dbo].[FORMA] WHERE ID_FORMA = $formaLicitacao");
    $regForma = $queryForma->fetch(PDO::FETCH_ASSOC);
    $nmForma = $regForma['NM_FORMA'] ?? '';
}

$_SESSION['redirecionar'] = 'viewLicitacao.php?idLicitacao=' . $idLicitacao;
$login = $_SESSION['login'];
$tela = 'Licitação';
$acao = 'Visualizada';
$idEvento = $idLicitacao;
$queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");

?>

<style>
    /* ============================================
   VIEW LICITAÇÃO - Estilo Rede de Ideias
   ============================================ */

    .page-container {
        padding: 32px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Hero Section */
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
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 28px;
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

    .page-hero-subtitle {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    /* Section Card */
    .section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        margin-bottom: 24px;
        overflow: hidden;
    }

    .section-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 20px 28px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-header ion-icon {
        font-size: 22px;
        color: #60a5fa;
    }

    .section-header h2 {
        color: #ffffff;
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    .section-content {
        padding: 28px;
    }

    /* Form Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .info-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .info-group.col-2 {
        grid-column: span 2;
    }

    .info-group.col-3 {
        grid-column: span 3;
    }

    .info-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-group label ion-icon {
        font-size: 14px;
        color: #94a3b8;
    }

    .info-value {
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        color: #334155;
        min-height: 20px;
    }

    .info-value.textarea {
        min-height: 80px;
        line-height: 1.6;
        white-space: pre-wrap;
    }

    .info-value a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
    }

    .info-value a:hover {
        text-decoration: underline;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        width: fit-content;
    }

    .status-badge.em-andamento {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.suspenso {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.encerrado {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-badge.rascunho {
        background: #f1f5f9;
        color: #64748b;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    /* File Table */
    .file-table-wrapper {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .file-table {
        width: 100%;
        border-collapse: collapse;
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
        border-bottom: 1px solid #e2e8f0;
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }

    .file-table th:hover {
        background: #f1f5f9;
        color: #334155;
    }

    .file-table th.sortable {
        position: relative;
        padding-right: 30px;
    }

    .file-table th.sortable::after {
        content: '⇅';
        position: absolute;
        right: 10px;
        opacity: 0.3;
        font-size: 12px;
    }

    .file-table th.sortable.asc::after {
        content: '↑';
        opacity: 1;
        color: #3b82f6;
    }

    .file-table th.sortable.desc::after {
        content: '↓';
        opacity: 1;
        color: #3b82f6;
    }

    .file-table td {
        padding: 16px 20px;
        font-size: 14px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }

    .file-table tbody tr:last-child td {
        border-bottom: none;
    }

    .file-table tbody tr:hover {
        background: #f8fafc;
    }

    .file-table a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .file-table a:hover {
        text-decoration: underline;
    }

    .file-table a ion-icon {
        font-size: 18px;
        color: #94a3b8;
    }

    .file-date {
        color: #64748b;
        font-size: 13px;
        white-space: nowrap;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #64748b;
    }

    .empty-state ion-icon {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 12px;
    }

    .empty-state p {
        font-size: 14px;
        margin: 0;
    }

    /* Actions */
    .page-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background: #0f172a;
        color: #ffffff;
    }

    .btn-primary:hover {
        background: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-outline {
        background: transparent;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #334155;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .info-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .info-group.col-3 {
            grid-column: span 2;
        }
    }

    @media (max-width: 768px) {
        .page-container {
            padding: 16px;
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

        .page-hero-subtitle {
            justify-content: center;
        }

        .section-header {
            padding: 16px 20px;
        }

        .section-content {
            padding: 20px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .info-group.col-2,
        .info-group.col-3 {
            grid-column: span 1;
        }

        .page-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        /* Tabela de anexos responsiva */
        .file-table th,
        .file-table td {
            padding: 12px 16px;
            font-size: 13px;
        }
    }

    @media (max-width: 600px) {

        /* Empilha colunas em mobile */
        .file-table {
            font-size: 12px;
        }

        .file-table th,
        .file-table td {
            padding: 10px 12px;
        }

        .file-table a {
            font-size: 13px;
        }

        .file-date {
            font-size: 11px;
        }
    }

    /* ============================================
       FILES SECTION - Modern Layout
       ============================================ */

    .files-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .files-count {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #64748b;
    }

    .files-count ion-icon {
        font-size: 18px;
    }

    .files-count strong {
        color: #0f172a;
        font-weight: 600;
    }

    .files-view-toggle {
        display: flex;
        gap: 4px;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 8px;
    }

    .files-view-btn {
        padding: 8px 12px;
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .files-view-btn:hover {
        color: #334155;
    }

    .files-view-btn.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .files-view-btn ion-icon {
        font-size: 16px;
    }

    /* Grid View */
    .files-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .file-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }

    .file-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .file-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .file-card:hover::before {
        transform: scaleX(1);
    }

    .file-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 8px;
    }

    .file-card-icon.pdf {
        background: #fee2e2;
        color: #dc2626;
    }

    .file-card-icon.doc {
        background: #dbeafe;
        color: #2563eb;
    }

    .file-card-icon.xls {
        background: #d1fae5;
        color: #059669;
    }

    .file-card-icon.img {
        background: #fef3c7;
        color: #d97706;
    }

    .file-card-icon.zip {
        background: #e0e7ff;
        color: #6366f1;
    }

    .file-card-icon.default {
        background: #f1f5f9;
        color: #64748b;
    }

    .file-card-info {
        flex: 1;
    }

    .file-card-name {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 4px 0;
        line-height: 1.4;
        word-break: break-word;
    }

    .file-card-date {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #64748b;
    }

    .file-card-date ion-icon {
        font-size: 14px;
    }

    .file-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    /* List View */
    .files-list {
        display: none;
    }

    .files-list.active {
        display: block;
    }

    .files-grid.hidden {
        display: none;
    }
</style>

<div class="page-container">

    <!-- Hero Section -->
    <div class="page-hero">
        <div class="page-hero-content">
            <div class="page-hero-icon">
                <ion-icon name="document-text-outline"></ion-icon>
            </div>
            <div class="page-hero-text">
                <h1><?php echo $tituloLicitacao; ?></h1>
                <div class="page-hero-subtitle">
                    <p>Visualização detalhada da licitação</p>
                    <?php if (isset($statusLicitacao) && $statusLicitacao !== '') {
                        $statusClass = '';
                        switch ($statusLicitacao) {
                            case 'Em Andamento':
                                $statusClass = 'em-andamento';
                                break;
                            case 'Suspenso':
                                $statusClass = 'suspenso';
                                break;
                            case 'Encerrado':
                                $statusClass = 'encerrado';
                                break;
                            case 'Rascunho':
                                $statusClass = 'rascunho';
                                break;
                        }
                        ?>
                        <span class="status-badge <?php echo $statusClass; ?>">
                            <span class="status-dot"></span>
                            <?php echo $statusLicitacao; ?>
                        </span>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Dados Principais -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="document-text-outline"></ion-icon>
            <h2>Dados Principais</h2>
        </div>
        <div class="section-content">
            <div class="info-grid">
                <?php if (isset($nmTipo) && $nmTipo !== '') { ?>
                    <div class="info-group">
                        <label><ion-icon name="pricetag-outline"></ion-icon> Tipo de Contratação</label>
                        <div class="info-value"><?php echo $nmTipo; ?></div>
                    </div>
                <?php } ?>

                <?php if (isset($codLicitacao) && $codLicitacao !== '') { ?>
                    <div class="info-group">
                        <label><ion-icon name="barcode-outline"></ion-icon> Código</label>
                        <div class="info-value"><?php echo $codLicitacao; ?></div>
                    </div>
                <?php } ?>

                <?php if (isset($respLicitacao) && $respLicitacao !== '') { ?>
                    <div class="info-group">
                        <label><ion-icon name="person-outline"></ion-icon> Responsável</label>
                        <div class="info-value"><?php echo $respLicitacao; ?></div>
                    </div>
                <?php } ?>

                <?php if (isset($objLicitacao) && $objLicitacao !== '') { ?>
                    <div class="info-group col-3">
                        <label><ion-icon name="document-outline"></ion-icon> Objeto</label>
                        <div class="info-value textarea"><?php echo trim($objLicitacao); ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Datas e Horários -->
    <?php if ((isset($dtAberLicitacao) && !strpos($dtAberLicitacao, '/1969')) || (isset($dtIniSessLicitacao) && !strpos($dtIniSessLicitacao, '/1969'))) { ?>
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="calendar-outline"></ion-icon>
                <h2>Datas e Horários</h2>
            </div>
            <div class="section-content">
                <div class="info-grid">
                    <?php if (isset($dtAberLicitacao) && !strpos($dtAberLicitacao, '/1969')) { ?>
                        <div class="info-group">
                            <label><ion-icon name="time-outline"></ion-icon> Data e Horário de Abertura</label>
                            <div class="info-value"><?php echo $dtAberLicitacao; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($dtIniSessLicitacao) && !strpos($dtIniSessLicitacao, '/1969')) { ?>
                        <div class="info-group">
                            <label><ion-icon name="play-circle-outline"></ion-icon> Início da Sessão de Disputa</label>
                            <div class="info-value"><?php echo $dtIniSessLicitacao; ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- Detalhes da Licitação -->
    <?php
    $hasDetails = (isset($modoLicitacao) && $modoLicitacao != '0') ||
        (isset($criterioLicitacao) && $criterioLicitacao != '0') ||
        (isset($regimeLicitacao) && $regimeLicitacao !== '') ||
        (isset($formaLicitacao) && $formaLicitacao != 0) ||
        (isset($vlLicitacao) && $vlLicitacao !== '') ||
        (isset($identificadorLicitacao) && $identificadorLicitacao !== '') ||
        (isset($localLicitacao) && $localLicitacao !== '');

    if ($hasDetails) { ?>
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="list-outline"></ion-icon>
                <h2>Detalhes da Licitação</h2>
            </div>
            <div class="section-content">
                <div class="info-grid">
                    <?php if (isset($modoLicitacao) && $modoLicitacao != '0') { ?>
                        <div class="info-group">
                            <label><ion-icon name="options-outline"></ion-icon> Modo de Disputa</label>
                            <div class="info-value"><?php echo $modoLicitacao; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($criterioLicitacao) && $criterioLicitacao != '0' && $nmCriterio !== '') { ?>
                        <div class="info-group">
                            <label><ion-icon name="checkmark-circle-outline"></ion-icon> Critério de Julgamento</label>
                            <div class="info-value"><?php echo $nmCriterio; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($regimeLicitacao) && $regimeLicitacao !== '') { ?>
                        <div class="info-group">
                            <label><ion-icon name="construct-outline"></ion-icon> Regime de Execução</label>
                            <div class="info-value"><?php echo $regimeLicitacao; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($formaLicitacao) && $formaLicitacao != 0 && $nmForma !== '') { ?>
                        <div class="info-group">
                            <label><ion-icon name="layers-outline"></ion-icon> Forma</label>
                            <div class="info-value"><?php echo $nmForma; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($vlLicitacao) && $vlLicitacao !== '') { ?>
                        <div class="info-group">
                            <label><ion-icon name="cash-outline"></ion-icon> Valor Estimado</label>
                            <div class="info-value"><?php echo $vlLicitacao; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($identificadorLicitacao) && $identificadorLicitacao !== '') { ?>
                        <div class="info-group">
                            <label><ion-icon name="finger-print-outline"></ion-icon> Identificador</label>
                            <div class="info-value"><?php echo $identificadorLicitacao; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($localLicitacao) && $localLicitacao !== '') { ?>
                        <div class="info-group col-3">
                            <label><ion-icon name="location-outline"></ion-icon> Local de Abertura</label>
                            <div class="info-value">
                                <a href="<?php echo $localLicitacao; ?>" target="_blank"><?php echo $localLicitacao; ?></a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- Observação -->
    <?php if (isset($obsLicitacao) && trim($obsLicitacao) !== '') { ?>
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="chatbubble-outline"></ion-icon>
                <h2>Observação</h2>
            </div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-group col-3">
                        <div class="info-value textarea"><?php echo trim($obsLicitacao); ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- Anexos -->
    <!-- Anexos -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="attach-outline"></ion-icon>
            <h2>Documentos Anexados</h2>
        </div>
        <div class="section-content">
            <?php
            $directory = "uploads" . '/' . $idLicitacao;
            $isDirectory = is_dir($directory);
            $anexos = array();

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
                                    AND DT_EXC_ANEXO IS NULL
                                )
                                SELECT
                                    ID_LICITACAO,
                                    MAX(CASE WHEN NM_ANEXO like '%_descricao' THEN LINK_ANEXO END) AS NM_ANEXO,
                                    MAX(CASE WHEN NM_ANEXO like '%_arquivo' THEN LINK_ANEXO END) AS LINK_ANEXO
                                FROM RankedAnexos
                                GROUP BY ID_LICITACAO, rn;";
            } else {
                $queryAnexo = "SELECT ID_LICITACAO, NM_ANEXO, LINK_ANEXO FROM ANEXO WHERE ID_LICITACAO = $idLicitacao AND DT_EXC_ANEXO IS NULL";
            }

            $queryAnexo2 = $pdoCAT->query($queryAnexo);

            while ($registros = $queryAnexo2->fetch(PDO::FETCH_ASSOC)) {
                $anexos[] = array(
                    'nmAnexo' => $registros['NM_ANEXO'],
                    'linkAnexo' => $registros['LINK_ANEXO'],
                    'timestamp' => null
                );
            }

            if ($isDirectory) {
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

            // Ordenação padrão por timestamp
            usort($anexos, function ($a, $b) {
                $aTime = isset($a['timestamp']) ? $a['timestamp'] : 0;
                $bTime = isset($b['timestamp']) ? $b['timestamp'] : 0;
                return $bTime - $aTime;
            });

            if (!empty($anexos)) {
                // Helper function para determinar tipo de arquivo
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
                        'txt' => ['icon' => 'document-text', 'class' => 'default'],
                    ];
                    return $icons[$ext] ?? ['icon' => 'document', 'class' => 'default'];
                }

                echo '<div class="files-header">';
                echo '<div class="files-count">';
                echo '<ion-icon name="folder-open-outline"></ion-icon>';
                echo '<span><strong>' . count($anexos) . '</strong> arquivo' . (count($anexos) > 1 ? 's' : '') . '</span>';
                echo '</div>';
                echo '<div class="files-view-toggle">';
                echo '<button class="files-view-btn active" data-view="grid" onclick="toggleFilesView(\'grid\')">';
                echo '<ion-icon name="grid-outline"></ion-icon>';
                echo '<span>Cards</span>';
                echo '</button>';
                echo '<button class="files-view-btn" data-view="list" onclick="toggleFilesView(\'list\')">';
                echo '<ion-icon name="list-outline"></ion-icon>';
                echo '<span>Lista</span>';
                echo '</button>';
                echo '</div>';
                echo '</div>';

                // GRID VIEW
                echo '<div class="files-grid" id="filesGrid">';
                foreach ($anexos as $anexo) {
                    if (!empty($anexo['nmAnexo'])) {
                        $fileInfo = getFileIcon($anexo['nmAnexo']);
                        echo '<a href="' . htmlspecialchars($anexo['linkAnexo']) . '" target="_blank" class="file-card-link">';
                        echo '<div class="file-card" data-timestamp="' . ($anexo['timestamp'] ?? 0) . '" data-nome="' . htmlspecialchars($anexo['nmAnexo']) . '">';
                        echo '<div class="file-card-icon ' . $fileInfo['class'] . '">';
                        echo '<ion-icon name="' . $fileInfo['icon'] . '-outline"></ion-icon>';
                        echo '</div>';
                        echo '<div class="file-card-info">';
                        echo '<p class="file-card-name">' . htmlspecialchars($anexo['nmAnexo']) . '</p>';
                        echo '<div class="file-card-date">';
                        echo '<ion-icon name="calendar-outline"></ion-icon>';
                        if ($anexo['timestamp']) {
                            echo date("d/m/Y H:i", $anexo['timestamp']);
                        } else {
                            echo '<span style="color: #94a3b8;">Não disponível</span>';
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</a>';
                    }
                }
                echo '</div>';

                // LIST VIEW
                echo '<div class="files-list" id="filesList">';
                echo '<div class="file-table-wrapper">';
                echo '<table class="file-table" id="anexosTable">';
                echo '<thead>';
                echo '<tr>';
                echo '<th class="sortable" data-column="nome" data-order="asc">Arquivo</th>';
                echo '<th class="sortable" data-column="data" data-order="desc">Data Inclusão</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($anexos as $anexo) {
                    if (!empty($anexo['nmAnexo'])) {
                        echo '<tr data-timestamp="' . ($anexo['timestamp'] ?? 0) . '" data-nome="' . htmlspecialchars($anexo['nmAnexo']) . '">';
                        echo '<td>';
                        echo '<a href="' . htmlspecialchars($anexo['linkAnexo']) . '" target="_blank">';
                        echo '<ion-icon name="document-outline"></ion-icon> ';
                        echo htmlspecialchars($anexo['nmAnexo']);
                        echo '</a>';
                        echo '</td>';
                        echo '<td class="file-date">';
                        if ($anexo['timestamp']) {
                            echo date("d/m/Y H:i:s", $anexo['timestamp']);
                        } else {
                            echo '<span style="color: #94a3b8;">Não disponível</span>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                }

                echo '</tbody></table>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="empty-state">';
                echo '<ion-icon name="folder-open-outline"></ion-icon>';
                echo '<p>Nenhum documento anexado</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="page-actions">
        <a href="consultarLicitacao.php" class="btn btn-primary">
            <ion-icon name="arrow-back-outline"></ion-icon>
            Voltar para Licitações
        </a>
        <?php
        // Exibe o botão "Editar Licitação" apenas para administradores (idPerfil == 9)
        if (isset($perfil['idPerfil']) && $perfil['idPerfil'] == 9) {
            ?>
            <a href="editarLicitacao.php?idLicitacao=<?php echo $idLicitacao; ?>" class="btn btn-outline">
                <ion-icon name="create-outline"></ion-icon>
                Editar Licitação
            </a>
        <?php } ?>
    </div>
</div>

<script>
    // Sistema de ordenação da tabela de anexos
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('anexosTable');

        if (!table) return;

        const headers = table.querySelectorAll('th.sortable');

        headers.forEach(header => {
            header.addEventListener('click', function () {
                const column = this.getAttribute('data-column');
                const currentOrder = this.getAttribute('data-order');
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

                // Remove classes de ordenação de todos os headers
                headers.forEach(h => {
                    h.classList.remove('asc', 'desc');
                });

                // Adiciona classe de ordenação ao header clicado
                this.classList.add(newOrder);
                this.setAttribute('data-order', newOrder);

                // Ordena a tabela
                sortTable(column, newOrder);
            });
        });

        function sortTable(column, order) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((a, b) => {
                let aValue, bValue;

                if (column === 'nome') {
                    aValue = a.getAttribute('data-nome').toLowerCase();
                    bValue = b.getAttribute('data-nome').toLowerCase();

                    if (order === 'asc') {
                        return aValue.localeCompare(bValue);
                    } else {
                        return bValue.localeCompare(aValue);
                    }
                } else if (column === 'data') {
                    aValue = parseInt(a.getAttribute('data-timestamp')) || 0;
                    bValue = parseInt(b.getAttribute('data-timestamp')) || 0;

                    if (order === 'asc') {
                        return aValue - bValue;
                    } else {
                        return bValue - aValue;
                    }
                }
            });

            // Reordena as linhas no DOM
            rows.forEach(row => tbody.appendChild(row));
        }

        // Marca a coluna "Data Inclusão" como ordenada descendente por padrão
        const dataHeader = table.querySelector('th[data-column="data"]');
        if (dataHeader) {
            dataHeader.classList.add('desc');
        }
    });

    // Toggle entre visualizações de arquivos
    function toggleFilesView(view) {
        const gridView = document.getElementById('filesGrid');
        const listView = document.getElementById('filesList');
        const buttons = document.querySelectorAll('.files-view-btn');

        if (!gridView || !listView) return;

        buttons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            }
        });

        if (view === 'grid') {
            gridView.classList.remove('hidden');
            listView.classList.remove('active');
        } else {
            gridView.classList.add('hidden');
            listView.classList.add('active');
        }

        // Salvar preferência
        localStorage.setItem('filesView', view);
    }

    // Restaurar preferência de visualização
    document.addEventListener('DOMContentLoaded', function () {
        const savedView = localStorage.getItem('filesView');
        if (savedView) {
            toggleFilesView(savedView);
        }
    });
</script>