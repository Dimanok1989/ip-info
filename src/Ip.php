<?php

namespace Kolgaev\IpInfo;

use Exception;
use Kolgaev\IpInfo\Models\Block;

class Ip extends DataBase
{
    /**
     * Проверка ip, учет статистики и вывод данных
     * 
     * @return array
     */
    public function check()
    {
        $ip = $this->ip();

        $response = [
            'block' => $this->checkBlockIp($ip),
        ];

        return $response;
    }

    /**
     * Проверка наличия блокировки IP
     * 
     * @param string $ip
     * @return bool|null
     */
    public function checkBlockIp($ip = null)
    {
        try {
            return Block::whereHost($ip)->whereIsBlock(1)->count() > 0;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Проверка ip клиента
     * 
     * @param bool $array Вернуть массив адресов
     * @return string|null
     */
    public function ip($array = false)
    {
        $list = [];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = explode(',', $_SERVER['HTTP_CLIENT_IP']);
            $list = array_merge($list, $ip);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $list = array_merge($list, $ip);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $list[] = $_SERVER['REMOTE_ADDR'];
        }

        $list = array_unique($list);

        return $array ? $list : ($list[0] ?? null);
    }
}
