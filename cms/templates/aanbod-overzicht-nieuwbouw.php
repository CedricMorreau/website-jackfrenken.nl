<?php

if (isset($_GET['searchHash'])) {
	
	$searchHash = $_GET['searchHash'];
	
	if (isset($_SESSION['search'][$searchHash])) {
		
		foreach ($_SESSION['search'][$searchHash] as $key => $val) {
			
			$_GET[$key] = $val;
		}
	}
	
	unset($_SESSION['search'][$searchHash]);
}

$filter['provincie'] = (isset($_POST['provincie'])) ? $_POST['provincie'] : ((isset($_GET['provincie'])) ? $_GET['provincie'] : 0);
$filter['plaatsnaam'] = (isset($_POST['plaatsnaam'])) ? $_POST['plaatsnaam'] : ((isset($_GET['plaatsnaam'])) ? $_GET['plaatsnaam'] : '');
$filter['radius'] = (isset($_POST['radius'])) ? $_POST['radius'] : ((isset($_GET['radius'])) ? $_GET['radius'] : 0);
$filter['prijsVan'] = (isset($_POST['prijsVan'])) ? $_POST['prijsVan'] : ((isset($_GET['prijsVan'])) ? $_GET['prijsVan'] : 0);
$filter['prijsTot'] = (isset($_POST['prijsTot'])) ? $_POST['prijsTot'] : ((isset($_GET['prijsTot'])) ? $_GET['prijsTot'] : '');
$filter['oppPerceel'] = (isset($_POST['oppPerceel'])) ? $_POST['oppPerceel'] : ((isset($_GET['oppPerceel'])) ? $_GET['oppPerceel'] : 0);
$filter['oppWoon'] = (isset($_POST['oppWoon'])) ? $_POST['oppWoon'] : ((isset($_GET['oppWoon'])) ? $_GET['oppWoon'] : 0);

// $filter['prijsOpAanvraag'] = (isset($_POST['prijsOpAanvraag'])) ? $_POST['prijsOpAanvraag'] : ((isset($_GET['prijsOpAanvraag'])) ? $_GET['prijsOpAanvraag'] : '');

$filter['bestemming'] = (isset($_POST['bestemming'])) ? $_POST['bestemming'] : ((isset($_GET['bestemming'])) ? $_GET['bestemming'] : ''); //str_f01
$filter['bedrijfswoning'] = (isset($_POST['bedrijfswoning'])) ? $_POST['bedrijfswoning'] : ((isset($_GET['bedrijfswoning'])) ? $_GET['bedrijfswoning'] : array()); //str_f02
$filter['hoofdfunctie'] = (isset($_POST['hoofdfunctie'])) ? $_POST['hoofdfunctie'] : ((isset($_GET['hoofdfunctie'])) ? $_GET['hoofdfunctie'] : array());
$filter['verkocht'] = (isset($_POST['verkocht'])) ? $_POST['verkocht'] : ((isset($_GET['verkocht'])) ? $_GET['verkocht'] : '');
$filter['video'] = (isset($_POST['video'])) ? $_POST['video'] : ((isset($_GET['video'])) ? $_GET['video'] : '');

$extraSearch = false;

if (!empty($filter['oppPerceel']) || !empty($filter['oppWoon'])) {
	
	$extraSearch = true;
}

// $filter['slaapkamers'] = (isset($_POST['slaapkamers'])) ? $_POST['slaapkamers'] : ((isset($_GET['slaapkamers'])) ? $_GET['slaapkamers'] : ''); //str_f02
// $filter['perceelOppervlakte'] = (isset($_POST['perceelOppervlakte'])) ? $_POST['perceelOppervlakte'] : ((isset($_GET['perceelOppervlakte'])) ? $_GET['perceelOppervlakte'] : ''); //str_f03
// $filter['woonfunctieOppervlakte'] = (isset($_POST['woonfunctieOppervlakte'])) ? $_POST['woonfunctieOppervlakte'] : ((isset($_GET['woonfunctieOppervlakte'])) ? $_GET['woonfunctieOppervlakte'] : ''); //str_f04
// $filter['openhuisVanaf'] = (isset($_POST['openhuisVanaf'])) ? $_POST['openhuisVanaf'] : ((isset($_GET['openhuisVanaf'])) ? $_GET['openhuisVanaf'] : '');

