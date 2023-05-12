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

        for ($x = 0; $x <= 10; $x++) {
            $carts = $iPaymu->add($faker->uuid, $faker->name, rand(1, 5), rand(10000, 1000000), $faker->name, rand(1, 5), rand(1, 5), rand(1, 5), rand(1, 5));
        }
        $response = $iPaymu->addCart($carts);
    }

    public function testRemoveProductFromCart()
    {
        $faker = Factory::create();
        $iPaymu = new iPaymu($_SERVER['apiKey'], $_SERVER['va'], $_SERVER['production']);

        for ($x = 0; $x <= 10; $x++) {
            $carts = $iPaymu->add($faker->uuid, $faker->name, rand(1, 5), rand(10000, 1000000), $faker->name, rand(1, 5), rand(1, 5), rand(1, 5), rand(1, 5));
        }
        $id = $faker->uuid;
        $iPaymu->addCart($carts);
        $iPaymu->remove($id);
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
        $redirectData = [
            'amount' => 50000,
            'expired' => 24,
            'expiredType' => 'hours',
            'referenceId' => 10101011,
            'paymentMethod' => 'va', //va, cstore
            'paymentChannel' => 'bni', //bag, mandiri, cimb, bni, 
        ];

        $redirectPayment = $iPaymu->redirectPayment();
    }
}
