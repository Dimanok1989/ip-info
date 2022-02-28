# Статистика сайта
Ведение статистики посещения сайта

### Установка
Первым делом необходимо установить пакет
```sh
composer require kolgaev/site-stats
```
Затем создать структуру базы данных используя файл `/config/database.sql`

После необходимо определить переменные в `env` окружении для подключения к базе данных
Если в Вашем проекте имеется файл `.env` или ему подобный, то достаточно добавить в него следующие переменные:
- `DB_HOST` - Адрес сервера базы данных
- `DB_NAME` - Наименование базы данных
- `DB_USER` - Имя пользователя базы данных
- `DB_PASS` - Пароль доступа к базе данных

Если в Вашем проекте не используется загрузка env окружения, то можно определить переменные в php
Например, создать отдельный файл с кофигурацией `env.php`
```php
<?php

putenv("DB_HOST=localhost");
putenv("DB_NAME=block_info");
putenv("DB_USER=root");
putenv("DB_PASS=pass");
```
и передать путь до этого файла конструктору класса 
```php
new \Kolgaev\IpInfo\Ip(__DIR__ . "/env.php");
```

Если Вы используете `apache`, то переменные можно определить в файле `.htaccess`

```sh
SetEnv DB_HOST localhost
SetEnv DB_NAME block_info
SetEnv DB_USER root
SetEnv DB_PASS pass
```

### Использование на самодельных движках
Если Ваш сайт не использует никаких `composer` зависимостей, то можно создать файл в корне проект с любым именем и подключить его в главном `index.php`
Но перед установкой пакет необходимо выполнить инициализацию `composer.json`, если тайкой файл отсутствует в корне проекта
```sh
composer init
```

Файл `site-stats.php`
```php
<?php

require __DIR__ . "/vendor/autoload.php";

try {
    $ip = (new \Kolgaev\IpInfo\Ip())->check();

    if (!empty($ip['block'])) {
        if ($ip['block'] == true) {
            http_response_code(500);
            exit;
        }
    }
} catch (\Exception $e) {
    //
}
```

И в `index.php` в самом начале
```php
require __DIR__ . "/site-stats.php";
```

Теперь в случае блокировки, дальнейшее выполнение кода будет остановлено и на странице отобразится `500` ошибка.
Код ошибки можно менять, например на `400`, `404`, `418 I’m a teapot («я — чайник»)` и тд

Список всех кодов можно взглянуть в [энциклопедии](https://ru.wikipedia.org/wiki/%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_%D0%BA%D0%BE%D0%B4%D0%BE%D0%B2_%D1%81%D0%BE%D1%81%D1%82%D0%BE%D1%8F%D0%BD%D0%B8%D1%8F_HTTP)

Если Ваш сайт уже использует зависимости и ранее у Вас уже подключен `/vendor/autoload.php`, то в файле `site-stats.php` можно исключить подгрузку `autoload.php` и в главном файле подгружать `site-stats.php` после `autoload.php`

[Подробнее об автозагрузке](https://getcomposer.org/doc/01-basic-usage.md#autoloading)