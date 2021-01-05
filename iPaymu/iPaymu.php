<?php

/**
 * @author iPaymu X Dev Fintech <support@ipaymu.com>
 */

namespace iPaymu;

require_once dirname(__FILE__) . '/Traits/CurlTrait.php';
require_once dirname(__FILE__) . '/Config.php';

use iPaymu\Exceptions\VANotFound;
use iPaymu\Exceptions\ApiKeyNotFound;

class iPaymu
{
    use Traits\CurlTrait;

    /**
     * iPaymu Api Key.
     *
     * @var
     */
    protected $apiKey;

    /**
     * iPaymu VA.
     *
     * @var
     */
    protected $va;

    /**
     * @var , Url redirect after payment page
     */
    protected $ureturn;

    /**
     * @var , Url Notify when transaction paid
     */
    protected $unotify;

    /**
     * @var , Url Redirect when user cancel the transaction
     */
    protected $ucancel;

    /**
     * @var , Cart Object Builder
     */
    protected $carts = [];

    /**
     * @var , Store Buyer information
     */
    protected $buyer;

    /**
     * @var , Store COD information
     */
    protected $cod;

    /**
     * @var , Store Amount information
     */
    protected $amount;

    /**
     * @var , Store Comments information
     */
    protected $comments;

    /**
     * @var , Store Expired information
     */
    protected $expired;

    /**
     * @var , Store Expired type in second
     */
    protected $expiredtype;

    /**
     * @var , Store API Url
     */
    protected $config;

