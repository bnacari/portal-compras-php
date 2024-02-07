<?php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';
include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * FROM [portalcompras].[dbo].[PERFIL] ORDER BY [NM_PERFIL], [DT_EXC_PERFIL]";

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
    $idPerfil = $registros['ID_PERFIL'];
    $nmPerfil = $registros['NM_PERFIL'];
    $status = $registros['DT_EXC_PERFIL'];
    $lgnCriador = $registros['LGN_CRIADOR_PERFIL'];
    

    echo "<tr id='row$idPerfil'>";
    echo "<td><label class='pubnmPerfil'>$nmPerfil</label></td>";

    echo "<td>$status</td>";
    echo "<td>$lgnCriador</td>";

    if ($status == null) {
        echo "<td style='text-align: center;'><a href='bd/perfil/desativa.php?idPerfil=$idPerfil' title='Desativar Perfil' style='color: red;'><i class='bi bi-dash'></i></a></td>";
    } else {
        echo "<td style='text-align: center;'><a href='bd/perfil/ativa.php?idPerfil=$idPerfil' title='Ativar Perfil'><i class='bi bi-check2'></i></a></td>";
    }

    echo "<td style='text-align: center;'>
            <button class='save-button' data-id='$idPerfil' hidden>Salvar</button>
            <button class='edit-button' data-id='$idPerfil'>Editar</button>
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
            $('#row' + rowId + ' .pubnmPerfil').replaceWith(`<input class='pubnmPerfil' type='text' value='${$('#row' + rowId + ' .pubnmPerfil').text()}' />`);
            $('#row' + rowId + ' .save-button').prop('hidden', false);
            $('#row' + rowId + ' .edit-button').prop('hidden', true);

        });

        $('.save-button').on('click', function() {
            event.preventDefault();

            var rowId = $(this).data('id');
            var nmPerfil = $('#row' + rowId + ' .pubnmPerfil').val();

            if (nmPerfil.trim() === '') {
                alert('Os campos são obrigatórios. Por favor, preencha todos os campos.');
            } else {
                window.location.href = `bd/perfil/update.php?idPerfil=${rowId}&nmPerfil=${nmPerfil}`;

                $('#row' + rowId + ' .pubnmPerfil').replaceWith(`<label class='pubnmPerfil'>${nmPerfil}</label>`);
                $('#row' + rowId + ' .save-button').prop('hidden', true);
                $('#row' + rowId + ' .edit-button').prop('hidden', false);
            }
        });
    });
</script>