<?php

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

$idItemMenu = filter_input(INPUT_GET, 'idItemMenu', FILTER_SANITIZE_NUMBER_INT);

$queryAdmin = "SELECT * 
                FROM [PortalCompras].[dbo].ITEMMENU IM
                LEFT JOIN SUBMENU SM ON SM.ID_SUBMENU = IM.ID_SUBMENU
                WHERE IM.ID_ITEMMENU = $idItemMenu";

$querySelect = $pdoCAT->query($queryAdmin);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $nmItemMenu = $registros['NM_ITEMMENU'];
    $linkItemMenu = $registros['LINK_ITEMMENU'];
    $idSubMenu = $registros['ID_SUBMENU'];
    $nmSubMenu = $registros['NM_SUBMENU'];
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

    /* Form Styles */
    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-col-4 {
        grid-column: span 4;
    }

    .form-col-12 {
        grid-column: span 12;
    }

    .form-group {
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
    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.2s ease;
        background-color: #ffffff;
        color: #1e293b;
        box-sizing: border-box;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Select2 Customization */
    .select2-container--default .select2-selection--single {
        height: auto !important;
        padding: 12px 16px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
        font-size: 14px !important;
    }

    .select2-container--default .select2-selection--single:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        padding: 0 !important;
        color: #1e293b !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        right: 8px !important;
    }

    .select2-dropdown {
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }

    .select2-results__option {
        padding: 10px 16px !important;
        font-size: 14px !important;
    }

    .select2-results__option--highlighted {
        background-color: #eff6ff !important;
        color: #1e40af !important;
    }

    /* Form Actions */
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

        .form-row {
            gap: 16px;
        }

        .form-col-4 {
            grid-column: span 12;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
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
    }
</style>

<!-- Inclua o jQuery Mask Plugin -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- CSS for searching -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- JS for searching -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<div class="modern-container">
    <!-- Hero Section -->
    <div class="page-hero">
        <div class="page-hero-content">
            <span class="page-hero-icon">✏️</span>
            <div class="page-hero-text">
                <h1>Editar ItemMenu</h1>
                <p><?php echo htmlspecialchars($nmItemMenu); ?></p>
            </div>
        </div>
    </div>

    <!-- Card de Edição -->
    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-edit"></i>
                Dados do ItemMenu
            </h2>
        </div>
        <div class="card-body">
            <form action="bd/itemmenu/update.php" method="post" id="formFiltrar">
                
                <input style="display:none" type="text" id="idItemMenu" name="idItemMenu" value="<?php echo $idItemMenu ?>">
                
                <div class="form-row">
                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-tag"></i>
                                Nome do ItemMenu <span class="required-star">*</span>
                            </label>
                            <input type="text" id="nmItemMenu" name="nmItemMenu" value="<?php echo htmlspecialchars($nmItemMenu) ?>" class="form-control" required autofocus>
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-link"></i>
                                Link do ItemMenu
                            </label>
                            <input type="text" id="linkItemMenu" name="linkItemMenu" value="<?php echo htmlspecialchars($linkItemMenu) ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-layer-group"></i>
                                SubMenu Relacionado <span class="required-star">*</span>
                            </label>
                            <select name="idSubMenu" id="idSubMenu" class="form-select" required>
                                <option value='' disabled>Selecione uma opção</option>
                                <?php
                                $querySelect2 = "SELECT * FROM portalcompras.dbo.[submenu] WHERE DT_EXC_SUBMENU IS NULL ORDER BY NM_SUBMENU";
                                $querySelect = $pdoCAT->query($querySelect2);

                                echo "<option value='" . $idSubMenu . "' selected>" . htmlspecialchars($nmSubMenu) . "</option>";
                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                                    if ($registros["ID_SUBMENU"] != $idSubMenu) {
                                        echo "<option value='" . $registros["ID_SUBMENU"] . "'>" . htmlspecialchars($registros["NM_SUBMENU"]) . "</option>";
                                    }
                                endwhile;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

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
    </div>
</div>

<script>
    function exibirAlertaIdMenu() {
        alert("Por favor, preencha o campo 'SubMenu relacionado'");
    }

    $(document).ready(function() {
        $('#idSubMenu').select2({
            width: '100%',
            placeholder: 'Selecione um submenu',
            allowClear: true
        });
    });
</script>