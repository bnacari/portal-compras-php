<?php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';

$querySelect2 = "SELECT * FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] ORDER BY [NM_CRITERIO]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

echo "<table class='rTablePublico'>";
echo "<thead>";
echo "<tr>";

echo "<th>Nome</th>";
echo "<th>Data Desativação</th>";
echo "<th>Login Criador</th>";

echo "<th style='text-align: center;'>Ativar / Desativar</th>";
echo "<th style='text-align: center;'>Editar</th>";

echo "</tr>";
echo "</thead>";
echo "<tbody>";

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idCriterio = $registros['ID_CRITERIO'];
    $nmCriterio = $registros['NM_CRITERIO'];
    $dtExcCriterio = $registros['DT_EXC_CRITERIO'];
    $lgnCriadorCriterio = $registros['LGN_CRIADOR_CRITERIO'];

    echo "<tr id='row$idCriterio'>";
    echo "<td><label class='pubnmCriterio'>$nmCriterio</label></td>";
    echo "<td>$dtExcCriterio</td>";
    echo "<td>$lgnCriadorCriterio</td>";

    if ($dtExcCriterio == null) {
        echo "<td style='text-align: center;'><a href='bd/criterio/desativa.php?idCriterio=$idCriterio' title='Desativar Criterio' style='color: red;'><i class='material-icons'>close</i></a></td>";
    } else {
        echo "<td style='text-align: center;'><a href='bd/criterio/ativa.php?idCriterio=$idCriterio' title='Ativar Criterio'><i class='material-icons'>check</i></a></td>";
    }

    echo "<td style='text-align: center;'>
            <button class='save-button' data-id='$idCriterio' hidden>Salvar</button>
            <button class='edit-button' data-id='$idCriterio'>Editar</button>
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
            $('#row' + rowId + ' .pubnmCriterio').replaceWith(`<input class='pubnmCriterio' type='text' value='${$('#row' + rowId + ' .pubnmCriterio').text()}' />`);
            $('#row' + rowId + ' .save-button').prop('hidden', false);
            $('#row' + rowId + ' .edit-button').prop('hidden', true);

        });

        $('.save-button').on('click', function() {
            event.preventDefault();

            var rowId = $(this).data('id');
            var nmCriterio = $('#row' + rowId + ' .pubnmCriterio').val();

            if (nmCriterio.trim() === '') {
                alert('Os campos são obrigatórios. Por favor, preencha todos os campos.');
            } else {
                window.location.href = `bd/criterio/update.php?idCriterio=${rowId}&nmCriterio=${nmCriterio}`;

                $('#row' + rowId + ' .pubnmCriterio').replaceWith(`<label class='pubnmCriterio'>${nmCriterio}</label>`);
                $('#row' + rowId + ' .save-button').prop('hidden', true);
                $('#row' + rowId + ' .edit-button').prop('hidden', false);
            }
        });
    });
</script>