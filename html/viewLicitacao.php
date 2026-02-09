<?php
// viewLicitacao.php - Redirecionamento para nova URL
$queryString = $_SERVER['QUERY_STRING'];
$novaUrl = 'licitacaoView.php' . ($queryString ? '?' . $queryString : '');
header("HTTP/1.1 301 Moved Permanently");
header("Location: $novaUrl");
exit;