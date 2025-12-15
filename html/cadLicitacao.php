<?php
//cadLicitacao.php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectPerfil.php');

foreach ($_SESSION['perfil'] as $perfil) {
    $idPerfil[] = $perfil['idPerfil'];

    if ($perfil['idPerfil'] == 9) {
        $isAdmin = 1;
    }
}

$idPerfilFinal = implode(',', $idPerfil);

?>

<style>
    /* Reset e correções para Materialize */
    .licitacao-form * {
        box-sizing: border-box;
    }

    .licitacao-form {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 30px;
        color: white;
    }

    .hero-icon {
        font-size: 32px;
        margin-bottom: 12px;
        opacity: 0.9;
    }

    .hero-title {
        font-size: 32px !important;
        font-weight: 600 !important;
        margin: 0 0 8px 0 !important;
        color: white !important;
    }

    .hero-subtitle {
        font-size: 16px;
        margin: 0;
        opacity: 0.9;
    }

    /* Section Cards */
    .section-card {
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .section-header {
        background: #131B2E;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-header i {
        font-size: 20px;
        color: white;
    }

    .section-header h2 {
        font-size: 16px !important;
        font-weight: 600 !important;
        margin: 0 !important;
        color: white !important;
        text-transform: none !important;
    }

    .section-body {
        padding: 32px 24px;
    }

    /* Form Grid */
    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .col-12 {
        grid-column: span 12;
    }

    .col-6 {
        grid-column: span 6;
    }

    .col-4 {
        grid-column: span 4;
    }

    .col-3 {
        grid-column: span 3;
    }

    .col-2 {
        grid-column: span 2;
    }

    /* Input Groups */
    .input-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .input-label {
        font-size: 13px;
        font-weight: 600;
        color: #5a6c7d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .input-label i {
        font-size: 16px;
    }

    .input-label .required-mark {
        color: #e74c3c;
    }

    /* Inputs - Sobrescrevendo Materialize */
    .licitacao-form input[type="text"],
    .licitacao-form input[type="date"],
    .licitacao-form input[type="time"],
    .licitacao-form textarea,
    .licitacao-form select {
        height: auto !important;
        padding: 12px 16px !important;
        border: 1px solid #e0e6ed !important;
        border-radius: 8px !important;
        font-size: 15px !important;
        color: #131B2E !important;
        background: white !important;
        margin: 0 !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .licitacao-form input[type="text"]:focus,
    .licitacao-form input[type="date"]:focus,
    .licitacao-form input[type="time"]:focus,
    .licitacao-form textarea:focus,
    .licitacao-form select:focus {
        border-color: #3498db !important;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1) !important;
        outline: none !important;
    }

    .licitacao-form textarea {
        min-height: 100px !important;
        resize: vertical !important;
        line-height: 1.6 !important;
        font-family: inherit !important;
    }

    /* Select - Removendo estilo Materialize */
    .licitacao-form select {
        display: block !important;
        cursor: pointer !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%235a6c7d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 16px center !important;
        padding-right: 44px !important;
    }

    .licitacao-form .select-wrapper {
        position: relative;
    }

    .licitacao-form .select-wrapper input {
        display: none !important;
    }

    .licitacao-form .caret {
        display: none !important;
    }

    .licitacao-form .dropdown-content {
        display: none !important;
    }

    /* Checkbox */
    .checkbox-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e0e6ed;
    }

    .licitacao-form input[type="checkbox"] {
        width: 20px !important;
        height: 20px !important;
        cursor: pointer !important;
        margin: 0 !important;
        opacity: 1 !important;
        position: relative !important;
        pointer-events: all !important;
    }

    .checkbox-wrapper label {
        font-size: 14px !important;
        color: #5a6c7d !important;
        line-height: 1.6 !important;
        cursor: pointer !important;
        flex: 1;
        margin: 0 !important;
    }

    /* Botões */
    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 24px;
    }

    .licitacao-form .btn {
        height: auto !important;
        padding: 14px 32px !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        border-radius: 8px !important;
        text-transform: none !important;
        box-shadow: none !important;
        line-height: normal !important;
        transition: all 0.2s ease !important;
    }

    .licitacao-form .btn-primary {
        background: #3498db !important;
    }

    .licitacao-form .btn-primary:hover {
        background: #2980b9 !important;
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3) !important;
    }

    .licitacao-form .btn-secondary {
        background: #95a5a6 !important;
    }

    .licitacao-form .btn-secondary:hover {
        background: #7f8c8d !important;
    }

    /* Responsividade */
    @media (max-width: 1024px) {
        .col-4 {
            grid-column: span 6;
        }
    }

    @media (max-width: 768px) {
        .licitacao-form {
            padding: 16px;
        }

        .hero-section {
            padding: 32px 24px;
        }

        .hero-title {
            font-size: 26px !important;
        }

        .section-body {
            padding: 24px 16px;
        }

        .form-row {
            gap: 16px;
        }

        .col-2,
        .col-3,
        .col-4,
        .col-6 {
            grid-column: span 12;
        }

        .form-actions {
            flex-direction: column;
        }

        .licitacao-form .btn {
            width: 100% !important;
        }
    }

    @media (max-width: 480px) {
        .licitacao-form {
            padding: 12px;
        }

        .hero-section {
            padding: 24px 16px;
            border-radius: 12px;
        }

        .hero-title {
            font-size: 22px !important;
        }

        .section-card {
            border-radius: 8px;
        }

        .section-body {
            padding: 20px 16px;
        }

        .licitacao-form input[type="text"],
        .licitacao-form input[type="date"],
        .licitacao-form input[type="time"],
        .licitacao-form textarea,
        .licitacao-form select {
            font-size: 16px !important;
            /* iOS zoom prevention */
        }
    }
