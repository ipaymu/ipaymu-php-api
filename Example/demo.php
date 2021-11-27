<?php

namespace App\Http\Controllers;

require_once base_path('vendor/ipaymu-php-api/iPaymu/iPaymu.php');

use iPaymu\iPaymu;

use Illuminate\Http\Request;

class PayController extends Controller
{
    //

    public function direct()
    {
        $apiKey = 'api-key';
        $va = 'va';
        $production = false;

        $ipaymu = new iPaymu($apiKey, $va, $production);

        $ipaymu->setURL([
            'ureturn' => 'https://your-website/thankyou_page',
            'unotify' => 'https://your-website/notify_page',
            'ucancel' => 'https://your-website/cancel_page',
        ]);

        $ipaymu->setBuyer([
            'name' => 'iPaymu',
            'phone' => '0851211121',
            'email' => 'ipaymu@ipaymu.com',
        ]);

        $iPaymu->addCart([
            'product' => ['product 1 ', 'product2 '],
            'quantity' => ['1', '2'],
            'price' => ['10000', '50000'],
            'description' => ['product-desc', 'product-desc 2'],
            'weight' => [1, 2], //nullable (kilogram)
            'height' => [10, 10], //nullable (cm)
            'length' => [30, 40], //nullable (cm)
            'width'  => [10, 50], //nullable (cm)
        ]);

        $ipaymu->setCOD([
            'pickupArea' => "76111",
            'pickupAddress' => "Denpasar",
            'deliveryArea' => "76111",
            'deliveryAddress' => "Denpasar",
        ]);

        //payment - direct
        $directData = [
            'amount' => 50000,
            'expired' => 24,
            'expiredType' => 'hours',
            'referenceId' => 10101011,
            'paymentMethod' => 'va', //va, cstore
            'paymentChannel' => 'bni', //bag, mandiri, cimb, bni, 

        ];

        $direct = $ipaymu->directPayment($directData);
    }
    
    public function redirect()
    {
        $apiKey = 'api-key';
        $va = 'va';
        $production = false;

        $ipaymu = new iPaymu($apiKey, $va, $production);

        $ipaymu->setURL([
            'ureturn' => 'https://your-website/thankyou_page',
            'unotify' => 'https://your-website/notify_page',
            'ucancel' => 'https://your-website/cancel_page',
        ]);

        $data = [
            'type' => 11,
            'status' => 1,
            'startdate' => '2019-12-01',
            'enddate' => '2019-12-30',
            // 'page' => 1,
            'order' => 'DESC',
            'limit' => -1 // -1 : all trx
        ];

        $ipaymu->setBuyer([
            'name' => 'iPaymu',
            'phone' => '0851211121',
            'email' => 'ipaymu@ipaymu.com',
        ]);

       $iPaymu->addCart([
            'product' => ['product 1 ', 'product2 '],
            'quantity' => ['1', '2'],
            'price' => ['10000', '50000'],
            'description' => ['product-desc', 'product-desc 2'],
            'weight' => [1, 2], //optional (kilogram)
            'height' => [10, 10], //optional (cm)
            'length' => [30, 40], //optional (cm)
            'width'  => [10, 50], //optional (cm)
        ]);

        $ipaymu->setCOD([
            'pickupArea' => "76111",
            'pickupAddress' => "Denpasar",
            'deliveryArea' => "76111",
            'deliveryAddress' => "Denpasar",
        ]);
        
        $paymentData = [
            'referenceId' => 1, //merchant reference (transaction id merchant) optional
        ];
        
        $redirectPayment = $ipaymu->redirectPayment($paymentData);
    }
}
