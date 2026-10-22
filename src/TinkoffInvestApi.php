<?php

namespace Metaseller\yii2TinkoffInvestApi2;

use Metaseller\TinkoffInvestApi2\TinkoffClientsFactory;
use yii\base\Configurable;
use Yii;

/**
 * Компонент доступа к Tinkoff Invest Api V2 через неофициальный PHP SDK for Tinkoff Invest API v2
 *
 * @package Metaseller\yii2TinkoffInvestApi2
 *
 * @see https://github.com/metaseller/tinkoff-invest-api-v2-php
 */
class TinkoffInvestApi extends TinkoffClientsFactory implements Configurable
{
    /**
     * @var string Токен доступа к Tinkoff Invest API 2
     *
     * @see https://tinkoff.github.io/investAPI/token/
     */
    public $apiToken;

    /**
     * @var string|null Значение AppName для запросов к Tinkoff Invest API 2
     *
     * @see https://tinkoff.github.io/investAPI/grpc/#appname
     */
    public $appName = null;

    /**
     * @inheritDoc
     */
    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            Yii::configure($this, $config);
        }

        parent::__construct($this->apiToken, $this->appName);
    }

    /**
     * Создает компонент через конфигурационный конструктор Yii2.
     */
    public static function create(?string $api_token): self
    {
        return new static(['apiToken' => $api_token]);
    }
}
