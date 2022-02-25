<?php

namespace Kolgaev\IpInfo;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

class DataBase
{
    /**
     * Инициализация объекта базы данных
     * 
     * @param null|string $config_path
     * @return void
     */
    public function __construct($config_path = null)
    {
        if ($config_path and file_exists($config_path))
            require $config_path;

        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => getenv('DB_HOST') ?: 'localhost',
            'database'  => getenv('DB_NAME') ?: 'database',
            'username'  => getenv('DB_USER') ?: 'root',
            'password'  => getenv('DB_PASS') ?: 'password',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setEventDispatcher(new Dispatcher(new Container));

        $capsule->setAsGlobal();

        $capsule->bootEloquent();
    }
}
