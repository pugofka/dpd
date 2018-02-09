<?php

namespace Pugofka\Dpd;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Dpd
{
    const DPD_CITIES_CACHE_NAME = 'dpd_cities';

    protected $client;

    public function __construct()
    {
        $this->client = new DpdClient();
    }

    /**
     * Get all cities from DPD and store it in Cache
     *
     * @return \Illuminate\Support\Collection
     */
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


    /**
     *  Find City in DPD from
     *
     * @param string $cityName
     * @return object
     * @throws \Exception
     */
    public function findCity(string $cityName)
    {
        $cities = $this->getCities();
        $city = (object) $cities->whereIn('cityName', $cityName);

        if($city->count() == 0)
            throw new \Exception('City is not found', 404);

        return $city->first();

    }


    /**
     * Method for get common cost from DPD.
     *
     * @param string $from
     * @param string $to
     * @param bool $selfPickup
     * @param bool $selfDelivery
     * @param float $weight
     * @param float $declaredValue
     * @param null $pickupDate
     * @param float|null $volume
     * @return array
     */
    public function getCostCommon (
        string $from,
        string $to,
        bool $selfPickup = true,
        bool $selfDelivery=false,
        float $weight = 0,
        float $declaredValue = 0,
        $pickupDate = null,
        float $volume = null
    )
    {
        $client = new \SoapClient($this->client->url."calculator2?wsdl",
            [
                'trace' => true,
                'keep_alive' => false
            ]
        );

        $cityFrom = $this->findCity($from);
        $cityTo = $this->findCity($to);

        $data['auth'] = $this->client->getAuthData();
        $data['pickup'] = [
            'cityId' => $cityFrom->cityId,
            'cityName'  => $cityFrom->cityName,
        ];
        $data['delivery' ] = [
            'cityId' => $cityTo->cityId,
            'cityName'  => $cityTo->cityName,
        ];
        $data['selfPickup'] = $selfPickup;
        $data['selfDelivery'] = $selfDelivery;
        $data['weight'] = $weight;
        if($volume)
            $data['volume'] = $volume;
        if($pickupDate)
            $data['pickupDate'] = Carbon::parse($pickupDate)->format('Y-m-d');
        if($declaredValue)
            $data['declaredValue'] = $declaredValue;

        $request['request'] = $data; //помещаем наш масив авторизации в масив запроса request.
        $result = $client->getServiceCost2($request); //обращаемся к функции getServiceCost2 и получаем варианты доставки.
        return $result = self::stdToArray($result);
    }


    /**
     *  Reformat response from DPD
     *
     * @param $obj
     * @return array
     */
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
}