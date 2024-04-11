<?php

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

<!-- FORMULÁRIOS DE CADASTRO -->
<div class="row container">
    <form action="bd/licitacao/create.php" method="post" class="col s12 formulario" enctype="multipart/form-data" onsubmit="return validarFormulario()">
        <fieldset class="formulario col s12">
            <h5 class="light" style="color: #404040">Criar Licitação</h5>
        </fieldset>
        <p>&nbsp;</p>
        <fieldset class="formulario" style="padding:15px; border-color:#eee; border-radius:10px">
            <!-- <h6><strong>Local a Visitar</strong></h6> -->

            <div class="input-field col s3">
                <select name="tipoLicitacao" id="tipoLicitacao">
                    <option value='' selected>Selecione uma opção</option>
                    <?php

                    if (isset($isAdmin)) {
                        $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' ORDER BY NM_TIPO";
                        $querySelect = $pdoCAT->query($querySelect2);
                    } else {
                        $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' AND ID_TIPO IN ($idPerfilFinal) ORDER BY NM_TIPO";
                        $querySelect = $pdoCAT->query($querySelect2);
                    }
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        echo "<option value='" . $registros["ID_TIPO"] . "'>" . $registros["NM_TIPO"] . " (" . $registros["SGL_TIPO"] . ")" . "</option>";
                    endwhile;
                    ?>
                </select>
                <!-- <input type="text" id="criterioLicitacao" name="criterioLicitacao"> -->
                <label>Tipo de Contratação *</label>
            </div>

            <div class="input-field col s3">
                <input type="text" id="codLicitacao" name="codLicitacao" required>
                <label>Código *</label>
            </div>

            <div class="input-field col s3">
                <select name="statusLicitacao" id="statusLicitacao">
                    <option value=''>Selecione uma opção</option>
                    <option value='Em Andamento' selected>Em Andamento</option>
                    <option value='Suspenso'>Suspensa</option>
                    <option value='Encerrado'>Encerrada</option>
                    <option value='Rascunho'>Rascunho</option>
                </select>

                <label>Status *</label>
            </div>

            <div class="input-field col s3">
                <input type="text" id="respLicitacao" name="respLicitacao" required>
                <label>Responsável *</label>
            </div>

            <div class="input-field col s12">
                <textarea type="text" id="objLicitacao" name="objLicitacao" required></textarea>
                <label>Objeto *</label>
            </div>
            <div class="input-field col s2">
                <input type="date" id="dtAberLicitacao" name="dtAberLicitacao">
                <label>Data de Abertura</label>
            </div>
            <div class="input-field col s2">
                <input type="time" id="hrAberLicitacao" name="hrAberLicitacao">
                <label>Horário de Abertura</label>
            </div>

            <div class="input-field col s2">
                <input type="date" id="dtIniSessLicitacao" name="dtIniSessLicitacao">
                <label>Início da Sessão de Disputa de Preços</label>
            </div>
            <div class="input-field col s2">
                <input type="time" id="hrIniSessLicitacao" name="hrIniSessLicitacao">
                <label>Início da Sessão de Disputa de Preços</label>
            </div>

            <div class="input-field col s4">
                <select name="modoLicitacao" id="modoLicitacao">
                    <option value='0' selected>Selecione uma opção</option>
                    <option value='Aberta'>Aberta</option>
                    <option value='Fechada'>Fechada</option>
                    <option value='Hibrida'>Híbrida</option>
                </select>
                <label>Modo de Disputa</label>
            </div>

            <div class="input-field col s4">
                <select name="criterioLicitacao" id="criterioLicitacao">
                    <option value='0' selected>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE DT_EXC_CRITERIO IS NULL ORDER BY NM_CRITERIO";
                    $querySelect = $pdoCAT->query($querySelect2);
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        echo "<option value='" . $registros["ID_CRITERIO"] . "'>" . $registros["NM_CRITERIO"] . "</option>";
                    endwhile;
                    ?>
                </select>
                <!-- <input type="text" id="criterioLicitacao" name="criterioLicitacao"> -->
                <label>Critério de Julgamento</label>
            </div>
            <div class="input-field col s4">
                <input type="text" id="regimeLicitacao" name="regimeLicitacao">
                <label>Regime de Execução</label>
            </div>

            <div class="input-field col s4">
                <select name="formaLicitacao" id="formaLicitacao">
                    <option value='0' selected>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] WHERE DT_EXC_FORMA IS NULL ORDER BY NM_FORMA";
                    $querySelect = $pdoCAT->query($querySelect2);
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        echo "<option value='" . $registros["ID_FORMA"] . "'>" . $registros["NM_FORMA"] . "</option>";
                    endwhile;
                    ?>
                </select>
                <label>Forma</label>
            </div>

            <div class="input-field col s4">
                <input type="text" id="vlLicitacao" name="vlLicitacao">
                <label>Valor Estimado</label>
            </div>

            <div class="input-field col s4">
                <input type="text" id="identificadorLicitacao" name="identificadorLicitacao">
                <label>Identificador</label>
            </div>

            <div class="input-field col s4">
                <input type="text" id="localLicitacao" name="localLicitacao">
                <label>Local de Abertura</label>
            </div>
            <div class="input-field col s12">
                <textarea type="text" id="obsLicitacao" name="obsLicitacao"></textarea>
                <label>Observação</label>
            </div>

            <div class="input-field col s12">
                <input type="checkbox" name="permitirAtualizacao" id="permitirAtualizacao">
                <label for="enviarAtualizacao">Permitir que os usuários sejam lembrados para futuras atualizações da licitação?</label>
            </div>

        </fieldset>

        <p>&nbsp;</p>

        <div class="input-field col s2">
            <button type="submit" class="btn blue">Salvar</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#codLicitacao').mask('000/0000');

        $('#codLicitacao').on('input', function() {
            var codLicitacao = $(this).val();
            var tipoLicitacao = $('#tipoLicitacao').val();

            if (codLicitacao.length === 8) {
                // Faz a requisição AJAX
                $.ajax({
                    url: 'verificaCodLicitacao.php',
                    method: 'GET',
                    data: {
                        codLicitacao: codLicitacao,
                        tipoLicitacao: tipoLicitacao
                    },
                    success: function(response) {
                        // console.log('Resposta do servidor:', response);
                        if (response == 1) {
                            $('#codLicitacao').val('');
                            alert('Código da Licitação já cadastrado.');
                        } else {

                        }
                    },
                    error: function(xhr, status, error) {
                        // Trate os erros de requisição AJAX, se necessário
                        console.error(error);
                    }
                });
            }
        });
    });

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