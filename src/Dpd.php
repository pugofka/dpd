<?php

namespace Pugofka\Dpd;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Dpd
{
    const DPD_CITIES_CACHE_NAME = 'dpd_cities';

    protected $client;

    public function __construct($number = null, $key = null, $testMode = null, $cacheLifeTimeInMinutes = null, $rest = false)
    {
        $this->client = new DpdClient($number, $key , $testMode , $cacheLifeTimeInMinutes, $rest);
    }

    public function getShipmentList($eshopOrderNum, $phone, $email, $orderNum)
    {
        $data['auth'] = $this->client->getAuthData();
        $data['eshopOrderNum'] = $eshopOrderNum;
        $data['phone'] = $phone;
        $data['email'] = $email;
        $data['orderNum'] = $orderNum;

        return $this->sendRestRequest($data, 'getShipmentList');
    }

    public function getParcelShopList($sessionId, $orderId)
    {
        $data['auth'] = [
            'sessionId' => $sessionId,
            'orderId' => $orderId
        ];

        return $this->sendRestRequest($data, 'getParcelShopList');
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
     * Get all cities from DPD and store it in Cache
     *
     * @return \Illuminate\Support\Collection
     */
    public function getParcelShops()
    {
        $client = new \SoapClient($this->client->url."geography2?wsdl",
            [
                'trace' => true,
                'keep_alive' => false
            ]
        );

        $data['auth'] = $this->client->getAuthData();

        $request['request'] = $data; //помещаем наш масив авторизации в масив запроса request.
        $result = $client->getParcelShops($request); //обращаемся к функции getCitiesCashPay  и получаем список городов.
        $result = self::stdToArray($result);

        return collect($result['return']);
    }


    /**
     *  Find City in DPD from
     *
     * @param string $cityName
     * @return object
     * @throws \Exception
     */
    public function findCityByName(string $cityName)
    {
        $cities = $this->getCities();
        $city = (object) $cities->whereIn('cityName', $cityName);

        if($city->count() == 0)
            throw new \Exception('City is not found', 404);

        return $city->first();

    }

    /**
     * Это дополнительный метод, для поиска города по ID
     * @param string $cityId
     * @return mixed
     * @throws \Exception
     *
     */
    public function findCityById(string $cityId)
    {
        $cities = $this->getCities();
        $city = (object) $cities->whereIn('cityId', $cityId);

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
     * @param string|null $serviceCode
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
        string $serviceCode = null,
        float $volume = null
    )
    {
        $client = new \SoapClient($this->client->url."calculator2?wsdl",
            [
                'trace' => true,
                'keep_alive' => false
            ]
        );

        $cityFrom = $this->findCityById($from);
        $cityTo = $this->findCityById($to);

        $data['auth'] = $this->client->getAuthData();
        $data['pickup'] = [
            'cityId' => $cityFrom['cityId'],
            'cityName'  => $cityFrom['cityName'],
        ];

        $data['delivery'] = [
            'cityId' => $cityTo['cityId'],
            'cityName'  => $cityTo['cityName'],
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
        if($serviceCode)
            $data['serviceCode'] = $serviceCode;

        $request['request'] = $data; //помещаем наш масив авторизации в масив запроса request.
        $result = $client->getServiceCost2($request); //обращаемся к функции getServiceCost2 и получаем варианты доставки.
        $result = self::stdToArray($result);
        if(isset($result['errorMessage'])) {
            if(is_string($result['errorMessage']))
                $error = $result['errorMessage'];
            else
                $error = json_decode($result['errorMessage']);
            throw new \Exception("Error from DPD: ".$error, 400);
        }
        return $result;
    }

    protected function sendRestRequest($data, $path)
    {
        $json = json_encode($data);

        $response = \Httpful\Request::post($this->client->url . $path)
            ->body($json)
            ->sendsJson()
            ->send();

        return json_decode($response->raw_body);
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