<?php

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

$date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_NUMBER_INT);

$queryLink = "SELECT TOP 1 [LINK] FROM [VisitaAgendada].[dbo].[LINK] WHERE [DT_EXC_LINK] IS NULL ORDER BY [ID_LINK] DESC";
$queryLink2 = $pdoCAT->query($queryLink);
while ($registros = $queryLink2->fetch(PDO::FETCH_ASSOC)) :
    $link = $registros["LINK"];
endwhile;

?>
<style>
    /* Ajuste para telas menores que 600px */
    @media (max-width: 700px) {}
</style>

<!-- FORMULÁRIOS DE CADASTRO -->
<div class="row container">
    <!-- EXIBO O OLHINHO PARA MOSTRAR/OCULTAR O CALENDÁRIO NA TELA -->
    <input type="checkbox" id="mostrarOcultarCalendar">
    <label for="mostrarOcultarCalendar" id="labelmostrarOcultarCalendar">
        <i class='material-icons' title="Exibir Calendário">event_available</i>
    </label>

    <div id="modal" class="modal">
        <div class="modal-content">
            <?php include 'calendar.php'; ?>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Fechar</a>
        </div>
    </div>

    <form id="visitaForm" class="col s12" enctype="multipart/form-data">
        <p>&nbsp;</p>

        <fieldset class="formulario col s12">
            <h5 class="light center">Criar solicitação de agendamento</h5>

            <div class="input-field col s12">
                <?php
                if (isset($_SESSION['msg'])) :
                    echo $_SESSION['msg'];
                    $_SESSION['msg'] = '';
                endif;
                ?>
            </div>
        </fieldset>

        <p>&nbsp;</p>

        <fieldset class="formulario">
            <h6><strong>Local a visitar</strong></h6>
            <div class="input-field col s12">
                <div>
                    <label>Local a visitar</label>
                </div>
                <select name="localVisitado" id="localVisitado" required oninvalid="exibirAlertaLocalVisitado()">
                    <option value='' disabled selected>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM [VisitaAgendada].[dbo].[LOCAL] where [DT_EXC_LOCAL] IS NULL ORDER BY NM_LOCAL";
                    $querySelect = $pdoCAT->query($querySelect2);
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        echo "<option value='" . $registros["ID_LOCAL"] . "'>" . $registros["NM_LOCAL"] . "</option>";
                    endwhile;
                    ?>
                </select>
            </div>
        </fieldset>

        <p>&nbsp;</p>

        <fieldset class="formulario">
            <h6><strong>Dados do responsável pela solicitação</strong></h6>
            <div class="input-field col s4">
                <input type="text" name="nmResponsavel" id="nmResponsavel" maxlength="100" required>
                <label>Nome do Responsável</label>
            </div>

            <div class="input-field col s4">
                <input type="email" name="emailResponsavel" id="emailResponsavel" maxlength="100" required>
                <label>E-mail para contato</label>
            </div>

            <div class="input-field col s4">
                <input type="text" name="telResponsavel" id="telResponsavel" maxlength="11" required>
                <label>Telefone para contato</label>
            </div>
        </fieldset>

        <p>&nbsp;</p>

        <fieldset class="formulario">
            <h6><strong>Dados da instituição do(a) solicitante</strong></h6>
            <div class="input-field col s12">
                <div>
                    <label>Público da visita</label>
                </div>
                <select name="respSolicitacao" id="respSolicitacao" required oninvalid="exibirAlertaRespSolicitacao()" onchange="changeRespSolicitacao()">>
                    <option value='' disabled selected>Selecione uma opção</option>
                    <?php
                    $querySelect2 = "SELECT * FROM [VisitaAgendada].[dbo].[PUBLICO] WHERE [DT_EXC_PUBLICO] IS NULL ORDER BY NM_PUBLICO";
                    $querySelect = $pdoCAT->query($querySelect2);
                    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        echo "<option value='" . $registros["ID_PUBLICO"] . "' data-exibir-instituicao='" . $registros["EXIBIR_INSTITUICAO"] . "'>" . $registros["NM_PUBLICO"] . "</option>";
                        $exibirInstituicao = $registros["EXIBIR_INSTITUICAO"];
                    endwhile;
                    ?>
                </select>
            </div>

            <div id="esconde" style="display: none">
                <div class="input-field col s12">
                    <input type="text" name="nmInstituicao" id="nmInstituicao" maxlength="100">
                    <label>Nome da instituição</label>
                </div>

                <div class="input-field col s4">
                    <input type="text" name="endInstituicao" id="endInstituicao" maxlength="100">
                    <label>Endereço</label>
                </div>

                <div class="input-field col s4">
                    <input type="text" name="bairroInstituicao" id="bairroInstituicao" maxlength="100">
                    <label>Bairro</label>
                </div>

                <div class="input-field col s4">
                    <input type="text" name="cidadeInstituicao" id="cidadeInstituicao" maxlength="100">
                    <label>Cidade</label>
                </div>

                <div class="input-field col s4">
                    <input type="text" name="telInstituicao" id="telInstituicao" maxlength="11">
                    <label>Telefone para contato</label>
                </div>

                <div class="input-field col s4">
                    <input type="email" name="emailInstituicao" id="emailInstituicao" maxlength="100">
                    <label>E-mail</label>
                </div>
                <div id="escondeCursoInstituicao" class="input-field col s4">
                    <input type="text" name="cursoInstituicao" id="cursoInstituicao" maxlength="100">
                    <label>Curso</label>
                </div>
                <div id="escondeSerieVisita" class="input-field col s4">
                    <div>
                        <label>Qual a série?</label>
                    </div>
                    <select name="serieVisita" id="serieVisita" required oninvalid="exibirAlertaSerieVisita()">
                        <option value='' disabled selected>Selecione uma opção</option>
                        <option value='6º Ano (Ensino Fundamental)'>6º Ano (Ensino Fundamental)</option>
                        <option value='7º Ano (Ensino Fundamental)'>7º Ano (Ensino Fundamental)</option>
                        <option value='8º Ano (Ensino Fundamental)'>8º Ano (Ensino Fundamental)</option>
                        <option value='9º Ano (Ensino Fundamental)'>9º Ano (Ensino Fundamental)</option>
                        <option value='1º Ano (Ensino Médio)'>1º Ano (Ensino Médio)</option>
                        <option value='2º Ano (Ensino Médio)'>2º Ano (Ensino Médio)</option>
                        <option value='3º Ano (Ensino Médio)'>3º Ano (Ensino Médio)</option>
                        <option value='Técnico/EJA'>Técnico/EJA</option>
                    </select>
                </div>
            </div>

        </fieldset>
        <p>&nbsp;</p>

        <fieldset class="formulario">
            <h6><strong>Dados da visita</strong></h6>
            <div class="input-field col s12">
                <div>
                    <label>Tipo da visita</label>
                </div>
                <select name="tipoVisita" id="tipoVisita" required oninvalid="exibirAlertaTipoVisita()">
                    <option value='' disabled selected>Selecione uma opção</option>
                    <option value='Presencial'>Presencial</option>
                    <option value='OnLine'>OnLine - Síncrona (à distância, em tempo real)</option>
                </select>
            </div>
            <div class="input-field col s12">
                <input type="date" name="dtVisita" id="dtVisita" value="<?php echo $date ?>" required onchange="changeDataVisita()">
                <label>Data da Visita</label>
                <p style="font-size: 12px; color: #A1A4A7;">Não é possível solicitar agendamento de datas com menos de 2 dias de antecedência.</p>
            </div>

            <div class="input-field col s12">
                <div>
                    <label>Turno de permanência</label>
                </div>
                <select name="turnoVisita" id="turnoVisita" required oninvalid="exibirAlertaTurnoVisita()">
                    <option value='' disabled selected>Selecione uma opção</option>
                    <option value='Manhã'>Manhã</option>
                    <option value='Tarde'>Tarde</option>
                </select>
            </div>

            <div class="input-field col s12">
                <input type="number" name="numVisitantes" id="numVisitantes" required min="20" max="30">
                <label>Número total de participantes (Mín.: 20 e Máx.: 30)</label>
            </div>

            <div class="input-field col s12">
                <textarea id="objVisita" name="objVisita" rows="10" required></textarea>
                <label>Objetivo da visita (expectativas e intenções)</label>
            </div>

            <div class="input-field col s12">
                <div>
                    <label>Há algum participante com deficiência?</label>
                </div>
                <select name="deficiente" id="deficiente" required oninvalid="exibirAlertaDeficiente()" onchange="changeDeficiente()">
                    <option value='' disabled selected>Selecione uma opção</option>
                    <option value='Sim'>Sim</option>
                    <option value='Não'>Não</option>
                </select>
            </div>

            <div id="escondeDeficiente" style="display: none">
                <div class="input-field col s12">
                    <textarea id="necessidadeDeficiente" name="necessidadeDeficiente" rows="10"></textarea>
                    <label>Qual necessidade?</label>
                </div>
            </div>

            <div class="input-field col s12">
                <input type="checkbox" id="checkDocumento" name="checkDocumento" required>
                <span class="checkmark"></span>

                <label class="container"><a href="<?php echo $link ?>" target="_blank">Li e estou de acordo com o regulamento ORIENTAÇÕES GERAIS PARA VISITA – ETA e ETE?</a>
                </label>
            </div>
            
            <p>&nbsp;</p>

            <div class="input-field col s12">
                <div class="g-recaptcha" name="captcha" id="captcha" data-sitekey="6LcUxQ0pAAAAAGQZNKu9jhaHVy-DJzKn4cAaiDhR"></div>
            </div>

            <p>&nbsp;</p>

            <div class="input-field col s2">
                <button type="submit" class="btn blue" onclick="validaCaptcha()">Cadastrar</button>
            </div>
        </fieldset>
        <p>&nbsp;</p>

        <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>

    </form>

    <?php //include_once 'dynamic-full-calendar.html'; 
    ?>

