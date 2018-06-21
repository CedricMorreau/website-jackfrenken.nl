<?php

ini_set('memory_limit','1024M');
set_time_limit(0);

error_reporting(E_ALL);
ini_set('display_errors', true);

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
include("inc/database.class.php");

// Start up the DB connection
$db = new Database();
$db->connection($dbd['username'], $dbd['passwd'], $dbd['server']);
$db->selectDatabase($dbd['database']);

// Set media to IN_PROGRESS
@file_put_contents('inc/mediaStatus.txt', 'IN_PROGRESS');

// This loops through all media, downloading it.. resizing it.. deleting it.. etc.
$rootFolder = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/og_media';

if (isset($_GET['batch']))
	$currentBatch = $_GET['batch'];
else
	$currentBatch = 1;

// Clear the media log
if ($currentBatch == 1)
	file_put_contents(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/cms/mod-importOG/sync_logs/logfile_media.txt', '');

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

// Select everything from DB with status <= 1
$query = "SELECT * FROM `tbl_OG_media` WHERE `media_status`<=1 LIMIT " . $perBatch;
$result = $db->prepare($query);
$count = count($result);

if ($count > 0) {

	addLog(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/cms/mod-importOG/sync_logs/logfile_media.txt', 'Starting media loop at batch ' . $currentBatch . ' with ' . $perBatch . ' per batch.', microtime(true));

	// Fetch all rows
	foreach ($result as $key => $row) {
	// while ($row = $db->fetch_assoc($result)) {

		// DB to select from
		if ($row['id_OG_wonen'] >= 1) {

			$nvmVestiging = 'object_NVMVestigingNR';
			$tiaraId = 'object_ObjectTiaraID';
			$prepend = 'wonen';
			$tableSelect = 'tbl_OG_wonen';
		}
		elseif ($row['id_OG_bog'] >= 1) {

			$nvmVestiging = 'object_NVMVestigingNR';
			$tiaraId = 'object_ObjectTiaraID';
			$prepend = 'bog';
			$tableSelect = 'tbl_OG_bog';
		}
		elseif ($row['id_OG_alv'] >= 1) {

			$nvmVestiging = 'object_NVMVestigingNR';
			$tiaraId = 'object_ObjectTiaraID';
			$prepend = 'alv';
			$tableSelect = 'tbl_OG_alv';
		}
		elseif ($row['id_OG_nieuwbouw_projecten'] >= 1) {

			$nvmVestiging = 'project_NVMVestigingNR';
			$tiaraId = 'project_ObjectTiaraID';
			$prepend = 'nieuwbouw_';
			$tableSelect = 'tbl_OG_nieuwbouw_projecten';
		}
		elseif ($row['id_OG_nieuwbouw_bouwtypes'] >= 1) {

			$nvmVestiging = 'bouwType_NVMVestigingNR';
			$tiaraId = 'bouwType_ObjectTiaraID';
			$prepend = 'nieuwbouw_';
			$tableSelect = 'tbl_OG_nieuwbouw_bouwTypes';
		}
		elseif ($row['id_OG_nieuwbouw_bouwnummers'] >= 1) {

			$nvmVestiging = 'bouwNummer_NVMVestigingNR';
			$tiaraId = 'bouwNummer_ObjectTiaraID';
			$prepend = 'nieuwbouw_';
			$tableSelect = 'tbl_OG_nieuwbouw_bouwNummers';
		}

		// Find vestiging ID
		$resultFind = $db->prepare("SELECT " . $nvmVestiging . " FROM `" . $tableSelect . "` WHERE " . $tiaraId . "=" . $row['object_ObjectTiaraID'] . " ORDER BY `id` DESC");

		if (count($resultFind) > 0) {

			$fetchFind = $resultFind[0];

			// Uniek ID van Vaassen binnen skarabee
			$uniqueId = $fetchFind[$nvmVestiging];

			// Image extensions
			$imgExtensions = array('jpeg', 'jpg', 'png', 'gif', 'JPEG', 'JPG', 'PNG', 'GIF');

			// First of, handle downloads
			if ($row['media_status'] == 0) {

				// Only if the file has an extension
				if (!empty($row['bestands_extensie'])) {

					// Ultimate root for the images
					$inputFolder = $rootFolder . '/' . $prepend . '_' . $uniqueId . '_' . $row['object_ObjectTiaraID'];

					// Check if folder exists
					@mkdir($inputFolder);

					// Temp name
					if (in_array($row['bestands_extensie'], $imgExtensions))
						$tempname = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '_temp.' . $row['bestands_extensie'];
					else
						$tempname = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '.' . $row['bestands_extensie'];

					$startTime = microtime(true);

					// Fetch the file from the internets
					$fileData = @file_get_contents($row['media_URL']);

					// Save to own server
					@file_put_contents($inputFolder . '/' . $tempname, $fileData);

					$endTime = microtime(true);
					$totalTime = $endTime - $startTime;

					addLog(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/cms/mod-importOG/sync_logs/logfile_media.txt', 'Finished downloading media with ID ' . $row['id'] . ' (' . $tempname . ', ' . human_filesize(filesize($inputFolder . '/' . $tempname)) . ' in ' . round($totalTime, 2) . ' second(s))', microtime(true));
				}

				// Set status to 1 for resize
				$db->prepare("UPDATE `tbl_OG_media` SET `media_status`=1 WHERE `id`=" . $row['id']);
			}
			// Second of, handle resizing
			elseif ($row['media_status'] == 1 && in_array($row['bestands_extensie'], $imgExtensions)) {

				// Ultimate root for the images
				$inputFolder = $rootFolder . '/' . $prepend . '_' . $uniqueId . '_' . $row['object_ObjectTiaraID'];

				// Temp name
				$tempname = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '_temp.' . $row['bestands_extensie'];

				// Resize name
				$resizename = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '.' . $row['bestands_extensie'];

				// Medium name
				$mediumname = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '_medium.' . $row['bestands_extensie'];

				// Thumb name
				$thumbname = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '_tn.' . $row['bestands_extensie'];

				// Original sizes
				list($width, $height, $type, $attr) = @getimagesize($inputFolder . '/' . $tempname);

				$startTime = microtime(true);

				if ($row['bestands_extensie'] == 'jpg'|| $row['bestands_extensie'] == 'JPG'|| $row['bestands_extensie'] == 'jpeg'|| $row['bestands_extensie'] == 'JPEG')
	            $image = @imagecreatefromjpeg($inputFolder . '/' . $tempname) ;
	            elseif ($row['bestands_extensie'] == 'png' || $row['bestands_extensie'] == 'PNG')
	            $image = @imagecreatefrompng($inputFolder . '/' . $tempname) ;
	            elseif ($row['bestands_extensie'] == 'gif' || $row['bestands_extensie'] == 'GIF')
	            $image = @imagecreatefromgif($inputFolder . '/' . $tempname) ;

				// Resize to resized
	            $newValues = resize_values($width, $height, 1920, 1440);
	            $tn = @imagecreatetruecolor($newValues['width'], $newValues['height']) ; 
	            
	            @imagecopyresampled($tn, $image, 0, 0, 0, 0, $newValues['width'], $newValues['height'], $width, $height) ; 

	            @imagejpeg($tn, $inputFolder . '/' . $resizename, 100);

	            @imagedestroy($tn);

	            // Resize to medium
	            $newValues = resize_values($width, $height, 786, 525);
	            $tn = @imagecreatetruecolor($newValues['width'], $newValues['height']) ; 
	            
	            @imagecopyresampled($tn, $image, 0, 0, 0, 0, $newValues['width'], $newValues['height'], $width, $height) ; 

	            @imagejpeg($tn, $inputFolder . '/' . $mediumname, 100);

	            @imagedestroy($tn);

	            // Resize to thumb
	            $newValues = resize_values($width, $height, 400, 300);
	            $tn = @imagecreatetruecolor($newValues['width'], $newValues['height']) ;
	            @imagecopyresampled($tn, $image, 0, 0, 0, 0, $newValues['width'], $newValues['height'], $width, $height) ; 

	            @imagejpeg($tn, $inputFolder . '/' . $thumbname, 100);

	            @imagedestroy($tn);

	            // Remove the temp file
	            unlink($inputFolder . '/' . $tempname);

	            $endTime = microtime(true);
				$totalTime = $endTime - $startTime;

				addLog(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/cms/mod-importOG/sync_logs/logfile_media.txt', 'Finished resizing media with ID ' . $row['id'] . ' in ' . round($totalTime, 2) . ' second(s)', microtime(true));

	            // Set status to 1 for resize
				$db->prepare("UPDATE `tbl_OG_media` SET `media_status`=2,`bestandsnaam`='" . $resizename . "',`bestandsnaam_tn`='" . $thumbname . "',`bestandsnaam_medium`='" . $mediumname . "' WHERE `id`=" . $row['id']);            
			}
			// If not an image, resize not possible.
			elseif ($row['media_status'] == 1 && !in_array($row['bestands_extensie'], $imgExtensions)) {

				// Only if the file has an extension
				if (!empty($row['bestands_extensie'])) {

					// Grab temp file
					// $tempdata = @file_get_contents($inputFolder . '/' . $row['object_ObjectTiaraID'] . '_' . $row['id'] . '_temp.' . $row['bestands_extensie']);

					// Remove old file
					// @unlink($inputFolder . '/' . $row['object_ObjectTiaraID'] . '_' . $row['id'] . '_temp.' . $row['bestands_extensie']);

					// Save new file
					// @file_put_contents($inputFolder . '/' . $row['object_ObjectTiaraID'] . '_' . $row['id'] . '.' . $row['bestands_extensie'], $tempdata);
				}

				// Not an image, just update status!
				$db->prepare("UPDATE `tbl_OG_media` SET `media_status`=2,`bestandsnaam`='" . $row['object_ObjectTiaraID'] . '_' . $row['id'] . '.' . $row['bestands_extensie'] . "' WHERE `id`=" . $row['id']);

			}
			// Status is -1, image should be redownloaded, unlink old files
			elseif ($row['media_status'] == -1) {

				// Ultimate root for the images
				$inputFolder = $rootFolder . '/' . $prepend . '_' . $uniqueId . '_' . $row['object_ObjectTiaraID'];

				// Resize name
				$resizename = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '.' . $row['bestands_extensie'];

				// Thumb name
				$thumbname = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '_tn.' . $row['bestands_extensie'];

				// Medium name
				$mediumname = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '_medium.' . $row['bestands_extensie'];

				@unlink($inputFolder . '/' . $resizename);
				@unlink($inputFolder . '/' . $thumbname);
				@unlink($inputFolder . '/' . $mediumname);

				addLog(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/cms/mod-importOG/sync_logs/logfile_media.txt', 'Media with ID ' . $row['id'] . ' has to be redownloaded. Old files unlinked.', microtime(true));

				// Set status to 0 for download
				$db->prepare("UPDATE `tbl_OG_media` SET `media_status`=0 WHERE `id`=" . $row['id']);   
			}
			// Finally.. if status is 2, delete record & image
			elseif ($row['media_status'] == -2) {

				// Ultimate root for the images
				$inputFolder = $rootFolder . '/' . $prepend . '_' . $uniqueId . '_' . $row['object_ObjectTiaraID'];

				// Resize name
				$resizename = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '.' . $row['bestands_extensie'];

				// Thumb name
				$thumbname = $row['object_ObjectTiaraID'] . '_' . $row['id'] . '_tn.' . $row['bestands_extensie'];

				@unlink($inputFolder . '/' . $resizename);
				@unlink($inputFolder . '/' . $thumbname);

				addLog(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/cms/mod-importOG/sync_logs/logfile_media.txt', 'Media with ID ' . $row['id'] . ' was deleted.', microtime(true));

				// Remove record
				$db->prepare("DELETE FROM `tbl_OG_media` WHERE `id`=" . $row['id']);
			}
		}
		else {

			// Corresponding object was NOT found. Delete row.
			$db->prepare("DELETE FROM `tbl_OG_media` WHERE `id`=" . $row['id']);
		}
	}

	// Redirect when doing manual sync, uncomment it
	header("Location: mediaPause.php?batch=" . ($currentBatch + 1));
	die();
}
else {

	addLog(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/cms/mod-importOG/sync_logs/logfile.txt', 'Media loop has finished successfully in ' . $currentBatch . ' batches. See logfile_media.txt for more info.', microtime(true));
}

// Set media to COMPLETED
@file_put_contents('inc/mediaStatus.txt', 'COMPLETED');

?>