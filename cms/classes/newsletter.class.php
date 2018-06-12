<?php

class Newsletter {

	private static $db;

	static function addUser($email, $groups, $db, $status, $firstname = '', $lastname = '', $fullname = '') {

		Newsletter::$db = $db;

		// Add user
		$userId = Newsletter::fetchUserId($email, $status, $firstname, $lastname, $fullname);

		// Add user to groups
		foreach ($groups as $key => $val) {

			Newsletter::addGroup($val, $userId);
		}
	}

	static function fetchUserId($email, $status, $firstname = '', $lastname = '', $fullname = '') {

		// Check if user exists
		$checkData = self::$db->prepare("SELECT `mod_ms_id` FROM `tbl_mod_mailSubscribers` WHERE `mod_ms_emailadres`=? LIMIT 1", "s", array($email));

		if (count($checkData) > 0)
			return $checkData[0]['mod_ms_id'];
		else {

			self::$db->prepare("INSERT INTO `tbl_mod_mailSubscribers` (`mod_ms_dateAdded`, `mod_ms_emailadres`, `mod_ms_naam`, `mod_ms_voornaam`, `mod_ms_achternaam`,`mod_ms_status`) VALUES(NOW(), ?, ?, ?, ?, ?)", "sssss", array($email, $fullname, $firstname, $lastname, $status));

			return self::$db->lastId();
		}
	}

	static function addGroup($group, $userId) {

		// First fetch group ID
		$fetchGroup = self::$db->prepare("SELECT `mod_mg_id` FROM `tbl_mod_mailGroups` WHERE `mod_mg_groepnaam`=? LIMIT 1", "s", array($group));

		if (count($fetchGroup) > 0) {

			// Check if user exists in group
			$fetchConnection = self::$db->prepare("SELECT `mod_mg_id`, `mod_ms_id` FROM `tbl_mod_mailSubscriberGroups` WHERE `mod_mg_id`=? AND `mod_ms_id`=?", "ii", array($fetchGroup[0]['mod_mg_id'], $userId));

			if (count($fetchConnection) == 0) {

				// Insert
				self::$db->prepare("INSERT INTO `tbl_mod_mailSubscriberGroups` (`mod_mg_id`, `mod_ms_id`) VALUES(?, ?)", "ii", array($fetchGroup[0]['mod_mg_id'], $userId));
			}
		}
	}
}

?>