</div>

<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->

<script>
    var onRecaptchaLoad = function() {
        // O script do reCAPTCHA foi carregado, agora você pode chamar sua função
        validaCaptcha();
    };

    function validaCaptcha() {
        var response = grecaptcha.getResponse();
        var nomeResponsavel = document.getElementById('nmResponsavel').value;

        if (nomeResponsavel.trim() != '') {
            if (response.length === 0) {
                alert("Favor clicar no CAPTCHA para validar seu formulário.");
                return false; // Impede a submissão do formulário
            }
        }

        // Se o reCAPTCHA foi preenchido, continue com a submissão do formulário
        return true;
    }

    $(document).ready(function() {
        $('#telResponsavel').mask('(00) 00000-0000');
        $('#telInstituicao').mask('(00) 00000-0000');
        $('.modal').modal();

        $('#mostrarOcultarCalendar').change(function() {
            if (this.checked) {
                // Se o checkbox estiver marcado, abra o modal
                $('#modal').modal('open');
            } else {
                // Se o checkbox estiver desmarcado, feche o modal
                $('#modal').modal('close');
            }
        });

        $("#visitaForm").submit(function(event) {
            // event.preventDefault(); // Impede o envio padrão do formulário
            var formData = new FormData(this);

            if (!validaCaptcha()) {
                return false;
            }
            // Realiza a requisição AJAX
            $.ajax({
                url: "bd/visita/create.php",
                type: "POST",
                data: formData,
                contentType: false, // Necessário para enviar arquivos
                processData: false, // Necessário para enviar arquivos
                success: function(response) {
                    // alert('sucesso');

                },
                error: function() {
                    // alert('fracasso');

                }
            });
        });

    });
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function exibirAlertaLocalVisitado() {
        alert("Por favor, selecione o campo 'Local a Visitar'");
    }

    function exibirAlertaRespSolicitacao() {
        alert("Por favor, selecione o campo 'Público da Visita'");
    }

    function exibirAlertaSerieVisita() {
        alert("Por favor, selecione o campo 'Qual a Série?'");
    }

    function exibirAlertaTipoVisita() {
        alert("Por favor, selecione o campo 'Tipo da Visita'");
    }

    function exibirAlertaTurnoVisita() {
        alert("Por favor, selecione o campo 'Turno de Permanência'");
    }

    function exibirAlertaDeficiente() {
        alert("Por favor, selecione o campo 'Há algum participante com deficiência?'");
    }

    function changeDataVisita() {
        var dataVisitaInput = document.getElementById("dtVisita");
        var dataVisita = new Date(dataVisitaInput.value);

        var dataAtual = new Date();
        dataAtual.setDate(dataAtual.getDate() + 2);

        // Se a dataVisita for menor que a dataAtual + 2 dias
        if (dataVisita < dataAtual) {
            // Define a dataVisita para a dataAtual + 2 dias
            dataVisita.setDate(dataAtual.getDate());
            dataVisitaInput.valueAsDate = dataVisita; // Atualiza o valor do campo

            // Exibe uma mensagem informando o ajuste automático
            alert('A data foi ajustada automaticamente para pelo menos 2 dias de antecedência.');
        }
    }

    function changeRespSolicitacao() {
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

    function changeDeficiente() {
        var deficiente = document.getElementById("deficiente").value;
        var escondeDeficiente = document.getElementById("escondeDeficiente");
        var necessidadeDeficiente = document.getElementById("necessidadeDeficiente");

        if (deficiente == 'Sim') {
            escondeDeficiente.style.display = "block";
            necessidadeDeficiente.required = true;
        } else {
            escondeDeficiente.style.display = "none";
            necessidadeDeficiente.required = false;
        }
    }

    document.getElementById('arquivo').addEventListener('change', function() {
        var fileInput = document.getElementById('arquivo');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.pdf)$/i;
        if (!allowedExtensions.exec(filePath)) {
            alert('Somente arquivo .PDF são aceitos.');
            fileInput.value = ''; // Limpa o campo de entrada de arquivo
            return false;
        }
    });
</script>