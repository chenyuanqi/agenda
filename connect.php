<?php

include_once './common.php';

$link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
mysql_select_db(DB_NAME, $link);
mysql_query("SET names UTF8");

date_default_timezone_set(TIME_ZONE);

