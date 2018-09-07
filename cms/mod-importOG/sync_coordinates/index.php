<?php

set_time_limit(0);

// Global configuration
include("inc/config.php");

// Database connection
include("inc/database.class.php");

// Start up the DB connection
$db = new Database();
$db->connection($dbd['username'], $dbd['passwd'], $dbd['server']);
$db->selectDatabase($dbd['database']);

// Set vestiging to IN_PROGRESS
// $db->prepare("UPDATE `tbl_OG_cronjob` SET `status`=1 WHERE `vestigingNr`=0 AND `feedType`='COORDS'");

// Cases
$cases = array();
$cases[] = 'wonen';
$cases[] = 'bog';

foreach ($cases as $key => $val) {

	switch ($val) {

		case 'wonen':

			// Fetch all where google_status=0
			$query = "SELECT `id`, `objectDetails_Adres_NL_Postcode` as `postcode`, `objectDetails_Adres_NL_Land` as `land` FROM `tbl_OG_wonen` WHERE `google_status`=0";
			$result = $db->prepare($query);

			if (count($result) > 0) {

				foreach ($result as $key => $data) {

					$postcode = str_replace(" ", "+", $data['postcode']);
					$land = $data['land'];

					$googleData = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $postcode . ",+" . $land . "&key=AIzaSyBCvhSY0IKeAhSzUph9Ud3LpBsp9wuUNuY");
					$googleArray = json_decode($googleData, TRUE);

					$readArray = $googleArray['results'][0];

					if (isset($readArray['geometry']['location']['lat']) && isset($readArray['geometry']['location']['lng'])) {

						$latitude = $readArray['geometry']['location']['lat'];
						$longitude = $readArray['geometry']['location']['lng'];

						// Update record
						$query = "UPDATE `tbl_OG_wonen` SET `google_lat`=" . $latitude . ", `google_lng`=" . $longitude . ", `google_status`=1 WHERE `id`=" . $data['id'];
						$db->prepare($query);
					}
				}
			}

			break;
		case 'alv':

			// Fetch all where google_status=0
			$query = "SELECT `id`, `object_ObjectDetails_Adres_Postcode` as `postcode`, `object_ObjectDetails_Adres_Straatnaam`, `object_ObjectDetails_Adres_Woonplaats` FROM `tbl_OG_alv` WHERE `google_status`=0 OR `google_status` IS NULL";
			$result = $db->prepare($query);

			if (count($result) > 0) {

				foreach ($result as $key => $data) {

					$postcode = str_replace(" ", "+", $data['postcode']);
					$land = 'NL';

					$postcode = str_replace(' ', '+', $data['object_ObjectDetails_Adres_Straatnaam']) . ',+' . str_replace(' ', '+', $data['object_ObjectDetails_Adres_Woonplaats']) . ',+' . $postcode;

					$googleData = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . $postcode . ",+" . $land . "&sensor=false");
					$googleArray = json_decode($googleData, TRUE);

					$readArray = $googleArray['results'][0];

					if (isset($readArray['geometry']['location']['lat']) && isset($readArray['geometry']['location']['lng'])) {

						$latitude = $readArray['geometry']['location']['lat'];
						$longitude = $readArray['geometry']['location']['lng'];

						// Update record
						$query = "UPDATE `tbl_OG_alv` SET `google_lat`=" . $latitude . ", `google_lng`=" . $longitude . ", `google_status`=1 WHERE `id`=" . $data['id'];
						$db->prepare($query);
					}
				}
			}

			break;
		case 'bog':

			// Fetch all where google_status=0
			$query = "SELECT `id`, `objectDetails_Adres_Postcode` as `postcode` FROM `tbl_OG_bog` WHERE `google_status`=0";
			$result = $db->prepare($query);

			if (count($result) > 0) {

				foreach ($result as $key => $data) {

					$postcode = str_replace(" ", "+", $data['postcode']);
					$land = 'Nederland';

					$googleData = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . $postcode . ",+" . $land . "&sensor=false");
					$googleArray = json_decode($googleData, TRUE);

					$readArray = $googleArray['results'][0];

					if (isset($readArray['geometry']['location']['lat']) && isset($readArray['geometry']['location']['lng'])) {

						$latitude = $readArray['geometry']['location']['lat'];
						$longitude = $readArray['geometry']['location']['lng'];

						// Update record
						$query = "UPDATE `tbl_OG_bog` SET `google_lat`=" . $latitude . ", `google_lng`=" . $longitude . ", `google_status`=1 WHERE `id`=" . $data['id'];
						$db->prepare($query);
					}
				}
			}

			break;
		default:
			break;
	}

	usleep(500000);
}

// Set vestiging to COMPLETED
// $db->prepare("UPDATE `tbl_OG_cronjob` SET `status`=2,`lastCheck`='" . time() . "' WHERE `vestigingNr`=0 AND `feedType`='COORDS'");

?>