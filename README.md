<img width="100" src="https://my.ipaymu.com/asset/images/logo-ipaymu.png">

The Official iPaymu-php-api
==============

This is the Official PHP wrapper/library for iPaymu Payment API, that is compatible with Composer. Visit [https://ipaymu.com](https://ipaymu.com) for more information about the product and see documentation at [https://ipaymu.com/en/api-documentation/](https://ipaymu.com/en/api-documentation/) for more technical details.

Free signup here [https://my.ipaymu.com/members/signup.htm](https://my.ipaymu.com/members/signup.htm) to accept payment online now!

## Installation
The best way to use this package is using [composer](https://getcomposer.org/)
```
composer require ipaymu/ipaymu-php-api
```

## Usage

### Requirement

First, get your apikey & va number from iPaymu dashboard.

### Initialization
```php
<?php
use iPaymu\iPaymu;

$apiKey = 'your-apikey';
$va = 'your-va';
$production = true;

$ipaymu = new iPaymu($apiKey, $va, $production);
```
### General


### Check Balance
```php
$balance = $iPaymu->checkBalance();
```

### Check Transaction
```php
$status = $iPaymu->checkTransaction($id);
```

#### Set URL
```php
$iPaymu->setURL([
    'ureturn' => 'https://your-website/thankyou_page',
    'unotify' => 'https://your-website/notify_page',
    'ucancel' => 'https://your-website/cancel_page',
]);
```

#### Set Buyer
```php

$buyer = $iPaymu->setBuyer([
    'name' => 'your-name',
    'phone' => 'your-phone',
    'email' => 'your-email',
]);
```

### Payment
There are 2 payment method: Payment Direct & Payment Redirect.
#### Add Product to Cart
First, please add product to shopping cart first before using this method
```php
$cart = $iPaymu->addCart([
        'name' => 'product-name',
        'quantity' => 'product-quantity',
        'price' => 'product-price',
]);
```

### Payment Direct
Payment direct method allows you to accept payment on your checkout page directly, this method works for any payment channel except for credit card.
```php
$cart = $iPaymu->payCstore('
        indomaret, 
        alfamart');
```

### Payment Redirect
In order accepting credit card, you must use Payment Redirect method. Upon checkout, you will be redirected to iPaymu.com payment page for further payment processing.
```php
$cart = $iPaymu->payVA('
        niaga,
        bni,
        bag,
        mandiri,
        bri,
        bca');
```


