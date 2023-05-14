<img width="100" src="https://my.ipaymu.com/asset/images/logo-ipaymu.png">

# The Official iPaymu-php-api

[![Latest Stable Version](https://poser.pugx.org/ipaymu/ipaymu-php-api/version)](https://packagist.org/packages/ipaymu/ipaymu-php-api) [![Total Downloads](https://poser.pugx.org/ipaymu/ipaymu-php-api/downloads)](https://packagist.org/packages/ipaymu/ipaymu-php-api) [![Latest Unstable Version](https://poser.pugx.org/ipaymu/ipaymu-php-api/v/unstable)](//packagist.org/packages/ipaymu/ipaymu-php-api) [![License](https://poser.pugx.org/ipaymu/ipaymu-php-api/license)](https://packagist.org/packages/ipaymu/ipaymu-php-api)

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

$iPaymu = new iPaymu($apiKey, $va, $production);
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

There are 2 payment methods: Payment Direct & Payment Redirect, with the following parameters:

##### paymentMethod

- va => Virtual Account
- banktransfer => Transfer Bank
- cstore => Convenience Store
- cod => Cash on Delivery
- qris => QRIS

#### paymentChannel

##### va

- bag => Bank Artha Graha
- bca => Bank Central ASIA
- bni => Bank Negara Indonesia
- cimb => Bank Cimb Niaga
- mandiri => Bank Mandiri
- bmi => Bank Muamalat Indonesia
- bri => Bank Rakyat Indonesia
- bsi => Bank Syariah Indonesia
- permata => Bank Permata
- danamon => Bank Danamon

##### cstore

- indomaret
- alfamart

##### cod

- rpx

##### qris

- qris

### Paramaters

| Parameter Request | Description                                                                                               | Type            | Mandatory |
| ----------------- | --------------------------------------------------------------------------------------------------------- | --------------- | --------- |
| account           | VA Number                                                                                                 | numeric         | Y         |
| name              | Customer Name                                                                                             | string          | Y         |
| email             | Customer E-mail                                                                                           | string          | Y         |
| phone             | Customer Phone                                                                                            | numeric         | Y         |
| amount            | Total Amount (price \* qty)                                                                               | numeric         | Y         |
| paymentMethod     | va, cstore, cod, qris                                                                             | string          | Y         |
| paymentChannel    | "**va:**" bag, bca, bni, cimb, mandiri, bmi, bri, bsi, permata, danamon "**cstore:**" indomaret, alfamart "**cod:**" rpx "**qris:**" qris  | string          | Y         |
| notifyUrl         | Return url when payment success                                                                           | string          | Y         |
| expired           | Expiration in hour                                                                                        | numeric         | N         |
| description       | Text description                                                                                          | string          | N         |
| referenceId       | Shopping cart order id                                                                                    | string          | N         |
| product           | Product Name                                                                                              | [array] string  | Y         |
| qty               | Quantity                                                                                                  | [array] numeric | Y         |
| price             | Product Price                                                                                             | [array] numeric | Y         |
| weight            | Product Weight                                                                                            | [array] numeric | Y         |
| length            | Product Length                                                                                            | [array] numeric | Y         |
| width             | Product Width                                                                                             | [array] numeric | Y         |
| height            | Product Height                                                                                            | [array] numeric | Y         |
| deliveryArea      | Postal Code Customer                                                                                      | numeric         | Y         |
| deliveryAddress   | Customer Address                                                                                          | string          | Y         |
| pickupArea        | Postal Code Shipper (Default Merchant Postal Code)                                                        | numeric         | N         |
| pickupAddress     | Shipper Address (Default Merchant Address)                                                                | string          | N         |

#### Add Product to Cart

First, please add product to shopping cart first before using this method

```php
$carts = [];
$carts = $iPaymu->add(
    'PROD0001', // product id (string)
    'Jacket', // product name (string)
    12000, // price (float)
    2, // quantity (int)
    'Size M', // description
    1, // product weight (int) (optional)
    1, // product length (int) (optional)
    1, // product weight (int) (optional)
    1 // product height (int) (optional)
);
$carts = $iPaymu->add(
    'PROD0002', // product id (string)
    'Shoe', // product name (string)
    150000, // price (float)
    2, // quantity (int)
    'Size 8', // description
    1, // product weight (int) (optional)
    1, // product length (int) (optional)
    1, // product weight (int) (optional)
    1 // product height (int) (optional)
);

$iPaymu->addCart($carts);
```

#### Set COD (Only if COD method)

```php
$delivery = $iPaymu->setCOD([
        'deliveryArea' => "76111",
        'deliveryAddress' => "Denpasar",
]);
```

#### Set Expired (for custom expired)

```php
// set your expiredPayment
$iPaymu->setExpired(24, 'hours'); // 24 hours
```

#### Set Reference ID (optional)

```php
$iPaymu->setReferenceId('123123');
```

#### Set Payment Notes (optional)

```php
$iPaymu->setComments('Payment TRX01');
```

### Payment Direct

Payment direct method allows you to accept payment on your checkout page directly, this method works for any payment channel except for credit card.

Set Payment Method and Payment Channel

```php
// set payment method
// check https://ipaymu.com/api-collection for list payment method
$iPaymu->setPaymentMethod('va');

// check https://ipaymu.com/api-collection for list payment channel
$iPaymu->setPaymentChannel('bca');
```

```php
$payment = $iPaymu->directPayment();
```

### Payment Redirect

In order accepting credit card, you must use Payment Redirect method. Upon checkout, you will be redirected to iPaymu.com payment page for further payment processing.

```php
$payment = $iPaymu->redirectPayment();
```

### Complete Code Example

Direct Payment Example

```php
$apiKey = 'QbGcoO0Qds9sQFDmY0MWg1Tq.xtuh1'; // your api key
$va = '1179000899'; // your va
$production = true; // set false to sandbox mode

$iPaymu = new iPaymu($apiKey, $va, $production);

// set callback url
$iPaymu->setURL([
    'ureturn' => 'https://your-website/thankyou_page',
    'unotify' => 'https://your-website/notify_page',
    'ucancel' => 'https://your-website/cancel_page',
]);

// set buyer name
$iPaymu->setBuyer([
    'name' => 'Bagus',
    'phone' => '08123123139',
    'email' => 'bagus@gmail.com',
]);

// set your reference id (optional)
$iPaymu->setReferenceId('123123');

// set your expiredPayment
$iPaymu->setExpired(24, 'hours'); // 24 hours

// set payment method
// check https://ipaymu.com/api-collection for list payment method
$iPaymu->setPaymentMethod('va');

// check https://ipaymu.com/api-collection for list payment channel
$iPaymu->setPaymentChannel('bca');

// payment notes (optional)
$iPaymu->setComments('Payment TRX01');

$carts = [];
$carts = $iPaymu->add(
    'PROD0001', // product id (string)
    'Jacket', // product name (string)
    12000, // price (float)
    3, // quantity (int)
    'Size M', // description
    1, // product weight (int) (optional)
    1, // product length (int) (optional)
    1, // product weight (int) (optional)
    1 // product height (int) (optional)
);
$carts = $iPaymu->add(
    'PROD0002', // product id (string)
    'Shoe', // product name (string)
    150000, // price (float)
    2, // quantity (int)
    'Size 8', // description
    1, // product weight (int) (optional)
    1, // product length (int) (optional)
    1, // product weight (int) (optional)
    1 // product height (int) (optional)
);

$iPaymu->addCart($carts);

return $iPaymu->directPayment();
```

Redirect Payment Example

```php
$apiKey = 'QbGcoO0Qds9sQFDmY0MWg1Tq.xtuh1'; // your api key
$va = '1179000899'; // your va
$production = true; // set false to sandbox mode

$iPaymu = new iPaymu($apiKey, $va, $production);

// set callback url
$iPaymu->setURL([
    'ureturn' => 'https://your-website/thankyou_page',
    'unotify' => 'https://your-website/notify_page',
    'ucancel' => 'https://your-website/cancel_page',
]);

// set buyer name
$iPaymu->setBuyer([
    'name' => 'Bagus',
    'phone' => '08123123139',
    'email' => 'bagus@gmail.com',
]);

// set your reference id (optional)
$iPaymu->setReferenceId('123123');

// set your expiredPayment
$iPaymu->setExpired(24, 'hours'); // 24 hours

// set cod param (optional)
$iPaymu->setCOD([
    'deliveryArea' => "76111",
    'deliveryAddress' => "Denpasar",
]);

$carts = [];
$carts = $iPaymu->add(
    'PROD0001', // product id (string)
    'Jacket', // product name (string)
    12000, // price (float)
    2, // quantity (int)
    'Size M', // description
    1, // product weight (int) (optional)
    1, // product length (int) (optional)
    1, // product weight (int) (optional)
    1 // product height (int) (optional)
);
$carts = $iPaymu->add(
    'PROD0002', // product id (string)
    'Shoe', // product name (string)
    150000, // price (float)
    2, // quantity (int)
    'Size 8', // description
    1, // product weight (int) (optional)
    1, // product length (int) (optional)
    1, // product weight (int) (optional)
    1 // product height (int) (optional)
);

$iPaymu->addCart($carts);

return $iPaymu->redirectPayment();
```
