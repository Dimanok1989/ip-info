<?php

namespace Kolgaev\IpInfo;

use Exception;
use Kolgaev\IpInfo\Models\Block;
use Kolgaev\IpInfo\Models\Statistic;
use Kolgaev\IpInfo\Models\Visit;

class Ip extends DataBase
{
    /**
     * Флаг блокировки клиента
     * 
     * @var null|bool
     */
    protected $block = null;

    /**
     * Проверка ip, учет статистики и вывод данных
     * 
     * @return array
     */
    public function check()
    {
        $this->ip = $this->ip();
        $this->block = $this->checkBlockIp();

        $story = $this->writeStory();

        return array_merge($story, [
            'block' => $this->block,
        ]);
    }

    /**
     * Проверка наличия блокировки IP
     * 
     * @return bool|null
     */
    public function checkBlockIp()
    {
        try {
            return Block::whereHost($this->ip)->whereIsBlock(1)->count() > 0;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Запись истории посещения
     * 
     * @return array
     */
    public function writeStory()
    {
        try {
            Visit::create([
                'ip' => $this->ip,
                'is_blocked' => $this->block,
                'page' => $_SERVER['REQUEST_URI'] ?? null,
                'method' => $_SERVER['REQUEST_METHOD'] ?? null,
                'referer' => $_SERVER['HTTP_REFERER'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'request_data' => [
                    'headers' => getallheaders(),
                    'post' => $_POST ?? null,
                    'get' => $_GET ?? null,
                ],
                'created_at' => date("Y-m-d H:i:s"),
            ]);

            $statistic = Statistic::firstOrNew([
                'date' => date("Y-m-d"),
                'ip' => $this->ip,
            ]);

            if ($this->block)
                $statistic->visits_drops++;
            else 
                $statistic->visits++;

            $statistic->save();

            $statistic = $statistic->only('visits', 'requests', 'visits_drops');

            return array_merge($statistic, [
                'visits_all' => $statistic['visits'] + $statistic['visits_drops'],
            ]);
        } catch (Exception $e) {
            return [
                'writeStory' => $e->getMessage(),
            ];
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
