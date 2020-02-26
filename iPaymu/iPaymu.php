<?php

/**
 * @author Fahdi Labib <fahdilabib@gmail.com>
 */

namespace iPaymu;

use iPaymu\Exceptions\ApiKeyInvalid;
use iPaymu\Exceptions\ApiKeyNotFound;
use iPaymu\Traits\CurlTrait;

class iPaymu
{
    use CurlTrait;

    /**
     * iPaymu Api Key.
     *
     * @var
     */
    protected $apiKey;

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
    public function __construct($apiKey = null, $production = false)
    {
        $this->config = new Config($production);
        $this->setApiKey($apiKey);
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

        foreach ($this->carts as $cart) {
            $productsName[] = $cart['name'];
            $productsPrice[] = $cart['price'];
            $productsQty[] = $cart['quantity'];
        }

        $params['product'] = $productsName;
        $params['price'] = $productsPrice;
        $params['quantity'] = $productsQty;

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
     * Check if Api Key inserted is valid or not.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function isApiKeyValid()
    {
        try {
            $this->checkBalance();

            return true;
        } catch (ApiKeyInvalid $e) {
            return false;
        }
    }

    /**
     * Check Balance.
     */
    public function checkBalance()
    {
        $response = $this->request($this->config->balance, [
            'key'    => $this->apiKey,
            'format' => 'json',
        ]);

        return $response;
    }

    /**
     * Check Transactions.
     */
    public function checkTransaction($id)
    {
        $response =  $this->request($this->config->transaction, [
            'key' => $this->apiKey,
            'id'  => $id,
            'format' => 'json',
        ]);

        return $response;
    }

    /**
     * Checkout Transactions.
     */
    public function checkoutTransaction()
    {
        $currentCarts = $this->buildCarts();
        $response =  $this->request($this->config->payment, [
            'key' => $this->apiKey,
            'payment' => 'payment',
            'product' => $currentCarts['product'],
            'price' => $currentCarts['price'],
            'quantity' => $currentCarts['quantity'],
            'comments' => $this->comments,
            'unotify' => $this->unotify,
            'ureturn' => $this->ureturn,
            'ucancel' => $this->ucancel,
            'format' => 'json',
        ]);

        return $response;
    }

    /**
     * Pay CStore.
     */
    public function payCstore($channel)
    {
        $checkout = $this->checkoutTransaction();
        $response =  $this->request($this->config->cstore, [
            'key' => $this->apiKey,
            'sessionID'  => $checkout['sessionID'],
            'channel' => $channel,
            'name' => $this->buyer['name'],
            'phone' => $this->buyer['phone'],
            'email' => $this->buyer['email'],
            'active' => $this->expired,
            'format' => 'json',
        ]);

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

        $response =  $this->request($url, [
            'key' => $this->apiKey,
            'price' => $this->amount,
            'name' => $this->buyer['name'],
            'phone' => $this->buyer['phone'],
            'email' => $this->buyer['email'],
            'notifyurl' => $this->unotify,
            'expired' => $this->expired,
            'expiredtype' => $this->expiredtype,
            'comment' => $this->comment,
            'reference' => $this->referenceid,
            'format' => 'json',
        ]);

        return $response;
    }

    /**
     * Pay Bank.
     */
    public function payBank()
    {
        $response =  $this->request($this->config->bankbca, [
            'key' => $this->apiKey,
            'amount' => $this->amount,
            'name' => $this->buyer['name'],
            'phone' => $this->buyer['phone'],
            'email' => $this->buyer['email'],
            'notifyUrl' => $this->unotify,
            'expired' => $this->expired,
            'format' => 'json',
        ]);

        return $response;
    }
}