// if ((!empty($filter['verkocht']) && $filter['verkocht'] == 1) || (!empty($filter['video']) && $filter['video'] == 1)) {

// 	$extraState = '';
// }
// else {

// 	$extraState = ", 'gearchiveerd'";
// }

$sql = "SELECT
		tbl_OG_nieuwbouw_projecten.id,
		tbl_OG_nieuwbouw_projecten.project_ObjectTiaraID,
		tbl_OG_nieuwbouw_projecten.project_NVMVestigingNR,
		
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Projectnaam,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Adres_Woonplaats,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Adres_Postcode,
		
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_KoopAanneemsom_Van,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_KoopAanneemsom_TotEnMet,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Huurprijs_Van,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Huurprijs_TotEnMet,
		
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Maten_Woonoppervlakte_Van,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Maten_Woonoppervlakte_TotEnMet,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Maten_Perceeloppervlakte_Van,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Maten_Perceeloppervlakte_TotEnMet,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Maten_Inhoud_Van,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_Maten_Inhoud_TotEnMet,
		
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_DatumStartBouw,
		tbl_OG_nieuwbouw_projecten.project_ProjectDetails_DatumOpleveringVanaf,
		tbl_OG_nieuwbouw_projecten.google_lat,
		tbl_OG_nieuwbouw_projecten.google_lng,
		
		project_ProjectDetails_Status_ObjectStatus,
		
		(SELECT COUNT(id) AS rsCount FROM tbl_OG_nieuwbouw_bouwTypes WHERE id_OG_nieuwbouw_projecten=tbl_OG_nieuwbouw_projecten.id) as aantal_bouwTypes,
		(SELECT COUNT(id) AS rsCount FROM tbl_OG_nieuwbouw_bouwNummers WHERE id_OG_nieuwbouw_projecten=tbl_OG_nieuwbouw_projecten.id) as aantal_bouwNummers,
		(SELECT COUNT(id) AS rsCount FROM tbl_OG_nieuwbouw_bouwNummers WHERE id_OG_nieuwbouw_projecten=tbl_OG_nieuwbouw_projecten.id AND `Status_ObjectStatus` IN ('verkocht', 'verhuurd', 'ingetrokken')) as aantal_verkochtBouwNummers
		
		FROM tbl_OG_nieuwbouw_projecten
			LEFT JOIN `tbl_OG_objectDetails` ON `ood_ogId`=`id` AND `ood_table`='tbl_OG_nieuwbouw_projecten'
			INNER JOIN `tbl_OG_provincies` ON `tbl_OG_provincies`.`id`=`tbl_OG_nieuwbouw_projecten`.`id_provincies`
		WHERE (NOT tbl_OG_nieuwbouw_projecten.id<0) AND `project_ProjectDetails_Status_ObjectStatus` NOT IN ('ingetrokken', 'gearchiveerd')";

// Extended search filter
if (!empty($filter['plaatsnaam']) || !empty($filter['radius']) || !empty($filter['prijsVan']) || !empty($filter['prijsTot']) || count($filter['bedrijfswoning']) > 0) {
	
	$classExtended = '';
}
else {
	
	$classExtended = 'closed';
}

$filter['orderBy'] = (isset($_POST['orderBy'])) ? $_POST['orderBy'] : ((isset($_GET['orderBy'])) ? $_GET['orderBy'] : 'datum,desc');

$splitOrder = explode(',', $filter['orderBy']);

$data['orderByReal'] = $splitOrder[0];
$filter['sortBy'] = $splitOrder[1];
$filter['p'] = (isset($_POST['p'])) ? $_POST['p'] : ((isset($_GET['p'])) ? $_GET['p'] : 1);

// Correct weird sortBy
if (!in_array($filter['sortBy'], array('asc', 'desc')))
	$filter['sortBy'] = 'DESC';
	
// Pronvincie filter
if (!empty($filter['provincie']) && $filter['provincie'] > 0 && is_numeric($filter['provincie'])) {
	
	$sql .= " AND `tbl_OG_nieuwbouw_projecten`.`id_provincies`=" . $cms['database']->escape($filter['provincie']);
}

