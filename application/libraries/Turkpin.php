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
class Turkpin {
   private $CI;

   private $base_url = "https://www.turkpin.com/api.php";
   private $properties;

   public function __construct() {
      $this->CI =& get_instance();

      //if (isset($config["base_url"])) $this->base_url = $config["base_url"];

      $this->properties = $this->CI->db->where("id", 1)->get("properties")->row();
   }

   private function __codeIgniter() {
      return $this->CI;
   }

	/**
	 * @param $game_code string Game Code
	 * @param $product_code string Product Code
	 * @param $mail string Customer mail
	 * @return array Array with status, error_code and error_message
	 */
   public function createEPIN($game_code, $product_code, $mail='') {
      $data = [
         "oyunKodu" => $game_code,
         "urunKodu" => $product_code,
         "adet" => 1,
         "character" => $mail
      ];

     $resp = $this->send_request("POST", "epinSiparisYarat", $data, [], false); 

      $content = json_decode(json_encode((array)simplexml_load_string($resp["response"])),true);
      if (isset($content['params']['HATA_NO']) && $content['params']['HATA_NO'] > 0) {
         return ["status" => false, "error_code" => $content['params']['HATA_NO'], "error_message" => $content['params']['HATA_ACIKLAMA']];
      }
      if (isset($content['params']['error']) && $content['params']['error'] > 0) {
         return ["status" => false, "error_code" => "E-".$content['params']['error'], "error_message" => $content['params']['error_desc']];
      }
      return [
         "status" => true,
         "epin" => $content['params']['epin_list']['epin']['code']
      ];
   }

	/**
	 * @param $game_code string Game Code
	 * @return array Array with status, data (array), an example of data array: ["params"=> ["error"=>0, "error_desc"=>"Islem Basarili", "oyun"=>6, "epinUrunListesi" => ["urun"=>[ ["name"=>"Valorant 175 VP", "id"=>10929, "stock"=>3443, "min_order"=>1, "max_order"=>0, "price"=>"13.445", "tax_type" => ""], ["name"=>"Valorant 740 VP", "id"=>10931, "stock"=>513, "min_order"=>1, "max_order"=>0, "price"=>"55.357", "tax_type" => ""] ]]]]
	 */
   public function getEpinProduct($game_code) {
      $data = [
         "oyunKodu" => $game_code,
      ];

      $resp = $this->send_request("POST", "epinUrunleri", $data, [], false); 

      $content = json_decode(json_encode((array)simplexml_load_string($resp["response"])),true);
      return [
         "status" => true,
         "data" => $content
      ];
   }

	/**
	 * @return array Array with status, data (array), an example of data array: ["params"=> ["error"=>0, "error_desc"=>"Islem Basarili", "oyunListesi" => ["oyun"=>[ ["name"=>"4Story", "id"=>104], ["name"=>"5Street", "id"=>638] ]]]]
	 */
   public function getGameList()
   {

      $data = [];
      $resp = $this->send_request("POST", "epinOyunListesi", $data, [], false); 

      $content = json_decode(json_encode((array)simplexml_load_string($resp["response"])),true);
      return [
         "status" => true,
         "data" => $content
      ];
   }
   private function send_request($type, $api, $data=[], $headers=[], $only_response=true) {
      $properties =  $this->properties;
      $data = ["APIRequest" => [ 
         "params" => array_merge(["username" => $properties->turkpin_username, "password" => $properties->turkpin_password, "cmd" => $api], $data)
      ]];
      $curl = curl_init();
      curl_setopt_array($curl, array(
         CURLOPT_URL => $this->base_url.(($type == "GET") ? "?".http_build_query($data) : ""),
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
         CURLOPT_POSTFIELDS => ($type == "GET") ? [] : 'DATA=<?xml version="1.0" encoding="utf-8"?>'.$this->array_as_xml($data),
         CURLOPT_HTTPHEADER => $headers,
      ));

      $result = curl_exec($curl);
      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);
      if ($only_response) return $result;
      return ["code" => $httpcode, "response" => $result];
   }

   private function array_as_xml($arr) {
      $xml = "";
      if (!is_array($arr)) return $xml;
      foreach ($arr as $key => $value) {
         $xml.="<".$key.">";
         if (is_array($value)) {
            $xml.=$this->array_as_xml($value);
         } else {
            $xml.=$value;
         }
         $xml.="</".$key.">";
      }
      return $xml;
   }

}
