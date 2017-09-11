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

    public function __construct()
    {
        $cache = app(Repository::class);
        $this->number = config('dpd.number');
        $this->key = config('dpd.key');
        $this->testMode = config('dpd.test_mode');
        $this->cacheLifeTimeInMinutes = config('dpd.cache_lifetime_in_minutes', 0);

        if($this->testMode)
            $this->url = self::API_URL_TEST;
        else
            $this->url = self::API_URL_PROD;

        $this->cache = $cache;
    }

    public function getAuthData()
    {
        return [
            'clientNumber' => $this->number,
            'clientKey' => $this->key
        ];
    }


}