// if (!empty($filter['verkocht']) && $filter['verkocht'] == 1) {

// 	$sql .= " AND `tbl_OG_nieuwbouw_projecten`.`object_ObjectDetails_Status_StatusType` IN ('Verkocht', 'verkocht', 'gearchiveerd') ";
// }

if (!empty($filter['video']) && $filter['video'] == 1) {
	
	// $sql .= " AND `tbl_OG_objectDetails`.`ood_helicopterVideo` IS NOT NULL AND `tbl_OG_objectDetails`.`ood_helicopterVideo`!='' ";
	$sql .= " AND (`tbl_OG_objectDetails`.`ood_helicopterVideo` IS NOT NULL AND `tbl_OG_objectDetails`.`ood_helicopterVideo`!='' ";
	
	// Handle video from media
	$sql .= " OR EXISTS (SELECT `tbl_OG_media`.`id` FROM `tbl_OG_media` WHERE `id_OG_nieuwbouw_projecten`=`tbl_OG_nieuwbouw_projecten`.`id` AND `media_Groep`='Overig' AND `media_MediaOmschrijving`='helicopter')) ";
}

// Plaatsnaam / postcode
if (!empty($filter['plaatsnaam'])) {
	
	// Construct next query
	$sql .= " AND (";
	
	// Postcode
	$sql .= "`tbl_OG_nieuwbouw_projecten`.`project_ProjectDetails_Adres_Postcode` LIKE '%" . $cms['database']->escape($filter['plaatsnaam']) . "%' ";
	
	// Plaatsnaam
	$sql .= "OR lower(`tbl_OG_nieuwbouw_projecten`.`project_ProjectDetails_Adres_Woonplaats`) LIKE '%" . $cms['database']->escape(strtolower($filter['plaatsnaam'])) . "%' ";
	
	// Straat
	// $sql .= "OR lower(`tbl_OG_nieuwbouw_projecten`.`object_ObjectDetails_Adres_Straatnaam`) LIKE '%" . $cms['database']->escape(strtolower($filter['plaatsnaam'])) . "%' ";
	
	// Close
	$sql .= ')';
}

// Prijs van
if (!empty($filter['prijsVan']) && $filter['prijsVan'] > 0 && is_numeric($filter['prijsVan'])) {
	
	$sql .= " AND `tbl_OG_nieuwbouw_projecten`.`project_ProjectDetails_KoopAanneemsom_Van`>=" . $cms['database']->escape($filter['prijsVan']) . " ";
}

// Prijs tot
if (!empty($filter['prijsTot']) && $filter['prijsTot'] > 0 && is_numeric($filter['prijsTot'])) {
	
	if ($filter['prijsTot'] > $filter['prijsVan']) {
		
		$sql .= " AND `tbl_OG_nieuwbouw_projecten`.`project_ProjectDetails_KoopAanneemsom_TotEnMet`<=" . $cms['database']->escape($filter['prijsTot']) . " ";
	}
}

// Radius
if (!empty($filter['plaatsnaam']) && !empty($filter['radius'])) {
	
	$filter['landcode'] = 'NL';
	
	include($documentRoot . 'inc/google_searchRadius.php');
	
	if (isset($boundingBox)) {
		
		$sql .= " OR (" .
				"`tbl_OG_nieuwbouw_projecten`.`google_status`=1 " .
				"AND `tbl_OG_nieuwbouw_projecten`.`google_lat` BETWEEN " . $cms['database']->escape($boundingBox['lat1']) . " AND " . $cms['database']->escape($boundingBox['lat2']) . " " .
				"AND `tbl_OG_nieuwbouw_projecten`.`google_lng` BETWEEN " . $cms['database']->escape($boundingBox['lon1']) . " AND " . $cms['database']->escape($boundingBox['lon2']) . " " .
				")";
	}
}

