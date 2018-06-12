<?php

class Content {

	/**
	 * Retrieves the values belonging to an article
	 * @param int $articleId
	 * @return array
	 */
	static function getArticleValues($articleId, $cms, $language) {
		
		// Fetch all values
		$articleData = $cms['database']->prepare("SELECT * FROM `tbl_mod_articleContentValues` LEFT JOIN `tbl_mod_articleAttributeValues` ON `mod_cv_attributeValueId`=`mod_av_id` LEFT JOIN `tbl_mod_articleAttributes` ON `mod_aa_id`=`mod_cv_attributeId` WHERE `mod_cv_articleId`=?  AND `mod_cv_languageId` IN (0, ?, 1)", "ii", array($articleId, $language));
		
		$dataArray = array();
		
		if (count($articleData) > 0) {
		
			$fallBackLang = array();
		
			// First filter the non-right languages
			if ($language != 1) {
		
				foreach ($articleData as $key => $val) {
		
					if ($val['mod_cv_languageId'] == 1) {
		
						$fallBackLang[$val['mod_cv_attributeId']] = $val;
		
						unset($articleData[$key]);
					}
				}
			}
		
			// Construct readable data
			foreach ($articleData as $key => $val) {
		
				if ($val['mod_cv_attributeValueId'] > 0) {
					
					$value = array();
		
					$value[$val['mod_cv_attributeValueId']] = $val['mod_av_value'];
				}
				elseif ($val['mod_cv_value'] != '') {
		
					$value = $val['mod_cv_value'];
				}
				elseif ($val['mod_cv_valueDate'] != '') {
		
					$value = $val['mod_cv_valueDate'];
				}
				elseif ($val['mod_cv_valueNum'] != '') {
		
					$value = $val['mod_cv_valueNum'];
				}
		
				if (empty($value) && isset($fallBackLang[$val['mod_cv_attributeId']])) {
		
					if ($fallBackLang[$val['mod_cv_attributeId']]['mod_cv_attributeValueId'] > 0) {
		
						$value = $fallBackLang[$val['mod_cv_attributeId']]['mod_av_value'];
					}
					elseif ($fallBackLang[$val['mod_cv_attributeId']]['mod_cv_value'] != '') {
		
						$value = $fallBackLang[$val['mod_cv_attributeId']]['mod_cv_value'];
					}
					elseif ($fallBackLang[$val['mod_cv_attributeId']]['mod_cv_valueDate'] != '') {
		
						$value = $fallBackLang[$val['mod_cv_attributeId']]['mod_cv_valueDate'];
					}
					elseif ($fallBackLang[$val['mod_cv_attributeId']]['mod_cv_valueNum'] != '') {
		
						$value = $fallBackLang[$val['mod_cv_attributeId']]['mod_cv_valueNum'];
					}
					else {
		
						$value = $fallBackLang[$val['mod_cv_attributeId']]['mod_cv_value'];
					}
				}
				
				if (isset($value))
					$dataArray[$val['mod_aa_fieldName']] = $value;
		
				$value = '';
			}
		}
		
		return $dataArray;
	}
	
	static function getCategories($articleId, $cms, $table, $field) {
		
		$cats = $cms['database']->prepare("SELECT * FROM `tbl_mod_catChain` INNER JOIN `tbl_mod_catData` ON `mod_cc_categoryId`=`mod_cd_id` WHERE `mod_cc_moduleTable`=? AND `mod_cc_moduleIdField`=? AND `mod_cc_moduleId`=?", "ssi", array($table, $field, $articleId));
		
		$catArr = array();
		
		if (count($cats) > 0) {
			
			foreach ($cats as $key => $val) {
				
				$catArr[$val['mod_cd_name']] = $val['mod_cd_id'];
			}
		}
		
		ksort($catArr);
		
		return $catArr;
	}
	
	static function showConnections($articleId, $cms, $showTypes = array()) {
		
		global $documentRoot, $dynamicRoot, $template;
		
		if (count($showTypes) > 0) {
			
			$extraSql = " AND `mod_ar_articleTypeId` IN (" . implode(',', $showTypes) . ")";
		}
		else {
			
			$extraSql = "";
		}
		
		// Fetch any connections it may have
		$data = $cms['database']->prepare("SELECT * FROM `tbl_mod_articleRelations` WHERE `mod_ar_mainId`=?" . $extraSql . " ORDER BY CASE WHEN `mod_ar_articleTypeId`=3 THEN 1 ELSE 0 END DESC, `mod_ar_articleTypeId` DESC", "i", array($articleId));
		
		if (count($data) > 0) {
			
			foreach ($data as $key => $val) {
				
				// See if a connection block exists for the article type
				if (file_exists($documentRoot . '/cms/blocks/connections/' . $val['mod_ar_articleTypeId'] . '.php')) {
					
					include($documentRoot . '/cms/blocks/connections/' . $val['mod_ar_articleTypeId'] . '.php');
				}
			}
		}
	}
}

?>