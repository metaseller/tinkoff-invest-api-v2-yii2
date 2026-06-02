Yii2 wrapper for unofficial PHP SDK for T-Invest API v2
================

Это расширение позволяет использовать неофициальное PHP SDK for T-Invest API v2 [metaseller/tinkoff-invest-api-v2-php](https://packagist.org/packages/metaseller/tinkoff-invest-api-v2-php), являясь вокруг него оберткой для вашего Yii2 приложения.

PS: До переименования, Т-Банк и Т-Инвестиции назывались "Тинькофф-банк" и "Тинькофф-Инвестиции", нейминги репозиториев, классов и методов пока сохранены в "старом стиле". 

Установка
------------
Самый оптимальный способ установки - это использование [composer](http://getcomposer.org/download/).

Если вы ранее уже ранее устанавливали в своем проекте [metaseller/tinkoff-invest-api-v2-php](https://packagist.org/packages/metaseller/tinkoff-invest-api-v2-php) через Composer, то для начала стоит выполнить
 ```
 $ php composer.phar remove metaseller/tinkoff-invest-api-v2-php
 ```

Если нет - пропустите этот шаг.

Далее для установки выполните
 ```
 $ php composer.phar require metaseller/tinkoff-invest-api-v2-yii2:*
 ```
или добавьте
 ```
 "metaseller/tinkoff-invest-api-v2-yii2": "*"
 ```
в секцию `require` вашего `composer.json` файла.


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

2. Теперь можно использовать компонент
 ```php
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

/** Выводим полученную информацию */
var_dump(['user_info' => [
    'prem_status' => $response->getPremStatus(),
    'qual_status' => $response->getQualStatus(),
    'qualified_for_work_with' => $response->getQualifiedForWorkWith(),
]]);

/**
 * @var GetInfoResponse $response - Получаем ответ, содержащий информацию о пользователе
 */
list($response, $status) = \Yii::$app->tinkoffInvest->usersServiceClient->GetInfo($request)->wait();

/** Выводим полученную информацию */
var_dump(['user_info' => [
    'prem_status' => $response->getPremStatus(),
    'qual_status' => $response->getQualStatus(),
    'qualified_for_work_with' => $response->getQualifiedForWorkWith(),
]]);
 ```

3. Вы можете рассмотреть пример робота-покупателя для T-Инвестиций, написанный на PHP [metaseller/tinkoff-robot-buyer](https://github.com/metaseller/tinkoff-robot-buyer), который реализует две стратегии: (1) - постоянная покупка с заданной скоростью на заданный аккаунт/портфель лотов выбранного ETF. (2) - Автоматическое управление и ребалансировка счета в соответствии с настройками, автоматическое поддержание баланса стоимости акций/облигаций, автоматическая парковка и распарковка свободный средств в денежные фонды.