// Perceeloppervlakte filter
if (!empty($filter['oppPerceel']) && $filter['oppPerceel'] > 0 && is_numeric($filter['oppPerceel'])) {
	
	$sql .= " AND `tbl_OG_nieuwbouw_projecten`.`project_ProjectDetails_Maten_Perceeloppervlakte_TotEnMet`>=" . $cms['database']->escape($filter['oppPerceel']) . " ";
}

// Woonoppervlakte filter
if (!empty($filter['oppWoon']) && $filter['oppWoon'] > 0 && is_numeric($filter['oppWoon'])) {
	
	$sql .= " AND `tbl_OG_nieuwbouw_projecten`.`project_ProjectDetails_Maten_Woonoppervlakte_TotEnMet`>=" . $cms['database']->escape($filter['oppPerceel']) . " ";
}

// Handle sorting
$tempSql = '';
switch ($data['orderByReal']) {
	
	case 'prijs':
		
		$tempSql .= " `tbl_OG_nieuwbouw_projecten`.`project_ProjectDetails_KoopAanneemsom_Van` " . $filter['sortBy'] . ", `tbl_OG_nieuwbouw_projecten`.`project_ProjectDetails_Adres_Woonplaats` ASC ";
		
		break;
	case 'plaatsnaam':
		
		$tempSql .= " `tbl_OG_nieuwbouw_projecten`.`project_ProjectDetails_Adres_Woonplaats` " . $filter['sortBy'] . " ";
		
		break;
	case 'datum':
		
		$tempSql .= " `tbl_OG_nieuwbouw_projecten`.`datum_toegevoegd` " . $filter['sortBy'] . " ";
		
		break;
		
	default:
		
		$tempSql .= " `tbl_OG_nieuwbouw_projecten`.`datum_toegevoegd` DESC ";
		
}

if (!empty($tempSql)) {
	
	$sql .= " ORDER BY" . $tempSql;
}

// Let's handle paging!
$currentPage = $filter['p'];

$perPage = 10;

$tempQ = $cms['database']->prepare($sql);
$totalRows = count($tempQ);

$totalPages = ceil($totalRows / $perPage);

if ($currentPage <= 0)
	$currentPage = 1;
	
if ($currentPage > $totalPages)
	$currentPage = $totalPages;
	
$currentResult = ($currentPage - 1) * $perPage;

if (!empty($filter['verkocht']) && $filter['verkocht'] == 1) {
	
	if ($currentResult != 0) {
		
		$currentResult -= 1;
	}
	else {
		
		$perPage -= 1;
	}
}
else {
	
	if ($currentResult > 1) {
		
		$currentResult -= 2;
	}
	else {
		
		$perPage -= 2;
	}
}

if ($totalRows > 0)
	$sql .= " LIMIT " . $currentResult . "," . $perPage;
	
// Kick out the query
$objects = $cms['database']->prepare($sql);

$searchQuery = '';

foreach ($filter as $key => $val) {
	
	if ($key != 'p' && $key != 'searchHash') {
		
		if (is_array($val)) {
			
			foreach ($val as $sKey => $sVal) {
				
				$searchQuery .= '&' . $key . '[]=' . $sVal;
			}
		}
		else {
			
			$searchQuery .= '&' . $key . '=' . $val;
		}
		
		$_SESSION['search'][md5('searchQuery_' . $_SERVER['REMOTE_ADDR'] . $searchQuery)] = $filter;
		unset($_SESSION['search'][md5('searchQuery_' . $_SERVER['REMOTE_ADDR'] . $searchQuery)]['p']);
		unset($_SESSION['search'][md5('searchQuery_' . $_SERVER['REMOTE_ADDR'] . $searchQuery)]['searchHash']);
		$MD5 = md5('searchQuery_' . $_SERVER['REMOTE_ADDR'] . $searchQuery);
	}
}

$searchQueryFull = '';

foreach ($filter as $key => $val) {
	
	if ($key != 'searchHash') {
		
		if (is_array($val)) {
			
			foreach ($val as $sKey => $sVal) {
				
				$searchQueryFull .= '&' . $key . '[]=' . $sVal;
			}
		}
		else {
			
			$searchQueryFull .= '&' . $key . '=' . $val;
		}
		
		$_SESSION['search'][md5('searchQueryFull_' . $_SERVER['REMOTE_ADDR'] . $searchQueryFull)] = $filter;
		unset($_SESSION['search'][md5('searchQueryFull_' . $_SERVER['REMOTE_ADDR'] . $searchQueryFull)]['searchHash']);
		$fullMD5 = md5('searchQueryFull_' . $_SERVER['REMOTE_ADDR'] . $searchQueryFull);
	}
}