</style>

<div class="licitacao-form">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-icon">💼</div>
        <h1 class="hero-title">Nova Licitação</h1>
        <p class="hero-subtitle">Cadastre uma nova licitação no sistema de compras</p>
    </div>

    <form action="bd/licitacao/create.php" method="post" enctype="multipart/form-data"
        onsubmit="return validarFormulario()">

        <!-- Seção: Informações Básicas -->
        <div class="section-card">
            <div class="section-header">
                <i class="material-icons">info</i>
                <h2>Informações Básicas</h2>
            </div>
            <div class="section-body">
                <div class="form-row">
                    <div class="input-group col-3">
                        <label class="input-label">
                            <i class="material-icons">category</i>
                            Tipo de Contratação <span class="required-mark">*</span>
                        </label>
                        <select name="tipoLicitacao" id="tipoLicitacao" required>
                            <option value=''>Selecione uma opção</option>
                            <?php
                            if (isset($isAdmin)) {
                                $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' ORDER BY NM_TIPO";
                                $querySelect = $pdoCAT->query($querySelect2);
                            } else {
                                $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' AND ID_TIPO IN ($idPerfilFinal) ORDER BY NM_TIPO";
                                $querySelect = $pdoCAT->query($querySelect2);
                            }
                            while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                echo "<option value='" . $registros["ID_TIPO"] . "'>" . $registros["NM_TIPO"] . " (" . $registros["SGL_TIPO"] . ")" . "</option>";
                            endwhile;
                            ?>
                        </select>
                    </div>

                    <div class="input-group col-3">
                        <label class="input-label">
                            <i class="material-icons">tag</i>
                            Código <span class="required-mark">*</span>
                        </label>
                        <input type="text" id="codLicitacao" name="codLicitacao" required>
                    </div>

                    <div class="input-group col-3">
                        <label class="input-label">
                            <i class="material-icons">toggle_on</i>
                            Status <span class="required-mark">*</span>
                        </label>
                        <select name="statusLicitacao" id="statusLicitacao" required>
                            <option value='Rascunho' selected>Rascunho</option>
                        </select>
                    </div>

                    <div class="input-group col-3">
                        <label class="input-label">
                            <i class="material-icons">person</i>
                            Responsável <span class="required-mark">*</span>
                        </label>
                        <input type="text" id="respLicitacao" name="respLicitacao" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group col-12">
                        <label class="input-label">
                            <i class="material-icons">description</i>
                            Objeto <span class="required-mark">*</span>
                        </label>
                        <textarea id="objLicitacao" name="objLicitacao" required></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group col-4">
                        <label class="input-label">
                            <i class="material-icons">fingerprint</i>
                            Identificador
                        </label>
                        <input type="text" id="identificadorLicitacao" name="identificadorLicitacao">
                    </div>

                    <div class="input-group col-4">
                        <label class="input-label">
                            <i class="material-icons">attach_money</i>
                            Valor Estimado
                        </label>
                        <input type="text" id="vlLicitacao" name="vlLicitacao">
                    </div>

                    <div class="input-group col-4">
                        <label class="input-label">
                            <i class="material-icons">location_on</i>
                            Local de Abertura
                        </label>
                        <input type="text" id="localLicitacao" name="localLicitacao">
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Datas e Horários -->
        <div class="section-card">
            <div class="section-header">
                <i class="material-icons">event</i>
                <h2>Datas e Horários</h2>
            </div>
            <div class="section-body">
                <div class="form-row">
                    <div class="input-group col-3">
                        <label class="input-label">
                            <i class="material-icons">calendar_today</i>
                            Data de Abertura
                        </label>
                        <input type="date" id="dtAberLicitacao" name="dtAberLicitacao">
                    </div>

                    <div class="input-group col-3">
                        <label class="input-label">
                            <i class="material-icons">schedule</i>
                            Horário de Abertura
                        </label>
                        <input type="time" id="hrAberLicitacao" name="hrAberLicitacao">
                    </div>

                    <div class="input-group col-3">
                        <label class="input-label">
                            <i class="material-icons">calendar_today</i>
                            Início da Sessão de Disputa
                        </label>
                        <input type="date" id="dtIniSessLicitacao" name="dtIniSessLicitacao">
                    </div>

                    <div class="input-group col-3">
                        <label class="input-label">
                            <i class="material-icons">schedule</i>
                            Horário da Sessão de Disputa
                        </label>
                        <input type="time" id="hrIniSessLicitacao" name="hrIniSessLicitacao">
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Detalhes da Licitação -->
        <div class="section-card">
            <div class="section-header">
                <i class="material-icons">settings</i>
                <h2>Detalhes da Licitação</h2>
            </div>
            <div class="section-body">
                <div class="form-row">
                    <div class="input-group col-4">
                        <label class="input-label">
                            <i class="material-icons">style</i>
                            Modo de Disputa
                        </label>
                        <select name="modoLicitacao" id="modoLicitacao">
                            <option value='0' selected>Selecione uma opção</option>
                            <option value='Aberta'>Aberta</option>
                            <option value='Fechada'>Fechada</option>
                            <option value='Hibrida'>Híbrida</option>
                        </select>
                    </div>

                    <div class="input-group col-4">
                        <label class="input-label">
                            <i class="material-icons">gavel</i>
                            Critério de Julgamento
                        </label>
                        <select name="criterioLicitacao" id="criterioLicitacao">
                            <option value='0' selected>Selecione uma opção</option>
                            <?php
                            $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE DT_EXC_CRITERIO IS NULL ORDER BY NM_CRITERIO";
                            $querySelect = $pdoCAT->query($querySelect2);
                            while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                echo "<option value='" . $registros["ID_CRITERIO"] . "'>" . $registros["NM_CRITERIO"] . "</option>";
                            endwhile;
                            ?>
                        </select>
                    </div>

                    <div class="input-group col-4">
                        <label class="input-label">
                            <i class="material-icons">format_list_bulleted</i>
                            Forma
                        </label>
                        <select name="formaLicitacao" id="formaLicitacao">
                            <option value='0' selected>Selecione uma opção</option>
                            <?php
                            $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] WHERE DT_EXC_FORMA IS NULL ORDER BY NM_FORMA";
                            $querySelect = $pdoCAT->query($querySelect2);
                            while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
                                echo "<option value='" . $registros["ID_FORMA"] . "'>" . $registros["NM_FORMA"] . "</option>";
                            endwhile;
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group col-12">
                        <label class="input-label">
                            <i class="material-icons">assignment</i>
                            Regime de Execução
                        </label>
                        <input type="text" id="regimeLicitacao" name="regimeLicitacao">
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group col-12">
                        <label class="input-label">
                            <i class="material-icons">comment</i>
                            Observação
                        </label>
                        <textarea id="obsLicitacao" name="obsLicitacao"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Configurações -->
        <div class="section-card">
            <div class="section-header">
                <i class="material-icons">tune</i>
                <h2>Configurações</h2>
            </div>
            <div class="section-body">
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
            <button type="submit" class="btn btn-primary">Salvar Licitação</button>
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancelar</button>
        </div>

    </form>
