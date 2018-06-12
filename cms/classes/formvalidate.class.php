<?php

class FormValidate {
	
	static $latestError;
	
	/**
	 * Checks whether a date both exists (ie. 2017-01-32) and is of $format
	 * @param date $date
	 * @param string $format The format of $val (such as Y-m-d)
	 * @return boolean
	 */
	static function date($date, $format = 'Y-m-d H:i:s') {
		
		$d = PP_DateTime::createFromFormat($format, $date);
		
		return $d && $d->format($format) == $date;
	}
	
	static function dateMin($data, $minDate) {
		
		$splitFormat = explode('||', $minDate);
		
		if (FormValidate::date($splitFormat[1], $splitFormat[0])) {
			
			$d = PP_DateTime::createFromFormat($splitFormat[0], $splitFormat[1]);
			$e = PP_DateTime::createFromFormat($splitFormat[0], $data);
			
			if ($e > $d)
				return true;
		}
		
		return false;
	}
	
	static function is($val, $type) {
		
		switch ($type) {
			
			case 'email':
				
				if (filter_var($val, FILTER_VALIDATE_EMAIL))
					return true;
					
				FormValidate::$latestError = 'VAL_0006';
				
				break;
				
			case 'postcode':
				
				$val = strtoupper(str_replace(' ' , '', $val));
				
				if (preg_match("/^\W*[1-9]{1}[0-9]{3}\W*[a-zA-Z]{2}\W*$/", $val))
					return true;
				
				break;
		}
		
		return false;
	}
	
	static function contains($value, $type) {
		
		// $type is an array which may contain more data
		if (is_array($type)) {
			
			foreach ($type as $key => $val) {
				
				// The value must contain $val amount of capital letters
				if ($key == 'capital') {
					
					if(preg_match_all('/[A-Z]/', $value) < $val)						
						$return = $val;
					
					FormValidate::$latestError = 'VAL_0010';
				}
			}
		}
		
		if (isset($return))
			return $return;
		
		return true;
	}
	
	/**
	 * Checks if a value is no longer than $length
	 * @param unknown $val
	 * @param int $length
	 * @return boolean
	 */
	static function maxLength($val, $length) {
		
		if (strlen($val) <= $length)
			return true;
		
		return false;
	}
	
	/**
	 * Counts whether too many options were selected
	 * @param array $val
	 * @param int $length
	 * @return boolean
	 */
	static function maxCount(Array $val, $length) {
		
		if (is_array($val) && count($val) <= $length)
			return true;
		
		return false;
	}
	
	/**
	 * Checks if a value is no shorter than $length
	 * @param unknown $val
	 * @param int $length
	 * @return boolean
	 */
	static function minLength($val, $length) {
		
		if (strlen($val) >= $length)
			return true;
		
		return false;
	}
	
	/**
	 * Counts whether enough options were selected
	 * @param array $val
	 * @param int $length
	 * @return boolean
	 */
	static function minCount(Array $val, $length) {
		
		if (is_array($val) && count($val) >= $length)
			return true;
		
		return false;
	}
	
	/**
	 * Checks whether a value (or array of values) is numeric
	 * @param mixed $val
	 * @return boolean
	 */
	static function numeric($val) {
		
		// If it's an array, check them all and return false on the first error
		if (is_array($val)) {
			
			foreach ($val as $checkVal) {
				
				if (!is_numeric($checkVal))
					return false;
			}
		}
		else {
			
			if (!is_numeric($val))
				return false;
		}
		
		return true;
	}
	
	/**
	 * Checks if a value is not empty 
	 * @param Mixed $val
	 * @return boolean
	 */
	static function required($val) {
		
		// If it's an array, validate it has multiple elements that are NOT empty
		if (is_array($val)) {
			
			if (count($val) > 0) {
				
				foreach ($val as $checkVal) {
					
					if (strlen($checkVal) > 0)
						return true;
				}
			}
		}
		else {
			
			if (strlen($val) > 0)
				return true;
		}
		
		return false;
	}
	
	static function uniqueDbField($value, $data, $db) {
		
		// Loop through keys to see if there's any ignore value
		$ignoreStr = '';
		
		foreach ($data as $key => $val) {
			
			$explode = explode('_', $key);
			
			if ($explode[0] == 'ignore') {
				
				$ignoreStr .= " AND `" . $explode[1] . "`!='" . $val . "'";
			}
		}
		
		// Check it
		$check = $db->prepare("SELECT * FROM `" . $data['table'] . "` WHERE `" . $data['key'] . "`=?" . $ignoreStr . " LIMIT 1", "s", array($value));
		
		if (count($check) > 0) {
			
			return false;
		}
		
		return true;
	}
	
	static function verifyPassword($pass, $id, $db) {
		
		// Find user
		$user = $db->prepare("SELECT * FROM `MED` WHERE `Id`=?", "i", array($id));
		
		if (count($user) > 0) {
			
			// Verify password
			$userConstruct = 'sha256:' . $user[0]['Wachtwoord_Count'] . ':' . $user[0]['Wachtwoord_Salt'] . ':' . $user[0]['Wachtwoord'];
			
			$passHash = Hash::validate_password($pass, $userConstruct);
			
			if ($passHash)
				return true;
		}
		
		return false;
	}
	
	static function getLatestErrorCode() {
	
		return FormValidate::$latestError;
	}
}