$searchQueryOrder = '';

foreach ($filter as $key => $val) {
	
	if ($key != 'p' && $key != 'orderBy' && $key != 'searchHash') {
		
		if (is_array($val)) {
			
			foreach ($val as $sKey => $sVal) {
				
				$searchQueryOrder .= '&' . $key . '[]=' . $sVal;
			}
		}
		else {
			
			$searchQueryOrder .= '&' . $key . '=' . $val;
		}
		
		$_SESSION['search'][md5('searchQueryOrder_' . $_SERVER['REMOTE_ADDR'] . $searchQueryOrder)] = $filter;
		unset($_SESSION['search'][md5('searchQueryOrder_' . $_SERVER['REMOTE_ADDR'] . $searchQueryOrder)]['p']);
		unset($_SESSION['search'][md5('searchQueryOrder_' . $_SERVER['REMOTE_ADDR'] . $searchQueryOrder)]['orderBy']);
		unset($_SESSION['search'][md5('searchQueryOrder_' . $_SERVER['REMOTE_ADDR'] . $searchQueryOrder)]['searchHash']);
		$orderMD5 = md5('searchQueryOrder_' . $_SERVER['REMOTE_ADDR'] . $searchQueryOrder);
	}
}

$searchQuerySort = '';

foreach ($filter as $key => $val) {
	
	if ($key != 'p' && $key != 'sortBy' && $key != 'searchHash') {
		
		if (is_array($val)) {
			
			foreach ($val as $sKey => $sVal) {
				
				$searchQuerySort .= '&' . $key . '[]=' . $sVal;
			}
		}
		else {
			
			$searchQuerySort .= '&' . $key . '=' . $val;
		}
		
		$_SESSION['search'][md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort)] = $filter;
		unset($_SESSION['search'][md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort)]['p']);
		unset($_SESSION['search'][md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort)]['sortBy']);
		unset($_SESSION['search'][md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort)]['searchHash']);
		$sortMD5 = md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort);
	}
}

$MD5None = generateHashNone($filter, array('searchHash', 'p'));

function generateHashNone($filters, $excludes = array()) {
	
	$searchQuerySort = '';
	
	foreach ($filters as $key => $val) {
		
		if (!in_array($key, $excludes)) {
			
			if (is_array($val)) {
				
				foreach ($val as $sKey => $sVal) {
					
					$searchQuerySort .= '&' . $key . '[]=' . $sVal;
				}
			}
			else {
				
				$searchQuerySort .= '&' . $key . '=' . $val;
			}
		}
	}
	
	return md5('searchQuerySort_' . $searchQuerySort);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	
	Core::redirect($template->findPermalink(35, 1) . '.html&searchHash=' . $fullMD5);
}

$noFilters = '26588d98cdfcdf9e83c2b9bb4644f49b';

?>

<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>

	</head>

	<body>
		<!-- KMH pixel --> 
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','kmhPixel','GTM-PX4GN2');</script> 
		<!-- End KMH pixel -->

		<div class="page-wrapper aanbod-overzicht">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">

				<?php include($documentRoot . "inc/aanbod-filtering-nieuwbouw.php"); ?>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
					
					<?php 
					
					if (count($objects) > 0) {
						
						foreach ($objects as $key => $val) {
							
							include ($documentRoot . "inc/templates/aanbod-nieuwbouw.php");
						}
					}
					
					?>
					
					<?php include($documentRoot . "inc/aanbod-banner.php"); ?>
					
					<?php
					
					$pagingUrl = $dynamicRoot . $template->getPermaLink($template->getCurrentLanguage()) . '.html?searchHash=' . $MD5;
					
					include($documentRoot . "inc/paging.php");
					
					?>
					</div>


				</div>
			</div>

			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

		<script>

		</script>
	</body>

</html>