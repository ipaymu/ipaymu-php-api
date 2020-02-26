<?php

/**
 * @author Fahdi Labib <fahdilabib@gmail.com>
 */

namespace iPaymu\Traits;

use iPaymu\Exceptions\ApiKeyInvalid;

trait CurlTrait
{
    /**
     * @param $config
     * @param $params
     *
     * @throws ApiKeyInvalid
     *
     * @return mixed
     */
    function genSignature($data)
    {

        $signature = $data;

        return $signature;
    }

    public function request($config, $params)
    {
        $signature = $this->genSignature($params);

        $params_string = http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config);
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type" => "application/json",
            "signature" => $signature,
            "va" => $params['va'],
            "timestamp" => date('Ymdhis')
        ));
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
     * @throws ApiKeyInvalid
     *
     * @return mixed
     */
    private function responseHandler($response)
    {
        switch (@$response['Status']) {
            case '-1001':
                throw new ApiKeyInvalid();
            default:
                return $response;
        }
    }
}
