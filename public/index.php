<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . "/../vendor/autoload.php";

$ip = new \Kolgaev\IpInfo\Ip(__DIR__ . "/../config/env.php");

echo json_encode($ip->check());
