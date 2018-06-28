<?php

include($_SERVER['DOCUMENT_ROOT'] . "/cms/inc/config.php");

if (isset($_GET['query_type'])) {

	$jsonArray = array();

	switch ($_GET['query_type']) {

		case 'plaatsnaam':

			switch ($_GET['ogType']) {

				case 'alv':

					$sql = array();

					$tempSql = $cms['database']->prepare("SELECT DISTINCT `object_ObjectDetails_Adres_Woonplaats` AS `rsWoonplaats`, Count(`object_ObjectDetails_Adres_Woonplaats`) AS `rsCountWoonplaats` FROM `tbl_OG_alv` WHERE NOT `object_ObjectDetails_Status_StatusType` IN ('ingetrokken', 'verkocht') AND `object_ObjectDetails_Adres_Woonplaats` LIKE '%" . $_GET['q'] . "%' GROUP BY `object_ObjectDetails_Adres_Woonplaats`");

					if (count($tempSql) > 0) {

						foreach ($tempSql as $key => $val) {

							$sql[] = $val;
						}
					}

					$tempSql = $cms['database']->prepare("SELECT DISTINCT `object_ObjectDetails_Adres_Postcode` AS `rsWoonplaats`, Count(`object_ObjectDetails_Adres_Postcode`) AS `rsCountWoonplaats` FROM `tbl_OG_alv` WHERE NOT `object_ObjectDetails_Status_StatusType` IN ('ingetrokken', 'verkocht') AND `object_ObjectDetails_Adres_Postcode` LIKE '%" . $_GET['q'] . "%' GROUP BY `object_ObjectDetails_Adres_Postcode`");

					if (count($tempSql) > 0) {

						foreach ($tempSql as $key => $val) {

							$sql[] = $val;
						}
					}

					break;
					
				case 'wonen':
					
					$sql = array();
					
					$tempSql = $cms['database']->prepare("SELECT DISTINCT `objectDetails_Adres_NL_Woonplaats` AS `rsWoonplaats`, Count(`objectDetails_Adres_NL_Woonplaats`) AS `rsCountWoonplaats` FROM `tbl_OG_wonen` WHERE NOT `objectDetails_StatusBeschikbaarheid_Status` IN ('ingetrokken', 'verkocht', 'Ingetrokken', 'Verkocht') AND `objectDetails_Adres_NL_Woonplaats` LIKE '%" . $_GET['q'] . "%' GROUP BY `objectDetails_Adres_NL_Woonplaats`");
					
					if (count($tempSql) > 0) {
						
						foreach ($tempSql as $key => $val) {
							
							$sql[] = $val;
						}
					}
					
					break;

				case 'wonen_koop':

					$sql = array();

					$tempSql = $cms['database']->prepare("SELECT DISTINCT `objectDetails_Adres_NL_Woonplaats` AS `rsWoonplaats`, Count(`objectDetails_Adres_NL_Woonplaats`) AS `rsCountWoonplaats` FROM `tbl_OG_wonen` WHERE NOT `objectDetails_StatusBeschikbaarheid_Status` IN ('ingetrokken', 'verkocht', 'Ingetrokken', 'Verkocht') AND `objectDetails_Adres_NL_Woonplaats` LIKE '%" . $_GET['q'] . "%' AND `objectDetails_Koop_Koopprijs`>0 GROUP BY `objectDetails_Adres_NL_Woonplaats`");

					if (count($tempSql) > 0) {

						foreach ($tempSql as $key => $val) {

							$sql[] = $val;
						}
					}

					break;
					
				case 'wonen_huur':
					
					$sql = array();
					
					$tempSql = $cms['database']->prepare("SELECT DISTINCT `objectDetails_Adres_NL_Woonplaats` AS `rsWoonplaats`, Count(`objectDetails_Adres_NL_Woonplaats`) AS `rsCountWoonplaats` FROM `tbl_OG_wonen` WHERE NOT `objectDetails_StatusBeschikbaarheid_Status` IN ('ingetrokken', 'verkocht', 'Ingetrokken', 'Verkocht') AND `objectDetails_Adres_NL_Woonplaats` LIKE '%" . $_GET['q'] . "%' AND `objectDetails_Huur_Huurprijs`>0 GROUP BY `objectDetails_Adres_NL_Woonplaats`");
					
					if (count($tempSql) > 0) {
						
						foreach ($tempSql as $key => $val) {
							
							$sql[] = $val;
						}
					}
					
					break;

				case 'bog':

					$sql = array();

					$tempSql = $cms['database']->prepare("SELECT DISTINCT `objectDetails_Adres_Woonplaats` AS `rsWoonplaats`, Count(`objectDetails_Adres_Woonplaats`) AS `rsCountWoonplaats` FROM `tbl_OG_bog` WHERE NOT `objectDetails_Status_StatusType` IN ('ingetrokken', 'verkocht', 'Ingetrokken', 'Verkocht') AND `objectDetails_Adres_Woonplaats` LIKE '%" . $_GET['q'] . "%' GROUP BY `objectDetails_Adres_Woonplaats`");

					if (count($tempSql) > 0) {

						foreach ($tempSql as $key => $val) {

							$sql[] = $val;
						}
					}

					break;
			}

			$objects = $sql;

			if (count($objects) > 0) {

				$counter = 0;
				foreach ($objects as $key => $val) {

					$jsonArray[$counter]['value'] = ucwords(strtolower($val['rsWoonplaats']));
					$jsonArray[$counter]['aantal'] = $val['rsCountWoonplaats'];

					$counter++;
				}
			}

			break;

		case 'straatnaam':

			switch ($_GET['ogType']) {

				case 'alv':

					$sql = array();

					$tempSql = $cms['database']->prepare("SELECT DISTINCT `object_ObjectDetails_Adres_Straatnaam` AS `rsWoonplaats`, Count(`object_ObjectDetails_Adres_Straatnaam`) AS `rsCountWoonplaats` FROM `tbl_OG_alv` WHERE NOT `object_ObjectDetails_Status_StatusType` IN ('ingetrokken', 'verkocht') AND `object_ObjectDetails_Adres_Straatnaam` LIKE '%" . $_GET['q'] . "%' GROUP BY `object_ObjectDetails_Adres_Straatnaam`");

					if (count($tempSql) > 0) {

						foreach ($tempSql as $key => $val) {

							$sql[] = $val;
						}
					}

					break;

				case 'wonen':

					$sql = array();

					$tempSql = $cms['database']->prepare("SELECT DISTINCT `objectDetails_Adres_NL_Straatnaam` AS `rsWoonplaats`, Count(`objectDetails_Adres_NL_Straatnaam`) AS `rsCountWoonplaats` FROM `tbl_OG_wonen` WHERE NOT `objectDetails_StatusBeschikbaarheid_Status` IN ('ingetrokken', 'verkocht', 'Ingetrokken', 'Verkocht') AND `objectDetails_Adres_NL_Straatnaam` LIKE '%" . $_GET['q'] . "%' GROUP BY `objectDetails_Adres_NL_Straatnaam`");

					if (count($tempSql) > 0) {

						foreach ($tempSql as $key => $val) {

							$sql[] = $val;
						}
					}

					break;

				case 'bog':

					$sql = array();

					$tempSql = $cms['database']->prepare("SELECT DISTINCT `objectDetails_Adres_Straatnaam` AS `rsWoonplaats`, Count(`objectDetails_Adres_Straatnaam`) AS `rsCountWoonplaats` FROM `tbl_OG_bog` WHERE NOT `objectDetails_Status_StatusType` IN ('ingetrokken', 'verkocht', 'Ingetrokken', 'Verkocht') AND `objectDetails_Adres_Straatnaam` LIKE '%" . $_GET['q'] . "%' GROUP BY `objectDetails_Adres_Straatnaam`");

					if (count($tempSql) > 0) {

						foreach ($tempSql as $key => $val) {

							$sql[] = $val;
						}
					}

					break;
			}

			$objects = $sql;

			if (count($objects) > 0) {

				$counter = 0;
				foreach ($objects as $key => $val) {

					$jsonArray[$counter]['value'] = ucwords(strtolower($val['rsWoonplaats']));
					$jsonArray[$counter]['aantal'] = $val['rsCountWoonplaats'];

					$counter++;
				}
			}

			break;
	}

	echo json_encode($jsonArray);
}

?>