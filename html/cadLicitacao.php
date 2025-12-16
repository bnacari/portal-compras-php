<?php
//cadLicitacao.php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectPerfil.php');

foreach ($_SESSION['perfil'] as $perfil) {
    $idPerfil[] = (int)$perfil['idPerfil']; // Força conversão para inteiro (segurança)

    if ($perfil['idPerfil'] == 9) {
        $isAdmin = 1;
    }
}

// Remove duplicatas e garante que só temos números inteiros
$idPerfil = array_unique($idPerfil);
$idPerfil = array_map('intval', $idPerfil);
$idPerfilFinal = implode(',', $idPerfil);

// Debug - descomente para verificar perfis disponíveis
// echo "<!-- Perfis do usuário: $idPerfilFinal | Admin: " . (isset($isAdmin) ? 'Sim' : 'Não') . " -->";

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

    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="modern-form-container">
    <!-- Hero Section -->
    <div class="page-hero">
        <div class="page-hero-content">
            <span class="page-hero-icon">💼</span>
            <div class="page-hero-text">
                <h1>Nova Licitação</h1>
                <p>Cadastre uma nova licitação no sistema de compras</p>
            </div>
        </div>
    </div>

    <form action="bd/licitacao/create.php" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario()">
        
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
                                <option value=''>Selecione uma opção</option>
                                <?php
                                // ADMINISTRADOR vê todos os tipos
                                if ($isAdmin == 1) {
                                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] 
                                                    WHERE DT_EXC_TIPO IS NULL 
                                                    AND NM_TIPO NOT LIKE 'ADMINISTRADOR' 
                                                    ORDER BY NM_TIPO";
                                    $querySelect = $pdoCAT->query($querySelect2);
                                } 
                                // USUÁRIO COMUM vê apenas os tipos de seus perfis
                                else {
                                    if (!empty($idPerfilFinal)) {
                                        $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] 
                                                        WHERE DT_EXC_TIPO IS NULL 
                                                        AND NM_TIPO NOT LIKE 'ADMINISTRADOR' 
                                                        AND ID_TIPO IN ($idPerfilFinal) 
                                                        ORDER BY NM_TIPO";
                                        $querySelect = $pdoCAT->query($querySelect2);
                                    } else {
                                        // Se não tem perfis, não executa query
                                        $querySelect = null;
                                    }
                                }
                                
                                // Exibe as opções
                                if ($querySelect) {
                                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                                        echo "<option value='" . $registros["ID_TIPO"] . "'>" . 
                                             htmlspecialchars($registros["NM_TIPO"]) . " (" . 
                                             htmlspecialchars($registros["SGL_TIPO"]) . ")" . 
                                             "</option>";
                                    endwhile;
                                } else {
                                    // Sem perfis atribuídos
                                    echo "<option value='' disabled>Nenhum tipo disponível para seu perfil</option>";
                                }
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
                            <input type="text" id="codLicitacao" name="codLicitacao" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-toggle-on"></i>
                                Status <span class="required-star">*</span>
                            </label>
                            <select name="statusLicitacao" id="statusLicitacao" class="form-select" required>
                                <option value='Rascunho' selected>Rascunho</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user-tie"></i>
                                Responsável <span class="required-star">*</span>
                            </label>
                            <input type="text" id="respLicitacao" name="respLicitacao" class="form-control" required>
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
                            <textarea id="objLicitacao" name="objLicitacao" class="form-textarea" required></textarea>
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
                            <input type="text" id="identificadorLicitacao" name="identificadorLicitacao" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-dollar-sign"></i>
                                Valor Estimado
                            </label>
                            <input type="text" id="vlLicitacao" name="vlLicitacao" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-map-marker-alt"></i>
                                Local de Abertura
                            </label>
                            <input type="text" id="localLicitacao" name="localLicitacao" class="form-control">
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
                <div class="form-row">
                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-calendar-day"></i>
                                Data de Abertura
                            </label>
                            <input type="date" id="dtAberLicitacao" name="dtAberLicitacao" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-clock"></i>
                                Horário de Abertura
                            </label>
                            <input type="time" id="hrAberLicitacao" name="hrAberLicitacao" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-calendar-day"></i>
                                Início da Sessão de Disputa
                            </label>
                            <input type="date" id="dtIniSessLicitacao" name="dtIniSessLicitacao" class="form-control">
                        </div>
                    </div>

                    <div class="form-col-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-clock"></i>
                                Horário da Sessão de Disputa
                            </label>
                            <input type="time" id="hrIniSessLicitacao" name="hrIniSessLicitacao" class="form-control">
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
                                <option value='0' selected>Selecione uma opção</option>
                                <option value='Aberta'>Aberta</option>
                                <option value='Fechada'>Fechada</option>
                                <option value='Hibrida'>Híbrida</option>
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
                                <option value='0' selected>Selecione uma opção</option>
                                <?php
                                $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE DT_EXC_CRITERIO IS NULL ORDER BY NM_CRITERIO";
                                $querySelect = $pdoCAT->query($querySelect2);
                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                                    echo "<option value='" . $registros["ID_CRITERIO"] . "'>" . htmlspecialchars($registros["NM_CRITERIO"]) . "</option>";
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
                                <option value='0' selected>Selecione uma opção</option>
                                <?php
                                $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] WHERE DT_EXC_FORMA IS NULL ORDER BY NM_FORMA";
                                $querySelect = $pdoCAT->query($querySelect2);
                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                                    echo "<option value='" . $registros["ID_FORMA"] . "'>" . htmlspecialchars($registros["NM_FORMA"]) . "</option>";
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
                            <input type="text" id="regimeLicitacao" name="regimeLicitacao" class="form-control">
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
                            <textarea id="obsLicitacao" name="obsLicitacao" class="form-textarea"></textarea>
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
                    <input type="checkbox" name="permitirAtualizacao" id="permitirAtualizacao">
                    <label for="permitirAtualizacao">
                        Permitir que os usuários sejam lembrados para futuras atualizações da licitação
                    </label>
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
                <i class="fas fa-check"></i>
                <span>Salvar Licitação</span>
            </button>
        </div>

    </form>
