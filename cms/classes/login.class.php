<?php

class Login {

	static function isLoggedIn() {

		return isset($_SESSION['vab']['login']);
	}

	static function isVabMember() {

		if (Login::isLoggedIn()) {

			return ($_SESSION['vab']['login']['mod_lid_type'] != 0 && $_SESSION['vab']['login']['mod_lid_type'] != 4);
		}
		else {

			return false;
		}
	}

	static function isVabCertified() {

		if (Login::isVabMember()) {

			return ($_SESSION['vab']['login']['mod_lid_certificering'] == 1);
		}
		else {

			return false;
		}
	}

	static function getMainOrg($db) {

		if (Login::isLoggedIn()) {

			$mainOrg = $db->prepare("SELECT * FROM `tbl_mod_ledenFuncties` INNER JOIN `tbl_mod_organisaties` ON `mod_lfn_werkgeverId`=`mod_org_id` WHERE `mod_lfn_isMain`=1 AND `mod_lfn_lidId`=?", "i", array($_SESSION['vab']['login']['mod_lid_id']));

			if (count($mainOrg) > 0) {

				return $mainOrg[0];
			}
		}

		return false;
	}
}

?>