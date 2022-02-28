<?php

namespace Kolgaev\IpInfo;

use Exception;
use Kolgaev\IpInfo\Models\AutomaticBlock;
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
     * Флаг автоматической блокировки клиента
     * 
     * @var null|bool
     */
    protected $auto_block = null;

    /**
     * Массив ошибок
     * 
     * @var array
     */
    protected $errors = [];

    /**
     * Массив ответа
     * 
     * @var array
     */
    protected $default_response = [
        'auto_block' => null,
        'block' => null,
        'requests' => 0,
        'visits' => 0,
        'visits_drops' => 0,
        'visits_all' => 0,
    ];

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

        $response = [
            'auto_block' => $this->auto_block,
            'block' => $this->block,
        ];

        if (count($this->errors)) {
            $response['errors'] = $this->errors;
        }

        return array_merge($this->default_response, $story, $response);
    }

    /**
     * Проверка наличия блокировки IP
     * 
     * @return bool|null
     */
    public function checkBlockIp()
    {
        if ($auto_block = $this->checkAutoBlock())
            return $auto_block;

        try {
            return Block::whereHost($this->ip)->whereIsBlock(1)->count() > 0;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            return null;
        }
    }

    /**
     * Проверка автоматической блокировки
     * 
     * @return bool|null
     */
    public function checkAutoBlock()
    {
        try {
            return $this->auto_block = AutomaticBlock::whereIp($this->ip)->where('date', date("Y-m-d"))->count() > 0;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
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
                'is_blocked' => (bool) $this->block,
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
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        try {
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
            $this->errors[] = $e->getMessage();
        }

        return [];
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
