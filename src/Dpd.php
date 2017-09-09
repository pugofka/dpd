<?php

namespace Pugofka\Dpd;

class Dpd
{
    protected $client;

    public function __construct()
    {
        $this->client = new DpdClient();
    }

    public function hello()
    {
        return 'hello';
    }

    public function getCities()
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $this->client->url.'geography2?wsdl');

        $arData['auth'] = array(
            'clientNumber' => $this->client->number,
            'clientKey' => $this->client->key);
//        $arRequest['request'] = $arData; //помещаем наш масив авторизации в масив запроса request.
//        $ret = $client->getCitiesCashPay($arRequest); //обращаемся к функции getCitiesCashPay  и получаем список городов.


        dd($res->getBody()->getContents());
        return $res->getBody()->getContents();

    }
}