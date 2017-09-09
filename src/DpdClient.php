<?php

namespace Pugofka\Dpd;

class DpdClient
{
    protected $number;
    protected $key;
    protected $testMode;
    protected $url;

    const API_URL_TEST = 'http://wstest.dpd.ru/services/';
    const API_URL_PROD = 'http://ws.dpd.ru/services/';

    public function __construct()
    {
        $this->number = config('dpd.number');
        $this->key = config('dpd.key');
        $this->testMode = config('dpd.test_mode');

        if($this->testMode)
            $this->url = self::API_URL_TEST;
        else
            $this->url = self::API_URL_PROD;

    }


}