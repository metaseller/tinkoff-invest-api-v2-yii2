<?php

use Metaseller\TinkoffInvestApi2\TinkoffClientsFactory;
use Metaseller\yii2TinkoffInvestApi2\TinkoffInvestApi;
use yii\base\Configurable;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';

set_error_handler(static function ($severity, $message, $file, $line): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function factoryValue(TinkoffInvestApi $component, string $property): mixed
{
    return (new ReflectionProperty(TinkoffClientsFactory::class, $property))->getValue($component);
}

check(PHP_VERSION_ID >= 80512, 'PHP 8.5.12 or newer is required.');
check(version_compare(phpversion('grpc') ?: '0', '1.83.1', '>='), 'ext-grpc 1.83.1 or newer is required.');

$component = Yii::createObject([
    'class' => TinkoffInvestApi::class,
    'apiToken' => 'test-token',
    'appName' => 'test.application',
]);
check($component instanceof Configurable, 'Yii2 configuration contract was lost.');
check(factoryValue($component, '_api_token') === 'test-token', 'Configured token was not passed to the SDK.');
check(factoryValue($component, '_app_name') === 'test.application', 'Configured app name was not passed to the SDK.');

$default = new TinkoffInvestApi(['apiToken' => 'test-token']);
check(factoryValue($default, '_app_name') === TinkoffClientsFactory::DEFAULT_APP_NAME, 'SDK default app name was lost.');
check(factoryValue(new TinkoffInvestApi(), '_api_token') === null, 'Empty configuration must remain supported.');

$created = TinkoffInvestApi::create('factory-token');
check(factoryValue($created, '_api_token') === 'factory-token', 'Static factory must use the Yii2 constructor.');
check(factoryValue(TinkoffInvestApi::create(null), '_api_token') === null, 'Static factory must accept a null token.');

class CustomTinkoffInvestApi extends TinkoffInvestApi
{
}
check(CustomTinkoffInvestApi::create('test-token') instanceof CustomTinkoffInvestApi, 'Static factory must preserve subclasses.');

try {
    new TinkoffInvestApi(['unknownOption' => true]);
    throw new RuntimeException('Unknown configuration option was silently accepted.');
} catch (Exception $exception) {
    check($exception->getMessage() === 'Undefined property $unknownOption', 'Unexpected configuration error.');
}

// Creating clients is lazy; no RPC or trading operation is performed.
$client = $component->usersServiceClient;
check($client instanceof \Tinkoff\Invest\V1\UsersServiceClient, 'SDK client could not be created.');
check($component->usersServiceClient === $client, 'SDK client cache was lost.');
$component->setApiToken('replacement-token');
check(factoryValue($component, '_users_service_client') === null, 'Changing the token must reset cached clients.');
$client->close();

echo "Yii2 / SDK compatibility checks passed.\n";
