<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

// Corrigido: INPUT_POST ao invés de INPUT_GET
$idMenu = filter_input(INPUT_POST, 'idMenu', FILTER_SANITIZE_SPECIAL_CHARS);
$nmMenu = filter_input(INPUT_POST, 'nmMenu', FILTER_SANITIZE_SPECIAL_CHARS);
$linkMenu = filter_input(INPUT_POST, 'linkMenu', FILTER_SANITIZE_SPECIAL_CHARS);
$redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'administracao';
$aba = filter_input(INPUT_POST, 'aba', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'estrutura';

$queryUpdate = "UPDATE [portalcompras].[dbo].[menu]
                SET [LINK_MENU]='$linkMenu'
                   ,[NM_MENU] = '$nmMenu'
                WHERE [ID_MENU]=$idMenu
                ";

$queryUpdate2 = $pdoCAT->query($queryUpdate);

$_SESSION['msg'] = "Menu atualizado com sucesso.";

$_SESSION['redirecionar'] = "../../{$redirect}.php?aba={$aba}";
$login = $_SESSION['login'];
$tela = 'Menu';
$acao = 'ATUALIZADO';
$idEvento = $idMenu;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");