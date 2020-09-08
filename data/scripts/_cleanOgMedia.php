<?php

include($_SERVER['DOCUMENT_ROOT'] . "/cms/inc/config.php");

foreach (new DirectoryIterator($_SERVER['DOCUMENT_ROOT'] . '/og_media') as $fileInfo) {

	if (!$fileInfo->isDot()) {

		$ex = explode('_', $fileInfo->getFilename());

		if ($ex[0] == 'wonen') {

			$check = $cms['database']->prepare("SELECT * FROM `tbl_OG_wonen` WHERE `object_NVMVestigingNR`=? AND `object_ObjectTiaraID`=? AND lower(`objectDetails_StatusBeschikbaarheid_Status`) NOT IN ('ingetrokken', 'gearchiveerd')", "ii", array($ex[1], $ex[2]));
			
			if (count($check) == 0) {

				$counter = 0;

				foreach (new DirectoryIterator($_SERVER['DOCUMENT_ROOT'] . '/og_media/' . $fileInfo->getFilename()) as $fileInfo2) {

					if (!$fileInfo2->isDot()) {

						unlink($_SERVER['DOCUMENT_ROOT'] . '/og_media/' . $fileInfo->getFilename() . '/' . $fileInfo2->getFilename());

						$counter++;
					}
				}
				
				if ($counter > 0)
					echo 'Deleted from ' . $fileInfo->getFilename() . ': ' . $counter . '<br>';

				unlink($_SERVER['DOCUMENT_ROOT'] . '/og_media/' . $fileInfo->getFilename());
			}
			else {

				echo 'Keep: ' . $fileInfo->getFilename() . '<br>';
			}
		}
	}
}