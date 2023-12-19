<?php

include_once 'bd/conexao.php';
include('protectAdmin.php');

$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$perfilUsuario = filter_input(INPUT_POST, 'perfilUsuario', FILTER_SANITIZE_SPECIAL_CHARS);

// var_dump($perfilUsuario);

$querySelect2 = "SELECT * FROM [ADCache].[dbo].[Users] U 
                    LEFT JOIN portalcompras.dbo.ADMINISTRADOR A ON A.EMAIL_ADM COLLATE SQL_Latin1_General_CP1_CI_AI = U.mail COLLATE SQL_Latin1_General_CP1_CI_AI
                    LEFT JOIN portalcompras.dbo.PERFIL p on P.ID_PERFIL = A.ID_PERFIL
                    WHERE 
                    (sAMAccountName LIKE '$nome' OR displayName LIKE '%$nome%')
                    AND IsEnabled = 1 
                    AND initials IS NOT NULL
                    --AND P.DT_EXC_PERFIL IS NULL 
                    ";

if ($perfilUsuario != 0){
    $querySelect2 .= " AND P.ID_PERFIL = $perfilUsuario";
}
                   
$querySelect = $pdoCAT->query($querySelect2);

while($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
    $matricula = $registros['initials'];
    $nome = $registros['displayName'];
    $login = $registros['sAMAccountName'];
    $unidade = $registros['department'];
    $email = $registros['mail'];
    $administrador = $registros['COD_ADM'];
    $status = $registros['STATUS'];
    $nmPerfil = $registros['NM_PERFIL'];
    $idPerfil = $registros['ID_PERFIL'];

    echo "<tr>";
    echo "  
            <td>$matricula</td>
            <td><strong>$login</strong></td>
            <td>$nome</td>
            <td>$unidade</td>
            <td>$email</td>
            <td>$nmPerfil</td>
            ";

    if ($_SESSION['admin']) {
        echo "<td style='text-align: center;'><a href='editarUsuario.php?matricula=$matricula'><i class='material-icons'>edit</i></a></td>";
    }

    echo "</tr>";

    

endwhile;