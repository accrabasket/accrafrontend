<?php

namespace Application\Model;

class common {

    public function __construct() {
        $this->cObj = new Curl();
    }

    public function curlhit($params = null, $method, $controller = 'companycontroller') {
        $queryStr = '';
        if (!empty($params)) {
            $queryStr = http_build_query($params);
        }
        $url = NODE_API . $controller . '/' . $method . '?' . $queryStr;
//echo $url;die;
        return $this->cObj->callCurl($url);
    }

    public function curlhitApi($params = null, $controller = 'application') {
        $queryStr = '';
        if (!empty($params['rqid'])) {
            $rqid = $params['rqid'];
            unset($params['rqid']);
        }
        if (!empty($params)) {
            $queryStr = json_encode($params);
//            $queryStr = http_build_query($params);
//            $queryStr = json_encode($queryStr);
        }
        $data['parameters'] = $queryStr;
        if (empty($rqid)) {
            $data['rqid'] = $this->genrateRqid($data['parameters']);
        } else {
            $data['rqid'] = $rqid;
        }
        $url = BASKET_API . $controller;
        $parametes = http_build_query($data);
        if ($params['method'] == 'checkout') {
	//echo $url = $url.'?'.$parametes;die;
        }

        return $this->cObj->callPostCurl($url, $parametes);
    }

    public function getLocationList($inputParams = array()) {
        $params = array();
        if (!empty($inputParams['address'])) {
            $params['address'] = $inputParams['address'];
        }
        if (!empty($inputParams['id'])) {
            $params['id'] = $inputParams['id'];
        }
        if (!empty($inputParams['active'])) {
            $params['active'] = $inputParams['active'];
        }
        if (!empty($inputParams['pagination'])) {
            $params['pagination'] = $inputParams['pagination'];
            $params['page'] = isset($inputParams['page']) ? $inputParams['page'] : 1;
        }
        $params['method'] = 'getLocationList';

        return $this->curlhitApi($params);
    }

    public function genrateRqid($parameters) {
        return $rqid = hash('sha512', APIKEY . $parameters);
    }

    function curlHitUsingBody($url, $parameters) {
//$data = json_encode($parametes);
        $logpath = $_SERVER['DOCUMENT_ROOT'] . 'public/log/' . date("Y-m-d") . '/ezeepaylogin';
        if (!file_exists($logpath . '/ezeepaylogin.txt')) {

            mkdir($logpath, 0775, true);
        }
//        $text = "\n Request - ".date('Y-m-d H:i:s')."\n".json_encode($parameters);
//      file_put_contents($logpath.'.txt', $text, FILE_APPEND); 
//$parameters = '';
//$parameters = {"merchantcode":"dsfdSSa22AAAj","agentcode":"5632","tokenid":"EZZ-202039605","checksum":"56f0ca4baca751a8d9b2002c27aa30fa5f571822be49933eb29455d73da89ee1efe896abaa00a9bad9990b87374c40562384e7f5f944fa0acf091827f4325933"};
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://apiref.ezipaygh.com/api/AfroBasket/AfroDataVerification",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 80a9dc7c-ac53-8a33-de34-5e84fb4e4df7"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $text = "\n Request - " . date('Y-m-d H:i:s') . "\n" . $parameters . "\n Ezeepay Response:-" . $response;
        file_put_contents($logpath . '.txt', $text, FILE_APPEND);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $response;
        }



        echo $url;
        echo $parameters;
        die;
//$parameters = json_encode($parameters);
        echo $parameters;
        $response = $this->cObj->callPostCurl($url, $parameters);
        $text = "\n Request - " . date('Y-m-d H:i:s') . "\n" . $parameters . "\n Ezeepay Response:-" . $response;
        file_put_contents($logpath . '.txt', $text, FILE_APPEND);
        echo $response;
        die;
        return $response;
    }

    function paymentWalletVerificationFromEzeepay($parameters) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://apiref.ezipaygh.com/api/AfroBasket/PaymentWalletVerification",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 0f434fc6-6c53-752a-6919-af167b3e6d9d"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    function deductAmountFromWallet($token, $parameters) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://apiref.ezipaygh.com/api/AfroBasket/PaymentByUserWallet",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 2108da4a-45b0-db08-53ee-e669af459252",
                "token: $token"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

}
