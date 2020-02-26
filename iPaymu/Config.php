<?php

/**
 * @author Fahdi Labib <fahdilabib@gmail.com>
 */

namespace iPaymu;

class Config
{
    public $balance,
        $transaction,
        $banklist,
        $redirectpayment,
        $directpayment,
        $cstore,
        $niagava,
        $bniva,
        $bagva,
        $mandiriva,
        $briva,
        $bcava,
        $bankbca,
        $codarea,
        $codrate,
        $codpickup,
        $codpayment,
        $codawb,
        $codtracking,
        $codhistory;

    /**
     * Config constructor.
     *
     * @param bool $production
     */
    public function __construct($production)
    {
        if ($production) {
            $base = 'https://my.ipaymu.com/api/v2';
            $this->va = '000001';
            $this->apikey = 'xxxxxx';
        } else {
            $base = 'https://sandbox.ipaymu.com/api/v2';
            $this->va = '11111';
            $this->apikey = 'fffffff';
        }
        /**
         * General API
         **/
        $this->balance          = $base . '/balance';
        $this->transaction      = $base . '/history';
        $this->banklist         = $base . '/banklist';
        /**
         * Payment API 
         **/
        $this->redirectpayment  = $base . '/payment';
        $this->directpayment    = $base . '/payment/direct';
        $this->cstore           = $base . '/payment/cstore';
        $this->niagava          = $base . '/payment/niagava';
        $this->bniva            = $base . '/payment/bniva';
        $this->bagva            = $base . '/payment/bagva';
        $this->mandiriva        = $base . '/payment/mandiriva';
        $this->briva            = $base . '/payment/briva';
        $this->bcava            = $base . '/payment/bcava';
        $this->bankbca          = $base . '/payment/bcatransfer';
        /** 
         * COD Payment
         **/
        $this->codarea          = $base . '/cod/getarea';
        $this->codrate          = $base . '/cod/getrate';
        $this->codpickup        = $base . '/cod/pickup';
        $this->codpayment       = $base . '/payment/cod';
        /**
         * COD Tracking
         **/
        $this->codawb           = $base . '/cod/getawb';
        $this->codtracking      = $base . '/cod/tracking';
        $this->codhistory       = $base . '/cod/history';

        return $this;
    }
}
