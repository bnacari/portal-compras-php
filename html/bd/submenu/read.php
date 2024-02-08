<?php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';

include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * 
                 FROM [PortalCompras].[dbo].SUBMENU SM
                 LEFT JOIN MENU M ON M.ID_MENU = SM.ID_MENU
                 --WHERE SM.DT_EXC_SUBMENU IS NULL
                 ORDER BY [NM_MENU], [NM_SUBMENU]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

echo "<table class='rTablePublico'>";
echo "<thead>";
echo "<tr>";

echo "<th>Nome</th>";
echo "<th>Menu</th>";
echo "<th>Link</th>";
echo "<th>Data Desativação</th>";
echo "<th style='text-align: center;'>Ativar / Desativar</th>";
echo "<th style='text-align: center;'>Editar</th>";

echo "</tr>";
echo "</thead>";
echo "<tbody>";

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idSubMenu = $registros['ID_SUBMENU'];
    $nmSubMenu = $registros['NM_SUBMENU'];
    $linkSubMenu = $registros['LINK_SUBMENU'];
    $dtExcSubMenu = $registros['DT_EXC_SUBMENU'];
    $nmMenu = $registros['NM_MENU'];

    echo "<tr id='row$idSubMenu'>";

    echo "<td><label class='nmMenu'>$nmSubMenu</label></td>";
    echo "<td>$nmMenu</td>";
    echo "<td><label class='linkMenu'>$linkSubMenu</label></td>";
    echo "<td><label>$dtExcSubMenu</label></td>";


    if ($dtExcSubMenu == null) {
        echo "<td style='text-align: center;'><a href='bd/submenu/desativa.php?idSubMenu=$idSubMenu' title='Desativar Menus' style='color: red;'><i class='bi bi-x-circle'></i></a></td>";
    } else {
        echo "<td style='text-align: center;'><a href='bd/submenu/ativa.php?idSubMenu=$idSubMenu' title='Ativar Menu'><i class='bi bi-check-lg'></i></a></td>";
    }

    if (isset($_SESSION['isAdmin'])) {
        echo "<td style='text-align: center;'><a href='editarSubMenu.php?idSubMenu=$idSubMenu'><i class='material-icons'>edit</i></a></td>";
    }
    

    echo "</tr>";
endwhile;

echo "</tbody>";
echo "</table>";
?>
