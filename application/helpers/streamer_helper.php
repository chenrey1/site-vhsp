<?php

function streamer_refresh_token($user_id, $streamer_obj, $streamer_refresh_date) {
	$ci = &get_instance();

	$refresh_time = 60*60;

	if (!$streamer_obj) {
		return false;
	}

	if (!array_key_exists("access_token", (array) $streamer_obj)) {
		return $streamer_obj;
	}

  	if ($refresh_time + $streamer_refresh_date < time()) {
	  	$get_user = streamer_send_request("https://streamlabs.com/api/v2.0/user", [], "GET", $streamer_obj->access_token);
		if ($get_user["success"]) {
			$user = json_decode($get_user["response"], true);
	  		if (array_key_exists("error", $user)) {
		  		addlog('streamer_refresh_token', 'Yayıncının kullanıcı bilgileri güncellenirken bir hata oluştu. Yayıncı: '.$streamer_obj->streamlabs->username.', Hata: '.$user["error_description"]);
		  		return $streamer_obj;
	  		}
		  	$streamer_obj = (object) array_merge((array)$streamer_obj, $user);
		  	$ci->db->where("id", $user_id)->update("user", ["streamer_info" => json_encode($streamer_obj), "streamer_refresh_date" => time()]);
		  	$streamer_db = $ci->db->where("id", $user_id)->get('user')->row()->streamer_info;
		  	$streamer_obj = json_decode($streamer_db, false);
		  	return $streamer_obj;
	  	} else {
  			addlog('streamer_refresh_token', 'Yayıncı bilgileri güncellenirken bir hata oluştu. Yayıncı: '.$streamer_obj->streamlabs->username.', Hata: '.$get_user["message"]);
	  		return $streamer_obj;
	  	}
  	}
  	return $streamer_obj;
}

function streamer_create_authorize($url) {
	$ci = &get_instance();
	$uid = $ci->session->userdata('info')['id'];

	$prop = $ci->db->where('id', 1)->get('properties')->row();
	$url2 = "https://streamlabs.com/api/v2.0/authorize?response_type=code&client_id={$prop->streamlabs_client_id}&scope=donations.read+donations.create&redirect_uri={$url}";
	redirect($url2, 'refresh');
	die();
}

function streamer_authorize($url, $code) {
	$ci = &get_instance();
	$uid = $ci->session->userdata('info')['id'];

	$prop = $ci->db->where('id', 1)->get('properties')->row();
	$request = streamer_send_request("https://streamlabs.com/api/v2.0/token", [
		"grant_type" => "authorization_code",
		"client_id" => $prop->streamlabs_client_id,
		"client_secret" => $prop->streamlabs_client_secret,
		"code" => $code,
		"redirect_uri" => $url
	], "POST");

	if ($request["success"]) {
		$json = json_decode($request["response"], true);
		if (json_last_error() != JSON_ERROR_NONE || is_string($json)) {
			addlog('streamer_authorize', 'Sistemsel bir hata oluştu. Bölüm:5, Kullanıcı ID: '.$uid.', Dönüt: '.$request["response"]);
			return ["success" => false, "message" => "Sistemsel bir hata oluştu."];
		}
		if (array_key_exists("error", $json)) {
			addlog('streamer_authorize', 'Sistemsel bir hata oluştu. Bölüm:2, Kullanıcı ID: '.$uid.', Hata: '.$json["error_description"]);
			return ["success" => false, "message" => "Sistemsel bir hata oluştu."];
		}

		$get_user = streamer_send_request("https://streamlabs.com/api/v2.0/user", [], "GET", $json["access_token"]);
  		if ($get_user["success"]) {
  			$user = json_decode($get_user["response"], true);
			if (json_last_error() != JSON_ERROR_NONE || is_string($user)) {
				addlog('streamer_authorize', 'Sistemsel bir hata oluştu. Bölüm:6, Kullanıcı ID: '.$uid.', Dönüt: '.$request["response"]);
				return ["success" => false, "message" => "Bu işlem için yeterli yetkiniz yok!"];
			}
			if (array_key_exists("error", $user)) {
				addlog('streamer_authorize', 'Sistemsel bir hata oluştu. Bölüm:4, Kullanıcı ID: '.$uid.', Hata: '.$json["error_description"]);
				return ["success" => false, "message" => "Sistemsel bir hata oluştu."];
			}
			$user = array_merge($user, $json);

			return ["success" => true, "user" => $user];
  		} else {
			addlog('streamer_authorize', 'Sistemsel bir hata oluştu. Bölüm:3, Kullanıcı ID: '.$uid.', Hata: '.$request["message"]);
			return ["success" => false, "message" => "Sistemsel bir hata oluştu."];
		}
	} else {
		addlog('streamer_authorize', 'Sistemsel bir hata oluştu. Bölüm:1,  Kullanıcı ID: '.$uid.', Hata: '.$request["message"]);
		return ["success" => false, "message" => "Sistemsel bir hata oluştu."];
	}
}

