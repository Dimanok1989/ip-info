# Статистика сайта
Ведение статистики посещения сайта

### Установка
Первым делом необходимо установить пакет
```sh
composer require kolgaev/site-stats
```
Затем создать структуру базы данных используя файл `config/database.sql`

### Использование на самодельных движках
Если Ваш сайт не использует никаких `composer` зависимостей, то можно создать файл в корне проект с любым именем и подключить его в главном `index.php`

Файл `site-stats.php`
```php
<?php

require __DIR__ . "/vendor/autoload.php";

$ip = (new \Kolgaev\IpInfo\Ip())->check();

if (!empty($ip['block'])) {
    if ($ip['block'] == true) {
        http_response_code(500);
        exit;
    }
}
```
И в `index.php` в самом начале
```php
require __DIR__ . "/site-stats.php";
```

Теперь в случае блокировки, дальнейшее выполнение кода будет остановлено и на странице отобразится `500` ошибка.
Код ошибки можно менять, например на `400`, `401`, `403`, `404` и тд
`418 I’m a teapot («я — чайник»)`
[Все коды ошибок](https://ru.wikipedia.org/wiki/%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_%D0%BA%D0%BE%D0%B4%D0%BE%D0%B2_%D1%81%D0%BE%D1%81%D1%82%D0%BE%D1%8F%D0%BD%D0%B8%D1%8F_HTTP)

Если Ваш сайт уже использует зависимости и ранее у Вас уже подключен `/vendor/autoload.php`, то в файле `site-stats.php` можно исключить подгрузку `autoload.php` и в главном файле подгружать `site-stats.php` после `autoload.php`