<?php

Class Inhoud {

	private $db;

	// Set up basics
	public function __construct($db) {

		$this->db = $db;
	}

	public function countReferenties() {

		$countData = Cache::get('count.referenties.inhousClass');

		if (!$countData) {

			$data = $this->db->prepare("SELECT * FROM `tbl_mod_articleContent` LEFT JOIN `tbl_cms_permaLinks` ON `cms_per_tableId`=`mod_co_id` WHERE `mod_co_articleTypeId`=5 AND EXISTS(SELECT * FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=57 AND `mod_cv_attributeValueId`=40 AND `mod_cv_articleId`=`mod_co_id`) AND `cms_per_tableName`='tbl_mod_articleContent' GROUP BY `mod_co_id`");

			if (count($data) > 0) {

				$countData = count($data);
			}
			else {

				$countData = 0;
			}

			Cache::set($countData, 'count.referenties.inhousClass', 180);
		}

		return $countData;
	}

	public function countNieuws() {

		$countData = Cache::get('count.nieuws.inhousClass');

		if (!$countData) {

			$data = $this->db->prepare("SELECT * FROM `tbl_mod_articleContent` LEFT JOIN `tbl_cms_permaLinks` ON `cms_per_tableId`=`mod_co_id` WHERE `mod_co_articleTypeId`=2 AND EXISTS(SELECT * FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=15 AND `mod_cv_attributeValueId`=5 AND `mod_cv_articleId`=`mod_co_id`) AND `cms_per_tableName`='tbl_mod_articleContent' GROUP BY `mod_co_id`");

			if (count($data) > 0) {

				$countData = count($data);
			}
			else {

				$countData = 0;
			}

			Cache::set($countData, 'count.nieuws.inhousClass', 180);
		}

		return $countData;
	}
}