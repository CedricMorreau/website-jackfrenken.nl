<?php

class Cipher {
	
	private $securekey, $iv;
	function __construct($textkey) {
		
		$this->securekey = hash('sha256',$textkey,TRUE);
	}
	
	function encrypt($input) {
		
		$this->iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_RAND);
		
		return base64_encode($this->iv) . ':' . base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_CBC, $this->iv));
	}
	
	function decrypt($input, $iv) {
		
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($input), MCRYPT_MODE_CBC, base64_decode($iv)));
	}
	
	function encryptAlt($input) {
		
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv));
	}
	
	function decryptAlt($input) {
		
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
	}
}

?>