    /**
     * iPaymu constructor.
     *
     * @param null  $apiKey
     *
     * @throws ApiKeyNotFound
     */
    public function __construct($apiKey = null, $va = null, $production = false)
    {
        $this->config = new Config($production);
        $this->setApiKey($apiKey);
        $this->setVa($va);
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param mixed $expired
     */
    public function setExpired($expired = 24)
    {
        $this->expired = $expired;
    }

    /**
     * @param mixed $url
     */
    public function setURL($url)
    {
        $this->ureturn = $url['ureturn'] ?? '';
        $this->ucancel = $url['ucancel'] ?? '';
        $this->unotify = $url['unotify'] ?? '';
    }

    /**
     * @param mixed $buyer
     */
    public function setBuyer($buyer)
    {
        $this->buyer['name'] = $buyer['name'] ?? '';
        $this->buyer['phone'] = $buyer['phone'] ?? '';
        $this->buyer['email'] = $buyer['email'] ?? '';
    }

    public function setCOD($cod)
    {
        $this->cod['pickupArea'] = $cod['pickupArea'] ?? '';
        $this->cod['pickupAddress'] = $cod['pickupAddress'] ?? '';
        $this->cod['deliveryArea'] = $cod['deliveryArea'] ?? '';
        $this->cod['deliveryAddress'] = $cod['deliveryAddress'] ?? '';
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @param mixed $cart
     */
    public function addCart($cart)
    {
        $this->carts[count($this->carts)] = $cart;
    }

    /**
     * @param Product $product
     */
    public function add($id, $product, $productsPrice, $productsQty, $productsDesc, $productsWeight, $productsLength, $productsWidth, $productsHeight)
    {
        $this->carts[] = [
            'id' => $id,
            'product' => $product,
            'price' => $productsPrice,
            'quantity' => $productsQty,
            'description' => $productsDesc,
            'weight' => $productsWeight,
            'length' => $productsLength,
            'width' => $productsWidth,
            'height' => $productsHeight
        ];
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        foreach ($this->carts as $key => $cart) {
            if (isset($cart['id']) == $id) {
                unset($this->carts[$key]);
            }
        }
    }

    /**
     * @param string $comments
     *
     * @return mixed
     */
    private function buildCarts()
    {
        $productsName = [];
        $productsPrice = [];
        $productsQty = [];
        $productsDesc = [];
        $productsWeight = [];
        $productsLength = [];
        $productsWidth = [];
        $productsHeight = [];

        foreach ($this->carts as $cart) {
            $productsName[] = $cart['product'] ?? '';
            $productsPrice[] = $cart['price'] ?? 1;
            $productsQty[] = $cart['quantity'] ?? 1;
            $productsDesc[] = $cart['description'] ?? '';
            $productsWeight[] = $cart['weight'] ?? 1;
            $productsLength[] = $cart['length'] ?? 1;
            $productsWidth[] = $cart['width'] ?? 1;
            $productsHeight[] = $cart['height'] ?? 1;
        }

        $params['product'] = $productsName;
        $params['price'] = $productsPrice;
        $params['quantity'] = $productsQty;
        $params['description'] = $productsDesc;
        $params['weight'] = $productsWeight;
        $params['length'] = $productsLength;
        $params['width'] = $productsWidth;
        $params['height'] = $productsHeight;

        return $params;
    }

    /**
     * Set ApiKey Value.
     *
     * @param null $apiKey Api Key from iPaymu Dashboard.
     *
     * @throws ApiKeyNotFound
     */
    public function setApiKey($apiKey = null)
    {
        if ($apiKey == null) {
            throw new ApiKeyNotFound();
        }
        $this->apiKey = $apiKey;
    }

    /**
     * Set VA Value.
     *
     * @param null $va from iPaymu Dashboard.
     *
     * @throws VANotFound
     */
    public function setVa($va = null)
    {
        if ($va == null) {
            throw new VANotFound();
        }
        $this->va = $va;
    }

    /**
     * List Trx.
     */
    public function historyTransaction($data)
    {
        $response = $this->request(
            $this->config->history,
            $data,
            [
                'va' => $this->va,
                'apikey' => $this->apiKey,
            ]
        );

        return $response;
    }

    /**
     * Check Balance.
     */
    public function checkBalance()
    {
        $response = $this->request(
            $this->config->balance,
            [
                'account' => $this->va
            ],
            [
                'va' => $this->va,
                'apikey' => $this->apiKey,
            ]
        );

        return $response;
    }

    /**
     * Check Transactions.
     */
    public function checkTransaction($id)
    {
        $response =  $this->request(
            $this->config->transaction,
            $id,
            [
                'va' => $this->va,
                'apikey' => $this->apiKey
            ]
        );

        return $response;
    }

    /**
     * Checkout Transactions redirect to payment page.
     */
    public function redirectPayment()
    {
        $currentCarts = $this->buildCarts();
        $response =  $this->request(
            $this->config->redirectpayment,
            [
                'account' => $this->va,
                'product' => $currentCarts['product'],
                'qty' => $currentCarts['quantity'],
                'price' => $currentCarts['price'],
                'description' => $currentCarts['description'],
                'notifyUrl' => $this->unotify,
                'returnUrl' => $this->ureturn,
                'cancelUrl' => $this->ucancel,
                'weight' => $currentCarts['weight'],
                'dimension' => ["1:1:1"],
                'name' => isset($this->buyer['name']),
                'email' => isset($this->buyer['email']),
                'phone' => isset($this->buyer['phone']),
                'pickupArea' => isset($this->cod['pickupArea']),
                'pickupAddress' => isset($this->cod['pickupAddress'])
            ],
            [
                'va' => $this->va,
                'apikey' => $this->apiKey
            ]
        );

        return $response;
    }

    /**
     * Checkout Transactions direct api call.
     */
    public function directPayment($data)
    {
        $currentCarts = $this->buildCarts();
        $data = [
            'account' => $this->va,
            'name' => isset($this->buyer['name']),
            'email' => isset($this->buyer['email']),
            'phone' => isset($this->buyer['phone']),
            'amount' => $data['amount'],
            'paymentMethod' => $data['paymentMethod'],
            'paymentChannel' => $data['paymentChannel'],
            'notifyUrl' => $this->unotify,
            'expired' => $data['expired'],
            'description' => $currentCarts['description'],
            'referenceId' => $data['referenceId'],
            'product' => $currentCarts['product'],
            'qty' => $currentCarts['quantity'],
            'price' => $currentCarts['price'],
            'weight' => $currentCarts['weight'],
            'length' => $currentCarts['length'],
            'width' => $currentCarts['width'],
            'height' => $currentCarts['height'],
            'deliveryArea' => isset($this->cod['deliveryArea']),
            'deliveryAddress' => isset($this->cod['deliveryAddress']),
            'pickupArea' => isset($this->cod['pickupArea']),
            'pickupAddress' => isset($this->cod['pickupAddress']),
            'expiredType' => $data['expiredType'] ?? ''
        ];

        $response =  $this->request(
            $this->config->directpayment,
            $data,
            [
                'va' => $this->va,
                'apikey' => $this->apiKey
            ]
        );

        return $response;
    }
}
