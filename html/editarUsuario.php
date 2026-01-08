<?php
//editarUsuario.php

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
$nomeBusca = filter_input(INPUT_GET, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$perfilBusca = filter_input(INPUT_GET, 'perfil', FILTER_SANITIZE_NUMBER_INT);

/////////////////////////////////////////////////////////////////////////

$queryAdmin = "SELECT * FROM USUARIO WHERE EMAIL_ADM like '$email'";
$querySelect = $pdoCAT->query($queryAdmin);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $existeUsuario = $registros['EMAIL_ADM'];
    $idUsuario = $registros['ID_ADM'];
endwhile;

if (isset($existeUsuario)) {
    $queryLE = "SELECT U.*, TL.*
    FROM USUARIO U 
    LEFT JOIN PERFIL_USUARIO PU ON U.ID_ADM = PU.ID_USUARIO
    LEFT JOIN TIPO_LICITACAO TL ON TL.ID_TIPO = PU.ID_TIPO_LICITACAO 
    WHERE U.ID_ADM = $idUsuario";

    $querySelectLE = $pdoCAT->query($queryLE);

    $perfilUsuario = array();
    while ($registrosLE = $querySelectLE->fetch(PDO::FETCH_ASSOC)) :
        $nmPerfil = $registrosLE['NM_TIPO'];
        $idPerfil = $registrosLE['ID_TIPO'];
        $nmUsuario = $registrosLE['NM_ADM'];
        $registroPU = array(
            'NM_TIPO' => $nmPerfil,
            'ID_TIPO' => $idPerfil
        );
        $perfilUsuario[] = $registroPU;
    endwhile;
} else {
    $queryInsert = "SELECT [ID]
                    ,[sAMAccountName]
                    ,[initials]
                    ,[department]
                    ,[physicalDeliveryOfficeName]
                    ,[displayName]
                    ,[telephoneNumber]
                    ,[mobile]
                    ,[mail]
                    ,[accountExpires]
                    ,[IsEnabled]
                    ,[objectCategory]
                FROM [ADCache].[dbo].[Users]
                where mail like '$email'";

    $queryInsert2 = $pdoCAT->query($queryInsert);

    while ($registros = $queryInsert2->fetch(PDO::FETCH_ASSOC)) :
        $matricula = $registros['initials'];
        $nmUsuario = $registros['displayName'];
        $mail = $registros['mail'];
        $login = $registros['sAMAccountName'];
    endwhile;

    $loginCriador = $_SESSION['login'];
    $querySelect2 = "INSERT INTO USUARIO VALUES ($matricula, '$nmUsuario', '$mail', GETDATE(), 'A', '$loginCriador', '$login', NULL, NULL)";
    $querySelect = $pdoCAT->query($querySelect2);

    // REGISTRO DE LOG
    $login = $_SESSION['login'];
    $tela = 'Usuário';
    $acao = 'Perfil CRIADO para ' . $nmUsuario;
    $idEvento = $matricula;
    $queryLOG = $pdoCAT->query("INSERT INTO auditoria VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");
}

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

    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 20px;
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
    }

    .form-control {
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

    .form-control:disabled,
    .form-control:read-only {
        background-color: #f8fafc;
        color: #64748b;
        cursor: not-allowed;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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

<!-- CSS for searching -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- JS for searching -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Select2 Múltiplo */
    .select2-container--default .select2-selection--multiple {
        background-color: #fff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
        min-height: 46px !important;
        padding: 4px 8px !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #eff6ff !important;
        border: 1px solid #bfdbfe !important;
        color: #1e40af !important;
        border-radius: 6px !important;
        padding: 4px 10px !important;
        margin: 2px 4px 2px 0 !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #1e40af !important;
        margin-right: 6px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #991b1b !important;
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

    .select2-results__option {
        padding: 10px 16px !important;
    }
</style>

<div class="modern-container">
    <div class="page-hero">
        <div class="page-hero-content">
            <span class="page-hero-icon">✏️</span>
            <div class="page-hero-text">
                <h1>Editar Usuário</h1>
                <p><?php echo htmlspecialchars($nmUsuario); ?></p>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="card-header">
            <h2>
                <i class="fas fa-user-edit"></i>
                Dados do Usuário e Perfis de Acesso
            </h2>
        </div>
        <div class="card-body">
            <form action="bd/usuario/update.php" method="post" id="formFiltrar">
                
                <input type="hidden" id="idUsuario" name="idUsuario" value="<?php echo $idUsuario ?>">
                
                <div class="form-row">
                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-envelope"></i>
                                E-mail
                            </label>
                            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email) ?>" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user"></i>
                                Nome Completo
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($nmUsuario) ?>" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-id-badge"></i>
                                Perfis de Acesso
                            </label>
                            <select name="perfilUsuario[]" id="perfilUsuario" multiple>
                                <?php
                                $querySelect2 = "SELECT * FROM TIPO_LICITACAO WHERE DT_EXC_TIPO IS NULL ORDER BY NM_TIPO";
                                $querySelect = $pdoCAT->query($querySelect2);

                                $queryPerfilUsuario = "SELECT TL.*
                                                        FROM USUARIO U 
                                                        LEFT JOIN PERFIL_USUARIO PU ON U.ID_ADM = PU.ID_USUARIO
                                                        LEFT JOIN TIPO_LICITACAO TL ON TL.ID_TIPO = PU.ID_TIPO_LICITACAO 
                                                        WHERE U.ID_ADM = $idUsuario";
                                $queryPerfisUsuario = $pdoCAT->query($queryPerfilUsuario);

                                $perfisUsuario = array();
                                while ($row = $queryPerfisUsuario->fetch(PDO::FETCH_ASSOC)) {
                                    $perfisUsuario[] = $row["ID_TIPO"];
                                }

                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                                    $valorLE = $registros["ID_TIPO"];
                                    $descricaoLE = $registros["NM_TIPO"];
                                    $selecionadoLE = in_array($valorLE, $perfisUsuario) ? 'selected' : '';
                                    echo "<option value='" . $valorLE . "' $selecionadoLE>" . htmlspecialchars($descricaoLE) . "</option>";
                                endwhile;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-modern btn-clear" id="btnCancelar">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </button>
                    <button type="submit" class="btn-modern btn-save">
                        <i class="fas fa-save"></i>
                        <span>Salvar Alterações</span>
                    </button>
                </div>

            </form>
            
            <!-- Form oculto para voltar com os parâmetros de busca -->
            <form id="formVoltar" action="administracao.php?aba=usuarios" method="post" style="display: none;">
                <input type="hidden" name="nome" value="<?php echo htmlspecialchars($nomeBusca ?? ''); ?>">
                <input type="hidden" name="perfilUsuario" value="<?php echo htmlspecialchars($perfilBusca ?? '0'); ?>">
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#perfilUsuario').select2({
            placeholder: 'Selecione os perfis de acesso',
            allowClear: true
        });
        
        // Botão Cancelar - voltar para busca
        $('#btnCancelar').on('click', function() {
            $('#formVoltar').submit();
        });
    });
</script>