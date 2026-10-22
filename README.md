Yii2 wrapper for unofficial PHP SDK for T-Invest API v2
================

Это расширение позволяет использовать неофициальное PHP SDK for T-Invest API v2 [metaseller/tinkoff-invest-api-v2-php](https://packagist.org/packages/metaseller/tinkoff-invest-api-v2-php), являясь вокруг него оберткой для вашего Yii2 приложения.

PS: До переименования, Т-Банк и Т-Инвестиции назывались "Тинькофф-банк" и "Тинькофф-Инвестиции", нейминги репозиториев, классов и методов пока сохранены в "старом стиле". 

Установка
------------
Требования версии 0.3.0:

* PHP `^8.5.12` (для разработки — 8.5.12, см. `.php-version`).
* Yii2 `^2.0.55`.
* SDK `metaseller/tinkoff-invest-api-v2-php` `^0.6.0`.
* Расширения PHP: gRPC `^1.83.1`, BCMath, Ctype, JSON и Mbstring.
* Транзитивные зависимости SDK: `google/protobuf` `^5.36.2`, `grpc/grpc` `^1.82`.

Нижняя граница Yii2 включает исправления совместимости с PHP 8.5 и исправление
безопасности в 2.0.55. Composer разрешает только стабильные версии зависимостей.
Расширение gRPC должно быть собрано для используемой версии PHP.

Самый оптимальный способ установки - это использование [composer](http://getcomposer.org/download/).

Если вы ранее уже ранее устанавливали в своем проекте [metaseller/tinkoff-invest-api-v2-php](https://packagist.org/packages/metaseller/tinkoff-invest-api-v2-php) через Composer, то для начала стоит выполнить
 ```
 $ php composer.phar remove metaseller/tinkoff-invest-api-v2-php
 ```

Если нет - пропустите этот шаг.

Далее для установки выполните
 ```
 $ php composer.phar require metaseller/tinkoff-invest-api-v2-yii2:^0.3.0
 ```
или добавьте
 ```
 "metaseller/tinkoff-invest-api-v2-yii2": "^0.3.0"
 ```
в секцию `require` вашего `composer.json` файла.

Для обновления существующего приложения используйте `composer update --with-all-dependencies`,
затем `composer check-platform-reqs`. Настройка `config.platform.php` задает версию
для разрешения зависимостей, но не устанавливает PHP и не заменяет проверку реального окружения.
Lock-файл библиотеки не поставляется; версии установки фиксируются в lock-файле приложения.
Для Bower-зависимостей Yii2 приложению нужен репозиторий `https://asset-packagist.org`
либо другой настроенный в приложении источник этих пакетов.

Проверки после установки зависимостей: `composer validate`, `composer check-platform-reqs`,
`composer test`, `composer audit`. Тест совместимости не выполняет RPC и торговых операций.


Использование
-----
0. Прочитайте требования к настройкам окружения в описании: https://github.com/metaseller/tinkoff-invest-api-v2-php


1. Добавьте компонент в ваш глобальный конфигурационный файл проекта `main.php`:
 ```php
 'components' => [
     'tinkoffInvest' => [
         'class' => 'Metaseller\yii2TinkoffInvestApi2\TinkoffInvestApi',
         'apiToken' => '<Your Tinkoff Invest Account Token>',
     ],
 ],
 ```

Информация где взять токен здесь - https://tinkoff.github.io/investAPI/token/ 

После создания компонента меняйте токен через `setApiToken($token)`, чтобы SDK
сбросил кеш клиентов. Прямое присваивание публичному `apiToken` предназначено
для начальной конфигурации. Замечания ревью версии 0.3.0 приведены в [CODE_REVIEW.md](CODE_REVIEW.md).

2. Теперь можно использовать компонент
 ```php
use Tinkoff\Invest\V1\GetInfoRequest;
use Tinkoff\Invest\V1\GetInfoResponse;

/**
 * Создаем экземпляр запроса информации об аккаунте к сервису
 *
 * Запрос не принимает никаких параметров на вход
 *
 * @see https://tinkoff.github.io/investAPI/users/#getinforequest
 */
$request = new GetInfoRequest();

/**
 * @var GetInfoResponse $response - Получаем ответ, содержащий информацию о пользователе
 */
list($response, $status) = \Yii::$app->tinkoffInvest->usersServiceClient->GetInfo($request)->wait();
\Yii::$app->tinkoffInvest->processRequestStatus($status);

/** Выводим полученную информацию */
var_dump(['user_info' => [
    'prem_status' => $response->getPremStatus(),
    'qual_status' => $response->getQualStatus(),
    'qualified_for_work_with' => $response->getQualifiedForWorkWith(),
]]);

 ```

3. Вы можете рассмотреть пример робота-покупателя для T-Инвестиций, написанный на PHP [metaseller/tinkoff-robot-buyer](https://github.com/metaseller/tinkoff-robot-buyer), который реализует две стратегии: (1) - постоянная покупка с заданной скоростью на заданный аккаунт/портфель лотов выбранного ETF. (2) - Автоматическое управление и ребалансировка счета в соответствии с настройками, автоматическое поддержание баланса стоимости акций/облигаций, автоматическая парковка и распарковка свободный средств в денежные фонды.
