<?php

Class P4 {
	
	static function validateData($data, $secret) {
		
		// Must always have a $data['key']
		if (!empty($data['signature'])) {
			
			// Validate if the sent data is valid.
			$secretKey = $secret;
			
			$verifySignature = $data['signature'];
			
			unset($data['signature']);
			
			ksort($data);
			
			// Signature (turn into sha1)
			$startHandtekening = '';
			
			// Alles netjes op een rij zetten
			foreach ($data as $key => $val) {
				
				$startHandtekening .= $key . '=' . urldecode($val);
			}
			
			// Append the secret key from P4
			$startHandtekening .= $secretKey;
			
			// Hash it
			$postHash = sha1($startHandtekening);
			
			// Compare hashes to verify the request
			if ($postHash == $verifySignature)
				return true;
		}
		
		return false;
	}
	
	static function buildSignature($data, $secret) {
		
		$return = '';
		
		// If any signature set, remove it
		unset($data['signature']);
		
		// Sort
		ksort($data);
		
		// Add to string
		foreach ($data as $key => $val) {
			
			$return .= $key . '=' . $val;
		}
		
		// Append secret
		$return .= $secret;
		
		return sha1($return);
	}
	
	static function sendRequest($page, $isPost = 0, $postData = '') {
		
		$ch = curl_init();
		
		// curl_setopt($ch, CURLOPT_USERPWD, "api:" . $key);
		$res = curl_setopt($ch, CURLOPT_URL, $page);
		if ($isPost == 1) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$result = curl_exec($ch);
		
		return $result;
	}
}