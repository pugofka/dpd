# dpd

For the DPD delivery service intergation library.

### register 

in /bootstrap/app.php

$app->register(Pugofka\Dpd\DpdServiceProvider::class);

### use

use Pugofka\Dpd\Dpd;

$dpd = new Dpd();

$dpd->getCities();

$dpd->getPostamats($cityCode);

$dpd->getCostCommon (
    int $from,
    int $to,
    bool $selfPickup = true,
    bool $selfDelivery=false,
    float $weight = 0,
    float $declaredValue = 0,
    float $volume = null,
    $pickupDate = null,
    $serviceCode = null
);
    
$dpd->setParcel(
    float $weight, 
    float $length, 
    float $width, 
    float $height, 
    float $quantity
);
