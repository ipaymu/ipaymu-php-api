<?php

use Faker\Factory;
use iPaymu\iPaymu;
use PHPUnit\Framework\TestCase;

final class CartTest extends TestCase
{
    public function testBuyer()
    {
        $faker = Factory::create();
        $iPaymu = new iPaymu($_SERVER['apiKey'], $_SERVER['va'], $_SERVER['production']);
        $iPaymu->setBuyer([
            $faker->name,
            $faker->phoneNumber,
            $faker->email,
        ]);
    }

    public function testURL()
    {
        $iPaymu = new iPaymu($_SERVER['apiKey'], $_SERVER['va'], $_SERVER['production']);
        $iPaymu->setURL([
            'ureturn' => 'https://your-website/thankyou_page',
            'unotify' => 'https://your-website/notify_page',
            'ucancel' => 'https://your-website/cancel_page',
        ]);
    }
    public function testAddProductToCart()
    {
        $faker = Factory::create();
        $iPaymu = new iPaymu($_SERVER['apiKey'], $_SERVER['va'], $_SERVER['production']);

        $response = $iPaymu->addCart([
            'product' => ['product 1 ', 'product2 '],
            'quantity' => ['1', '2'],
            'price' => ['10000', '50000'],
            'description' => ['product-desc', 'product-desc 2'],
            'weight' => [1, 2], //nullable (kilogram)
            'height' => [10, 10], //nullable (cm)
            'length' => [30, 40], //nullable (cm)
            'width'  => [10, 50], //nullable (cm)
        ]);
    }

    public function testCOD()
    {
        $iPaymu = new iPaymu($_SERVER['apiKey'], $_SERVER['va'], $_SERVER['production']);
        $iPaymu->setCOD([
            'pickupArea' => "76111",
            'pickupAddress' => "Denpasar",
            'deliveryArea' => "76111",
            'deliveryAddress' => "Denpasar",
        ]);
    }

    public function testDirectPayment()
    {
        $iPaymu = new iPaymu($_SERVER['apiKey'], $_SERVER['va'], $_SERVER['production']);
        $this->testBuyer();
        $this->testURL();
        $this->testAddProductToCart();
        $this->testCOD();
        $directData = [
            'amount' => 50000,
            'expired' => 24,
            'expiredType' => 'hours',
            'referenceId' => 10101011,
            'paymentMethod' => 'va', //va, cstore
            'paymentChannel' => 'bni', //bag, mandiri, cimb, bni, 
        ];

        $directPayment = $iPaymu->directPayment($directData);
    }
    public function testRedirectPayment()
    {
        $iPaymu = new iPaymu($_SERVER['apiKey'], $_SERVER['va'], $_SERVER['production']);
        $this->testBuyer();
        $this->testURL();
        $this->testAddProductToCart();
        $this->testCOD();
        $paymentData = [
            'referenceId' => 1,
        ];

        $redirectPayment = $iPaymu->redirectPayment($paymentData);
    }
}
