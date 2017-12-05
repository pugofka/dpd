<?php

namespace Pugofka\Dpd;

class Dpd
{
    protected $client;

    protected $parcel;

    public function __construct()
    {
        $this->client = new DpdClient();

    }

    public function getCities()
    {
        $result = $this->client->invoke('getCitiesCashPay');

        return $result;
    }

    public function getPostamats($cityCode) {

        $data['cityCode'] = $cityCode;

        $result = $this->client->invoke('getParcelShops', $data);

        return $result;
    }

    public function getCostCommon (
        int $from,
        int $to,
        bool $selfPickup = true,
        bool $selfDelivery=false,
        float $weight = 0,
        float $declaredValue = 0,
        float $volume = null,
        $pickupDate = null,
        $serviceCode = null
    )
    {
        $data['selfDelivery'] = $selfDelivery;
        $data['selfPickup'] = $selfPickup;
        $data['weight'] = $weight;

        if($volume) {
            $data['volume'] = $volume;
        }

        if($declaredValue) {
            $data['declaredValue'] = $declaredValue;
        }

        if ($pickupDate) {
            $data['pickupDate'] = date('Y-m-d', strtotime($pickupDate));
        }

        if($declaredValue) {
            $data['serviceCode'] = $serviceCode;
        }

        $data['pickup'] = [
            'cityId'  => $from,
        ];

        $data['delivery' ] = [
            'cityId'  => $to,
        ];

        if ($this->parcel) {
            $data['parcel'] = $this->parcel;
        }

        $result = $this->client->invoke('getServiceCost2', $data);

        return $result;
    }

    public function setParcel(float $weight, float $length, float $width, float $height, float $quantity) {

        $this->parcel = [
            'weight' => $weight,
            'length' => $length,
            'width' => $width,
            'height' => $height ,
            'quantity' => $quantity,
        ];

    }

}