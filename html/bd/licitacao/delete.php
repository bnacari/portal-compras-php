<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

include('protectPerfil.php');

$queryDeleteLicitacao = "SELECT TIPO_LICITACAO FROM [portalcompras].[dbo].DETALHE_LICITACAO WHERE ID_LICITACAO = $idLicitacao";
$queryDeleteLicitacao2 = $pdoCAT->query($queryDeleteLicitacao);

while ($registros = $queryDeleteLicitacao2->fetch(PDO::FETCH_ASSOC)) :
    $idTipo = $registros['TIPO_LICITACAO'];
endwhile;

foreach ($_SESSION['perfil'] as $perfil) {
    if ($perfil['idPerfil'] == $idTipo || isset($_SESSION['isAdmin'])) {
       $isAdminProtect = 1;
    }
}
if ($isAdminProtect != 1) {
    $_SESSION['msg'] = 'Usuário tentando acessar área restrita!';
    header('Location: ../../index.php');
    exit;
}

$queryDeleteLicitacao = "UPDATE [portalcompras].[dbo].[LICITACAO] SET DT_EXC_LICITACAO = getdate() WHERE ID_LICITACAO = $idLicitacao";
$queryDeleteLicitacao2 = $pdoCAT->query($queryDeleteLicitacao);

$queryDeleteDETLicitacao = "UPDATE [portalcompras].[dbo].[DETALHE_LICITACAO] SET DT_EXC_LICITACAO = getdate() WHERE ID_LICITACAO = $idLicitacao";
$queryDeleteDETLicitacao2 = $pdoCAT->query($queryDeleteDETLicitacao);

$_SESSION['msg'] = "Licitação excluída com sucesso.";

// header("Location: ../../administracao.php?aba=usuarios");

$_SESSION['redirecionar'] = '../licitacao.php';
$login = $_SESSION['login'];
$tela = 'Licitação';
$acao = 'Excluída';
$idEvento = $idLicitacao;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
