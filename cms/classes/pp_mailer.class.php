<?php

// Class welke mail verstuurd via comm.pixelplus.nl
class PP_Mailer {

	private $apiKey;
	private $mailObject = array();

	public function addField($fieldType, $fieldValue) {

		if ($fieldType == 'to' || $fieldType == 'cc' || $fieldType == 'bcc') {

			if (isset($this->mailObject[$fieldType]) && !empty($this->mailObject[$fieldType]))
				$this->mailObject[$fieldType] .= ',' . $fieldValue;
			else {
				$this->mailObject[$fieldType] = $fieldValue;
			}
		}
		else {

			$this->mailObject[$fieldType] = $fieldValue;
		}
	}

	public function send() {

		return $this->loadPage('http://comm.pixelplus.nl/api/v1/', 1, $this->mailObject);
	}

	private function loadPage($page, $isPost = 0, $postData = '') {

		$ch = curl_init();

		// curl_setopt($ch, CURLOPT_USERPWD, "api:" . $key);
		$res = curl_setopt($ch, CURLOPT_URL, $page);
		if ($isPost == 1) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$result = curl_exec($ch);

		if(curl_error($ch))
		{
		    die('error:' . curl_error($ch));
		}

		curl_close($ch);

		return $result;
	}
}