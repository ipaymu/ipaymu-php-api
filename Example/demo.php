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

        $ipaymu->addCart([
            'product' => 'product-name',
            'quantity' => 1,
            'price' => 50000,
            'description' => 'product-desc',
            'weight' => 1,
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
    public function payVa()
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

        $ipaymu->addCart([
            'product' => 'product-name',
            'quantity' => 1,
            'price' => 50000,
            'description' => 'product-desc',
            'weight' => 1,
        ]);

        $ipaymu->setCOD([
            'pickupArea' => "76111",
            'pickupAddress' => "Denpasar",
            'deliveryArea' => "76111",
            'deliveryAddress' => "Denpasar",
        ]);

        $redirectPayment = $ipaymu->redirectPayment();
    }
}
