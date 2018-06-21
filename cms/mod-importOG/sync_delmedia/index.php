<?php
// FIXIT SB: Dit script loopt nog niet. Hoe, wat, waar met Schep?

set_time_limit(0);
error_reporting(E_ALL);

// Global configuration
include("inc/config.php");

// Database connection
include("inc/database.class.php");

// Start up the DB connection
$db = new Database();
$db->connection($dbd['username'], $dbd['passwd'], $dbd['server']);
$db->selectDatabase($dbd['database']);

// Set vestiging to IN_PROGRESS
//$db->prepare("UPDATE `tbl_OG_cronjob` SET `status`=1 WHERE `vestigingNr`=0 AND `feedType`='MEDIA'");

// Sets status to ingetrokken after specific date
// Date definitions (in days)
// Also contains valid og types, quote to disable
$days = array();
$days[] = 'bog';
$days[] = 'wonen';
// $days[] = 'alv';
//$days[] = 'nieuwbouw';

foreach ($days as $key => $val) {

	switch ($val) {

		case 'bog':

				// Fetch all ingetrokken
				$query = "SELECT `id`, `object_ObjectTiaraID` AS `tiaraID` FROM `tbl_OG_bog` WHERE `objectDetails_Status_StatusType` IN ('Ingetrokken', 'ingetrokken')";
				$result = $db->prepare($query);

				if (count($result) > 0) {

					foreach ($result as $sKey => $sVal) {

						// Set media to -2
						$query = "UPDATE `tbl_OG_media` SET media_status=-2 WHERE `object_ObjectTiaraID`=" . $sVal['tiaraID'];
						$db->prepare($query);
					}

				}
			break;
		case 'wonen':

				// Fetch all ingetrokken
				$query = "SELECT id, object_ObjectTiaraID AS tiaraID FROM `tbl_OG_wonen` WHERE objectDetails_StatusBeschikbaarheid_Status IN ('Ingetrokken', 'ingetrokken')";
				$result = $db->prepare($query);

				if (count($result) > 0) {

					foreach ($result as $sKey => $sVal) {

						// Set media to -2
						$query = "UPDATE `tbl_OG_media` SET media_status=-2 WHERE `object_ObjectTiaraID`=" . $sVal['tiaraID'];
						$db->prepare($query);
					}

				}
			break;
		case 'alv':

				// Fetch all ingetrokken
				$query = "SELECT id, object_ObjectTiaraID AS tiaraID FROM `tbl_OG_alv` WHERE object_ObjectDetails_Status_StatusType IN ('Ingetrokken', 'ingetrokken')";
				$result = $db->prepare($query);

				if (count($result) > 0) {

					foreach ($result as $sKey => $sVal) {

						// Set media to -2
						$query = "UPDATE `tbl_OG_media` SET media_status=-2 WHERE `object_ObjectTiaraID`=" . $sVal['tiaraID'];
						$db->prepare($query);
					}

				}
			break;
		default:
			break;
	}
}

// Set vestiging to COMPLETED
//$db->prepare("UPDATE `tbl_OG_cronjob` SET status=2,lastCheck='" . time() . "' WHERE vestigingNr=0 AND feedType='MEDIA'");

?>