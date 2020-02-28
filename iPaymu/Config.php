<?php

/**
 * @author iPaymu X Dev Fintech <support@ipaymu.com>
 */

namespace iPaymu;

class Config
{
    public $balance,
        $transaction,
        $history,
        $banklist,
        $redirectpayment,
        $directpayment,
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
        } else {
            $base = 'https://sandbox.ipaymu.com/api/v2';
        }
        /**
         * General API
         **/
        $this->balance          = $base . '/balance';
        $this->transaction      = $base . '/transaction';
        $this->history          = $base . '/history';
        $this->banklist         = $base . '/banklist';

        /**
         * Payment API 
         **/
        $this->redirectpayment  = $base . '/payment';
        $this->directpayment    = $base . '/payment/direct';

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
