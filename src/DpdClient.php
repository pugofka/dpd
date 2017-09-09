<?php

namespace Pugofka\Dpd;

class DpdClient
{
    public $number;
    public $key;
    public $url;
    protected $testMode;

    const API_URL_TEST = 'http://wstest.dpd.ru/rest/application.wadl';
    const API_URL_PROD = 'http://ws.dpd.ru/rest/application.wadl';

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