<?php

// We are sent a place
$plaatsnaam = strtolower($filter['plaatsnaam']);
$radius = $filter['radius'];
$land = $filter['landcode'];

// Load up the array from searchRadius
$dataArray = unserialize(file_get_contents($documentRoot . 'inc/google_searchRadius.txt'));

// echo '<pre>';
// print_r($dataArray);
// echo '</pre>';

// Check if in array
if (!isset($dataArray[$plaatsnaam . '_' . $land])) {
	$googleData = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($plaatsnaam) . ",+" . urlencode($land) . "&sensor=false");
	$googleArray = json_decode($googleData, TRUE);

	$readArray = (isset($googleArray['results'][0])) ? $googleArray['results'][0] : array();

	// echo 'from google';
}
else {

	// echo 'from file';

	$readArray['geometry']['location']['lat'] = $dataArray[$plaatsnaam . '_' . $land]['lat'];
	$readArray['geometry']['location']['lng'] = $dataArray[$plaatsnaam . '_' . $land]['lon'];
}

if (isset($readArray['geometry']['location']['lat']) && isset($readArray['geometry']['location']['lng'])) {

	$latitude = $readArray['geometry']['location']['lat'];
	$longitude = $readArray['geometry']['location']['lng'];

	// Save to file
	$dataArray[$plaatsnaam . '_' . $land]['lat'] = $latitude;
	$dataArray[$plaatsnaam . '_' . $land]['lon'] = $longitude;
	file_put_contents($documentRoot . 'inc/google_searchRadius.txt', serialize($dataArray));

	$boundingBox = array();
	$boundingBox = getBoundingBox($latitude, $longitude, $radius);

	$boundingBox['original_lat'] = $latitude;
	$boundingBox['original_lng'] = $longitude;

	// $output = '';

	// foreach ($boundingBox as $key => $val) {

	// 	$output .= '[' . $key . '=' . $val . ']';
	// }

	// echo $output;

	// Oude json output
	// $json = json_encode($boundingBox);

	// echo $json;
}

function getBoundingBox($lat_degrees,$lon_degrees,$distance_in_miles) {

	// getBoundingBox
	// hacked out by ben brown <ben@xoxco.com>
	// http://xoxco.com/clickable/php-getboundingbox
	// given a latitude and longitude in degrees (40.123123,-72.234234) and a distance in miles
	// calculates a bounding box with corners $distance_in_miles away from the point specified.
	// returns $min_lat,$max_lat,$min_lon,$max_lon 
	//$radius = 3963.1; // of earth in miles
	$radius = 6730.0; // of earth in km

	// bearings
	$due_north = 0;
	$due_south = 180;
	$due_east = 90;
	$due_west = 270;

	// convert latitude and longitude into radians
	$lat_r = deg2rad($lat_degrees);
	$lon_r = deg2rad($lon_degrees);

	// find the northmost, southmost, eastmost and westmost corners $distance_in_miles away
	// original formula from
	// http://www.movable-type.co.uk/scripts/latlong.html
	$northmost  = asin(sin($lat_r) * cos($distance_in_miles/$radius) + cos($lat_r) * sin ($distance_in_miles/$radius) * cos($due_north));
	$southmost  = asin(sin($lat_r) * cos($distance_in_miles/$radius) + cos($lat_r) * sin ($distance_in_miles/$radius) * cos($due_south));

	$eastmost = $lon_r + atan2(sin($due_east)*sin($distance_in_miles/$radius)*cos($lat_r),cos($distance_in_miles/$radius)-sin($lat_r)*sin($lat_r));
	$westmost = $lon_r + atan2(sin($due_west)*sin($distance_in_miles/$radius)*cos($lat_r),cos($distance_in_miles/$radius)-sin($lat_r)*sin($lat_r));

	$northmost = rad2deg($northmost);
	$southmost = rad2deg($southmost);

	$eastmost = rad2deg($eastmost);
	$westmost = rad2deg($westmost);

	// sort the lat and long so that we can use them for a between query
	if ($northmost > $southmost) {

		$lat1 = $southmost;
		$lat2 = $northmost;
	}
	else {

		$lat1 = $northmost;
		$lat2 = $southmost;
	}

	if ($eastmost > $westmost) {

		$lon1 = $westmost;
		$lon2 = $eastmost;
	}
	else {

		$lon1 = $eastmost;
		$lon2 = $westmost;
	}

	return array('lat1' => $lat1, 'lat2' => $lat2, 'lon1' => $lon1, 'lon2' => $lon2);

// [lat1=50.777533589492][lat2=51.118056619588][lon1=5.7212562881702][lon2=6.0824118599368]

}

?>