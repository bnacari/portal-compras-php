<?php

include_once 'bd/conexao.php';
include_once('../../protectAdmin.php');

$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$perfilUsuario = filter_input(INPUT_POST, 'perfilUsuario', FILTER_SANITIZE_SPECIAL_CHARS);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar e processar os dados do formulário
    // var_dump($perfilUsuario);
    $querySelect2 = "WITH UserLicitacao AS (
                        SELECT 
                            U.*,
                            A.*
                        FROM [ADCache].[dbo].[Users] U 
                        FULL OUTER JOIN portalcompras.dbo.USUARIO A ON A.EMAIL_ADM COLLATE SQL_Latin1_General_CP1_CI_AI = U.mail COLLATE SQL_Latin1_General_CP1_CI_AI 
                        LEFT JOIN portalcompras.dbo.PERFIL_USUARIO PU on pu.ID_USUARIO = A.ID_ADM
                        LEFT JOIN portalcompras.dbo.TIPO_LICITACAO TL ON TL.ID_TIPO = PU.ID_TIPO_LICITACAO
                        WHERE
                        (U.sAMAccountName LIKE '%$nome%' OR U.displayName LIKE '%$nome%' OR A.NM_ADM like '%$nome%' OR A.EMAIL_ADM LIKE '%$nome%')
                    ";

    if ($perfilUsuario != 0) {
        $querySelect2 .= " AND PU.ID_TIPO_LICITACAO = $perfilUsuario";
    }

    $querySelect2 .= ")
    SELECT distinct *
    FROM UserLicitacao ";

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
        $nmPerfil = $registros['NM_TIPO'];
        $idPerfil = $registros['ID_TIPO'];


        echo "<tr>";
        echo "  
            <td>$matricula</td>
            <td><strong>$login</strong></td>
            <td>$nome</td>
            <td>$unidade</td>
            <td>$email</td>
            ";

        foreach ($_SESSION['perfil'] as $perfil) {
            if ($perfil['idPerfil'] == 9) {
                if ($unidade == 'externo') {
                    if ($status == 'A') {
                        echo "<td style='text-align: center;'><a href='bd/usuario/desativa.php?email=$email' style='color:red'><i class='bi bi-x-circle'></i></a></td>";
                    } else {
                        echo "<td style='text-align: center;'><a href='bd/usuario/ativa.php?email=$email'><i class='bi bi-check-lg'></i></a></td>";
                    }
                } else {
                    echo "<td style='text-align: center;'></td>";
                }
            }
        }

        foreach ($_SESSION['perfil'] as $perfil) {
            if ($perfil['idPerfil'] == 9) {
                echo "<td style='text-align: center;'><a href='editarUsuario.php?email=$email'><i class='bi bi-sliders'></i></a></td>";
            }
        }

        echo "</tr>";


    endwhile;
}
