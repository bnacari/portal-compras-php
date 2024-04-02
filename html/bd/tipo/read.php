<?php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';
include_once('../../protectAdmin.php');


$querySelect2 = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] ORDER BY [NM_TIPO]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

echo "<table class='rTablePublico'>";
echo "<thead>";
echo "<tr>";

echo "<th>Nome</th>";
echo "<th>Sigla</th>";
echo "<th>Data Desativação</th>";
echo "<th>Login Criador</th>";

echo "<th style='text-align: center;'>Ativar / Desativar</th>";
echo "<th style='text-align: center;'>Editar</th>";

echo "</tr>";
echo "</thead>";
echo "<tbody>";

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idTipo = $registros['ID_TIPO'];
    $nmTipo = $registros['NM_TIPO'];
    $sglTipo = $registros['SGL_TIPO'];
    $dtExcTipo = $registros['DT_EXC_TIPO'];
    $lgnCriadorTipo = $registros['LGN_CRIADOR_TIPO'];

    echo "<tr id='row$idTipo'>";
    echo "<td><label class='pubnmTipo'>$nmTipo</label></td>";
    echo "<td><label class='pubsglTipo'>$sglTipo</label></td>";
    echo "<td>$dtExcTipo</td>";
    echo "<td>$lgnCriadorTipo</td>";

    if ($dtExcTipo == null) {
        echo "<td style='text-align: center;'><a href='bd/tipo/desativa.php?idTipo=$idTipo' title='Desativar Tipo' style='color: red;'><i class='bi bi-x-circle'></i></a></td>";
    } else {
        echo "<td style='text-align: center;'><a href='bd/tipo/ativa.php?idTipo=$idTipo' title='Ativar Tipo'><i class='bi bi-check-lg'></i></a></td>";
    }

    echo "<td style='text-align: center;'>
            <button class='save-button' data-id='$idTipo' hidden>Salvar</button>
            <button class='edit-button' data-id='$idTipo'>Editar</button>
        </td>";

    echo "</tr>";
endwhile;

echo "</tbody>";
echo "</table>";
?>

<script>
    $(document).ready(function() {
        $('.edit-button').on('click', function() {
            var rowId = $(this).data('id');
            $('#row' + rowId + ' .pubnmTipo').replaceWith(`<input class='pubnmTipo' type='text' value='${$('#row' + rowId + ' .pubnmTipo').text()}' />`);
            $('#row' + rowId + ' .pubsglTipo').replaceWith(`<input class='pubsglTipo' type='text' value='${$('#row' + rowId + ' .pubsglTipo').text()}' />`);

            $('#row' + rowId + ' .save-button').prop('hidden', false);
            $('#row' + rowId + ' .edit-button').prop('hidden', true);

        });

        $('.save-button').on('click', function() {
            event.preventDefault();

            var rowId = $(this).data('id');
            var nmTipo = $('#row' + rowId + ' .pubnmTipo').val();
            var sglTipo = $('#row' + rowId + ' .pubsglTipo').val();

            if (nmTipo.trim() === '') {
                alert('Os campos são obrigatórios. Por favor, preencha todos os campos.');
            } else {
                window.location.href = `bd/tipo/update.php?idTipo=${rowId}&nmTipo=${nmTipo}&sglTipo=${sglTipo}`;

                $('#row' + rowId + ' .pubnmTipo').replaceWith(`<label class='pubnmTipo'>${nmTipo}</label>`);
                $('#row' + rowId + ' .pubsglTipo').replaceWith(`<label class='pubsglTipo'>${sglTipo}</label>`);
                $('#row' + rowId + ' .save-button').prop('hidden', true);
                $('#row' + rowId + ' .edit-button').prop('hidden', false);
            }
        });
    });
</script>