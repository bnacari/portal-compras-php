<?php

$serverName = getenv('DB_HOST');
$database = getenv('DB_NAME');
$uid = getenv('DB_USER');
$pwd = getenv('DB_PASS');

$utf8 = header('Content-Type: text/html; charset=utf-8');
$pdoCAT = new PDO("sqlsrv:server=$serverName;Database=$database", $uid, $pwd);
// $pdoCAT->set_charset('utf-8');

?>

