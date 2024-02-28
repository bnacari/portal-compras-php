<?php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';

include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * 
                FROM [PortalCompras].[dbo].ItemMENU IM
                LEFT JOIN SUBMENU SM ON SM.ID_SUBMENU = IM.ID_SUBMENU
                LEFT JOIN MENU M ON M.ID_MENU = SM.ID_MENU
                --WHERE SM.DT_EXC_ItemMENU IS NULL
                ORDER BY [NM_SUBMENU], [NM_ItemMENU]
                    ";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

echo "<table class='rTablePublico'>";
echo "<thead>";
echo "<tr>";

echo "<th>Nome</th>";
echo "<th>Menu</th>";
echo "<th>Submenu</th>";
echo "<th>Link</th>";
echo "<th>Data Desativação</th>";
echo "<th style='text-align: center;'>Ativar / Desativar</th>";
echo "<th style='text-align: center;'>Editar</th>";

echo "</tr>";
echo "</thead>";
echo "<tbody>";

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idItemMenu = $registros['ID_ITEMMENU'];
    $nmItemMenu = $registros['NM_ITEMMENU'];
    $linkItemMenu = $registros['LINK_ITEMMENU'];
    $dtExcItemMenu = $registros['DT_EXC_ITEMMENU'];
    $nmSubMenu = $registros['NM_SUBMENU'];
    $nmMenu = $registros['NM_MENU'];


    echo "<tr id='row$idItemMenu'>";

    echo "<td><label class='nmMenu'>$nmItemMenu</label></td>";
    echo "<td>$nmMenu</td>";
    echo "<td>$nmSubMenu</td>";
    echo "<td><label class='linkMenu'>$linkItemMenu</label></td>";
    echo "<td><label>$dtExcItemMenu</label></td>";


    if ($dtExcItemMenu == null) {
        echo "<td style='text-align: center;'><a href='bd/itemmenu/desativa.php?idItemMenu=$idItemMenu' title='Desativar Menus' style='color: red;'><i class='bi bi-x-circle'></i></a></td>";
    } else {
        echo "<td style='text-align: center;'><a href='bd/itemmenu/ativa.php?idItemMenu=$idItemMenu' title='Ativar Menu'><i class='bi bi-check-lg'></i></a></td>";
    }

    if (isset($_SESSION['isAdmin'])) {
        echo "<td style='text-align: center;'><a href='editarItemMenu.php?idItemMenu=$idItemMenu'><i class='material-icons'>tune</i></a></td>";
    }
    

    echo "</tr>";
endwhile;

echo "</tbody>";
echo "</table>";
?>
