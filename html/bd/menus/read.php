<?php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';

include('protectAdmin.php');

$querySelect2 = "SELECT * FROM [portalcompras].[dbo].[menu] ORDER BY [NM_MENU]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

echo "<table class='rTablePublico'>";
echo "<thead>";
echo "<tr>";

echo "<th>Nome</th>";
echo "<th>Link</th>";
echo "<th>Data Desativação</th>";
echo "<th style='text-align: center;'>Ativar / Desativar</th>";
echo "<th style='text-align: center;'>Editar</th>";

echo "</tr>";
echo "</thead>";
echo "<tbody>";

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idMenu = $registros['ID_MENU'];
    $nmMenu = $registros['NM_MENU'];
    $linkMenu = $registros['LINK_MENU'];
    $dtExcMenu = $registros['DT_EXC_MENU'];

    echo "<tr id='row$idMenu'>";

    echo "<td><label class='nmMenu'>$nmMenu</label></td>";
    echo "<td><label class='linkMenu'>$linkMenu</label></td>";
    echo "<td><label>$dtExcMenu</label></td>";


    if ($dtExcMenu == null) {
        echo "<td style='text-align: center;'><a href='bd/menus/desativa.php?idMenu=$idMenu' title='Desativar Menus' style='color: red;'><i class='material-icons'>close</i></a></td>";
    } else {
        echo "<td style='text-align: center;'><a href='bd/menus/ativa.php?idMenu=$idMenu' title='Ativar Menu'><i class='material-icons'>check</i></a></td>";
    }

    echo "<td style='text-align: center;'>
            <button class='save-button' data-id='$idMenu' hidden>Salvar</button>
            <button class='edit-button' data-id='$idMenu'>Editar</button>
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
            var nmMenu = $('#row' + rowId + ' .nmMenu').text();
            var linkMenu = $('#row' + rowId + ' .linkMenu').text();

            // Substituir os labels por inputs
            $('#row' + rowId + ' .nmMenu').replaceWith(`<input class='nmMenu' type='text' value='${nmMenu}' />`);
            $('#row' + rowId + ' .linkMenu').replaceWith(`<input class='linkMenu' type='text' value='${linkMenu}' />`);

            $('#row' + rowId + ' .save-button').prop('hidden', false);
            $('#row' + rowId + ' .edit-button').prop('hidden', true);
        });

        $('.save-button').on('click', function() {
            event.preventDefault();

            var rowId = $(this).data('id');
            var nmMenu = $('#row' + rowId + ' .nmMenu').val();
            var linkMenu = $('#row' + rowId + ' .linkMenu').val();

            if (nmMenu.trim() === '') {
                alert('Os campos são obrigatórios. Por favor, preencha todos os campos.');
            } else {
                // Redirecionar para a página de atualização com os parâmetros
                window.location.href = `bd/menus/update.php?idMenu=${rowId}&nmMenu=${encodeURIComponent(nmMenu)}&linkMenu=${encodeURIComponent(linkMenu)}`;
            }
        });
    });
</script>