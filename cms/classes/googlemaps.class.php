<?php

class Googlemaps {

	static function calculateDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {

		// Check if in cache
		global $documentRoot;

		// See if cache exists
		if (file_exists($documentRoot . 'data/cache/google/distance/' . md5($latitudeFrom . '_' . $longitudeFrom) . '.txt')) {

			$data = file_get_contents($documentRoot . 'data/cache/google/distance/' . md5($latitudeFrom . '_' . $longitudeFrom) . '.txt');

			return $data;
		}
		else {

			// convert from degrees to radians
			$latFrom = deg2rad($latitudeFrom);
			$lonFrom = deg2rad($longitudeFrom);
			$latTo = deg2rad($latitudeTo);
			$lonTo = deg2rad($longitudeTo);

			$latDelta = $latTo - $latFrom;
			$lonDelta = $lonTo - $lonFrom;

			$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

			file_put_contents($documentRoot . 'data/cache/google/distance/' . md5($latitudeFrom . '_' . $longitudeFrom) . '.txt', ($angle * $earthRadius));

			return $angle * $earthRadius;
		}
	}
}