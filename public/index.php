<?php

require __DIR__ . "/../vendor/autoload.php";

try {
    $ip = new \Kolgaev\IpInfo\Ip(__DIR__ . "/../config/env.php");
    $check = $ip->check();

    if (!empty($check['block'])) {
        if ($check['block'] == true) {
            http_response_code(500);
            exit;
        }
    }

    if (getenv("KOLGAEV_STATS_DEBUG")) {
        echo json_encode($check);
    }
} catch (\Exception $e) {
    if (getenv("KOLGAEV_STATS_DEBUG")) {
        echo json_encode([
            'error' => $e->getMessage(),
        ]);
    }
}
