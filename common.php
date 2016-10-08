<?php
error_reporting(0);
header("Content-Type: text/html; charset=utf-8");

include_once './config.php';
if (ACCESS_TOKEN !== $_COOKIE['CYQ']) {
    header("location:validate.php");
}