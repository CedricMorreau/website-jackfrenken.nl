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

// Sets status to ingetrokken after specific date
// Date definitions (in days)
// Also contains valid og types, quote to disable
$days = array();
$days['bog'] = 60;
$days['wonen'] = 60;
// $days['landelijk'] = 30;
// $days['alv'] = 60;

foreach ($days as $key => $val) {

	// Calculate date difference. Convert days to seconds
	$seconds = $val * 24 * 60 * 60;
	// Timestamp of last date
	$lastDate = time() - $seconds;
	// Convert to YYYY-MM-DD HH-MM-SS
	$readableDate = date('Y-m-d H:i:s', $lastDate);

	// Run queries
	switch($key) {

		case 'bog':
			$db->prepare("UPDATE `tbl_OG_bog` 
					SET `objectDetails_Status_StatusType`='Ingetrokken'
					WHERE
					`objectDetails_Status_StatusType` IN ('Verhuurd','Verkocht')
					AND `objectDetails_DatumWijziging`<'" . $readableDate . "'");
			break;
		case 'wonen':
			$db->prepare("UPDATE `tbl_OG_wonen` 
					SET `objectDetails_StatusBeschikbaarheid_Status`='Ingetrokken'
					WHERE
					`objectDetails_StatusBeschikbaarheid_Status` IN ('gearchiveerd', 'verkocht', 'Verkocht', 'verhuurd', 'Verhuurd')
					AND `datum_gewijzigd`<'" . $readableDate . "'");

			// $db->prepare("UPDATE `tbl_OG_wonen` 
			// 		SET `objectDetails_StatusBeschikbaarheid_Status`='Ingetrokken'
			// 		WHERE
			// 		`object_Web_Prioriteit`!=80
			// 		AND NOT `objectDetails_StatusBeschikbaarheid_Status` IN ('gearchiveerd', 'verkocht', 'Verkocht', 'verhuurd', 'Verhuurd')
			// 		AND `inFeed`=0");
			break;
		case 'landelijk':
			$db->prepare("UPDATE `tbl_OG_wonen` 
					SET `objectDetails_StatusBeschikbaarheid_Status`='Ingetrokken'
					WHERE
					`object_Web_Prioriteit`=80
					AND `objectDetails_StatusBeschikbaarheid_Status` IN ('Verhuurd','Verkocht')
					AND `datum_gewijzigd`<'" . $readableDate . "'");
			break;
		case 'alv':
			$db->prepare("UPDATE `tbl_OG_alv` 
					SET `object_ObjectDetails_Status_StatusType`='Ingetrokken'
					WHERE
					`object_ObjectDetails_Status_StatusType` IN ('Verhuurd','Verkocht','verhuurd','verkocht')
					AND `datum_gewijzigd`<'" . $readableDate . "'");
			break;
		case 'nieuwbouw':
			$db->prepare("UPDATE `tbl_OG_nieuwbouw_bouwNummers` 
					SET `bouwNummer_BouwNummerDetails_Status_ObjectStatus`='Ingetrokken'
					WHERE
					`bouwNummer_BouwNummerDetails_Status_ObjectStatus` IN ('Verhuurd','Verkocht')
					AND `datum_gewijzigd`<'" . $readableDate . "'");
			break;
		default:
			break;
	}
}

?>