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
    protected $expiredType;

    /**
     * @var , Store Reference ID
     */
    protected $referenceId;

    /**
     * @var , Store API Url
     */
    protected $config;

     /**
     * @var , Store Payment Method
     */
    protected $paymentMethod;


    /**
     * @var , Store Payment Channel
     */
    protected $paymentChannel;

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
     * @param string $refId
     */
    public function setReferenceId($refId)
    {
        $this->referenceId = $refId;
    }

    /**
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @param string $paymentChannel
     */
    public function setPaymentChannel($paymentChannel)
    {
        $this->paymentChannel = $paymentChannel;
    }

    /**
     * @param int $expired
     * @param string $expiredType
     */
    public function setExpired(int $expired = 24, string $expiredType = 'hours')
    {
        $this->expired = $expired;
        $this->expiredType = $expiredType;
    }

    /**
     * @param mixed $url
     */
    public function setURL($url)
    {
        $this->ureturn = $url['ureturn'] ?? null;
        $this->ucancel = $url['ucancel'] ?? null;
        $this->unotify = $url['unotify'] ?? null;
    }

    /**
     * @param mixed $buyer
     */
    public function setBuyer($buyer)
    {
        $this->buyer['name'] = $buyer['name'] ?? null;
        $this->buyer['phone'] = $buyer['phone'] ?? null;
        $this->buyer['email'] = $buyer['email'] ?? null;
    }

    public function setCOD($cod)
    {
        $this->cod['pickupArea'] = $cod['pickupArea'] ?? null;
        $this->cod['pickupAddress'] = $cod['pickupAddress'] ?? null;
        $this->cod['deliveryArea'] = $cod['deliveryArea'] ?? null;
        $this->cod['deliveryAddress'] = $cod['deliveryAddress'] ?? null;
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
        // dd($this->carts);
        // $this->carts[array_keys($this->carts)][count($this->carts)] = $cart;
        $this->carts[count($this->carts)] = $cart;
        // $this->carts = $cart;
        // dd($this->carts);
    }

    /**
     * @param Product $product
     */
    public function add($id, string $product, float $productsPrice, int $productsQty, string $productsDesc = null, $productsWeight = null, $productsLength = null, $productsWidth = null, $productsHeight = null)
    {
        $this->carts[] = [
            'id' => $id,
            'product' => trim($product),
            'price' => trim($productsPrice),
            'quantity' => trim($productsQty),
            'description' => trim($productsDesc),
            'weight' => trim($productsWeight),
            'length' => trim($productsLength),
            'width' => trim($productsWidth),
            'height' => trim($productsHeight)
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
        $productDimension = [];
        $productsLength   = [];
        $productsWidth    = [];
        $productsHeight   = [];
        foreach($this->carts as $rcarts) {
            if(!empty($rcarts['product'])) {
                $productsName[] = trim($rcarts['product']);
            }
            if(!empty($rcarts['price'])) {
                $productsPrice[] = trim(floatval($rcarts['price']));
            }

            if(!empty($rcarts['quantity'])) {
                $productsQty[] = trim(intval($rcarts['quantity']));
            }

            if(!empty($rcarts['description'])) {
                $productsDesc[] = trim($rcarts['description']);
            }

            if(!empty($rcarts['weight'])) {
                $productsWeight[] = trim($rcarts['weight']);
            }

            if(!empty($rcarts['length']) && !empty($rcarts['width']) && !empty($rcarts['height'])) {

                $length  = trim($rcarts['length'] ?? 0);
                $width   = trim($rcarts['width']?? 0);
                $height  = trim($rcarts['height'] ?? 0);
                $productDimension[] = $length . ':'  . $width . ':' . $height;
            }

        }


        $params['product'] = $productsName ?? null;
        $params['price'] = $productsPrice ?? null;
        $params['quantity'] = $productsQty ?? null;
        $params['description'] = $productsDesc ?? null;
        $params['weight'] = $productsWeight ?? null;
        $params['dimension'] = $productDimension ?? null;
        $params['length']  = $productsLength;
        $params['width']  = $productsWidth;
        $params['height']  = $productsHeight;

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
            [
                'transactionId' => $id
            ],
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
    public function redirectPayment($paymentData = null)
    {
        $currentCarts = $this->buildCarts();

        $response =  $this->request(
            $this->config->redirectpayment,
            [
                'account' => $this->va,
                'product' => $currentCarts['product'] ?? null,
                'qty' => $currentCarts['quantity'] ?? null,
                'price' => $currentCarts['price'] ?? null,
                'description' => $currentCarts['description'] ?? null,
                'notifyUrl' => $this->unotify,
                'returnUrl' => $this->ureturn,
                'cancelUrl' => $this->ucancel,
                'weight' => $currentCarts['weight'] ?? null,
                'dimension' => $currentCarts['dimension'] ?? null,
                'name' => $this->buyer['name'] ?? null,
                'email' => $this->buyer['email'] ?? null,
                'phone' => $this->buyer['phone'] ?? null,
                'pickupArea' => $this->cod['pickupArea'] ?? null,
                'pickupAddress' => $this->cod['pickupAddress'] ?? null,
                'buyerName' => $this->buyer['name'] ?? null,
                'buyerEmail' => $this->buyer['email'] ?? null,
                'buyerPhone' => $this->buyer['phone'] ?? null,
                'referenceId' => $this->referenceId ?? null,
                'expired' => $this->expired ?? 24,
                'expiredType' => $this->expiredType ?? 'hours'
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
    public function directPayment()
    {
        $currentCarts = $this->buildCarts();
        $total = 0;
        foreach($currentCarts['price'] as $key => $rcart) {
            $total += $rcart * $currentCarts['quantity'][$key];
        }
        $this->amount =  $total;


        $data = [
            'account' => $this->va,
            'name' => $this->buyer['name'] ?? null,
            'email' => $this->buyer['email'] ?? null,
            'phone' => $this->buyer['phone'] ?? null,
            'amount' => $this->amount ?? 0,
            'paymentMethod' => $this->paymentMethod ?? null,
            'paymentChannel' => $this->paymentChannel ?? null,
            'comments' => $this->comments ?? null,
            'notifyUrl' => $this->unotify,
            'description' => $currentCarts['description'] ?? null,
            'referenceId' => $this->referenceId ?? null,
            'product' => $currentCarts['product'] ?? null,
            'qty' => $currentCarts['quantity'] ?? null,
            'price' => $currentCarts['price'] ?? null,
            'weight' => $currentCarts['weight'] ?? null,
            'length' => $currentCarts['length'] ?? null,
            'width' => $currentCarts['width'] ?? null,
            'height' => $currentCarts['height'] ?? null,
            'deliveryArea' => $this->cod['deliveryArea'] ?? null,
            'deliveryAddress' => $this->cod['deliveryAddress'] ?? null,
            'pickupArea' => $this->cod['pickupArea'] ?? null,
            'pickupAddress' => $this->cod['pickupAddress'] ?? null,
            'expired' => $this->expired ?? 24,
            'expiredType' => $this->expiredType ?? 'hours'
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
