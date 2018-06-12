<?php

class Core {
	
	// Class which contains several core functions.
	
	/**
	 * This function constructs all $_GETs into one URL to maintain the data while, for example, changing pages
	 * @param {Array} get All $_GET data.
	 */
	static function constructGetUrl($get, $customArray = array()) {
		
		$tempVar = '';
		
		foreach ($get as $key => $val) {
			
			if ($key != 'page' && $key != 'p') {
				
				// If key exists in customArray, use that.
				$trueVal = (isset($customArray[$key])) ? $customArray[$key] : $val;
				
				if (isset($customArray[$key])) unset($customArray[$key]);
				
				if (empty($tempVar))
					$tempVar = '?'.$key.'='.$trueVal;
					else
						$tempVar .= '&'.$key.'='.$trueVal;
			}
		}
		
		// If any customArray left, prependddd
		if (count($customArray) > 0) {
			
			foreach ($customArray as $subKey => $subVal) {
				
				$tempVar .= (empty($tempVar)) ? '?' . $subKey . '=' . $subVal : '&' . $subKey . '=' . $subVal;
			}
		}
		
		return $tempVar;
		
	}
	
	/**
	 * This functions checks whether a page was called through an ajax request or not.
	 */
	static function isAjaxCall() {
		
		global $config;
		
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
			return true;
			else {
				header("Location: ".$config['website']['url']."cms/");
				die();
			}
			
	}
	
	/**
	 * This function makes a hard redirect with a die() to prevent following code from being executed
	 */
	static function redirect($location) {
		
		header("Location: " . $location);
		die();
	}
	
	/**
	 * This function compares the PHP version and tells whether it matches or not
	 * @param version string The version you wish to compare to
	 */
	static function comparePHPVer($version) {
		
		if ($version == phpversion())
			return true;
			else
				return false;
				
	}
	
	/**
	 * Function which replace special characters; for example in a URL
	 * @param string $input The string to edit
	 * @param string $type The type (such as dir, filename, etc.)
	 */
	static function replaceSpecialChars($input, $type) {
		
		switch ($type) {
			
			case 'dir':
				
				$replacements = array("&amp;", " ", "?", "/", "\\", "|", ":", ";", "<", ">", "*", ".", "&", "\n", "+", ",");
				$replaceValues = array("en", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "en", "-", "-", "");
				
				return str_replace($replacements, $replaceValues, $input);
				
				break;
				
			case 'permaLink':
				
				$text = utf8_encode($input);
				$text = preg_replace('/[^\\pL0-9]+/u', '-', $text);
				$text = trim($text, "-");
				$text = iconv("utf-8", "us-ascii//TRANSLIT", $text);
				$text = preg_replace('/[^-a-z0-9]+/i', '', $text);
				return strtolower($text);
				
				break;
				
			case 'dirUrl':
				
				$replacements = array("&");
				$replaceValues = array("-");
				
				return str_replace($replacements, $replaceValues, $input);
				
				break;
		}
	}
	
	/**
	 * Function which formats a number
	 * @param int $num The number
	 * @param int $dec The amount of decimals
	 * @param string $format Format type (NL / US)
	 * @return string Formatted number
	 */
	static function formatNum($num, $format = 'NL') {
		
		$num = (float)$num;
		
		if ($format == 'valuta') {
			
			$number = number_format($num, 2, ',', '.');
			$number = str_replace(',00', ',-', $number);
			
			return '&euro; ' . $number;
		}
		elseif ($format == 'valutaAlt') {
			
			$number = number_format($num, 2, ',', '.');
			
			return '&euro; ' . $number;
		}
		elseif ($format == 'NL')
		return number_format($num, Core::numberOfDecimals($num), ',', '.');
		else
			return number_format($num, Core::numberOfDecimals($num));
	}
	
	static function convertNum($num, $format = 'US') {
		
		if ($format == 'US') {
			
			// Strip dots
			$num = str_replace('.', '', $num);
			// Replace commas with dots
			$num = str_replace(',', '.', $num);
			
			// Count occurences of dot
			$countDots = substr_count($num, '.');
			
			// If more than one, strip excessive dots
			if ($countDots > 1) {
				
				$num = preg_replace("/\./", "", $num, ($countDots - 1));
			}
			
			return floatval($num);
		}
	}
	
	/**
	 * Function which supports formatNum by determining the amount of decimals a number has
	 * Source: http://stackoverflow.com/a/2430214
	 * @param type $value The number
	 * @return int Amount of decimals
	 */
	static function numberOfDecimals($value) {
		
		if ((int)$value == $value) {
			return 0;
		}
		else if (! is_numeric($value)) {
			// throw new Exception('numberOfDecimals: ' . $value . ' is not a number!');
			return false;
		}
		
		return strlen($value) - strrpos($value, '.') - 1;
	}
	
