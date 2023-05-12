<?php

/**
 * @author iPaymu X Dev Fintech <support@ipaymu.com>
 */

namespace iPaymu\Traits;

use iPaymu\Exceptions\Unauthorized;

trait CurlTrait
{

    /**
     * @param $config
     * @param $params
     *
     * @throws Unauthorized
     *
     * @return mixed
     */
    function genSignature($data, $credentials)
    {
        $body = json_encode($data, JSON_UNESCAPED_SLASHES);
        $requestBody  = strtolower(hash('sha256', $body));
        $secret       = $credentials['apikey'];
        $va           = $credentials['va'];
        $stringToSign = 'POST:' . $va . ':' . $requestBody . ':' . $secret;
        $signature    = hash_hmac('sha256', $stringToSign, $secret);

        return $signature;
    }

    public function request($config, $params, $credentials)
    {
        $signature = $this->genSignature($params, $credentials);
        $timestamp = Date('YmdHis');
        $headers = array(
            'Content-Type: application/json',
            'va: ' . $credentials['va'],
            'signature: ' . $signature,
            'timestamp: ' . $timestamp
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, JSON_UNESCAPED_SLASHES));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $request = curl_exec($ch);

        if ($request === false) {
            echo 'Curl Error: ' . curl_error($ch);
        } else {
            $result = $this->responseHandler(json_decode($request, true));

            return $result;
        }

        curl_close($ch);
        exit;
    }

    /**
     * @param $response
     *
     * @throws Unauthorized
     *
     * @return mixed
     */
    private function responseHandler($response)
    {
        switch (@$response['Status']) {
            case '401':
                throw new Unauthorized();
            default:
                return $response;
        }
    }
}
