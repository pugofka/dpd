# dpd

Библиотека для работы с сервисом доставки DPD

### Использование

```php
use Pugofka\Dpd\Dpd;

$dpd = new Dpd();

// Получить список городов
$dpd->getCities();

// Посчитать стоимость доставки
$dpd->getCostCommon (
    string $from, // ID города отправления
    string $to, // ID города назначения
    bool $selfPickup = true, // Самопривоз на терминал.
    bool $selfDelivery=false, // Самовывоз с терминала
    float $weight = 0, // вес в кг
    float $declaredValue = 0, // Объявленная ценность
    $pickupDate = null, // Предполагаемая дата приёма груза, 2014-05-21
    float $volume = null // Объем в м3
);
```
    
