<?php

require __DIR__ . "/../vendor/autoload.php";

$ip = new \Kolgaev\IpInfo\Ip(__DIR__ . "/../config/env.php");

echo json_encode($ip->check());
