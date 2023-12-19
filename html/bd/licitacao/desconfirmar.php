<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include('protectAdmin.php');

$login = $_SESSION['login'];

$idVisita = filter_input(INPUT_GET, 'idVisita', FILTER_SANITIZE_NUMBER_INT);

$queryUpdateAprovacao = "UPDATE [VisitaAgendada].[dbo].[VISITA] SET DT_REALIZADA_VISITA = NULL WHERE ID_VISITA = $idVisita";

$queryUpdateAprovacao2 = $pdoCAT->query($queryUpdateAprovacao);

$_SESSION['msg'] = "<p class='center red-text'>".'Visita<strong> DESCONFIRMADA </strong>com <strong>sucesso</strong>.'."</p>";

// header("Location: ../../consultarUsuario.php");

$_SESSION['redirecionar'] = '../../consultarVisita.php';
// $login = $_SESSION['login'];
// $tela = 'Ativa ADM';
// $acao = 'ADM Cadastrado: '.$matricula;
// $evento = 0;
redirecionar($_SESSION['redirecionar']);