<?php
//editarLicitacao.php

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

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
        $idTipo = $registros['ID_TIPO'];
        $nmTipo = $registros['NM_TIPO'];
        $dtExcTipo = $registros['DT_EXC_TIPO'];
    endwhile;
}
$querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[CRITERIO_LICITACAO] WHERE ID_CRITERIO = $criterioLicitacao";
$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
    $idCriterio = $registros['ID_CRITERIO'];
    $nmCriterio = $registros['NM_CRITERIO'];
    $dtExcCriterio = $registros['DT_EXC_CRITERIO'];
endwhile;

$querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[FORMA] WHERE ID_FORMA = $formaLicitacao";
$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
    $idForma = $registros['ID_FORMA'];
    $nmForma = $registros['NM_FORMA'];
    $dtExcForma = $registros['DT_EXC_FORMA'];
endwhile;
/////////////////////////////////////////////////////////////////////////

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

    /* Estilos para o formulário de licitação */
    .modern-form-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px;
    }

    .page-header {
        margin-bottom: 32px;
    }

    .header-content {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .page-title i {
        font-size: 32px;
        color: #0f172a;
    }

    .page-subtitle {
        font-size: 16px;
        color: #64748b;
        margin: 0;
    }

    .modern-fieldset {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        margin-bottom: 32px;
        overflow: hidden;
    }

    .fieldset-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 24px 32px;
        border-bottom: 1px solid #e2e8f0;
    }

    .fieldset-header h5 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .fieldset-header i {
        font-size: 20px;
    }

    .fieldset-content {
        padding: 32px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-col-3 {
        grid-column: span 3;
    }

    .form-col-2 {
        grid-column: span 2;
    }

    .form-col-6 {
        grid-column: span 6;
    }

    .form-col-4 {
        grid-column: span 4;
    }

    .form-col-12 {
        grid-column: span 12;
    }

    .form-group {
        position: relative;
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-group label i {
        font-size: 16px;
    }

    .form-group label .required-star {
        color: #ef4444;
    }

    .form-control,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.2s ease;
        background-color: #ffffff;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        color: #1e293b;
        box-sizing: border-box;
    }

    .form-control:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
        line-height: 1.6;
    }

    .form-select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }

    .checkbox-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        margin: 0;
        accent-color: #3b82f6;
    }

    .checkbox-wrapper label {
        font-size: 14px;
        color: #475569;
        line-height: 1.6;
        cursor: pointer;
        flex: 1;
        margin: 0;
        text-transform: none;
        letter-spacing: normal;
        font-weight: normal;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 32px;
    }

    .btn-modern {
        padding: 14px 32px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-save {
        background: #0f172a;
        color: white;
    }

    .btn-save:hover {
        background: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-clear {
        background: #ffffff;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    .btn-clear:hover {
        background: #f1f5f9;
        color: #334155;
    }

    /* Dropzone para upload de arquivos */
    .dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .dropzone:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .dropzone.dragover {
        border-color: #3b82f6;
        background: #dbeafe;
    }

    .dropzone i {
        font-size: 48px;
        color: #64748b;
        margin-bottom: 16px;
    }

    /* ============================================
       FILES MANAGEMENT - Modern Layout
       ============================================ */

    .files-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
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

    /* Grid View - Cards */
    .files-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }

    .file-card-edit {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }

    .file-card-edit::before {
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

    .file-card-edit:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .file-card-edit:hover::before {
        transform: scaleX(1);
    }

    .file-card-header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .file-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
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
        min-width: 0;
    }

    .file-card-name {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 8px 0;
        line-height: 1.4;
        word-break: break-word;
    }

    .file-card-name a {
        color: inherit;
        text-decoration: none;
    }

    .file-card-name a:hover {
        color: #3b82f6;
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

    .file-card-actions {
        display: flex;
        gap: 8px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }

    .file-action-btn {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
    }

    .file-action-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #334155;
    }

    .file-action-btn.delete {
        color: #ef4444;
    }

    .file-action-btn.delete:hover {
        background: #fef2f2;
        border-color: #fca5a5;
    }

    .file-action-btn.edit {
        color: #3b82f6;
    }

    .file-action-btn.edit:hover {
        background: #eff6ff;
        border-color: #93c5fd;
    }

    .file-action-btn.save {
        color: #10b981;
    }

    .file-action-btn.save:hover {
        background: #f0fdf4;
        border-color: #86efac;
    }

    .file-action-btn ion-icon {
        font-size: 16px;
    }

    /* List View */
    .files-list {
        display: none;
        margin-top: 20px;
    }

    .files-list.active {
        display: block;
    }

    .files-grid.hidden {
        display: none;
    }

    /* Tabela de arquivos */
    .files-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }

    .files-table thead {
        background: #f8fafc;
    }

    .files-table th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        color: #64748b;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }

    .files-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        font-size: 14px;
    }

    .files-table tbody tr:last-child td {
        border-bottom: none;
    }

    .files-table tr:hover {
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
    }

    .action-icon {
        color: #64748b;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .action-icon:hover {
        color: #0f172a;
        transform: scale(1.1);
    }

    .action-icon.delete {
        color: #ef4444;
    }

    .action-icon.delete:hover {
        color: #dc2626;
    }

    .action-icon.success {
        color: #10b981;
    }

    .action-icon.success:hover {
        color: #059669;
    }

    .edited-name {
        width: 100%;
        padding: 8px 12px;
        border: 2px solid #3b82f6;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        display: none;
    }

    .edited-name:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    /* Sobrescrevendo estilos do Materialize */
    form select {
        display: block !important;
    }

    form .select-wrapper input {
        display: none !important;
    }

    form .caret {
        display: none !important;
    }

    form .dropdown-content {
        display: none !important;
    }

    @media (max-width: 1024px) {

        .form-col-3,
        .form-col-4 {
            grid-column: span 6;
        }
    }

    @media (max-width: 768px) {
        .modern-form-container {
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

        .page-title {
            font-size: 26px;
        }

        .fieldset-content {
            padding: 24px 16px;
        }

        .form-row {
            gap: 16px;
        }

        .form-col-2,
        .form-col-3,
        .form-col-4,
        .form-col-6 {
            grid-column: span 12;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
        }

        .files-table {
            font-size: 12px;
        }

        .files-table th,
        .files-table td {
            padding: 8px;
        }

        .files-grid {
            grid-template-columns: 1fr;
        }

        .file-card-actions {
            flex-direction: column;
        }
    }

    @media (max-width: 480px) {
        .modern-form-container {
            padding: 16px 12px;
        }

        .page-title {
            font-size: 22px;
        }

        .page-title i {
            font-size: 28px;
        }

        .fieldset-header {
            padding: 20px 24px;
        }

        .fieldset-header h5 {
            font-size: 18px;
        }

        .fieldset-content {
            padding: 20px 16px;
        }
    }

    /* Esconde botão salvar por padrão */
    .save-button {
        display: none !important;
    }

    .save-button.show {
        display: flex !important;
    }
</style>

<div class="modern-form-container">
    <!-- Hero Section -->
    <div class="page-hero">
        <div class="page-hero-content">
            <div class="page-hero-icon">
                <ion-icon name="document-text-outline"></ion-icon>
            </div>
            <div class="page-hero-text">
                <h1>Editar Licitação <?php echo $tituloLicitacao; ?></h1>
                <p><?php echo $nmTipo . ' ' . $codLicitacao; ?></p>
            </div>
        </div>
    </div>

    <form action="bd/licitacao/update.php" method="post" enctype="multipart/form-data"
        onsubmit="return validarFormulario()">

        <input type="text" name="idLicitacao" id="idLicitacao" value="<?php echo $idLicitacao ?>" style="display:none"
            readonly required>
        <div id="idLicitacao" data-id="<?php echo $idLicitacao; ?>"></div>

        <!-- Seção: Informações Básicas -->
        <div class="modern-fieldset">
            <div class="fieldset-header">
                <h5>
                    <i class="fas fa-info-circle"></i>
                    Informações Básicas
                </h5>
            </div>
            <div class="fieldset-content">
                <div class="form-row">
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-file-contract"></i>
                                Tipo de Contratação <span class="required-star">*</span>
                            </label>
                            <select name="tipoLicitacao" id="tipoLicitacao" class="form-select" required>
                                <option value='' disabled>Selecione uma opção</option>
                                <?php
                                if ($isAdmin == 1) {
                                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' ORDER BY NM_TIPO";
                                    $querySelect = $pdoCAT->query($querySelect2);
                                } else {
                                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' AND ID_TIPO IN ($idPerfilFinal) ORDER BY NM_TIPO";
                                    $querySelect = $pdoCAT->query($querySelect2);
                                }

                                echo "<option value='" . $idTipo . "' selected>" . $nmTipo . "</option>";
                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                    if ($registros["ID_TIPO"] != $idTipo) {
                                        echo "<option value='" . $registros["ID_TIPO"] . "'>" . $registros["NM_TIPO"] . "</option>";
                                    }
                                endwhile;
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-barcode"></i>
                                Código <span class="required-star">*</span>
                            </label>
                            <input type="text" id="codLicitacao" name="codLicitacao" value="<?php echo $codLicitacao ?>"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-toggle-on"></i>
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

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user-tie"></i>
                                Responsável <span class="required-star">*</span>
                            </label>
                            <input type="text" id="respLicitacao" name="respLicitacao"
                                value="<?php echo $respLicitacao ?>" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col-12">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-file-alt"></i>
                                Objeto <span class="required-star">*</span>
                            </label>
                            <textarea id="objLicitacao" name="objLicitacao" class="form-textarea"
                                required><?php echo $objLicitacao ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-fingerprint"></i>
                                Identificador
                            </label>
                            <input type="text" id="identificadorLicitacao" name="identificadorLicitacao"
                                value="<?php echo $identificadorLicitacao ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-dollar-sign"></i>
                                Valor Estimado
                            </label>
                            <input type="text" id="vlLicitacao" name="vlLicitacao" value="<?php echo $vlLicitacao ?>"
                                class="form-control">
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-map-marker-alt"></i>
                                Local de Abertura
                            </label>
                            <input type="text" id="localLicitacao" name="localLicitacao"
                                value="<?php echo $localLicitacao ?>" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Datas e Horários -->
        <div class="modern-fieldset">
            <div class="fieldset-header">
                <h5>
                    <i class="fas fa-calendar-alt"></i>
                    Datas e Horários
                </h5>
            </div>
            <div class="fieldset-content">
                <?php
                if (date('Y', strtotime($dtAberLicitacao)) == 1969) {
                    $dtAberLicitacao = '';
                    $hrAberLicitacao = '';
                }
                if (date('Y', strtotime($dtIniSessLicitacao)) == 1969) {
                    $dtIniSessLicitacao = '';
                    $hrIniSessLicitacao = '';
                }
                ?>
                <div class="form-row">
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-calendar-day"></i>
                                Data de Abertura
                            </label>
                            <input type="date" id="dtAberLicitacao" name="dtAberLicitacao"
                                value="<?php echo $dtAberLicitacao ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-clock"></i>
                                Horário de Abertura
                            </label>
                            <input type="time" id="hrAberLicitacao" name="hrAberLicitacao"
                                value="<?php echo $hrAberLicitacao ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-calendar-day"></i>
                                Início da Sessão de Disputa
                            </label>
                            <input type="date" id="dtIniSessLicitacao" name="dtIniSessLicitacao"
                                value="<?php echo $dtIniSessLicitacao ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-clock"></i>
                                Horário da Sessão de Disputa
                            </label>
                            <input type="time" id="hrIniSessLicitacao" name="hrIniSessLicitacao"
                                value="<?php echo $hrIniSessLicitacao ?>" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Detalhes da Licitação -->
        <div class="modern-fieldset">
            <div class="fieldset-header">
                <h5>
                    <i class="fas fa-cog"></i>
                    Detalhes da Licitação
                </h5>
            </div>
            <div class="fieldset-content">
                <div class="form-row">
                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-exchange-alt"></i>
                                Modo de Disputa
                            </label>
                            <select name="modoLicitacao" id="modoLicitacao" class="form-select">
                                <option value='0' <?php echo ($modoLicitacao === '0') ? 'selected' : '0'; ?>>Selecione uma
                                    opção</option>
                                <option value='Aberta' <?php echo ($modoLicitacao === 'Aberta') ? 'selected' : ''; ?>>
                                    Aberta</option>
                                <option value='Fechada' <?php echo ($modoLicitacao === 'Fechada') ? 'selected' : ''; ?>>
                                    Fechada</option>
                                <option value='Hibrida' <?php echo ($modoLicitacao === 'Hibrida') ? 'selected' : ''; ?>>
                                    Híbrida</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-balance-scale"></i>
                                Critério de Julgamento
                            </label>
                            <select name="criterioLicitacao" id="criterioLicitacao" class="form-select">
                                <?php
                                $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE DT_EXC_CRITERIO IS NULL";
                                $querySelect = $pdoCAT->query($querySelect2);
                                if ($idCriterio != 0) {
                                    echo "<option value='" . $idCriterio . "' selected>" . $nmCriterio . "</option>";
                                } else {
                                    echo "<option value='0' selected>Selecione uma opção</option>";
                                }
                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                    if ($registros["ID_CRITERIO"] != $idCriterio) {
                                        echo "<option value='" . $registros["ID_CRITERIO"] . "'>" . $registros["NM_CRITERIO"] . "</option>";
                                    }
                                endwhile;
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-list-ul"></i>
                                Forma
                            </label>
                            <select name="formaLicitacao" id="formaLicitacao" class="form-select">
                                <?php
                                $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] WHERE DT_EXC_FORMA IS NULL";
                                $querySelect = $pdoCAT->query($querySelect2);

                                if ($idForma != 0) {
                                    echo "<option value='" . $idForma . "' selected>" . $nmForma . "</option>";
                                } else {
                                    echo "<option value='0' selected>Selecione uma opção</option>";
                                }
                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                    if ($registros["ID_FORMA"] != $idForma) {
                                        echo "<option value='" . $registros["ID_FORMA"] . "'>" . $registros["NM_FORMA"] . "</option>";
                                    }
                                endwhile;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col-12">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-tasks"></i>
                                Regime de Execução
                            </label>
                            <input type="text" id="regimeLicitacao" name="regimeLicitacao"
                                value="<?php echo $regimeLicitacao ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col-12">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-comment"></i>
                                Observação
                            </label>
                            <textarea id="obsLicitacao" name="obsLicitacao"
                                class="form-textarea"><?php echo $obsLicitacao ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Configurações -->
        <div class="modern-fieldset">
            <div class="fieldset-header">
                <h5>
                    <i class="fas fa-sliders-h"></i>
                    Configurações
                </h5>
            </div>
            <div class="fieldset-content">
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="permitirAtualizacao" id="permitirAtualizacao" <?php echo ($permitirAtualizacao == 1) ? 'checked' : ''; ?>>
                    <label for="permitirAtualizacao">
                        Permitir que os usuários sejam lembrados para futuras atualizações da licitação
                    </label>
                </div>
            </div>
        </div>

        <!-- Seção: Anexos -->
        <div class="modern-fieldset">
            <div class="fieldset-header">
                <h5>
                    <i class="fas fa-paperclip"></i>
                    Gerenciar Anexos
                </h5>
            </div>
            <div class="fieldset-content">
                <div class="form-row">
                    <div class="form-col-12">
                        <div id="drop-zone" class="dropzone" onclick="handleClick(event)" ondrop="handleDrop(event)"
                            ondragover="handleDragOver(event)">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p style="margin: 16px 0 0 0; color: #64748b;">Arraste e solte os arquivos aqui ou clique
                                para selecionar</p>
                            <p style="margin: 8px 0 0 0; color: #94a3b8; font-size: 13px;">Apenas arquivos PDF ou ZIP
                            </p>
                        </div>
                    </div>
                </div>

                <div id="filelist">
                    <?php
                    $directory = "uploads" . '/' . $idLicitacao;
                    $anexos = array();

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
                        ];
                        return $icons[$ext] ?? ['icon' => 'document', 'class' => 'default'];
                    }

                    if (is_dir($directory)) {
                        $files = scandir($directory);
                        $files = array_diff($files, array('.', '..'));

                        $anexosDiretorio = array();
                        foreach ($files as $file) {
                            $anexosDiretorio[] = array(
                                'nmAnexo' => $file,
                                'linkAnexo' => $directory . '/' . $file,
                                'timestamp' => filemtime($directory . '/' . $file),
                            );
                        }

                        usort($anexosDiretorio, function ($a, $b) {
                            return $b['timestamp'] - $a['timestamp'];
                        });

                        if (!empty($anexosDiretorio)) {
                            echo '<div class="files-header">';
                            echo '<div class="files-count">';
                            echo '<ion-icon name="folder-open-outline"></ion-icon>';
                            echo '<span><strong>' . count($anexosDiretorio) . '</strong> arquivo' . (count($anexosDiretorio) > 1 ? 's' : '') . '</span>';
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
                            // GRID VIEW
                            echo '<div class="files-grid" id="filesGrid">';
                            foreach ($anexosDiretorio as $index => $anexo) {
                                $fileInfo = getFileIcon($anexo['nmAnexo']);
                                echo '<div class="file-card-edit" id="card_row_' . $index . '">';
                                echo '<div class="file-card-header">';
                                echo '<div class="file-card-icon ' . $fileInfo['class'] . '">';
                                echo '<ion-icon name="' . $fileInfo['icon'] . '-outline"></ion-icon>';
                                echo '</div>';
                                echo '<div class="file-card-info">';
                                echo '<div class="file-card-name nmAnexo">';
                                echo '<a href="' . $directory . '/' . $anexo['nmAnexo'] . '" target="_blank">' . $anexo['nmAnexo'] . '</a>';
                                echo '<input type="text" class="edited-name" value="' . htmlspecialchars($anexo['nmAnexo']) . '">';
                                echo '</div>';
                                echo '<div class="file-card-date">';
                                echo '<ion-icon name="calendar-outline"></ion-icon>';
                                echo date("d/m/y H:i", $anexo['timestamp']);
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="file-card-actions">';
                                echo '<a href="javascript:void(0);" class="file-action-btn edit edit-button" data-id="' . $index . '">';
                                echo '<ion-icon name="create-outline"></ion-icon>';
                                echo '<span>Editar</span>';
                                echo '</a>';
                                // BOTÃO SALVAR ESCONDIDO POR PADRÃO
                                echo '<a href="javascript:void(0);" class="file-action-btn save save-button" data-id="' . $index . '" style="display:none !important;">';
                                echo '<ion-icon name="checkmark-outline"></ion-icon>';
                                echo '<span>Salvar</span>';
                                echo '</a>';
                                echo '<a href="javascript:void(0);" onclick="confirmDelete(\'' . htmlspecialchars($anexo['nmAnexo']) . '\', \'' . $directory . '\', \'' . $idLicitacao . '\')" class="file-action-btn delete">';
                                echo '<ion-icon name="trash-outline"></ion-icon>';
                                echo '<span>Excluir</span>';
                                echo '</a>';
                                echo '</div>';
                                echo '</div>';
                            }
                            echo '</div>';

                            // LIST VIEW
                            echo '<div class="files-list" id="filesList">';
                            echo '<table class="files-table">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>Lista de Documentos</th>';
                            echo '<th>Data Inclusão</th>';
                            echo '<th style="text-align: center;">Excluir</th>';
                            echo '<th style="text-align: center;">Editar</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            foreach ($anexosDiretorio as $index => $anexo) {
                                echo '<tr id="row_' . $index . '">';
                                echo '<td class="nmAnexo">';
                                echo '<a href="' . $directory . '/' . $anexo['nmAnexo'] . '" target="_blank"><ion-icon name="document-outline"></ion-icon> ' . htmlspecialchars($anexo['nmAnexo']) . '</a>';
                                echo '<input type="text" class="edited-name" value="' . htmlspecialchars($anexo['nmAnexo']) . '">';
                                echo '</td>';
                                echo '<td>' . date("d/m/y H:i:s", $anexo['timestamp']) . '</td>';
                                echo '<td style="text-align: center;">';
                                echo '<a href="javascript:void(0);" onclick="confirmDelete(\'' . htmlspecialchars($anexo['nmAnexo']) . '\', \'' . $directory . '\', \'' . $idLicitacao . '\')" title="Excluir Arquivo">';
                                echo '<i class="fas fa-times-circle action-icon delete"></i>';
                                echo '</a></td>';
                                echo '<td style="text-align: center;">';
                                echo '<a href="javascript:void(0);" class="edit-button" data-id="' . $index . '" title="Editar"><i class="fas fa-edit action-icon"></i></a>';
                                // BOTÃO SALVAR ESCONDIDO POR PADRÃO
                                echo '<a href="javascript:void(0);" class="save-button" data-id="' . $index . '" title="Salvar" style="display:none;"><i class="fas fa-check action-icon success"></i></a>';
                                echo '</td>';
                                echo '</tr>';
                            }

                            echo '</tbody></table>';
                            echo '</div>';
                        }
                    }

                    // Anexos do banco de dados
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
                        $anexos[] = array(
                            'nmAnexo' => $registros['NM_ANEXO'],
                            'linkAnexo' => $registros['LINK_ANEXO'],
                            'dtExcAnexo' => $registros['DT_EXC_ANEXO'],
                        );
                    }

                    if (!empty($anexos)) {
                        echo '<table class="files-table" style="margin-top: 24px;">';
                        echo '<thead><tr><th>Anexos do Banco</th><th>Excluído?</th><th style="text-align: center;">Ação</th></tr></thead><tbody>';

                        foreach ($anexos as $anexo) {
                            echo '<tr>';
                            echo '<td><a href="' . $anexo['linkAnexo'] . '" target="_blank"><ion-icon name="document-outline"></ion-icon> ' . $anexo['nmAnexo'] . '</a></td>';
                            echo '<td>' . $anexo['dtExcAnexo'] . '</td>';

                            if (!isset($anexo['dtExcAnexo'])) {
                                echo '<td style="text-align: center;"><a href="javascript:void(0);" onclick="confirmDelete(\'' . $anexo['nmAnexo'] . '\', \'' . $anexo['linkAnexo'] . '\', \'' . $idLicitacao . '\')" title="Excluir Arquivo"><i class="fas fa-times-circle action-icon delete"></i></a></td>';
                            } else {
                                echo '<td style="text-align: center;"><a href="javascript:void(0);" onclick="confirmDelete(\'' . $anexo['nmAnexo'] . '\', \'' . $anexo['linkAnexo'] . '\', \'' . $idLicitacao . '\', \'' . $anexo['dtExcAnexo'] . '\')" title="Restaurar Arquivo"><i class="fas fa-check-circle action-icon success"></i></a></td>';
                            }
                            echo '</tr>';
                        }

                        echo '</tbody></table>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Ações do Formulário -->
        <div class="form-actions">
            <button type="button" class="btn-modern btn-clear" onclick="window.history.back()">
                <i class="fas fa-times"></i>
                <span>Cancelar</span>
            </button>
            <button type="submit" class="btn-modern btn-save">
                <i class="fas fa-save"></i>
                <span>Salvar Alterações</span>
            </button>
        </div>

    </form>
</div>

<script>
    // ============================================
    // INICIALIZAÇÃO
    // ============================================
    $(document).ready(function () {
        // Destruir Materialize Select
        $('form select').formSelect('destroy');

        // Máscara no código da licitação
        $('#codLicitacao').mask('000/0000');
        validarCodLicitacao();

        // ============================================
        // EDITAR ARQUIVO - Grid e Lista
        // ============================================
        $(document).on('click', '.edit-button', function () {
            var rowId = $(this).data('id');

            // Selecionar elementos em ambas as views
            var $rowNmAnexo = $('#row_' + rowId + ' .nmAnexo');
            var $cardNmAnexo = $('#card_row_' + rowId + ' .nmAnexo');

            // Pegar o nome do arquivo
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

            // Salvar nome atual
            $('#row_' + rowId + ', #card_row_' + rowId).data('currentName', currentName);

            // Esconder link e mostrar input em AMBAS as views
            $rowNmAnexo.find('a').hide();
            $rowNmAnexo.find('.edited-name').val(currentName).show();

            $cardNmAnexo.find('a').hide();
            $cardNmAnexo.find('.edited-name').val(currentName).show();

            // Esconder botão editar e mostrar botão salvar em AMBAS as views
            $('#row_' + rowId + ' .edit-button').hide();
            $('#row_' + rowId + ' .save-button').show();

            $('#card_row_' + rowId + ' .edit-button').hide();
            // Quando editar, mostrar botão salvar assim:
            $('#card_row_' + rowId + ' .save-button').css('display', 'flex').show();
            // Selecionar texto até a extensão
            var $editedNameInput = $rowNmAnexo.find('.edited-name');
            if ($editedNameInput.length > 0 && $editedNameInput.is(':visible')) {
                $editedNameInput.focus();
                var dotIndex = currentName.lastIndexOf('.');
                if (dotIndex > 0) {
                    $editedNameInput[0].setSelectionRange(0, dotIndex);
                }
            } else {
                // Se o input da lista não estiver visível, focar no do card
                var $cardInput = $cardNmAnexo.find('.edited-name');
                if ($cardInput.length > 0 && $cardInput.is(':visible')) {
                    $cardInput.focus();
                    var dotIndex = currentName.lastIndexOf('.');
                    if (dotIndex > 0) {
                        $cardInput[0].setSelectionRange(0, dotIndex);
                    }
                }
            }
        });

        // ============================================
        // SALVAR ARQUIVO - Grid e Lista
        // ============================================
        $(document).on('click', '.save-button', function () {
            var rowId = $(this).data('id');
            var directory = '<?php echo $directory; ?>';
            var currentName = $('#row_' + rowId + ', #card_row_' + rowId).data('currentName');

            // Pegar novo nome de qualquer uma das views que estiver visível
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

            console.log('Salvando arquivo:', {
                rowId: rowId,
                currentName: currentName,
                newName: newName,
                directory: directory
            });

            renameFile(rowId, currentName, newName, directory);
        });

        // ============================================
        // CANCELAR EDIÇÃO AO PRESSIONAR ESC
        // ============================================
        $(document).on('keydown', '.edited-name', function (e) {
            if (e.key === 'Escape') {
                var $container = $(this).closest('[id^="row_"], [id^="card_row_"]');
                var rowId = $container.attr('id').replace(/\D/g, '');
                cancelEdit(rowId);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                var $container = $(this).closest('[id^="row_"], [id^="card_row_"]');
                var rowId = $container.attr('id').replace(/\D/g, '');
                $('#row_' + rowId + ' .save-button, #card_row_' + rowId + ' .save-button').first().click();
            }
        });

        // ============================================
        // VALIDAR CARACTERES INVÁLIDOS DURANTE DIGITAÇÃO
        // ============================================
        $(document).on('input', '.edited-name', function () {
            var value = $(this).val();
            // Remove caracteres inválidos para nomes de arquivo
            var sanitized = value.replace(/[<>:"/\\|?*]/g, '');
            if (value !== sanitized) {
                $(this).val(sanitized);
                $(this).css('border-color', '#ef4444');
                setTimeout(() => {
                    $(this).css('border-color', '');
                }, 500);
            }
        });

        // Restaurar preferência de visualização
        const savedView = localStorage.getItem('filesViewEdit');
        if (savedView && (savedView === 'grid' || savedView === 'list')) {
            toggleFilesViewEdit(savedView);
        }
    });

    // ============================================
    // VALIDAÇÃO DO CÓDIGO DA LICITAÇÃO
    // ============================================
    $('#codLicitacao').on('input', function () {
        validarCodLicitacao();
    });

    $('#codLicitacao').on('blur', function () {
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
                success: function (response) {
                    if (response == 1) {
                        $('#codLicitacao').val('');
                        $('#codLicitacao').focus();
                        alert('Código da Licitação já cadastrado.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }
    }

    // ============================================
    // RENOMEAR ARQUIVO
    // ============================================
    function renameFile(rowId, currentName, newName, directory) {
        // Validar nome
        if (!newName || newName.trim() === '') {
            alert('Nome do arquivo não pode ser vazio');
            return;
        }

        // Se o nome não mudou, cancelar edição
        if (newName === currentName) {
            cancelEdit(rowId);
            return;
        }

        console.log('Enviando requisição AJAX:', {
            rowId: rowId,
            currentName: currentName,
            newName: newName,
            directory: directory
        });

        $.ajax({
            url: 'renameFile.php',
            method: 'POST',
            data: {
                rowId: rowId,
                currentName: currentName,
                newName: newName,
                directory: directory
            },
            dataType: 'json',
            success: function (response) {
                console.log('Resposta do servidor:', response);

                if (response.success) {
                    var newFileName = response.newFileName;

                    // Atualizar GRID VIEW
                    var $cardNmAnexo = $('#card_row_' + rowId + ' .nmAnexo');
                    $cardNmAnexo.html('<a href="' + directory + '/' + newFileName + '" target="_blank">' + newFileName + '</a>');
                    $cardNmAnexo.append('<input type="text" class="edited-name" value="' + newFileName + '" style="display:none;">');

                    // Atualizar LIST VIEW
                    var $rowNmAnexo = $('#row_' + rowId + ' .nmAnexo');
                    $rowNmAnexo.html('<a href="' + directory + '/' + newFileName + '" target="_blank"><ion-icon name="document-outline"></ion-icon> ' + newFileName + '</a>');
                    $rowNmAnexo.append('<input type="text" class="edited-name" value="' + newFileName + '" style="display:none;">');

                    // Mostrar botão editar e esconder botão salvar
                    $('#row_' + rowId + ' .edit-button, #card_row_' + rowId + ' .edit-button').show();
                    $('#row_' + rowId + ' .save-button, #card_row_' + rowId + ' .save-button').hide();

                    // Atualizar data-currentName
                    $('#row_' + rowId + ', #card_row_' + rowId).data('currentName', newFileName);

                    // Mensagem de sucesso
                    if (response.message && response.message !== 'Arquivo renomeado com sucesso') {
                        alert(response.message);
                    }
                } else {
                    alert('Erro ao renomear arquivo: ' + (response.message || 'Erro desconhecido'));
                    console.error('Debug info:', response.debug);
                    cancelEdit(rowId);
                }
            },
            error: function (xhr, status, error) {
                console.error('Erro AJAX:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);

                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    alert('Erro: ' + (errorResponse.message || 'Erro ao renomear arquivo'));
                    console.error('Debug info:', errorResponse.debug);
                } catch (e) {
                    alert('Erro ao renomear arquivo. Verifique o console para mais detalhes.');
                }

                cancelEdit(rowId);
            }
        });
    }

    // ============================================
    // CANCELAR EDIÇÃO
    // ============================================
    function cancelEdit(rowId) {
        var $nmAnexoCell = $('#row_' + rowId + ' .nmAnexo, #card_row_' + rowId + ' .nmAnexo');
        $nmAnexoCell.find('a').show();
        $nmAnexoCell.find('.edited-name').hide();
        $('#row_' + rowId + ' .edit-button, #card_row_' + rowId + ' .edit-button').show();
        $('#row_' + rowId + ' .save-button, #card_row_' + rowId + ' .save-button').hide();
    }

    // ============================================
    // TOGGLE VISUALIZAÇÃO DE ARQUIVOS
    // ============================================
    function toggleFilesViewEdit(view) {
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
        localStorage.setItem('filesViewEdit', view);
    }

    // ============================================
    // VALIDAR FORMULÁRIO
    // ============================================
    function validarFormulario() {
        var tipoLicitacao = document.getElementById('tipoLicitacao').value;
        validarCodLicitacao();

        if (tipoLicitacao === '') {
            alert('Por favor, selecione uma opção para o Tipo de Contratação.');
            return false;
        }

        return true;
    }

    // ============================================
    // CONFIRMAR EXCLUSÃO DE ARQUIVO
    // ============================================
    function confirmDelete(file, directory, idLicitacao, dtExcAnexo) {
        if (confirm('Tem certeza que deseja excluir o arquivo?')) {
            $.ajax({
                url: 'excluir_arquivo.php',
                type: 'GET',
                data: {
                    file: file,
                    directory: directory,
                    idLicitacao: idLicitacao,
                    dtExcAnexo: dtExcAnexo
                },
                success: function (response) {
                    $('#filelist').load(window.location.href + ' #filelist');
                },
                error: function () {
                    alert('Erro ao excluir o arquivo.');
                }
            });
        }
    }

    // ============================================
    // UPLOAD DE ARQUIVOS - DRAG AND DROP
    // ============================================
    var idLicitacao = document.getElementById('idLicitacao').value;

    function handleDrop(event) {
        event.preventDefault();
        document.getElementById('drop-zone').classList.remove('dragover');
        var files = event.dataTransfer.files;
        handleFiles(files, idLicitacao);
    }

    function handleClick(event) {
        var inputElement = document.createElement("input");
        inputElement.type = "file";
        inputElement.multiple = true;
        inputElement.addEventListener("change", function () {
            handleFiles(this.files, idLicitacao);
        });
        inputElement.click();
    }

    function handleFiles(files, idLicitacao) {
        if (files.length > 0) {
            var formData = new FormData();

            for (var i = 0; i < files.length; i++) {
                if (files[i].type === 'application/pdf' || files[i].name.endsWith('.zip')) {
                    formData.append('files[]', files[i]);
                } else {
                    alert('O arquivo "' + files[i].name + '" não é um PDF ou ZIP. Por favor, selecione apenas arquivos PDF ou ZIP.');
                    return;
                }
            }

            formData.append('idLicitacao', idLicitacao);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    updateFileList();
                    $('#filelist').load(window.location.href + ' #filelist', function () {
                        // Restaurar visualização após reload
                        const savedView = localStorage.getItem('filesViewEdit');
                        if (savedView) {
                            toggleFilesViewEdit(savedView);
                        }
                    });
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
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var files = response.files;

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

    document.getElementById('drop-zone').addEventListener('dragleave', function (event) {
        event.preventDefault();
        document.getElementById('drop-zone').classList.remove('dragover');
    });
</script>