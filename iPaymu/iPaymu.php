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

        foreach ($this->carts as $cart) {
            $productsName[] = $cart['name'];
            $productsPrice[] = $cart['price'];
            $productsQty[] = $cart['quantity'];
            $productsDesc[] = $cart['description'];
            $productsWeight[] = $cart['weight'];
        }

        $params['product'] = $productsName;
        $params['price'] = $productsPrice;
        $params['quantity'] = $productsQty;
        $params['description'] = $productsDesc;
        $params['weight'] = $productsWeight;

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
                'name' => $this->buyer['name'],
                'email' => $this->buyer['email'],
                'phone' => $this->buyer['phone'],
                'pickupArea' => $this->cod['pickupArea'],
                'pickupAddress' => $this->cod['pickupAddress']
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
        $response =  $this->request(
            $this->config->directpayment,
            [
                'account' => $this->va,
                'name' => $this->buyer['name'],
                'email' => $this->buyer['email'],
                'phone' => $this->buyer['phone'],
                'amount' => $data['amount'],
                'notifyUrl' => $this->unotify,
                'expired' => $data['expired'],
                'expiredType' => $data['expiredType'],
                'comments' => $data['comments'],
                'referenceId' => $data['referenceId'],
                'paymentMethod' => $data['paymentMethod'],
                'paymentChannel' => $data['paymentChannel']
            ],
            [
                'va' => $this->va,
                'apikey' => $this->apiKey
            ]
        );

        return $response;
    }

    /**
     * Pay CStore.
     */
    public function payCstore($data)
    {
        $response =  $this->request(
            $this->config->cstore,
            [
                'account' => $this->va,
                'name' => $this->buyer['name'],
                'email' => $this->buyer['email'],
                'phone' => $this->buyer['phone'],
                'amount' => $data['amount'],
                'notifyUrl' => $this->unotify,
                'expired' => $data['expired'],
                'expiredType' => $data['expiredType'],
                'comments' => $data['comments'],
                'referenceId' => $data['referenceId'],
                'channel' => $data['paymentChannel']
            ],
            [
                'va' => $this->config->va,
                'apikey' => $this->config->apikey
            ]
        );

        return $response;
    }

    /**
     * Pay VA.
     */
    public function payVA($store)
    {
        switch ($store) {
            case 'niaga':
                $url = $this->config->niagava;
                break;
            case 'bni':
                $url = $this->config->bniva;
                break;
            case 'bag':
                $url = $this->config->bagva;
                break;
            case 'mandiri':
                $url = $this->config->mandiriva;
                break;
            case 'bri':
                $url = $this->config->briva;
                break;
            case 'bca':
                $url = $this->config->bcava;
                break;
        }

        $response =  $this->request(
            $url,
            [
                'name' => $this->buyer['name'],
                'phone' => $this->buyer['phone'],
                'email' => $this->buyer['email'],
                'amount' => $this->amount,
                'notifyurl' => $this->unotify,
                'expired' => $this->expired,
                'expiredType' => $this->expiredtype,
                'comments' => $this->comments,
                'referenceId' => $this->referenceid,
                'va' => $this->va
            ],
            [
                'va' => $this->config->va,
                'apikey' => $this->config->apikey,
            ]
        );

        return $response;
    }

    /**
     * Pay Bank.
     */
    public function payBank()
    {
        $response =  $this->request(
            $this->config->bankbca,
            [
                'key' => $this->apiKey,
                'amount' => $this->amount,
                'name' => $this->buyer['name'],
                'phone' => $this->buyer['phone'],
                'email' => $this->buyer['email'],
                'notifyUrl' => $this->unotify,
                'expired' => $this->expired,
                'format' => 'json',
            ],
            [
                'va' => $this->config->va,
                'apikey' => $this->config->apikey,
            ]
        );

        return $response;
    }
}
