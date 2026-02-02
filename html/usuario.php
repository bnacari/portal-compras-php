<?php
//administracao.php?aba=usuarios

// // session_start();
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

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
        margin-bottom: 32px;
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

    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-col-6 {
        grid-column: span 6;
    }

    .form-col-5 {
        grid-column: span 5;
    }

    .form-col-1 {
        grid-column: span 1;
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

    .btn-search {
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
        background: #0f172a;
        color: white;
        width: 100%;
        height: 46px;
        margin-top: 30px;
    }

    .btn-search:hover {
        background: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

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

        .form-col-6,
        .form-col-5,
        .form-col-1 {
            grid-column: span 12;
        }

        .btn-search {
            margin-top: 0;
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

<!-- CSS for searching -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- JS for searching -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Forçar estilos do Select2 */
    span.select2-container {
        z-index: 10 !important;
    }

    .select2-container--default .select2-selection--single {
        background-color: #fff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
        height: 46px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1e293b !important;
        line-height: 44px !important;
        padding-left: 16px !important;
        padding-right: 40px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 10px !important;
    }

    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6 !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    .select2-dropdown {
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
        margin-top: 4px !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #eff6ff !important;
        color: #1e40af !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #dbeafe !important;
        color: #1e40af !important;
    }

    .select2-results__option {
        padding: 10px 16px !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 8px 12px !important;
    }
</style>

<div class="modern-container">
    <div class="page-hero">
        <div class="page-hero-content">
            <div class="page-hero-icon">
                <ion-icon name="people-outline"></ion-icon>
            </div>
            <div class="page-hero-text">
                <h1>Administrar Usuários</h1>
                <p>Gerencie os usuários e seus perfis de acesso ao sistema</p>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-search"></i>
                Buscar Usuários
            </h2>
        </div>
        <div class="card-body">
            <form action="administracao.php?aba=usuarios" method="post" id="formFiltrar">
                <div class="form-row">
                    <div class="form-col-6">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user"></i>
                                Nome ou Login do Usuário
                            </label>
                            <input type="text" name="nome" id="nome" class="form-control" maxlength="100"
                                style="text-transform: uppercase" autofocus placeholder="Digite o nome ou login"
                                value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-col-5">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-id-badge"></i>
                                Perfil de Acesso
                            </label>
                            <select name="perfilUsuario" id="perfilUsuario" class="form-select">
                                <option value='0' <?php echo (!isset($_POST['perfilUsuario']) || $_POST['perfilUsuario'] == '0') ? 'selected' : ''; ?>>Todos os perfis</option>
                                <?php
                                $querySelect2 = "SELECT * FROM TIPO_LICITACAO WHERE DT_EXC_TIPO IS NULL ORDER BY NM_TIPO";
                                $querySelect = $pdoCAT->query($querySelect2);
                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                    $selected = (isset($_POST['perfilUsuario']) && $_POST['perfilUsuario'] == $registros["ID_TIPO"]) ? 'selected' : '';
                                    echo "<option value='" . $registros["ID_TIPO"] . "' $selected>" . $registros["NM_TIPO"] . "</option>";
                                endwhile;
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-col-1">
                        <button type="submit" class="btn-search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-users"></i>
                Usuários Encontrados
            </h2>
        </div>
        <div class="card-body">
            <div class="content3">
                <?php include_once 'bd/usuario/read.php'; ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Inicializar Select2
        $('#perfilUsuario').select2({
            placeholder: 'Todos os perfis',
            allowClear: true
        });

        // Se tiver valor selecionado, trigger change para atualizar visual
        <?php if (isset($_POST['perfilUsuario']) && $_POST['perfilUsuario'] != '0'): ?>
            $('#perfilUsuario').trigger('change');
        <?php endif; ?>
    });
</script>