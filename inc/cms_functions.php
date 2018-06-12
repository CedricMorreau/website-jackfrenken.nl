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

?>