<?php
/*
   ____ _                
  / ___| | ___  _____  __
 | |  _| |/ _ \/ _ \ \/ /
 | |_| | |  __/ (_) >  < 
  \____|_|\___|\___/_/\_\
                         
  Gleox Ultimated Library
   For Featured Projects

    https://gleox.com

  Version: 1.0.0
  Library Version: 1.0.0 
*/

/**
 * Gleox Ultimated Library
 *
 * @package Gleox Ultimated Library
 * @version 1.0.0
 * @category Libraries
 * @author Furkan Mercimek
 * @link https://gleox.com
 */
class Pinabi {
    private $CI;

    private $base_url = "https://api.pinabi.com/api/";
    private $properties;

    public function __construct() {
        $this->CI =& get_instance();
    }

    private function __codeIgniter() {
        return $this->CI;
    }

    public function getProducts() {
        return $this->send_request("GET", "getAllCategoriesAndProducts");
    }
    public function getProductsByType($type) {
        return $this->send_request("POST", "getAllCategoriesAndProductsByType", [
            "type" => $type
        ]);
    }

    /**
     * @param $data array ["transactionId" => "1234567890", "quantity" => 1, "productId" => 1, "phoneNumber" => "905555555555", "clientPlayerInfo" => "Player Info", "clientPlayerNickname" => "Player Nickname"]
     * @return mixed
     * @throws Exception
     */
    public function createOrder($data) {
        return $this->send_request("POST", "setOrder", $data);
    }

    public function getOrder($transactionId, $productId) {
        return $this->send_request("POST", "orderInfo", [
            "transactionId" => $transactionId,
            "productId" => $productId
        ]);
    }

    public function getBalance() {
        return $this->send_request("GET", "getBalance");
    }

    private function send_request($type, $api, $data=[], $headers=[], $only_response=true) {
        $this->CI->load->helper("api");
        $properties =  getAPIsettings()["pinabi"];
        $headers = array_merge([
            "apiUser: ".$properties->apiUser,
            "secretKey: ".$properties->secretKey,
            "Authorization: Basic ".$properties->Authorization,
        ], $headers);
        if ($type == "POST") $headers[] = "Content-Type: application/json";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url.$api.(($type == "GET") ? "?".http_build_query($data) : ""),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POSTFIELDS => ($type == "GET") ? [] : json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($only_response) return $result;
        return ["code" => $httpcode, "response" => $result];
   }
}