	static function buildTree($array, $counters, $activeId = 0, $pathArray = array()) {
		
		global $dynamicRoot;
		
		// Function which recursively builds the tree for dynatree to handle
		
		// Find the active counter to increment
		$lastKey = count($counters) - 1;
		
		// Output variable
		if (count($counters) == 1)
			$output = '<div id="tree"><ul id="treeData" style="display: none;">';
			else
				$output = '';
				
				// Loop through
				foreach ($array as $key => $val) {
					
					if ($key != 'data' && $key != 'var::count') {
						
						// Increment counter
						$counters[$lastKey]++;
						
						// Generate the key
						$id = implode('.', $counters);
						
						$splitKey = explode('_catId_', $key);
						
						$expanded = (in_array($splitKey[1], $pathArray)) ? ' expanded' : '';
						$active = ($splitKey[1] == $activeId) ? ' active' : '';
						
						$rootType = ($val['data']['var::root'] == 1) ? "root: '1'" : '';
						
						if (!empty($active)) {
							
							$output .= '<li id="' . $splitKey[1] . '" data="' . $rootType . '" class="folder' . $expanded . $active . '">' . $splitKey[0];
						}
						else {
							
							$output .= '<li id="' . $splitKey[1] . '" data="' . $rootType . '" class="folder' . $expanded . '"><a href="' . $dynamicRoot . strtolower(CMS::g('common', 'cms_activeClient')) . '/media/index.html?p=filter&id=' . $splitKey[1] . '" target="_self">' . $splitKey[0] . '</a>';
						}
						
						
						if (is_array($val) && $key != 'data' && $key != 'var::count') {
							
							$tempCounters = $counters;
							$tempCounters[] = 0;
							
							$output .= '<ul>' . Core::buildTree($val, $tempCounters, $activeId, $pathArray) . '</ul>';
						}
						
						$output .= '</li>';
					}
				}
				if (count($counters) == 1)
					$output .= '</ul></div>';
					
					return $output;
	}
	
	static function buildTreePages($array, $counters, $activeId = 0, $pathArray = array()) {
		
		global $dynamicRoot;
		
		// Function which recursively builds the tree for dynatree to handle
		
		// Find the active counter to increment
		$lastKey = count($counters) - 1;
		
		// Output variable
		if (count($counters) == 1)
			$output = '<div id="tree"><ul id="treeData" style="display: none;">';
			else
				$output = '';
				
				// Loop through
				foreach ($array as $key => $val) {
					
					if ($key != 'data' && $key != 'var::count') {
						
						// Increment counter
						$counters[$lastKey]++;
						
						// Generate the key
						$id = implode('.', $counters);
						
						$splitKey = explode('_catId_', $key);
						
						$expanded = (in_array($splitKey[1], $pathArray)) ? ' expanded' : '';
						$active = ($splitKey[1] == $activeId) ? ' active' : '';
						
						$rootType = ($val['data']['var::root'] == 1) ? "root: '1'" : '';
						
						if (!empty($active)) {
							
							$output .= '<li id="' . $splitKey[1] . '" data="' . $rootType . '" class="file' . $expanded . $active . '">' . $splitKey[0];
						}
						else {
							
							$output .= '<li id="' . $splitKey[1] . '" data="' . $rootType . '" class="file' . $expanded . '"><a href="' . $dynamicRoot . strtolower(CMS::g('common', 'cms_activeClient')) . '/indeling/detail.html?id=' . $splitKey[1] . '" target="_self">' . $splitKey[0] . '</a>';
						}
						
						
						if (is_array($val) && $key != 'data' && $key != 'var::count') {
							
							$tempCounters = $counters;
							$tempCounters[] = 0;
							
							$output .= '<ul>' . Core::buildTreePages($val, $tempCounters, $activeId, $pathArray) . '</ul>';
						}
						
						$output .= '</li>';
					}
				}
				if (count($counters) == 1)
					$output .= '</ul></div>';
					
					return $output;
	}
	
	/**
	 * This functions converts EU dateformats to US formats
	 */
	static function convertDateTimeFormat($org, $format = 'Y-m-d'){
		if (strtotime($org)){
			return date($format, strtotime($org));
		} else
			
			return false;
	}
	
	static function buildContentArray($data) {
		
		// A content string is built like key][value[[Ã‚Â¥]]key][value
		
		$contentArray = array();
		
		// Split on [[Ã‚Â¥]]
		$splitStr = explode('[[Ã‚Â¥]]', $data);
		
		foreach ($splitStr as $key => $val) {
			
			// Split on ][
			$subSplit = explode('][', $val);
			
			// Add to array
			$contentArray[$subSplit[0]] = $subSplit[1];
		}
		
		return $contentArray;
	}
	
	static function vd($var, $type = 'print_r') {
		
		echo '<pre>';
		
		if ($type == 'vd') {
			
			var_dump($var);
		}
		else {
			
			print_r($var);
		}
		
		echo '</pre>';
	}
	
