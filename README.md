# dpd

Laravel package for DPD logistic (Russian logistic service)

## Usage

```php
use Pugofka\Dpd\Dpd;

$dpd = new Dpd();

// get all available cities
$dpd->getCities();

// calculate delivery cost
$dpd->getCostCommon (
    string $from, // ID city from
    string $to, // ID city to
    bool $selfPickup = true, 
    bool $selfDelivery=false, 
    float $weight = 0, // weight in kg
    float $declaredValue = 0, 
    $pickupDate = null, // format 2014-05-21
    float $volume = null 
);

// get all available pickpoints
$dpd->getPickPoints();
```
    
