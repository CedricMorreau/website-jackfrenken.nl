<?php

// Most simplistic cache method
class Cache {

	// Function which sets a cache
	static function set($data, $name, $expires) {

		global $documentRoot;

		$expireTime = time() + $expires;

		$array = array();
		$array['data'] = $data;
		$array['expireTime'] = $expireTime;

		// Generate mapname
		$mapName = substr(md5($name), 0, 1);

		@mkdir($documentRoot . 'data/cache/auto/' . $mapName, 0777);

		file_put_contents($documentRoot . 'data/cache/auto/' . $mapName . '/' . md5($name) . '.txt', serialize($array));
	}

	// Function which gets a cache
	static function get($name) {

		global $documentRoot;

		// Determine subfolder
		$mapName = substr(md5($name), 0, 1);

		// Check if file exists
		if (file_exists($documentRoot . 'data/cache/auto/' . $mapName . '/' . md5($name) . '.txt')) {

			// Read file
			$fileData = unserialize(file_get_contents($documentRoot . 'data/cache/auto/' . $mapName . '/' . md5($name) . '.txt'));

			if ($fileData['expireTime'] < time()) {

				return false;
			}
			else {

				return $fileData['data'];
			}
		}
		else {

			return false;
		}
	}
}

?>