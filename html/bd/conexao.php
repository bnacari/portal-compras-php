<?php

$serverName = getenv('DB_HOST');
$database = getenv('DB_NAME');
$uid = getenv('DB_USER');
$pwd = getenv('DB_PASS');

header('Content-Type: text/html; charset=utf-8');

try {
    $pdoCAT = new PDO("sqlsrv:server=$serverName;Database=$database", $uid, $pwd, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Log do erro sem expor detalhes ao usuário
    error_log('Erro de conexão com banco de dados: ' . $e->getMessage());
    die('Erro ao conectar com o banco de dados. Tente novamente mais tarde.');
}

?>