	static function calcAvailableDay() {
		
		global $documentRoot;
		
		include($documentRoot . 'inc/inc_feestDagen.php');
		
		$timestamp = time();
		
		$date1 = DateTime::createFromFormat('H:i a', date('h:i:s a', $timestamp));
		$date2 = DateTime::createFromFormat('H:i a', '8:30 am');
		$date3 = DateTime::createFromFormat('H:i a', '5:30 pm');
		
		if (date('N', $timestamp) != 6 && date('N', $timestamp) != 7 && !in_array(date('d-m', $timestamp), $feestDagen[date('Y', $timestamp)]) && $date1 > $date2 && $date1 < $date3) {
			
			return 'today';
		}
		else {
			
			$whileCounter = 0;
			
			while ($whileCounter >= 0) {
				
				$useTime = $timestamp + (86400 * $whileCounter);
				
				$whileCounter++;
				
				if (date('d-m', $timestamp) == date('d-m', $useTime))
					continue;
					
					$currentTime = DateTime::createFromFormat('H:i a', date('h:i:s a', $useTime));
					
					if (date('d-m', $timestamp) == date('d-m', $useTime) && ($currentTime < $date2 || $currentTime > $date3))
						continue;
						
						if (date('N', $useTime) == 6 || date('N', $useTime) == 7)
							continue;
							
							if (isset($feestDagen[date('Y', $useTime)]) && in_array(date('d-m', $useTime), $feestDagen[date('Y', $useTime)]))
								continue;
								
								$returnDay = $useTime;
								
								break;
			}
			
			return $returnDay;
		}
	}
	
	static function numToMonth($month) {
		
		switch ($month) {
			
			case 1:
				
				return 'jan';
				break;
			case 2:
				
				return 'feb';
				break;
			case 3:
				
				return 'maa';
				break;
			case 4:
				
				return 'apr';
				break;
			case 5:
				
				return 'mei';
				break;
			case 6:
				
				return 'jun';
				break;
			case 7:
				
				return 'jul';
				break;
			case 8:
				
				return 'aug';
				break;
			case 9:
				
				return 'sep';
				break;
			case 10:
				
				return 'okt';
				break;
			case 11:
				
				return 'nov';
				break;
			case 12:
				
				return 'dec';
				break;
		}
	}
	
	static function numToMonthFull($month) {
		
		$arrMonths = array(1 => 'januari', 2 => 'februari', 3 => 'maart', 4 => 'april', 5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'augustus', 9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'december');
		
		return $arrMonths[$month];
	}
	
	static function numToDay($num) {
		
		$arrDays = array(0 => 'zondag', 1 => 'maandag', 2 => 'dinsdag', 3 => 'woensdag', 4 => 'donderdag', 5 => 'vrijdag', 6 => 'zaterdag');
		
		return $arrDays[$num];
	}
	
	static function breakLine($data, $lineLength, $maxLines) {
		
		$explodeData = explode(' ',  $data);
		
		$currentChars = 0;
		$currentLine = 1;
		$newStr = '';
		
		foreach ($explodeData as $key => $val) {
			
			$wordCount = strlen($val) + 1;
			
			if (($lineLength - $currentChars) > $wordCount) {
				
				$currentChars += $wordCount;
				$newStr = (empty($newStr)) ? $val : $newStr . ' ' . $val;
			}
			else {
				
				if ($currentLine == $maxLines) {
					
					// How much space is left?
					
					$spaceLeft = $lineLength - $currentChars;
					
					if ($spaceLeft > 5) {
						
						$newStr .= ' ' . substr($val, 0, ($spaceLeft - 3));
					}
					
					$newStr .= '&hellip;';
					
					break;
				}
				else {
					
					$currentLine++;
					$currentChars = $wordCount;
					
					$newStr = (empty($newStr)) ? $val : $newStr . ' ' . $val;
				}
			}
		}
		
		return $newStr;
	}
	
	static function handleTweet($tweet) {
		
		$tweet = preg_replace('!(((f|ht)tp(s)?://)[-a-zA-ZÃ�Â°-Ã‘ï¿½Ã�ï¿½-Ã�Â¯()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" target="_blank">$1</a>', $tweet);
		$tweet = preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '\1<a href="https://twitter.com/hashtag/\2?src=hash" target="_blank">#\2</a>', $tweet);
		$tweet = preg_replace('/(^|\s)@(\w*[a-zA-Z_]+\w*)/', '\1<a href="https://twitter.com/\2" target="_blank">@\2</a>', $tweet);
		
		return $tweet;
	}
	
	static function formatValue($number, $showZeros = false, $prefix = '&euro; ') {
		
		// Format the money
		$returnVal = number_format($number, 2, ',', '.');
		
		if (!$showZeros)
			$returnVal = str_replace(',00', ',-', $returnVal);
			
			return $prefix . $returnVal;
	}
	
	static function fixSite($site) {
		
		// Check if it starts with http
		if (substr($site, 0, 4) == 'http') {
			
			return $site;
		}
		else {
			
			// Place it
			return 'http://' . $site;
		}
	}
	
	static function formatSite($site) {
		
		return str_replace(array('http://', 'https://'), array('', ''), $site);
	}
	
	static function isFall() {
		
		// Year doesn't really matter, so use the current year
		$start = date('Y-09-21 00:00:00');
		$end = date('Y-03-21 00:00:00');
		
		$current = date('Y-m-d H:i:s');
		
		if ($current >= $start || $current <= $end)
			return true;
			
			return false;
	}
}

?>