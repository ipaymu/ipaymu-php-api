<img width="100" src="https://my.ipaymu.com/asset/images/logo-ipaymu.png">

iPaymu-php-api
==============

This is the Official PHP wrapper/library for iPaymu Payment API, that is compatible with Composer. Visit [https://ipaymu.com](https://ipaymu.com) for more information about the product and see documentation at [https://ipaymu.com/en/api-documentation/](https://ipaymu.com/en/api-documentation/) for more technical details.

Free signup here [https://my.ipaymu.com/members/signup.htm](https://my.ipaymu.com/members/signup.htm) to accept payment online now!

## Installation
The best way to use this package is using [composer](https://getcomposer.org/)
```
composer require ipaymu/ipaymu-php-api
```

## Usage

### Initialization
```php
<?php
use iPaymu\iPaymu;

$production = true; // Set to false for sandbox
$iPaymu = new iPaymu('your-api-key', $production);
```

### Set URL
```php
$iPaymu->setURL([
    'ureturn' => 'https://your-website/thankyou_page',
    'unotify' => 'https://your-website/notify_page',
    'ucancel' => 'https://your-website/cancel_page',
]);
```

### Set Buyer
```php
<?php
$iPaymu->setBuyer([
    'name' => 'your-name',
    'phone' => 'your-phone',
    'email' => 'your-email',
]);
```

### Check API Key Validity
```php
$iPaymu->isApiKeyValid();
```

### Check Balance
```php
$iPaymu->checkBalance();
```

### Add Product to Cart
```php
$cart = $iPaymu->addCart([
        'name' => 'product-name',
        'quantity' => 'product-quantity',
        'price' => 'product-price',
]);
```

### Pay Cstore
Please add product to cart first before using this method
```php
$cart = $iPaymu->payCstore('
        indomaret, 
        alfamart');
```

### Pay VA
```php
$cart = $iPaymu->payVA('
        niaga,
        bni,
        bag,
        mandiri,
        bri,
        bca');
```

### Pay Bank
```php
$cart = $iPaymu->payBank('bankbca');
```

### Check Transaction Status
```php
$iPaymu->checkTransaction("transaction-id");
```

