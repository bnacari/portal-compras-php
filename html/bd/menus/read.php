<?php
//menus/read.php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';

include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * FROM [portalcompras].[dbo].[menu] ORDER BY [NM_MENU]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

$count = 0;
$menus = [];

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
    $menus[] = $registros;
    $count++;
}

if ($count === 0) {
    echo '<div class="empty-state">';
    echo '<div class="empty-state-icon">📋</div>';
    echo '<h3>Nenhum menu cadastrado</h3>';
    echo '<p>Cadastre um novo menu usando o formulário acima.</p>';
    echo '</div>';
} else {
    echo "<table class='menus-table'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th><i class='fas fa-tag'></i> Nome</th>";
    echo "<th><i class='fas fa-link'></i> Link</th>";
    echo "<th><i class='fas fa-toggle-on'></i> Status</th>";
    echo "<th style='text-align: center;'><i class='fas fa-power-off'></i> Ação</th>";
    echo "<th style='text-align: center;'><i class='fas fa-edit'></i> Editar</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($menus as $registros) {
        $idMenu = $registros['ID_MENU'];
        $nmMenu = $registros['NM_MENU'];
        $linkMenu = $registros['LINK_MENU'];
        $dtExcMenu = $registros['DT_EXC_MENU'];

        echo "<tr id='row$idMenu'>";

        echo "<td><label class='nmMenu'>" . htmlspecialchars($nmMenu) . "</label></td>";
        echo "<td><label class='linkMenu'>" . htmlspecialchars($linkMenu) . "</label></td>";
        
        // Status Badge
        echo "<td style='text-align: center;'>";
        if ($dtExcMenu == null) {
            echo "<span class='status-badge ativo'>Ativo</span>";
        } else {
            echo "<span class='status-badge inativo'>Inativo</span>";
        }
        echo "</td>";

        // Ativar/Desativar
        echo "<td style='text-align: center;'>";
        if ($dtExcMenu == null) {
            echo "<a href='bd/menus/desativa.php?idMenu=$idMenu' title='Desativar Menu' class='btn-icon deactivate'>";
            echo "<i class='fas fa-times-circle'></i>";
            echo "</a>";
        } else {
            echo "<a href='bd/menus/ativa.php?idMenu=$idMenu' title='Ativar Menu' class='btn-icon activate'>";
            echo "<i class='fas fa-check-circle'></i>";
            echo "</a>";
        }
        echo "</td>";

        // Editar
        echo "<td style='text-align: center;'>";
        echo "<button class='btn-action btn-save save-button' data-id='$idMenu' hidden>";
        echo "<i class='fas fa-save'></i> Salvar";
        echo "</button>";
        echo "<button class='btn-action btn-edit edit-button' data-id='$idMenu'>";
        echo "<i class='fas fa-edit'></i> Editar";
        echo "</button>";
        echo "</td>";

        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
}
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

            // Mostrar botão salvar, esconder botão editar
            $('#row' + rowId + ' .save-button').prop('hidden', false);
            $('#row' + rowId + ' .edit-button').prop('hidden', true);
        });

        $(document).on('click', '.save-button', function(event) {
            event.preventDefault();

            var rowId = $(this).data('id');
            var nmMenu = $('#row' + rowId + ' .nmMenu').val();
            var linkMenu = $('#row' + rowId + ' .linkMenu').val();

            if (nmMenu.trim() === '') {
                alert('O nome do menu é obrigatório. Por favor, preencha o campo.');
            } else {
                // Redirecionar para a página de atualização com os parâmetros
                window.location.href = `bd/menus/update.php?idMenu=${rowId}&nmMenu=${encodeURIComponent(nmMenu)}&linkMenu=${encodeURIComponent(linkMenu)}`;
            }
        });
    });
</script>