</div>

<script>
    $(document).ready(function () {
        // Desabilita a inicialização automática do Materialize para selects neste formulário
        $('.licitacao-form select').formSelect('destroy');

        $('#codLicitacao').mask('000/0000');
    });

    // Evento 'input' no campo 'codLicitacao'
    $('#codLicitacao').on('input', function () {
        validarCodLicitacao();
    });

    // Evento 'change' no dropdown 'tipoLicitacao'
    $('#tipoLicitacao').on('change', function () {
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
                success: function (response) {
                    // console.log('Resposta do servidor:', response);
                    if (response == 1) {
                        $('#codLicitacao').val('');
                        $('#codLicitacao').focus(); // Mudar o foco para o campo codLicitacao
                        alert('Código da Licitação já cadastrado.');
                    } else {
                        // Código da Licitação não cadastrado, continuar com outras ações se necessário
                    }
                },
                error: function (xhr, status, error) {
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
    document.addEventListener('DOMContentLoaded', function () {
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
                deleteButton.addEventListener('click', function () {
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

<script>
    $(document).ready(function () {
        $('#codLicitacao').mask('000/0000');
    });

    // Evento 'input' no campo 'codLicitacao'
    $('#codLicitacao').on('input', function () {
        validarCodLicitacao();
    });

    // Evento 'change' no dropdown 'tipoLicitacao'
    $('#tipoLicitacao').on('change', function () {
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
                success: function (response) {
                    // console.log('Resposta do servidor:', response);
                    if (response == 1) {
                        $('#codLicitacao').val('');
                        $('#codLicitacao').focus(); // Mudar o foco para o campo codLicitacao
                        alert('Código da Licitação já cadastrado.');
                    } else {
                        // Código da Licitação não cadastrado, continuar com outras ações se necessário
                    }
                },
                error: function (xhr, status, error) {
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
    document.addEventListener('DOMContentLoaded', function () {
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
                deleteButton.addEventListener('click', function () {
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