</div>

<script>
    $(document).ready(function() {
        // Desabilita a inicialização automática do Materialize para selects neste formulário
        $('form select').formSelect('destroy');
        
        $('#codLicitacao').mask('000/0000');
    });

    // Evento 'input' no campo 'codLicitacao'
    $('#codLicitacao').on('input', function() {
        validarCodLicitacao();
    });

    // Evento 'change' no dropdown 'tipoLicitacao'
    $('#tipoLicitacao').on('change', function() {
        // Verifica se o campo 'codLicitacao' está preenchido
        if ($('#codLicitacao').val().length === 8) {
            validarCodLicitacao();
        }
    });

    function validarCodLicitacao() {
        var codLicitacao = $('#codLicitacao').val();
        var tipoLicitacao = $('#tipoLicitacao').val();

        if (codLicitacao.length == 8) {
            // Faz a requisição AJAX
            $.ajax({
                url: 'verificaCodLicitacao.php',
                method: 'GET',
                data: {
                    codLicitacao: codLicitacao,
                    tipoLicitacao: tipoLicitacao
                },
                dataType: 'json',
                success: function(response) {
                    // console.log('Resposta do servidor:', response);
                    if (response == 1) {
                        $('#codLicitacao').val('');
                        $('#codLicitacao').focus(); // Mudar o foco para o campo codLicitacao
                        alert('Código da Licitação já cadastrado.');
                    } else {
                        // Código da Licitação não cadastrado, continuar com outras ações se necessário
                    }
                },
                error: function(xhr, status, error) {
                    // Trate os erros de requisição AJAX, se necessário
                    console.error(error);
                }
            });
        }
    }

    function validarFormulario() {
        var tipoLicitacao = document.getElementById('tipoLicitacao').value;
        var statusLicitacao = document.getElementById('statusLicitacao').value;

        if (tipoLicitacao === '') {
            alert('Por favor, selecione uma opção para o "Tipo de Contratação".');
            return false; // Evita o envio do formulário se a validação falhar
        }

        if (statusLicitacao === '') {
            alert('Por favor, selecione uma opção para o "Status".');
            return false; // Evita o envio do formulário se a validação falhar
        }

        // Continue com o envio do formulário se a validação passar
        return true;
    }

    // RESPONSÁVEL PELA INCLUSÃO / EXCLUSÃO DE ANEXOS
    document.addEventListener('DOMContentLoaded', function() {
        const anexos = document.getElementById('anexos');
        if (anexos) {
            const fileTableBody = document.getElementById('tableBody');

            anexos.addEventListener('change', handleFileSelect);

            function handleFileSelect(event) {
                const files = event.target.files;

                for (const file of files) {
                    addFileToTable(file);
                }
            }

            function addFileToTable(file) {
                const row = fileTableBody.insertRow();
                const cell1 = row.insertCell(0);
                const cell2 = row.insertCell(1);

                cell1.textContent = file.name;

                const deleteButton = document.createElement('i');
                deleteButton.className = 'material-icons delete-icon';
                deleteButton.textContent = 'remove';
                deleteButton.style.cursor = 'pointer';
                deleteButton.addEventListener('click', function() {
                    row.remove();
                });

                cell2.appendChild(deleteButton);
            }
        }
    });
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function perfilTerceiros() {
        var respSolicitacao = document.getElementById("respSolicitacao").value;
        var exibirInstituicao = document.querySelector("#respSolicitacao option:checked").getAttribute("data-exibir-instituicao");
        var esconde = document.getElementById("esconde");
        var escondeSerieVisita = document.getElementById("escondeSerieVisita");
        var escondeCursoInstituicao = document.getElementById("escondeCursoInstituicao");

        var nmInstituicaoInput = document.getElementById("nmInstituicao");
        var cidadeInstituicaoInput = document.getElementById("cidadeInstituicao");
        var serieVisitaInput = document.getElementById("serieVisita");
        var cursoInstituicaoInput = document.getElementById("cursoInstituicao");

        if (exibirInstituicao == 1) {
            escondeSerieVisita.style.display = "none";
            escondeCursoInstituicao.style.display = "none";
            esconde.style.display = "block";
            nmInstituicaoInput.required = true;
            cidadeInstituicaoInput.required = true;
            serieVisitaInput.required = false;
            if (respSolicitacao == 6) {
                escondeSerieVisita.style.display = "block";
                serieVisitaInput.required = true;
            }
            if (respSolicitacao == 11) {
                escondeCursoInstituicao.style.display = "block";
                cursoInstituicaoInput.required = true;
            }
        } else {
            esconde.style.display = "none";
            escondeSerieVisita.style.display = "none";
            escondeCursoInstituicao.style.display = "none";
            nmInstituicaoInput.required = false;
            cidadeInstituicaoInput.required = false;
            serieVisitaInput.required = false;
            cursoInstituicaoInput.required = false;
        }
    }
</script>