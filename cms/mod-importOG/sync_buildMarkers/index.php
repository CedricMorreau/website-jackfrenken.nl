<?php

// Also builds a cache of filter functions

set_time_limit(0);

// Global configuration
include("inc/config.php");

// Database connection
include("inc/database.class.php");

// Start up the DB connection
$db = new Database();
$db->connection($dbd['username'], $dbd['passwd'], $dbd['server']);
$db->selectDatabase($dbd['database']);

// Cases
$cases = array();
$cases[] = 'wonen_koop';
$cases[] = 'wonen_huur';
$cases[] = 'bog';
$cases[] = 'nieuwbouw';

foreach ($cases as $key => $val) {

	switch ($val) {

		case 'wonen_koop':

			$markerData = array();

			// Fetch all where google_status=0
			$query = "SELECT * FROM `tbl_OG_wonen` WHERE (NOT `tbl_OG_wonen`.`objectDetails_StatusBeschikbaarheid_Status` IN ('Ingetrokken', 'ingetrokken', 'verkocht', 'gearchiveerd')) AND `object_Web_Prioriteit`!=80 AND `objectDetails_Bouwvorm`!='nieuwbouw' AND (`objectDetails_Koop_Koopprijs`>0)";
			$result = $db->prepare($query);

			$hoofdfuncties['eengezinswoning'] = 'eengezinswoning';
			$hoofdfuncties['herenhuis'] = 'herenhuis';
			$hoofdfuncties['villa'] = 'villa';
			$hoofdfuncties['landhuis'] = 'landhuis';
			$hoofdfuncties['bungalow'] = 'bungalow';
			$hoofdfuncties['woonboerderij'] = 'woonboerderij';
			$hoofdfuncties['grachtenpand'] = 'grachtenpand';
			$hoofdfuncties['woonboot'] = 'woonboot';
			$hoofdfuncties['stacaravan'] = 'stacaravan';
			$hoofdfuncties['woonwagen'] = 'woonwagen';
			$hoofdfuncties['landgoed'] = 'landgoed';

			$objectDataArray = array();
			$objectDataArray['functions'] = array();
			$places = array();

			if (count($result) > 0) {

				$counter = 0;
				foreach ($result as $key => $data) {

					$fetchImage = $db->prepare("SELECT `bestandsnaam_tn` FROM `tbl_OG_media` WHERE `id_OG_wonen`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($data['id']));

					if (count($fetchImage) > 0) {

						$image = '/' . 'og_media/wonen_' . $data['object_NVMVestigingNR'] . '_' . $data['object_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_tn'];
					}
					else {

						$image = '/' . 'img/aanbod_geen-afbeelding_tn01.svg';
					}

					$_imageArray[$data['id']] = $image;

					// Fetch permalink
					$permaLink = $db->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=109 AND `cms_per_moduleId`=?", "i", array($data['id']));

					if (count($permaLink) > 0)
						$href = '/' . $permaLink[0]['cms_per_link'];
					else
						$href = '/' . 'error/404';

					$markerData[] = "arr_markerData[" . $counter . "] = func_markerData(" . $data['google_lat'] . "," . $data['google_lng']. ",'" . addslashes(obj_generateAddress($data['objectDetails_Adres_NL_Straatnaam'], $data['objectDetails_Adres_NL_Huisnummer'], $data['objectDetails_Adres_NL_HuisnummerToevoeging'])). "','" . addslashes($data['objectDetails_Adres_NL_Woonplaats']). "','Nederland','" . $href. ".html'," . $data['id'] . ",'" . $image. "', 'WONEN');";

					// Also hop in some filter data
					if (isset($hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']])) {

						if (!isset($objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]]))
							$objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]] = 1;
						else
							$objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]]++;
					}

					if (!in_array(strtolower($data['objectDetails_Adres_NL_Woonplaats']), $places))
						$places[] = strtolower($data['objectDetails_Adres_NL_Woonplaats']);

					$counter++;
				}
			}

			if (count($markerData) > 0) {

				$fileData = implode('', $markerData);
			}
			else {

				$fileData = '';
			}

			asort($places);

			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/markers_wonen_koop.txt', $fileData);

			// Also save the filter data
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/filters_wonen_koop.txt', serialize($objectDataArray));

			// Also save the city filter
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/places_wonen_koop.txt', serialize($places));

			// And save the total count
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/count_wonen_koop.txt', serialize(count($result)));

			break;
			
		case 'wonen_huur':
			
			$markerData = array();
			
			// Fetch all where google_status=0
			$query = "SELECT * FROM `tbl_OG_wonen` WHERE (NOT `tbl_OG_wonen`.`objectDetails_StatusBeschikbaarheid_Status` IN ('Ingetrokken', 'ingetrokken', 'verkocht', 'gearchiveerd')) AND `object_Web_Prioriteit`!=80 AND `objectDetails_Bouwvorm`!='nieuwbouw' AND (`objectDetails_Huur_Huurprijs`>0)";
			$result = $db->prepare($query);
			
			$hoofdfuncties['eengezinswoning'] = 'eengezinswoning';
			$hoofdfuncties['herenhuis'] = 'herenhuis';
			$hoofdfuncties['villa'] = 'villa';
			$hoofdfuncties['landhuis'] = 'landhuis';
			$hoofdfuncties['bungalow'] = 'bungalow';
			$hoofdfuncties['woonboerderij'] = 'woonboerderij';
			$hoofdfuncties['grachtenpand'] = 'grachtenpand';
			$hoofdfuncties['woonboot'] = 'woonboot';
			$hoofdfuncties['stacaravan'] = 'stacaravan';
			$hoofdfuncties['woonwagen'] = 'woonwagen';
			$hoofdfuncties['landgoed'] = 'landgoed';
			
			$objectDataArray = array();
			$objectDataArray['functions'] = array();
			$places = array();
			
			if (count($result) > 0) {
				
				$counter = 0;
				foreach ($result as $key => $data) {
					
					$fetchImage = $db->prepare("SELECT `bestandsnaam_tn` FROM `tbl_OG_media` WHERE `id_OG_wonen`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($data['id']));
					
					if (count($fetchImage) > 0) {
						
						$image = '/' . 'og_media/wonen_' . $data['object_NVMVestigingNR'] . '_' . $data['object_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_tn'];
					}
					else {
						
						$image = '/' . 'img/aanbod_geen-afbeelding_tn01.svg';
					}
					
					$_imageArray[$data['id']] = $image;
					
					// Fetch permalink
					$permaLink = $db->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=109 AND `cms_per_moduleId`=?", "i", array($data['id']));
					
					if (count($permaLink) > 0)
						$href = '/' . $permaLink[0]['cms_per_link'];
						else
							$href = '/' . 'error/404';
							
							$markerData[] = "arr_markerData[" . $counter . "] = func_markerData(" . $data['google_lat'] . "," . $data['google_lng']. ",'" . addslashes(obj_generateAddress($data['objectDetails_Adres_NL_Straatnaam'], $data['objectDetails_Adres_NL_Huisnummer'], $data['objectDetails_Adres_NL_HuisnummerToevoeging'])). "','" . addslashes($data['objectDetails_Adres_NL_Woonplaats']). "','Nederland','" . $href. ".html'," . $data['id'] . ",'" . $image. "', 'WONEN');";
							
							// Also hop in some filter data
							if (isset($hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']])) {
								
								if (!isset($objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]]))
									$objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]] = 1;
									else
										$objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]]++;
							}
							
							if (!in_array(strtolower($data['objectDetails_Adres_NL_Woonplaats']), $places))
								$places[] = strtolower($data['objectDetails_Adres_NL_Woonplaats']);
								
								$counter++;
				}
			}
			
			if (count($markerData) > 0) {
				
				$fileData = implode('', $markerData);
			}
			else {
				
				$fileData = '';
			}
			
			asort($places);
			
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/markers_wonen_huur.txt', $fileData);
			
			// Also save the filter data
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/filters_wonen_huur.txt', serialize($objectDataArray));
			
			// Also save the city filter
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/places_wonen_huur.txt', serialize($places));
			
			// And save the total count
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/count_wonen_huur.txt', serialize(count($result)));
			
			break;

		case 'buitenstate':

			$markerData = array();

			// Fetch all where google_status=0
			$query = "SELECT * FROM `tbl_OG_wonen` WHERE (NOT `tbl_OG_wonen`.`objectDetails_StatusBeschikbaarheid_Status` IN ('Ingetrokken', 'ingetrokken', 'verkocht', 'gearchiveerd')) AND `objectDetails_Bouwvorm`!='nieuwbouw'";
			$result = $db->prepare($query);

			$hoofdfuncties['eengezinswoning'] = 'eengezinswoning';
			$hoofdfuncties['herenhuis'] = 'herenhuis';
			$hoofdfuncties['villa'] = 'villa';
			$hoofdfuncties['landhuis'] = 'landhuis';
			$hoofdfuncties['bungalow'] = 'bungalow';
			$hoofdfuncties['woonboerderij'] = 'woonboerderij';
			$hoofdfuncties['grachtenpand'] = 'grachtenpand';
			$hoofdfuncties['woonboot'] = 'woonboot';
			$hoofdfuncties['stacaravan'] = 'stacaravan';
			$hoofdfuncties['woonwagen'] = 'woonwagen';
			$hoofdfuncties['landgoed'] = 'landgoed';

			$objectDataArray = array();
			$objectDataArray['functions'] = array();
			$places = array();

			if (count($result) > 0) {

				$counter = 0;
				foreach ($result as $key => $data) {

					$fetchImage = $db->prepare("SELECT `bestandsnaam_tn` FROM `tbl_OG_media` WHERE `id_OG_wonen`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($data['id']));

					if (count($fetchImage) > 0) {

						$image = '/' . 'og_media/wonen_' . $data['object_NVMVestigingNR'] . '_' . $data['object_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_tn'];
					}
					else {

						$image = '/' . 'img/aanbod_geen-afbeelding_tn01.svg';
					}

					$_imageArray[$data['id']] = $image;

					// Fetch permalink
					$permaLink = $db->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=115 AND `cms_per_moduleId`=?", "i", array($data['id']));

					if (count($permaLink) > 0)
						$href = '/' . $permaLink[0]['cms_per_link'];
					else
						$href = '/' . 'error/404';

					$markerData[] = "arr_markerData[" . $counter . "] = func_markerData(" . $data['google_lat'] . "," . $data['google_lng']. ",'" . addslashes(obj_generateAddress($data['objectDetails_Adres_NL_Straatnaam'], $data['objectDetails_Adres_NL_Huisnummer'], $data['objectDetails_Adres_NL_HuisnummerToevoeging'])). "','" . addslashes($data['objectDetails_Adres_NL_Woonplaats']). "','Nederland','" . $href. ".html'," . $data['id'] . ",'" . $image. "', 'WONEN');";

					// Also hop in some filter data
					if (isset($hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']])) {

						if (!isset($objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]]))
							$objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]] = 1;
						else
							$objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]]++;
					}

					if (!in_array(strtolower($data['objectDetails_Adres_NL_Woonplaats']), $places))
						$places[] = strtolower($data['objectDetails_Adres_NL_Woonplaats']);

					$counter++;
				}
			}

			if (count($markerData) > 0) {

				$fileData = implode('', $markerData);
			}
			else {

				$fileData = '';
			}

			asort($places);

			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/markers_buitenstate.txt', $fileData);

			// Also save the filter data
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/filters_buitenstate.txt', serialize($objectDataArray));

			// Also save the city filter
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/places_buitenstate.txt', serialize($places));

			// And save the total count
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/count_buitenstate.txt', serialize(count($result)));

			break;

		case 'alv':

			$markerData = array();
			
			// Fetch all where google_status=0
			$query = "SELECT * FROM `tbl_OG_alv` WHERE (NOT `tbl_OG_alv`.`object_ObjectDetails_Status_StatusType` IN ('Ingetrokken', 'ingetrokken', 'gearchiveerd'))";
			$result = $db->prepare($query);
			
			$hoofdfuncties['Akkerbouwbedrijf'] = 'akkerbouw';
			$hoofdfuncties['Tuinbouwbedrijf'] = 'tuinbouw';
			$hoofdfuncties['Pluimveebedrijf'] = 'veehouderij';
			$hoofdfuncties['Varkenshouderij'] = 'veehouderij';
			$hoofdfuncties['Vleeskalverenbedrijf'] = 'veehouderij';
			$hoofdfuncties['Melkveehouderij'] = 'melkveehouderij';
			$hoofdfuncties['Manege/pensionstalling'] = 'paardenhouderij';
			$hoofdfuncties['Paardenhouderij'] = 'paardenhouderij';
			$hoofdfuncties['ManegePensionstalling'] = 'paardenhouderij';
			$hoofdfuncties['Overig'] = 'overig';
			$hoofdfuncties['LosseGrond'] = 'lossegrond';
			$hoofdfuncties['Losse grond'] = 'lossegrond';
			$hoofdfuncties['Vollegrondstuinbouwbedrijf'] = 'tuinbouw';
			
			$objectDataArray = array();
			$objectDataArray['functions'] = array();
			
			if (count($result) > 0) {
			
				$counter = 0;
				foreach ($result as $key => $data) {
			
					$fetchImage = $db->prepare("SELECT `bestandsnaam_tn` FROM `tbl_OG_media` WHERE `id_OG_alv`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($data['id']));
			
					if (count($fetchImage) > 0) {
			
						$image = '/' . 'og_media/alv_' . $data['object_NVMVestigingNR'] . '_' . $data['object_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_tn'];
					}
					else {
			
						$image = '/' . 'img/aanbod_geen-afbeelding_tn01.gif';
					}
			
					$_imageArray[$data['id']] = $image;
			
					// Fetch permalink
					$permaLink = $db->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=8 AND `cms_per_moduleId`=?", "i", array($data['id']));
			
					if (count($permaLink) > 0)
						$href = '/' . $permaLink[0]['cms_per_link'];
						else
							$href = '/' . 'error/404';
			
							$markerData[] = "arr_markerData[" . $counter . "] = func_markerData(" . $data['google_lat'] . "," . $data['google_lng']. ",'" . addslashes(obj_generateAddress($data['object_ObjectDetails_Adres_Straatnaam'], $data['object_ObjectDetails_Adres_Huisnummer_Hoofdnummer'], $data['object_ObjectDetails_Adres_HuisnummerToevoeging'])). "','" . addslashes($data['object_ObjectDetails_Adres_Woonplaats']). "','Nederland','" . $href. ".html'," . $data['id'] . ",'" . $image. "');";
			
							// Also hop in some filter data
							if (isset($hoofdfuncties[$data['AenLV_Hoofdfunctie']])) {
			
								if (!isset($objectDataArray['functions'][$hoofdfuncties[$data['AenLV_Hoofdfunctie']]]))
									$objectDataArray['functions'][$hoofdfuncties[$data['AenLV_Hoofdfunctie']]] = 1;
								else
									$objectDataArray['functions'][$hoofdfuncties[$data['AenLV_Hoofdfunctie']]]++;
							}
			
							$counter++;
				}
			}
			
			if (count($markerData) > 0) {
			
				$fileData = implode('', $markerData);
			}
			else {
			
				$fileData = '';
			}
			
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/markers_alv.txt', $fileData);
			
			// Also save the filter data
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/filters_alv.txt', serialize($objectDataArray));

			break;

		case 'nieuwbouw':

			$markerData = array();

			// Fetch all where google_status=0
			$query = "SELECT * FROM `tbl_OG_wonen` WHERE (NOT `tbl_OG_wonen`.`objectDetails_StatusBeschikbaarheid_Status` IN ('Ingetrokken', 'ingetrokken', 'verkocht', 'gearchiveerd')) AND `objectDetails_Bouwvorm`='nieuwbouw'";
			$result = $db->prepare($query);

			$hoofdfuncties['eengezinswoning'] = 'eengezinswoning';
			$hoofdfuncties['herenhuis'] = 'herenhuis';
			$hoofdfuncties['villa'] = 'villa';
			$hoofdfuncties['landhuis'] = 'landhuis';
			$hoofdfuncties['bungalow'] = 'bungalow';
			$hoofdfuncties['woonboerderij'] = 'woonboerderij';
			$hoofdfuncties['grachtenpand'] = 'grachtenpand';
			$hoofdfuncties['woonboot'] = 'woonboot';
			$hoofdfuncties['stacaravan'] = 'stacaravan';
			$hoofdfuncties['woonwagen'] = 'woonwagen';
			$hoofdfuncties['landgoed'] = 'landgoed';

			$objectDataArray = array();
			$objectDataArray['functions'] = array();
			$places = array();

			if (count($result) > 0) {

				$counter = 0;
				foreach ($result as $key => $data) {

					$fetchImage = $db->prepare("SELECT `bestandsnaam_tn` FROM `tbl_OG_media` WHERE `id_OG_wonen`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($data['id']));

					if (count($fetchImage) > 0) {

						$image = '/' . 'og_media/wonen_' . $data['object_NVMVestigingNR'] . '_' . $data['object_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_tn'];
					}
					else {

						$image = '/' . 'img/aanbod_geen-afbeelding_tn01.svg';
					}

					$_imageArray[$data['id']] = $image;

					// Fetch permalink
					$permaLink = $db->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=107 AND `cms_per_moduleId`=?", "i", array($data['id']));

					if (count($permaLink) > 0)
						$href = '/' . $permaLink[0]['cms_per_link'];
					else
						$href = '/' . 'error/404';

					$markerData[] = "arr_markerData[" . $counter . "] = func_markerData(" . $data['google_lat'] . "," . $data['google_lng']. ",'" . addslashes(obj_generateAddress($data['objectDetails_Adres_NL_Straatnaam'], $data['objectDetails_Adres_NL_Huisnummer'], $data['objectDetails_Adres_NL_HuisnummerToevoeging'])). "','" . addslashes($data['objectDetails_Adres_NL_Woonplaats']). "','Nederland','" . $href. ".html'," . $data['id'] . ",'" . $image. "', 'WONEN');";

					// Also hop in some filter data
					if (isset($hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']])) {

						if (!isset($objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]]))
							$objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]] = 1;
						else
							$objectDataArray['functions'][$hoofdfuncties[$data['wonen_Woonhuis_SoortWoning']]]++;
					}

					if (!in_array(strtolower($data['objectDetails_Adres_NL_Woonplaats']), $places))
						$places[] = strtolower($data['objectDetails_Adres_NL_Woonplaats']);

					$counter++;
				}
			}

			if (count($markerData) > 0) {

				$fileData = implode('', $markerData);
			}
			else {

				$fileData = '';
			}

			asort($places);

			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/markers_nieuwbouw.txt', $fileData);

			// Also save the filter data
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/filters_nieuwbouw.txt', serialize($objectDataArray));

			// Also save the city filter
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/places_nieuwbouw.txt', serialize($places));

			// And save the total count
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/count_nieuwbouw.txt', serialize(count($result)));

			break;

		// case 'nieuwbouw':

		// 	$markerData = array();

		// 	// Fetch all where google_status=0
		// 	$query = "SELECT * FROM `tbl_OG_nieuwbouw_projecten` WHERE (NOT `tbl_OG_nieuwbouw_projecten`.`project_ProjectDetails_Status_ObjectStatus` IN ('Ingetrokken', 'ingetrokken', 'gearchiveerd'))";
		// 	$result = $db->prepare($query);

		// 	$places = array();

		// 	if (count($result) > 0) {

		// 		$counter = 0;
		// 		foreach ($result as $key => $data) {

		// 			$fetchImage = $db->prepare("SELECT `bestandsnaam_tn` FROM `tbl_OG_media` WHERE `id_OG_nieuwbouw_projecten`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($data['id']));

		// 			if (count($fetchImage) > 0) {

		// 				$image = '/' . 'og_media/nieuwbouw__' . $data['project_NVMVestigingNR'] . '_' . $data['project_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_tn'];
		// 			}
		// 			else {

		// 				$image = '/' . 'img/aanbod_geen-afbeelding_tn01.svg';
		// 			}

		// 			$_imageArray[$data['id']] = $image;

		// 			// Fetch permalink
		// 			$permaLink = $db->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=77 AND `cms_per_moduleId`=?", "i", array($data['id']));

		// 			if (count($permaLink) > 0)
		// 				$href = '/' . $permaLink[0]['cms_per_link'];
		// 			else
		// 				$href = '/' . 'error/404';

		// 			$markerData[] = "arr_markerData[" . $counter . "] = func_markerData(" . $data['google_lat'] . "," . $data['google_lng']. ",'" . '' . "','" . addslashes($data['project_ProjectDetails_Adres_Woonplaats']). "','Nederland','" . $href. ".html'," . $data['id'] . ",'" . $image. "');";

		// 			if (!in_array(strtolower($data['project_ProjectDetails_Adres_Woonplaats']), $places))
		// 				$places[] = strtolower($data['project_ProjectDetails_Adres_Woonplaats']);

		// 			$counter++;
		// 		}
		// 	}

		// 	if (count($markerData) > 0) {

		// 		$fileData = implode('', $markerData);
		// 	}
		// 	else {

		// 		$fileData = '';
		// 	}

		// 	asort($places);

		// 	file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/markers_nieuwbouw.txt', $fileData);

		// 	// Also save the city filter
		// 	file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/places_nieuwbouw.txt', serialize($places));

		// 	break;
		case 'bog':

			$markerData = array();
			$hoofdfuncties = array();

			// Fetch all where google_status=0
			$query = "SELECT * FROM `tbl_OG_bog` WHERE (NOT `tbl_OG_bog`.`objectDetails_Status_StatusType` IN ('Ingetrokken', 'ingetrokken', 'verkocht', 'gearchiveerd'))";
			$result = $db->prepare($query);

			$hoofdfuncties['Bedrijfsruimte'] = 'Bedrijfsruimte';
			$hoofdfuncties['Bouwgrond'] = 'Bouwgrond';
			$hoofdfuncties['Horeca'] = 'Horeca';
			$hoofdfuncties['Kantoorruimte'] = 'Kantoorruimte';
			$hoofdfuncties['Overige'] = 'Overige';
			$hoofdfuncties['Winkelruimte'] = 'Winkelruimte';

			$objectDataArray = array();
			$objectDataArray['functions'] = array();

			if (count($result) > 0) {

				$counter = 0;
				foreach ($result as $key => $data) {

					$fetchImage = $db->prepare("SELECT `bestandsnaam_tn` FROM `tbl_OG_media` WHERE `id_OG_bog`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($data['id']));

					if (count($fetchImage) > 0) {

						$image = '/' . 'og_media/bog_' . $data['object_NVMVestigingNR'] . '_' . $data['object_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_tn'];
					}
					else {

						$image = '/' . 'img/aanbod_geen-afbeelding_tn01.svg';
					}

					$_imageArray[$data['id']] = $image;

					// Fetch permalink
					$permaLink = $db->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=113 AND `cms_per_moduleId`=?", "i", array($data['id']));

					if (count($permaLink) > 0)
						$href = '/' . $permaLink[0]['cms_per_link'];
					else
						$href = '/' . 'error/404';

					$markerData[] = "arr_markerData[" . $counter . "] = func_markerData(" . $data['google_lat'] . "," . $data['google_lng']. ",'" . addslashes(obj_generateAddress($data['objectDetails_Adres_Straatnaam'], $data['objectDetails_Adres_Huisnummer'], $data['objectDetails_Adres_HuisnummerToevoeging'])). "','" . addslashes($data['objectDetails_Adres_Woonplaats']). "','Nederland','" . $href. ".html'," . $data['id'] . ",'" . $image. "', 'BOG');";

					$explodedTemp = explode(',', $data['objectDetails_Bestemming_Hoofdbestemming']);

					foreach ($explodedTemp as $key => $val) {

						if (isset($hoofdfuncties[$val])) {

							if (!isset($objectDataArray['functions'][$hoofdfuncties[$val]]))
								$objectDataArray['functions'][$hoofdfuncties[$val]] = 1;
							else
								$objectDataArray['functions'][$hoofdfuncties[$val]]++;
						}
					}

					$counter++;
				}
			}

			if (count($markerData) > 0) {

				$fileData = implode('', $markerData);
			}
			else {

				$fileData = '';
			}

			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/markers_bog.txt', $fileData);

			// Also save the filter data
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/filters_bog.txt', serialize($objectDataArray));

			// And save the total count
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/cache/og/count_bog.txt', serialize(count($result)));

			break;
		default:
			break;
	}
}

function obj_generateAddress($street, $number, $add) {

	$tempAddress = '';

	if (!empty($street))
		$tempAddress .= $street;

	if (!empty($number))
		$tempAddress .= ' ' . $number;

	if (!empty($add))
		$tempAddress .= ' ' . $add;

	return $tempAddress;
}

?>