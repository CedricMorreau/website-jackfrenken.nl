<?php

// FIXIT SB:	Onderstaand moet je misschien nog aanpassen Sander.
//				Dit script werkt maar tot 1 januari
//				Let wel op: Als er géén classes zijn ook de attribuut (class="") niet tonen.
$herfst = false;
$today = date('Y-m-d');
$fallStart = date('Y-m-d', strtotime('09/21/'. date('Y')));
$fallEnd = date('Y-m-d', strtotime('03/21/'. (date('Y') + 1)));

if (($today > $fallStart) && ($today < $fallEnd)) {

	$herfst = true;
	$sfeerbeeld = $dynamicRoot . '_development/img/home-herfst.jpg';
}

function obj_splitValues($value) { 

	$split = explode(',', $value);

	$return = array();

	foreach ($split as $subKey => $subVal) {

		// if ($subKey != 0)
		// 	echo ', ';

		$return[] = utf8_encode(str_replace(array('[', ']'), array('',''), $subVal));
	}

	return ucfirst(implode(', ', $return));
}

function obj_generateAddress($street, $number, $add) {
	
	$tempAddress = '';
	
	if (!empty($street))
		$tempAddress .= utf8_encode($street);
		
		if (!empty($number))
			$tempAddress .= ' ' . $number;
			
			if (!empty($add))
				$tempAddress .= ' ' . $add;
				
				return $tempAddress;
}

function obj_generateGoogleAddress($place, $street, $number, $add, $postCode = '') {
	
	$tempAddress = '';
	
	if (!empty($street))
		$tempAddress .= $street;
		
		if (!empty($number))
			$tempAddress .= '+' . $number;
			
			if (!empty($add))
				$tempAddress .= '+' . $add;
				
				if (!empty($postCode))
					$postCode = str_replace(' ', '+', $postCode) . '+';
					
					if (!empty($place))
						$tempAddress .= ',+' . $postCode . $place;
						
						return $tempAddress;
}

function obj_generateCost($amount, $space = '') {
	
	$return = '&euro;' . $space;
	
	// Depending on whether it has behind ,
	if (($amount / floor($amount)) == 1) {
		
		$return .= number_format($amount, 0, ",", ".") . ',-';
	}
	else {
		
		$return .= number_format($amount, 2, ",", ".");
	}
	
	return $return;
}

function obj_showPrice($koopPrijsVoorvoegsel, $koopPrijs, $koopConditie, $huurPrijs, $huurConditie) {
	
	$priceText='';
	
	if (($koopPrijs <= 1 && is_null($huurPrijs)) || (is_null($koopPrijs) && is_null($huurPrijs))) {
		
		$priceText = 'Prijs op aanvraag';
	}
	elseif ($koopPrijsVoorvoegsel == 'prijs op aanvraag') {
		
		$priceText = 'Prijs op aanvraag';
	}
	else {
		
		if (!is_null($koopPrijs) && !empty($koopConditie)) {
			
			switch ($koopConditie) {
				
				case 'kosten koper':
					
					$type = 'k.k.';
					break;
					
				case 'vrij op naam':
					
					$type = 'v.o.n.';
					break;
					
				default:
					
					$type = 'k.k.';
					break;
			}
			
			if ($koopPrijs <= 1)
				$priceText = 'Prijs op aanvraag';
			else
				$priceText = obj_generateCost($koopPrijs) . ' ' . $type;
		}
		
		if ((!is_null($huurPrijs) && !empty($huurPrijs)) || (!is_null($huurConditie) && !empty($huurConditie))) {
			
			if ($huurPrijs == 1) {
				
				$priceText = 'Huurprijs op aanvraag';
			}
			else {
			
				if (!empty($priceText)){
					$priceText .= '<br>';
				}
				
				if (!is_null($huurPrijs) && !empty($huurPrijs)) {
					$priceText .= obj_generateCost($huurPrijs) . ' ' . str_replace(array('vierkante meter'), array('m<sup>2</sup>'), $huurConditie);
				} else {
					$priceText .= 'Huurprijs op aanvraag';
				}
			}
		}
	}
	
	return $priceText;
}

?>