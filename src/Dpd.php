<?php

namespace Pugofka\Dpd;

use Illuminate\Support\Facades\Cache;

class Dpd
{
    const DPD_CITIES_CACHE_NAME = 'dpd_cities';

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

        if (Cache::has(self::DPD_CITIES_CACHE_NAME)) {
            $cities = collect(Cache::get(self::DPD_CITIES_CACHE_NAME));
        }
        else {
            $cities = Cache::remember(self::DPD_CITIES_CACHE_NAME, $this->client->cacheLifeTimeInMinutes, function () {
                $client = new \SoapClient($this->client->url."geography2?wsdl",
                    [
                        'trace' => true,
                        'keep_alive' => false
                    ]
                );

                $data['auth'] = $this->client->getAuthData();

                $request['request'] = $data; //помещаем наш масив авторизации в масив запроса request.
                $result = $client->getCitiesCashPay($request); //обращаемся к функции getCitiesCashPay  и получаем список городов.
                $result = self::stdToArray($result);
                return collect($result['return']);
            });
        }

        return $cities;
    }

    protected function stdToArray($obj)
    {
        $rc = (array)$obj;
        foreach($rc as $key=>$item){
            $rc[$key]= (array)$item;
            foreach($rc[$key] as $keys=>$items){
                $rc[$key][$keys]= (array)$items;
            }
        }
        return $rc;
    }

    public function findCity(string $cityName)
    {
        $cities = $this->getCities();
        $city = (object) $cities->whereIn('cityName', $cityName)->first();

        return $city;

    }


}