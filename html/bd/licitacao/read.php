<?php

include_once 'bd/conexao.php';
include_once 'redirecionar.php';

$lgnCriador = $_SESSION['login'];

$tituloLicitacaoFilter = filter_input(INPUT_POST, 'tituloLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$statusLicitacaoFilter = filter_input(INPUT_POST, 'statusLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$dtIniLicitacaoFilter = filter_input(INPUT_POST, 'dtIniLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$dtFimLicitacaoFilter = filter_input(INPUT_POST, 'dtFimLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);

if (!isset($statusLicitacaoFilter) || empty($statusLicitacaoFilter)) {
    $statusLicitacaoFilter = 'Em Andamento';
}
// var_dump($dtIniLicitacaoFilter);

if (isset($tituloLicitacaoFilter)) {
    $tituloLicitacaoFilterSQL = " like '%$tituloLicitacaoFilter%'";
} else {
    $tituloLicitacaoFilterSQL = " like '%%'";
}

if ($statusLicitacaoFilter !== 'vazio') {
    if ($statusLicitacaoFilter == 'Em Andamento') {
        $statusLicitacaoFilter = "like 'Em Andamento'";
    } else if ($statusLicitacaoFilter == 'Suspenso') {
        $statusLicitacaoFilter = "like 'Suspenso'";
    } else if ($statusLicitacaoFilter == 'Encerrado') {
        $statusLicitacaoFilter = "like 'Encerrado'";
    } else if ($statusLicitacaoFilter == 'Rascunho') {
        $statusLicitacaoFilter = "like 'Rascunho'";
    }
} else {
    $statusLicitacaoFilter = "NOT LIKE 'Rascunho'";
}

if (isset($dtIniLicitacaoFilter)) {
    $dtIniLicitacaoFilterSQL = " between '$dtIniLicitacaoFilter' and '$dtFimLicitacaoFilter'";
} else {
    $dtIniLicitacaoFilterSQL = " IS NOT NULL";
    $dtFimLicitacaoFilter = " IS NOT NULL";
}

$querySelect2 = "SELECT  
                    DISTINCT D.*, L.ID_LICITACAO, L.DT_LICITACAO, TIPO.NM_TIPO AS NM_TIPO
                    FROM
                    LICITACAO L
                    LEFT JOIN ANEXO A ON L.ID_LICITACAO = A.ID_LICITACAO
                    LEFT JOIN DETALHE_LICITACAO D ON D.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN TIPO_LICITACAO TIPO ON D.TIPO_LICITACAO = TIPO.ID_TIPO
                WHERE
                    D.STATUS_LICITACAO $statusLicitacaoFilter
                    AND L.DT_EXC_LICITACAO IS NULL
                ";

$querySelect2 .= " AND (D.COD_LICITACAO $tituloLicitacaoFilterSQL OR D.OBJETO_LICITACAO $tituloLicitacaoFilterSQL)";
// var_dump($tituloLicitacaoFilterSQL);

$querySelect2 .= " AND L.DT_LICITACAO $dtIniLicitacaoFilterSQL ";

$querySelect2 .= " ORDER BY L.[DT_LICITACAO] DESC";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

// var_dump($querySelect);
// exit();

echo "<table class='rTableLicitacao'>";

echo "<tbody>";

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idLicitacao = $registros['ID_LICITACAO'];
    $idTipoLicitacao = $registros['TIPO_LICITACAO'];
    $tipoLicitacao = $registros['NM_TIPO'];
    $codLicitacao = $registros['COD_LICITACAO'];
    $statusLicitacao = $registros['STATUS_LICITACAO'];
    $dtLicitacao = date('d/m/Y', strtotime($registros['DT_LICITACAO']));
    $objLicitacao = $registros['OBJETO_LICITACAO'];
    $permitirAtualizacao = $registros['ENVIO_ATUALIZACAO_LICITACAO'];

    if (isset($tipoLicitacao)) {
        $tituloLicitacao = $tipoLicitacao . ' - ' . $codLicitacao;
    } else {
        $tituloLicitacao = $codLicitacao;
    }
    // VERIFICO SE O USUÁRIO JÁ ESTÁ CADASTRADO PARA RECEBER FUTURAS ATUALIZAÇÕES NA LICITAÇÃO
    $idAtualizacao = null;
    $email = $_SESSION['email'];
    $queryUpdateLicitacao = "SELECT ID_ATUALIZACAO 
                            FROM ATUALIZACAO 
                            WHERE ID_LICITACAO = $idLicitacao 
                            AND EMAIL_ADM LIKE '$email' 
                            AND DT_EXC_ATUALIZACAO IS NULL";
    $queryUpdateLici2 = $pdoCAT->query($queryUpdateLicitacao);
    while ($registros = $queryUpdateLici2->fetch(PDO::FETCH_ASSOC)) :
        $idAtualizacao = $registros['ID_ATUALIZACAO'];
    endwhile;


    echo "<tr>";

    // echo "<td>$idLicitacao</td>";
    // echo "<td title='$objLicitacao'><strong>$tituloLicitacao</strong></td>";
    echo "<td title='$objLicitacao'>
            <p>
                <a href='viewLicitacao.php?idLicitacao=$idLicitacao'>
                    <strong><h7>$tituloLicitacao</h7></strong>
                </a> ";

    foreach ($_SESSION['perfil'] as $perfil) {
        // Verifica se o ID do perfil é igual a 9
        if ($perfil['idPerfil'] == 9 || $perfil['idPerfil'] == $idTipoLicitacao ) {
            echo "<a href='editarLicitacao.php?idLicitacao=$idLicitacao' style='color:#999999;' title='Editar Licitação'><i class='material-icons'>tune</i></a>";

            echo "<a href='#' onclick='confirmDelete($idLicitacao)' style='color:#999999; padding-left:5px' title='Apagar Licitação'><i class='material-icons'>delete</i></a>";
        }
    }

    if ($permitirAtualizacao == 1 && isset($email)) {
        if (!isset($idAtualizacao)) {
            echo "<a href='#' onclick='enviarAtualizacao($idLicitacao)' style='color:#FF1919; padding-left:5px' title='Usuário receberá notificação em caso de atualização da licitação.'><i class='material-icons'>favorite_border</i></a>";
        } else {
            echo "<a href='#' onclick='desativarAtualizacao($idAtualizacao)' style='color:#FF1919; padding-left:5px' title='Usuário deixará de receber notificação em caso de atualização da licitação.'><i class='material-icons'>favorite</i></a>";
        }
    }

    echo "</p>
            <p style='color:#999999'>$objLicitacao</p>
            <label class='custom-label'><strong>$dtLicitacao</strong></label>";
    if ($statusLicitacao == 'Em Andamento') {
        echo "<fieldset class='custom-fieldset'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    } else if ($statusLicitacao == 'Suspenso') {
        echo "<fieldset class='custom-fieldset suspenso'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    } else if ($statusLicitacao == 'Rascunho') {
        echo "<fieldset class='custom-fieldset rascunho'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    } else {
        echo "<fieldset class='custom-fieldset encerrado'><label class='custom-label'>$statusLicitacao</label></fieldset>";
    }
    echo "</td>";

    echo "</tr>";
endwhile;

echo "</tbody>";
echo "</table>";

echo "
<script>
function confirmDelete(idLicitacao) {
    var resposta = confirm('Tem certeza que deseja excluir a licitação?');

    if (resposta) {
        window.location.href = 'bd/licitacao/delete.php?idLicitacao=' + idLicitacao;
    }
}

function enviarAtualizacao(idLicitacao) {
    
    window.location.href = 'bd/licitacao/enviarAtualizacao.php?idLicitacao=' + idLicitacao;
    
}

function desativarAtualizacao(idAtualizacao) {
    
    window.location.href = 'bd/atualizacao/desativar.php?idAtualizacao=' + idAtualizacao;
    
}
</script>";
