<?php

namespace App\Http\Controllers;

require_once base_path('vendor/ipaymu-php-api/iPaymu/iPaymu.php');

use iPaymu\iPaymu;

use Illuminate\Http\Request;

class PayController extends Controller
{
    //
    public function payVa()
    {
        $apiKey = 'QbGcoO0Qds9sQFDmY0MWg1Tq.xtuh1';
        $va = '1179000899';
        $production = false;

        $ipaymu = new iPaymu($apiKey, $va, $production);

        $ipaymu->setURL([
            'ureturn' => 'https://your-website/thankyou_page',
            'unotify' => 'https://your-website/notify_page',
            'ucancel' => 'https://your-website/cancel_page',
        ]);

        // balance
        $balance = $ipaymu->checkBalance();

        // list trx
        $data = [
            'type' => 11,
            'status' => 1,
            'startdate' => '2019-12-01',
            'enddate' => '2019-12-30',
            // 'page' => 1,
            'order' => 'DESC',
            'limit' => -1 // -1 : all trx
        ];

        $listTrx = $ipaymu->historyTransaction($data);

        //payment - redirect
        $ipaymu->setBuyer([
            'name' => 'Krisna',
            'phone' => '0851211121',
            'email' => 'krisna@ipaymu.com',
        ]);

        $ipaymu->addCart([
            'name' => 'product-name',
            'quantity' => 1,
            'price' => 50000,
            'description' => 'product-desc',
            'weight' => 1,
        ]);

        $ipaymu->setCOD([
            'pickupArea' => "76111",
            'pickupAddress' => "Denpasar",
        ]);

        $redirectPayment = $ipaymu->redirectTransaction();

        //payment - direct
        $directData = [
            'amount' => 50000,
            'expired' => 24,
            'expiredType' => 'hours',
            'comments' => 'comments',
            'referenceId' => 10101011,
            'paymentMethod' => 'va', //va, cstore
            'paymentChannel' => 'bag', //bag, mandiri, cimb, bni, 

        ];

        $direct = $ipaymu->directTransaction($directData);

        dd($direct);
    }
}
