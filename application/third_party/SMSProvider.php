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
namespace Gleox\OneClickSolutions\SimpleLib;
class SMSProvider
{
	protected $CI;
	protected $sms_messages;
	protected $options;
	protected $provider;

	public function __construct() {
		$this->CI =& get_instance();
		$this->sms_messages = array();
		$this->options = array();
		$this->provider = null;
	}

	protected function __codeIgniter() {
		return $this->CI;
	}

	/**
	 * @param $provider_name string
	 * @return $this
	 * @throws \Exception
	 */
	public function set_provider($provider_name) {
		if (file_exists(APPPATH . "third_party/sms_providers/{$provider_name}.php")) {
			require_once(APPPATH . "third_party/sms_providers/{$provider_name}.php");
			$provider_class = "Gleox\\OneClickSolutions\\SimpleLib\\SMSProviders\\{$provider_name}";
			$this->provider = new $provider_class();
		} else {
			throw new \Exception("SMS provider file not found!");
		}
		return $this;
	}

	/**
	 * @param $options array
	 * @return $this
	 */
	public function set_options($options = array()) {
		$this->options = $options;
		return $this;
	}

	/**
	 * @param $sms_message SMS_Message
	 * @return $this
	 */
	public function add_message($sms_message) {
		$this->sms_messages[] = $sms_message;
		return $this;
	}

	/**
	 * @return array[SMS_Message]
	 */
	public function get_sms_messages() {
		return $this->sms_messages;
	}

	/**
	 * @param $sms_message_index
	 * @param $sms_message
	 * @return $this
	 */
	public function set_sms_message($sms_message_index, $sms_message) {
		$this->sms_messages[$sms_message_index] = $sms_message;
		return $this;
	}

	/**
	 * @param array $options
	 * @throws Exception
	 */
	public function send_sms($options = array()) {
		if (!$this->provider) {
			throw new \Exception("SMS provider is not set!");
		}
		$sms_messages = $this->sms_messages;
		$sms_options = array_merge($this->options, $options);

		if (method_exists($this->provider, "send_bulk_sms") && count($sms_messages) > 1) {
			$this->provider->send_bulk_sms($sms_options);
		}
		else {
			foreach ($sms_messages as $sms_message) {
				$message = $sms_message->getMessage();
				$gsm = $sms_message->getGSM();

				$this->provider->send_sms($gsm, $message, $sms_options);
			}
		}
	}

	public function get_provider() {
		return $this->provider;
	}

}

class SMS_Message {
	private $message;
	private $gsm;

	/**
	 * SMS_Message constructor.
	 * @param $message string message
	 * @param $gsm string phone number
	 */
	function __construct($message, $gsm) {
		$this->message = $message;
		$this->gsm = $gsm;
	}

	/**
	 * @return string message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return string gsm
	 */
	public function getGSM() {
		return $this->gsm;
	}
}
