<?php

set_time_limit(0);

// BELANGRIJK: skarabee gebruikt géén Tiara ID
// Het veld object_ObjectTiaraID wordt gevuld met het unieke ID van skarabee!!

// Check if media is in progress
$mediaStatus = @file_get_contents('inc/mediaStatus.txt');

//if (trim($mediaStatus) == 'IN_PROGRESS')
//	die();

// Include the import functions
include("inc/import_functions.php");

// Global configuration
include("inc/config.php");

// Database connection
include("inc/mssql.class.php");

// Start up the DB connection
$db = new mssql($db['server'], $db['database'], $db['username'], $db['passwd']);

$rootFolder = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/og_media';

if (isset($_GET['batch']))
	$currentBatch = $_GET['batch'];
else
	$currentBatch = 1;

// It will handle 50 requests per batch... requests are downloads, resizes, deletions, etc.
$perBatch = 50;

// Function to resize while keeping proportions
function resize_values($width, $height, $min, $max){
    
    if ($width > $height) {

    	if ($width > $min || $height > $min) {

	        $divide = $width / $min;

	        $newWidth = $min;
	        $newHeight = ceil($height / $divide);
    	}
    	else {

    		$newWidth = $width;
    		$newHeight = $height;
    	}
    }
    else {

    	if ($width > $min || $height > $min) {

	        $divide = $height / $max;

	        $newHeight = $max;
	        $newWidth = ceil($width / $divide);
    	}
    	else {

    		$newWidth = $width;
    		$newHeight = $height;
    	}
    }

    return array('width' => $newWidth, 'height' => $newHeight);
}

// Calculate start record
$startRecord = (($currentBatch - 1) * 50) + 1;
$endRecord = $currentBatch * 50;

// Select the top 50
$query = "SELECT TOP 50 * FROM [tbl_OG_media] WHERE id>=" . $startRecord . " AND id<=" . $endRecord . " AND bestands_extensie='jpg'";
$result = $db->query($query);
$count = $db->row_count($result);

if ($count > 0) {

	while ($row = $db->fetch_assoc($result)) {

		// DB to select from
		if ($row['id_OG_wonen'] >= 1)
			$tableSelect = 'tbl_OG_wonen';
		elseif ($row['id_OG_bog'] >= 1)
			$tableSelect = 'tbl_OG_bog';
		elseif ($row['id_OG_alv'] >= 1)
			$tableSelect = 'tbl_OG_alv';
		elseif ($row['id_OG_nieuwbouw'] >= 1)
			$tableSelect = 'tbl_OG_nieuwbouw';

		// Find vestiging ID
		$resultFind = $db->query("SELECT object_NVMVestigingNR FROM [" . $tableSelect . "] WHERE object_ObjectTiaraID=" . $row['object_ObjectTiaraID']);
		$fetchFind = $db->fetch_assoc($resultFind);

		// Uniek ID van Vaassen binnen skarabee
		$uniqueId = $fetchFind['object_NVMVestigingNR'];

		// Ultimate root for the images
		$inputFolder = $rootFolder . '/' . $uniqueId . '_' . $row['object_ObjectTiaraID'];

		// Temp name
		$tempname = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '.' . $row['bestands_extensie'];

		// Medium name
		$mediumname = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '_medium.' . $row['bestands_extensie'];

		// Original sizes
		list($width, $height, $type, $attr) = @getimagesize($inputFolder . '/' . $tempname);

		$startTime = microtime(true);

		if ($row['bestands_extensie'] == 'jpg'|| $row['bestands_extensie'] == 'JPG'|| $row['bestands_extensie'] == 'jpeg'|| $row['bestands_extensie'] == 'JPEG')
	    $image = @imagecreatefromjpeg($inputFolder . '/' . $tempname) ;
	    elseif ($row['bestands_extensie'] == 'png' || $row['bestands_extensie'] == 'PNG')
	    $image = @imagecreatefrompng($inputFolder . '/' . $tempname) ;
	    elseif ($row['bestands_extensie'] == 'gif' || $row['bestands_extensie'] == 'GIF')
	    $image = @imagecreatefromgif($inputFolder . '/' . $tempname) ;

	    // Resize to medium
	    $newValues = resize_values($width, $height, 635, 476);
	    $tn = @imagecreatetruecolor($newValues['width'], $newValues['height']) ; 
	    
	    @imagecopyresampled($tn, $image, 0, 0, 0, 0, $newValues['width'], $newValues['height'], $width, $height) ; 

	    @imagejpeg($tn, $inputFolder . '/' . $mediumname, 100);

	    @imagedestroy($tn);

	    // Remove the temp file
	    //@unlink($inputFolder . '/' . $tempname);

	    $endTime = microtime(true);
		$totalTime = $endTime - $startTime;

		addLog(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/cms/mod-importOG/sync_logs/logfile_media.txt', 'Finished resizing media with ID ' . $row['id'] . ' in ' . round($totalTime, 2) . ' second(s)', microtime(true));

	    // Set status to 1 for resize
		$db->query("UPDATE [tbl_OG_media] SET bestandsnaam_medium='" . $mediumname . "' WHERE id=" . $row['id']);
	}
}

// Redirect when doing manual sync, uncomment it
header("Location: manualPause.php?batch=" . ($currentBatch + 1));
die();

?>