<?php

namespace Pugofka\Dpd;

class DpdClient
{
    public $number;
    public $key;
    public $url;
    public $cacheLifeTimeInMinutes;
    protected $testMode;

    private $client;

    /** @var int */

    const API_URL_TEST = 'http://wstest.dpd.ru/services/';
    const API_URL_PROD = 'http://ws.dpd.ru/services/';

    const apiMethods = [
        'getCitiesCashPay' => 'geography',
        'getParcelShops' => 'geography',
        'getServiceCost2' => 'calculator',
    ];

    public function __construct()
    {
        $this->number = config('dpd.number');
        $this->key = config('dpd.key');
        $this->testMode = config('dpd.test_mode');
        $this->cacheLifeTimeInMinutes = config('dpd.cache_lifetime_in_minutes', 0);

        if($this->testMode)
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

    public function getConnection($path) {

        if ($this->client) {
            return $this->client;
        }

        try {

            $client = new \SoapClient($this->url . $path . "2?wsdl",
                [
                    'trace' => true,
                    'keep_alive' => false
                ]
            );

            $this->client = $client;

        } catch (\Exception $e) {

            return $result['errorMessage'] = 'dpd service connection error';

        }

        return $client;
    }

    public function invoke($method, $data = [], $toArray = true) {
        try {

            $client = $this->getConnection(
                self::apiMethods[$method]
            );

            $data['auth'] = $this->getAuthData();

            $request['request'] = $data;

            $result = $client->{$method}($request);

            if ($toArray) {
                $result = $this->_parceObj2Arr($result->return);
            }

            return $result;

        } catch (\Exception $e) {

            return $result['errorMessage'] = $e->getMessage();

        }
    }

    private function _parceObj2Arr($obj, $isUTF = 1, $arr = array()) {
        $isUTF = $isUTF ? 1 : 0;
        if (is_object($obj) || is_array($obj)) {
            $arr = array();
            for (reset($obj); list($k, $v) = each($obj);) {
                if ($k === "GLOBALS")
                    continue;
                $arr[$k] = $this->_parceObj2Arr($v, $isUTF, $arr);
            }
            return $arr;
        } elseif (gettype($obj) == 'boolean') {
            return $obj ? 'true' : 'false';
        } else {
//            if ($isUTF && gettype($obj) == 'string')
//                $obj = iconv('utf-8', 'windows-1251', $obj);
            return $obj;
        }
    }

}