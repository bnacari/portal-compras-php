<?php

include_once 'bd/conexao.php';
include_once('../../protectAdmin.php');

$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$perfilUsuario = filter_input(INPUT_POST, 'perfilUsuario', FILTER_SANITIZE_SPECIAL_CHARS);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar e processar os dados do formulário
    // var_dump($perfilUsuario);
    $querySelect2 = "SELECT *
                    FROM [ADCache].[dbo].[Users] U 
                    FULL OUTER JOIN portalcompras.dbo.ADMINISTRADOR A ON A.EMAIL_ADM COLLATE SQL_Latin1_General_CP1_CI_AI = U.mail COLLATE SQL_Latin1_General_CP1_CI_AI 
                    LEFT JOIN portalcompras.dbo.PERFIL p on P.ID_PERFIL = A.ID_PERFIL
                    WHERE 
                    (sAMAccountName LIKE '$nome' OR displayName LIKE '%$nome%' OR A.NM_ADM like '%$nome%' OR A.EMAIL_ADM LIKE '%$nome%')
                    ";

    if ($perfilUsuario != 0) {
        $querySelect2 .= " AND P.ID_PERFIL = $perfilUsuario";
    }

    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
        $matricula = $registros['initials'];

        if (isset($registros['displayName'])) {
            $nome = $registros['displayName'];
            $unidade = $registros['department'];
            $login = $registros['sAMAccountName'];
        } else {
            $nome = $registros['NM_ADM'];
            $unidade = 'externo';
            $login = $registros['LGN_ADM'];
        }


        if (isset($registros['mail'])) {
            $email = $registros['mail'];
        } else {
            $email = $registros['EMAIL_ADM'];
        }

        $administrador = $registros['ID_ADM'];
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

        if ($_SESSION['admin'] == 5) {
            if ($unidade == 'externo') {
                if ($status == 'A') {
                    echo "<td style='text-align: center;'><a href='bd/usuario/desativa.php?email=$email' style='color:red'><i class='material-icons'>close</i></a></td>";
                } else {
                    echo "<td style='text-align: center;'><a href='bd/usuario/ativa.php?email=$email'><i class='material-icons'>check</i></a></td>";
                }
            }
            else {
                echo "<td style='text-align: center;'></td>";
            }
        }

        if ($_SESSION['admin'] == 5) {
                echo "<td style='text-align: center;'><a href='editarUsuario.php?email=$email'><i class='material-icons'>edit</i></a></td>";
        }

        echo "</tr>";


    endwhile;
}
