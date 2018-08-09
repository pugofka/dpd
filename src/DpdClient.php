<?php

namespace Pugofka\Dpd;

use Illuminate\Contracts\Cache\Repository;

class DpdClient
{
    public $number;
    public $key;
    public $url;
    public $cacheLifeTimeInMinutes;
    protected $testMode;

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $cache;

    /** @var int */

    const API_URL_TEST = 'http://wstest.dpd.ru/services/';
    const API_URL_PROD = 'http://ws.dpd.ru/services/';

    public function __construct($number = null, $key = null, $testMode = null, $cacheLifeTimeInMinutes = null)
    {
        $cache = app(Repository::class);
        $this->cache = $cache;

        $number ? $this->number = $number : $this->number = config('dpd.number');
        $key ? $this->key = $key : $this->key = config('dpd.key');
        $testMode ? $this->testMode = $testMode : $this->testMode = config('dpd.test_mode');
        $cacheLifeTimeInMinutes ? $this->cacheLifeTimeInMinutes = $cacheLifeTimeInMinutes : $this->cacheLifeTimeInMinutes = config('dpd.cache_lifetime_in_minutes', 0);

        if ($this->testMode)
            $this->url = self::API_URL_TEST;
        else
            $this->url = self::API_URL_PROD;
    }

    public function getAuthData()
    {
        return [
            'clientNumber' => $this->number,
            'clientKey' => $this->key
        ];
    }


}