<?php

require __DIR__ . "/../vendor/autoload.php";

try {
    $ip = new \Kolgaev\IpInfo\Ip(__DIR__ . "/../config/env.php");

    if (!empty($ip['block'])) {
        if ($ip['block'] == true) {
            http_response_code(500);
            exit;
        }
    }

    if (getenv("KOLGAEV_STATS_DEBUG")) {
        echo json_encode($ip->check());
    }
} catch (\Exception $e) {
    if (getenv("KOLGAEV_STATS_DEBUG")) {
        echo json_encode([
            'error' => $e->getMessage(),
        ]);
    }
}
