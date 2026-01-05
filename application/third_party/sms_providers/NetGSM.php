<?php
/*
   ____ _
  / ___| | ___  _____  __
 | |  _| |/ _ \/ _ \ \/ /
 | |_| | |  __/ (_) >  <
  \____|_|\___|\___/_/\_\

    Gleox Simple Library
   For Featured Projects

    https://gleox.com

  Version: 1.0.0
  Library Version: 1.0.0
*/
namespace Gleox\OneClickSolutions\SimpleLib\SMSProviders;
class NetGSM {

	public function send_sms($gsm, $message, $sms_options) {
		$result = $this->send_http_request("http://api.netgsm.com.tr/sms/send/get/", [
			"usercode" => $sms_options["username"],
			"password" => $sms_options["password"],
			"gsmno" => $gsm,
			"message" => $message,
			"msgheader" => $sms_options["header"],
			"startdate" => $sms_options["startdate"] ?? "",
			"stopdate" => $sms_options["stopdate"] ?? "",
			"filter" => $sms_options["filter"] ?? "",
			"dil" => "TR"
		]);
		$exp = explode(" ", $result);
		$data = [];
		if ($exp[0] == "00" || $exp[0] == "01" || $exp[0] == "02" || $exp[0] == "347022009") {
			$data["success"] = true;
			$data["query_id"] = trim($exp[1] ?? "");
		} else {
			$data["success"] = false;
			if ($exp[0] == "20") $data["err_msg"] = "Mesajınız mesaj metnindeki bir problemden dolayı veya <br>standart maksimum karakter sayısı aşıldığı için gönderilemedi.";
			if ($exp[0] == "30") $data["err_msg"] = "Geçersiz kullanıcı adı veya şifre<br>veya API erişim iznininiz yok.";
			if ($exp[0] == "40") $data["err_msg"] = "Mesaj Başlığınız Sistem Tanımlı Değildir.";
			if ($exp[0] == "70") $data["err_msg"] = "Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.Yöneticinize Başvurun";
		}
		return $data;
	}
	public function send_bulk_sms($sms_options=[]) {
		$sms_options["type"] = $sms_options["type"] ?? "n:n";
		$xml = '<?xml version="1.0" encoding="UTF-8"?><mainbody><header>';
		$xml .= '<company wh="1" dil="TR">Netgsm</company>';
		$xml .= '<usercode>' . $sms_options["username"] . '</usercode>';
		$xml .= '<password>' . $sms_options["password"] . '</password>';
		$xml .= '<startdate>'.($sms_options["startdate"] ?? "").'</startdate>';
		$xml .= '<stopdate>'.($sms_options["stopdate"] ?? "").'</stopdate>';
		$xml .= '<type>'.$sms_options["type"].'</type>';
		$xml .= '<msgheader>' . $sms_options["header"] . '</msgheader>';
		if (isset($sms_options["filter"])) $xml .= '<filter>'.$sms_options["filter"] . '</filter>';
		$xml .= '</header>';

		$xml .= '<body>';

		if ($sms_options["type"] == "n:n") {
			foreach ($this->SMS_Messages as $SMS) {
				$xml .= '<mp><msg><![CDATA[' . $SMS->getMessage() . ']]></msg><no>' . $SMS->getGSM() . '</no></mp>';
			}
		} else {
			$xml .= '<mp><msg><![CDATA[' . $this->SMS_Messages[0]->getMessage() . ']]></msg>';
			foreach ($this->SMS_Messages as $SMS) {
				$xml .= '<no>' . $SMS->getGSM() . '</no>';
			}
			$xml .= '</mp>';
		}

		$xml .= '</body></mainbody>';

		$result = (string)$this->send_xml_request("http://api.netgsm.com.tr/sms/send/xml", $xml);
		$exp = explode(" ", $result);
		$data = [];
		if ($exp[0] == "00" || $exp[0] == "01" || $exp[0] == "02" || $exp[0] == "347022009") {
			$data["success"] = true;
			$data["query_id"] = trim($exp[1] ?? "");
		} else {
			$data["success"] = false;
			if ($exp[0] == "20") $data["err_msg"] = "Mesajınız mesaj metnindeki bir problemden dolayı veya <br>standart maksimum karakter sayısı aşıldığı için gönderilemedi.";
			if ($exp[0] == "30") $data["err_msg"] = "Geçersiz kullanıcı adı veya şifre<br>veya API erişim iznininiz yok.";
			if ($exp[0] == "40") $data["err_msg"] = "Mesaj Başlığınız Sistem Tanımlı Değildir.";
			if ($exp[0] == "70") $data["err_msg"] = "Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.Yöneticinize Başvurun";
		}
		return $data;
	}


	private function send_xml_request($url, $content) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	private function send_http_request($url, $content) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/x-www-form-urlencoded"));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

}
