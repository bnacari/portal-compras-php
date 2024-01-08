<?php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';

include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * FROM [portalcompras].[dbo].[FORMA] ORDER BY [NM_FORMA]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

echo "<table class='rTablePublico'>";
echo "<thead>";
echo "<tr>";

echo "<th>Nome</th>";
echo "<th>Data Desativação</th>";
echo "<th style='text-align: center;'>Ativar / Desativar</th>";
echo "<th style='text-align: center;'>Editar</th>";

echo "</tr>";
echo "</thead>";
echo "<tbody>";

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idForma = $registros['ID_FORMA'];
    $nmForma = $registros['NM_FORMA'];
    $dtExcForma = $registros['DT_EXC_FORMA'];

    echo "<tr id='row$idForma'>";

    echo "<td><label class='nmForma'>$nmForma</label></td>";
    echo "<td><label>$dtExcForma</label></td>";


    if ($dtExcForma == null) {
        echo "<td style='text-align: center;'><a href='bd/forma/desativa.php?idForma=$idForma' title='Desativar Formas' style='color: red;'><i class='material-icons'>close</i></a></td>";
    } else {
        echo "<td style='text-align: center;'><a href='bd/forma/ativa.php?idForma=$idForma' title='Ativar Forma'><i class='material-icons'>check</i></a></td>";
    }

    echo "<td style='text-align: center;'>
            <button class='save-button' data-id='$idForma' hidden>Salvar</button>
            <button class='edit-button' data-id='$idForma'>Editar</button>
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
            var nmForma = $('#row' + rowId + ' .nmForma').text();

            // Substituir os labels por inputs
            $('#row' + rowId + ' .nmForma').replaceWith(`<input class='nmForma' type='text' value='${nmForma}' />`);

            $('#row' + rowId + ' .save-button').prop('hidden', false);
            $('#row' + rowId + ' .edit-button').prop('hidden', true);
        });

        $('.save-button').on('click', function() {
            event.preventDefault();

            var rowId = $(this).data('id');
            var nmForma = $('#row' + rowId + ' .nmForma').val();

            if (nmForma.trim() === '') {
                alert('Os campos são obrigatórios. Por favor, preencha todos os campos.');
            } else {
                // Redirecionar para a página de atualização com os parâmetros
                window.location.href = `bd/forma/update.php?idForma=${rowId}&nmForma=${encodeURIComponent(nmForma)}`;
            }
        });
    });
</script>