function streamer_send_donate($streamer_obj, $uid, $donor, $message, $identifier, $amount, $currency, $skip_alert=false) {
	if (strlen($donor)<2 || strlen($donor)>25) {
		return ["success" => false, "message" => "Kullanıcı Adı 2-25 karakter aralığında olmalıdır.".strlen($donor)];
	}
	if (strlen($message)>255) {
		return ["success" => false, "message" => "Mesajınız 255 karakterden kısa olmalıdır."];
	}
	$data = [
		"name" => $donor,
		"message" => $message,
		"identifier" => $identifier,
		"amount" => $amount,
		"currency" => $currency,
	];
	$data["skip_alert"] = $skip_alert ? "yes" : "no";
	$send_donation = streamer_send_request("https://streamlabs.com/api/v2.0/donations", $data, "POST", $streamer_obj->access_token);
	if ($send_donation["success"]) {
		$donate = json_decode($send_donation["response"], true);
		if (array_key_exists("error", $donate)) {
			addlog('streamer_send_donate', 'Mesaj gönderilirken bir hata oluştu. Kullanıcı ID: '.$uid.', Yayıncı: '.$streamer_obj->streamlabs->username.', Hata: '.$donate["error_description"]);
			return ["success" => false, "message" => "Bağışınız gönderilirken bir hata oluştu."];
		}
		return ["success" => true, "message" => "Bağışınız başarıyla gönderildi."];
	} else {
		addlog('streamer_send_donate', 'Mesaj gönderilirken bir hata oluştu. Kullanıcı ID: '.$uid.', Yayıncı: '.$streamer_obj->streamlabs->username);
		return ["success" => false, "message" => "Bağışınız gönderilirken bir hata oluştu."];
	}
}
 
function streamer_send_request($url, $data, $type="GET", $access_token=false) {
	$curl = curl_init();
	$opts = [
	  	CURLOPT_URL => $url,
	  	CURLOPT_RETURNTRANSFER => true,
	  	CURLOPT_ENCODING => "",
	  	CURLOPT_MAXREDIRS => 10,
	  	CURLOPT_TIMEOUT => 30,
	  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  	CURLOPT_CUSTOMREQUEST => $type,
	  	CURLOPT_HTTPHEADER => [
	    	"Accept: application/json"
	  	],
	];
	if ($access_token) {
		$opts[CURLOPT_HTTPHEADER][] = "Authorization: Bearer ".$access_token;
	}
	if ($type == "GET") {
		$opts[CURLOPT_URL] = $url."?".implode("&", array_map(function($k, $v) {return $k."=".$v;}, array_keys($data), $data));
	} else {
		$opts[CURLOPT_POSTFIELDS] = implode("&", array_map(function($k, $v) {return $k."=".$v;}, array_keys($data), $data));
		$opts[CURLOPT_POST] = true;
	}
	curl_setopt_array($curl, $opts);
	
	$response = curl_exec($curl);
	$err = curl_error($curl);
	$info = curl_getinfo($curl);

	curl_close($curl);

	if ($err) {
	  	return ["success" => false, "message" => $err];
	} else {
	  	return ["success" => true, "response" => $response, "info" => $info];
	}
}
