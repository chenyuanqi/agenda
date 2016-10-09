<?php

include_once './common.php';

try
{
    $link = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS, [ PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "UTF8"' ]);
}
catch ( PDOException $e )
{
    echo 'Connection failed: '.$e->getMessage();
}

date_default_timezone_set(TIME_ZONE);

