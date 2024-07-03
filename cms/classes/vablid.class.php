<?php

class Vablid {

	static $permissionData;

	static function generateName($data, $userId, $db, $noTitles = 0) {

		$elitistCats = array(
			59 => 'AA',
			60 => 'BA',
			61 => 'BC',
			62 => 'CB',
			63 => 'FB',
			64 => 'FFP',
			65 => 'MAB',
			66 => 'MB',
			67 => 'MBA',
			68 => 'Msc',
			69 => 'QT',
			70 => 'RBc',
			71 => 'RT',
			84 => 'ab',
			85 => 'BRE',
			86 => 'FA',
			88 => 'PhD',
			89 => 'RB',
			90 => 'RMT'
		);

		$fullName = '';

		if (!empty($data)) {

			if (Vablid::cp('aanhef', $data['mod_lid_id'])) {
			
				if (!empty($data['mod_lid_aanhef']))
					$fullName .= ucwords($data['mod_lid_aanhef']) . '.';
			}

			if (Vablid::cp('titels', $data['mod_lid_id']) && !$noTitles) {

				$arrShown = array();

				// Are we really going to add these elitist certified letters... yes we are. Let's mess up the database.
				$fetchCats = $db->prepare("SELECT * FROM `tbl_mod_catChain` LEFT JOIN `tbl_mod_catData` ON `mod_cd_id`=`mod_cc_categoryId` WHERE `mod_cc_moduleTable`='tbl_mod_leden_titels' AND `mod_cc_moduleId`=?", "i", array($data['mod_lid_id']));

				if (count($fetchCats) > 0) {

					foreach ($fetchCats as $key => $val) {

						if (!in_array($val['mod_cd_id'], $arrShown)) {

							$arrShown[] = $val['mod_cd_id'];
							$fullName .= ' ' . strtolower($val['mod_cd_name']);
						}
					}
				}
				
				// if (!empty($data['mod_lid_titels']))
				// 	$fullName .= ' ' . $data['mod_lid_titels'];
			}

			if (Vablid::cp('voorletters', $data['mod_lid_id'])) {
				
				if (!empty($data['mod_lid_voorletters']))
					$fullName .= ' ' . $data['mod_lid_voorletters'];
			}

			if (Vablid::cp('voornaam', $data['mod_lid_id'])) {
				
				if (!empty($data['mod_lid_voornaam']))
					$fullName .= ' (' . utf8_encode_compat($data['mod_lid_voornaam']) . ')';
			}

			if (!empty($data['mod_lid_tussenvoegsels']))
				$fullName .= ' ' . utf8_encode_compat($data['mod_lid_tussenvoegsels']);

			if (!empty($data['mod_lid_achternaam']))
				$fullName .= ' ' . utf8_encode_compat($data['mod_lid_achternaam']);

			if (Vablid::cp('certificeringen', $data['mod_lid_id']) && !$noTitles) {

				$arrShown = array();

				// Are we really going to add these elitist certified letters... yes we are. Let's mess up the database.
				$fetchCats = $db->prepare("SELECT * FROM `tbl_mod_catChain` WHERE `mod_cc_moduleTable` IN ('tbl_mod_leden', 'tbl_mod_leden_certs') AND `mod_cc_moduleId`=?", "i", array($data['mod_lid_id']));

				if (count($fetchCats) > 0) {

					foreach ($fetchCats as $key => $val) {

						if (isset($elitistCats[$val['mod_cc_categoryId']])) {

							if (!in_array($val['mod_cc_categoryId'], $arrShown)) {

								$arrShown[] = $val['mod_cc_categoryId'];
								$fullName .= ' ' . $elitistCats[$val['mod_cc_categoryId']];
							}
						}
					}
				}
			}
		}

		return $fullName;
	}

	static function generatePhoto($data) {

		global $dynamicRoot, $documentRoot;

		$old = $data['mod_lid_pasfoto'];
		$new = $data['mod_lid_pasfotoNieuw'];

		// Handle current pic
		// If still an old photo
		if (empty($new) && !empty($old)) {

			$photoUrl = $dynamicRoot . 'upload/leden/' .  str_replace(' ', '%20', $old);
		}
		elseif (!empty($new)) {

			if (strpos($new, '/media') !== false) {

				$photoUrl = $new;
			}
			else {

				if (file_exists($documentRoot . 'upload/leden_site/' . $new))
					$photoUrl = $dynamicRoot . 'upload/leden_site/' . str_replace(' ', '%20', $new);
				else
					$photoUrl = $dynamicRoot . 'upload/leden/' . str_replace(' ', '%20', $new);
			}
		}
		else {

			$photoUrl = '';
		}

		return $photoUrl;
	}

	static function cp($data, $lidId) {

		if (!isset(self::$permissionData[$lidId])) {

			self::fetchPermissions($lidId);
		}

		if (isset(self::$permissionData[$lidId]['per_' . $data])) {

			return self::$permissionData[$lidId]['per_' . $data];			
		}
		else {

			return true;
		}
	}

	static function fetchPermissions($lidId) {

		global $cms;

		if (!isset(self::$permissionData[$lidId])) {

			$perms = $cms['database']->prepare("SELECT * FROM `tbl_mod_ledenPrivacy` WHERE `mod_lpr_lidId`=?", "i", array($lidId));

			if (count($perms) > 0) {

				self::$permissionData[$lidId] = array();

				foreach ($perms as $key => $val) {

					self::$permissionData[$lidId][$val['mod_lpr_privacyName']] = $val['mod_lpr_privacySetting'];
				}
			}
			else {

				self::$permissionData[$lidId] = array();
			}
		}
	}
}

?>