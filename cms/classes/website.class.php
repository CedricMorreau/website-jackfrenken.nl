<?php

/*
 * Class added 2017-03-09 for website-specific functions. Changes per website.
 */
class Website {
	
	/**
	 * Show the time range when the store is open (false if closed)
	 * @param Database $db
	 * @param string $date
	 * @return boolean|array False if none found
	 */
	static function getOpenHours($db, $date) {
		
		// First of all check if for this date there is a specific override
		$override = $db->prepare("SELECT * FROM `tbl_mod_openingsTijden` WHERE `mod_ot_type`='override' AND `mod_ot_dateOverride`=?", "s", array($date));
		
		if (count($override) > 0) {
				
			// If closed (no times set!)
			if (is_null($override[0]['mod_ot_openFrom']) || is_null($override[0]['mod_ot_openTo']))
				return false;
			
			// Build an array
			$array = array('from' => $override[0]['mod_ot_openFrom'], 'to' => $override[0]['mod_ot_openTo']);
				
			return $array;
		}
		else {
			
			// No override, fetch current day
			$currentDay = strtolower(date('l', strtotime($date)));
			
			// Fetch the data
			$data = $db->prepare("SELECT * FROM `tbl_mod_openingsTijden` WHERE `mod_ot_type`='default' AND `mod_ot_name`=?", "s", array($currentDay));
			
			if (count($data) > 0) {
				
				// If closed (no times set!)
				if (is_null($data[0]['mod_ot_openFrom']) || is_null($data[0]['mod_ot_openTo']))
					return false;
				
				// Build an array
				$array = array('from' => $data[0]['mod_ot_openFrom'], 'to' => $data[0]['mod_ot_openTo']);
				
				return $array;
			}
		}
		
		// If we get here, nothing was found
		return false;
	}

	/**
	 * Check if the first upcoming $day is special
	 * @param Database $db
	 * @param string $day
	 * @return boolean
	 */
	static function hasNextDay($db, $day) {
		
		// Get next day
		$nextDay = date('Y-m-d', strtotime('next ' . $day));
		
		// See if an override exists for this day
		$data = $db->prepare("SELECT * FROM `tbl_mod_openingsTijden` WHERE `mod_ot_type`='override' AND `mod_ot_dateOverride`=?", "s", array($nextDay));
		
		if (count($data) > 0) {
			
			return true;
		}
		
		return false;
	}
	
	static function getCatImage($catId) {
		
		if (!isset($_SESSION['filter_images']))
			$_SESSION['filter_images'] = array();
		
		if (isset($_SESSION['filter_images'][$catId]))
			return $_SESSION['filter_images'][$catId];
			
		$arrImages = array(
			// De Zon straalt
			130 => array(
				'img/filter-algemeen.png', 'img/filter-algemeen-darja.png'
			),
			// Tip van onze styliste
			131 => array(
				'img/filter-stylist.png', 'img/filter-algemeen-darja.png'
			),
			// Vakman aan het woord
			132 => array(
				'img/filter-vakman.png', 'img/filter-vakman-01.png', 'img/filter-vakman-02.png', 'img/filter-vakman-03.png', 'img/filter-vakman-04.png'
			),
		);
		
		$var = $arrImages[$catId][array_rand($arrImages[$catId])];
		
		$_SESSION['filter_images'][$catId] = $var;
		
		return $var;
	}
	
	/**
	 * Returns a classname for a specific product group
	 * @param int $groupId ID of the group
	 * @return string|boolean Returns false if not found
	 */
	static function groupToClass($groupId) {
		
		$arrGroups = array(
			1 => 'vloeren',
			2 => 'raamdecoratie', 
			3 => 'buitenzonwering',
			4 => 'insectenwering',
			5 => 'wandbekleding',
			6 => 'accessoires'
		);
		
		if (isset($arrGroups[$groupId]))
			return $arrGroups[$groupId];
		
		return false;
	}
	
	/**
	 * Returns a brand image, with link if necessary
	 * @param unknown $name
	 * @param unknown $logo
	 * @param unknown $url
	 * @return string
	 */
	static function showBrand($name, $logo, $url) {
		
		$data = '';
		
		if (substr($url, 0, 8) == 'article_') {
			
			global $template;
			
			$explode = explode('_', $url);
			
			$url = $template->getArticleUrl($explode[1]);
		}
		
		/* <a href="<?php echo $url; ?>" title="<?php echo $values['brand_name']; ?>" target="_blank"><img src="<?php echo $values['brand_logo']; ?>" alt="Logo <?php echo $values['brand_name']; ?>"></a>*/
		
		if ($url !== false && $url != '404.html')
			$data .= '<a href="' . $url . '" title="' . $name . '" target="_blank">';
		
		$data .= '<img src="' . $logo . '" alt="Logo ' . $name . '">';
		
		if ($url !== false && $url != '404.html')
			$data .= '</a>';
		
		return $data;
	}
}