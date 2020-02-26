<?php
/**
 * @author Franky So <frankyso.mail@gmail.com>
 */
use Faker\Factory;
use frankyso\iPaymu\iPaymu;
use PHPUnit\Framework\TestCase;

/**
 * @author Franky So <frankyso.mail@gmail.com>
 */
final class CartTest extends TestCase
{
    public function testAddProductToCart()
    {
        $faker = Factory::create();
        $iPaymu = new iPaymu($_SERVER['APP_KEY']);

        for ($x = 0; $x <= 10; $x++) {
            $iPaymu->cart()->add($faker->uuid, $faker->name, rand(1, 5), rand(10000, 1000000));
        }
    }

    public function testRemoveProductFromCart()
    {
        $faker = Factory::create();
        $iPaymu = new iPaymu($_SERVER['APP_KEY']);

        for ($x = 0; $x <= 10; $x++) {
            $id = $faker->uuid;
            $iPaymu->cart()->add($id, $faker->name, rand(1, 5), rand(10000, 1000000));
            $iPaymu->cart()->remove($id);
        }
    }

    public function testCheckout()
    {
        $faker = Factory::create();
        $iPaymu = new iPaymu($_SERVER['APP_KEY']);

        for ($x = 0; $x <= 3; $x++) {
            $iPaymu->cart()->add($faker->uuid, $faker->name, rand(1, 5), rand(10000, 1000000));
        }

        $response = $iPaymu->cart()->checkout('no comment');
        $this->assertArrayHasKey('url', $response);
    }
}
