<?php

// [page][section/header/block/headerfoto
// [page][navigation/1

class Templates {
	
	private $cms;
	private $pageId;
	private $moduleId = 0;
	private $language;
	private $currentPage;
	private $pageMultiValues = array();
	private $currentTemplate;
	private $blockValues = array();
	private $permaLinks;
	private $landCode;
	private $staticBlocks = array();
	private $customVars = array();
	private $previewMode = false;
	public $detailPage = 0;
	
	public function __construct($permaLink, $cms, $language = 0) {

		$this->cms = $cms;
		
		// First off find language ID
		if (count($this->cms['languages']) >= 1) {
			
			$findCode = explode('/', $permaLink);
			$this->landCode = $findCode[0];
			
			// if (count($this->cms['languages']) > 1)
			unset($findCode[0]);
			
			$permaLink = implode('/', $findCode);
		}
		else {
			
			$this->landCode = $cms['languages'][0]['cms_la_shortName'];
		}
		
		// Split permaLink (for module detail pages)
		$splitPerma = explode('/', $permaLink);
		
		// Preview mode: If first element = '_preview', set site to preview mode, meaning offline pages/articles can be viewed
		if ($splitPerma[0] == '_preview') {
			
			// Remove it
			unset($splitPerma[0]);
			
			// Unsplit it
			$permaLink = implode('/', $splitPerma);
			
			// Set site to preview mode
			$this->previewMode = true;
		}
		
		// Pop last element
		$tempPage = array_pop($splitPerma);
		
		// Reconstruct
		$modulePermaLink = implode('/', $splitPerma);
		
		// If $tempPage is a number.. we're filtering, handle it as such
		if (ctype_digit($tempPage)) {
			
			$_GET['werkveld'] = $tempPage;
			
			$splitPerma = explode('/', $modulePermaLink);
			array_pop($splitPerma);
			
			$permaLink = implode('/', $splitPerma);
			
			$modulePermaLink = '';
		}
		
		// If we're not in preview mode, only check mod_pa_status=1
		$stateCheck = '';
		
		if (!$this->previewMode)
			$stateCheck = ' AND `mod_pa_status`=1';
		
		// See if the permalink exists.. as a module ID!
			$validatePermalink = $this->cms['database']->prepare("SELECT * FROM `tbl_cms_permaLinks` LEFT JOIN `tbl_mod_pages` ON `mod_pa_id`=`cms_per_tableId` WHERE `cms_per_tableName`=? AND `cms_per_link`=? AND `mod_pa_type`=4", "ss", array(
			'tbl_mod_pages',
			$modulePermaLink
		));
		
		if (count($validatePermalink) > 0) {
			
			$this->detailPage = $tempPage;
		}
		else {
			
			$validatePermalink = $this->cms['database']->prepare("SELECT * FROM `tbl_cms_permaLinks` LEFT JOIN `tbl_mod_pages` ON `mod_pa_id`=`cms_per_tableId` WHERE `cms_per_tableName`=? AND `cms_per_link`=? AND `mod_pa_type`!=4", "ss", array(
				'tbl_mod_pages',
				$permaLink
			));
		}
		
		if (count($validatePermalink) <= 0) {
			
			header("HTTP/1.0 404 Not Found");
			
			// Fetch 404 page?
			$validatePermalink = $this->cms['database']->prepare("SELECT * FROM `tbl_cms_permaLinks` LEFT JOIN `tbl_mod_pages` ON `mod_pa_id`=`cms_per_tableId` WHERE `cms_per_tableName`=? AND `cms_per_link`=? AND `mod_pa_type`=1", "ss", array(
				'tbl_mod_pages',
				'404-pagina-niet-gevonden'
			));
		}
		
		if (count($validatePermalink) > 0) {
			
			if ($language == 0)
				$this->language = $validatePermalink[0]['cms_per_languageId'];
			else
				$this->language = $language;
			
			$this->moduleId = $validatePermalink[0]['cms_per_moduleId'];
			
			// Validate that the permalink actually links to a page
			$validatePage = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pages` WHERE `mod_pa_id`=?" . $stateCheck. " LIMIT 1", "i", array(
				$validatePermalink[0]['cms_per_tableId']
			));
			
			if (count($validatePage) > 0) {
				
				$this->pageId = $validatePage[0]['mod_pa_id'];
				$this->currentPage = $validatePage[0];
				
				// Fetch the multi-values
				$selectMultiValues = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageValues` WHERE `mod_pv_pageId`=? AND `mod_pv_languageId`=? ORDER BY `mod_pv_name` ASC", "ii", array(
					$this->pageId,
					$this->language
				));
				
				if (count($selectMultiValues) > 0) {
					
					foreach ( $selectMultiValues as $sKey => $sVal ) {
						
						$this->pageMultiValues[$sVal['mod_pv_name']] = $sVal['mod_pv_value'];
					}
				}
				
				// Fetch template
				$template = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplates` WHERE `mod_te_id`=? LIMIT 1", "i", array(
					$validatePage[0]['mod_pa_templateId']
				));
				
				if (count($template) > 0) {
					
					$this->currentTemplate = $template[0];
					
					// Fetch all values
					$blockData = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplateBlockValues` WHERE `mod_bv_languageId` IN (0, ?) AND `mod_bv_pageId`=? ORDER BY `mod_bv_name` ASC", "ii", array(
						$this->language,
						$this->pageId
					));
					
					if (count($blockData) > 0) {
						
						foreach ( $blockData as $key => $val ) {
							
							$this->blockValues[$val['mod_bv_blockId']][$val['mod_bv_languageId']][$val['mod_bv_name']] = $val['mod_bv_value'];
						}
					}
					
					// Fetch remaining language permalinks
					$this->permaLinks = array();
					
					$this->permaLinks[$this->language] = $permaLink;
					
					if (count($this->cms['languages']) > 1) {
						
						// Fetch remaining permaLinks
						$permaLinks = $this->cms['database']->prepare("SELECT * FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableField`='mod_pa_id' AND `cms_per_tableId`=? AND `cms_per_languageId`!=?", "ii", array(
							$this->pageId,
							$this->language
						));
						
						if (count($permaLinks) > 0) {
							
							foreach ( $permaLinks as $key => $val ) {
								
								$this->permaLinks[$val['cms_per_languageId']] = $val['cms_per_link'];
							}
						}
						
						// Check if we're on the right permalink
					
					}
				}
				else
					Core::redirect('/');
			}
			else
				Core::redirect('/');
		}
		else
			Core::redirect('/');
	}
	
	public function previewMode() {
		
		return $this->previewMode;
	}
	
	public function setCustomVar($varName, $value) {

		$this->customVars[$varName] = $value;
		
		return true;
	}
	
	public function getCustomVar($varName) {

		if (isset($this->customVars[$varName]))
			return $this->customVars[$varName];
		else
			return false;
	}
	
	public function correctLanguage($langId) {

		$this->language = $langId;
	}
	
	public function handleRedirect() {

		// Check if page is a redirect
		if ($this->currentPage['mod_pa_type'] == 2 && $this->currentPage['mod_pa_redirectValue'] >= 1) {
			
			Core::redirect($this->findPermalink($this->currentPage['mod_pa_redirectValue'], 1));
		}
		// External redirect?
		elseif ($this->currentPage['mod_pa_type'] == 3 && $this->currentPage['mod_pa_redirectValue'] != '') {
			
			$redirectUrl = str_replace(array(
				'https:///',
				'http:///'
			), array(
				'/',
				'/'
			), $this->currentPage['mod_pa_redirectValue']);
			
			Core::redirect($redirectUrl);
		}
	}
	
	public function generatePermaLink($contentId, $contentTitle, $languageId) {

		$permaTitle = Core::replaceSpecialChars($contentTitle, 'permaLink');
		
		// First of all, fetch page ID
		$pageId = $this->cms['database']->prepare("SELECT `mod_at_pageId` FROM `tbl_mod_articleTypes` WHERE `mod_at_id`=?", "i", array(
			$contentId
		));
		
		if (count($pageId) > 0) {
			
			// Fetch actual permaLink!
			$permaLink = $this->findPermalink($pageId[0]['mod_at_pageId'], 1);
			
			// Construct full one
			return $permaLink . '/' . $permaTitle;
		}
	}
	
	public function getCurrentPageURL() {

		return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	
	public function getModuleId() {

		return $this->moduleId;
	}
	
	public function cmsData($data, $extraData = '') {

		// Figure out what root part we're in
		$rootParts = explode('][', $data);
		
		// Switch
		switch ($rootParts[0]) {
			
			case 'page' :
				
				// Split up the $rootParts[1] part (variables etc.)
				$dataArray = explode('/', $rootParts[1]);
				
				// Case on $dataArray[0]
				switch ($dataArray[0]) {
					
					case 'section' :
						
						$returnData = $this->handleSection($dataArray);
						break;
					case 'navigation' :
						
						$returnData = $this->handleNavigation($dataArray, $extraData);
						break;
					case 'global' :
						
						$returnData = $this->handleGlobal($dataArray);
						break;
				}
				
				break;
		}
		
		if (! isset($returnData))
			return 'Error 101.1: Opgegeven data-element kon niet gevonden worden (' . $data . ').';
		else
			return $returnData;
	}
	
	public function getPageData($var) {

		return (isset($this->currentPage['mod_pa_' . $var])) ? $this->currentPage['mod_pa_' . $var] : false;
	}
	
	public function getPageDataMulti($var) {

		return (isset($this->pageMultiValues[$var])) ? $this->pageMultiValues[$var] : false;
	}
	
	public function getData($var, $blockId, $returnType = 0) {

		// Rewrite to involve language ID 0 (default values)
		if (! isset($this->blockValues[$blockId][$this->language])) {
			
			if (isset($this->blockValues[$blockId][0][$var])) {
				
				$returnValue = $this->handlePlaceHolders($this->blockValues[$blockId][0][$var]);
				
				if ($returnType == 1)
					echo $returnValue;
				
				return $returnValue;
			}
			else {
				
				$blockData = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplateBlockValues` WHERE `mod_bv_blockId`=? AND `mod_bv_languageId` IN (0, ?) AND `mod_bv_pageId`=? ORDER BY `mod_bv_name` ASC", "iii", array(
					$blockId,
					$this->language,
					$this->pageId
				));
				
				if (count($blockData) > 0) {
					
					$this->blockValues[$blockId] = array();
					
					foreach ( $blockData as $key => $val ) {
						
						$this->blockValues[$blockId][$this->language][$val['mod_bv_name']] = $val['mod_bv_value'];
					}
				}
			}
		}
		
		if (! isset($this->blockValues[$blockId][$this->language][$var])) {
			
			if (isset($this->blockValues[$blockId][0][$var])) {
				
				$returnValue = $this->handlePlaceHolders($this->blockValues[$blockId][0][$var]);
				
				if ($returnType == 1)
					echo $returnValue;
				
				return $returnValue;
			}
			else {
				
				if ($returnType == 1)
					echo 'status:error';
				
				return false;
			}
		}
		else {
			
			$returnValue = $this->handlePlaceHolders($this->blockValues[$blockId][$this->language][$var]);
			
			if ($returnType == 1)
				echo $returnValue;
			
			return $returnValue;
		}
	}
	
	public function findParent() {

		$tree = new NestedTree('tbl_mod_pages', 'mod_pa_', $this->cms['database'], 'sortOrder');
		
		$singlePath = $tree->singlePath($this->pageId);
		
		$count = count($singlePath);
		
		if ($count >= 2)
			return $singlePath[($count - 2)]['id'];
		else
			return false;
	}
	
	public function findHighestParent($pageId = 0) {

		if ($pageId == 0)
			$pageId = $this->pageId;
		
		$tree = new NestedTree('tbl_mod_pages', 'mod_pa_', $this->cms['database'], 'sortOrder');
		
		$singlePath = $tree->singlePath($pageId);
		
		$count = count($singlePath);
		
		if ($count >= 2) {
			
			if ($singlePath[($count - 2)]['id'] != 1) {
				
				return $this->findHighestParent($singlePath[($count - 2)]['id']);
			}
			else
				return $pageId;
		}
		else
			return false;
	}
	
	/**
	 * Retrieves the values belonging to an article
	 * 
	 * @param int $articleId        	
	 * @return array
	 */
	public function getArticleValues($articleId) {

		// Fetch all values
		$articleData = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_articleContentValues` LEFT JOIN `tbl_mod_articleAttributeValues` ON `mod_cv_attributeValueId`=`mod_av_id` LEFT JOIN `tbl_mod_articleAttributes` ON `mod_aa_id`=`mod_cv_attributeId` WHERE `mod_cv_articleId`=?  AND `mod_cv_languageId` IN (0, ?, 1)", "ii", array(
			$articleId,
			$this->getCurrentLanguage()
		));
		
		$dataArray = array();
		
		if (count($articleData) > 0) {
			
			$fallBackLang = array();
			
			// First filter the non-right languages
			if ($this->getCurrentLanguage() != 1) {
				
				foreach ( $articleData as $key => $val ) {
					
					if ($val['mod_cv_languageId'] == 1) {
						
						$fallBackLang[$val['mod_cv_attributeId']] = $val;
						
						unset($articleData[$key]);
					}
				}
			}
			
			// Construct readable data
			foreach ( $articleData as $key => $val ) {
				
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
	
	/**
	 * Returns a URL to an article based on its overview etc.
	 * 
	 * @param int $articleId        	
	 * @return string The URL
	 */
	public function getArticleUrl($articleId) {

		$url = '';
		
		// Find the article, permalink and page type
		$article = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_articleContent` INNER JOIN `tbl_mod_articleTypes` ON `mod_co_articletypeId`=`mod_at_id` INNER JOIN `tbl_cms_permaLinks` ON (`cms_per_tableId`=`mod_co_id` AND `cms_per_tableName`='tbl_mod_articleContent') WHERE `mod_co_id`=?", "i", array(
			$articleId
		));
		
		if (count($article) > 0) {
				
			// Check if the page actually has a sub page
			if (! is_null($article[0]['mod_at_pageId'])) {
				
				// Build the URL
				$url = $this->findPermalink($article[0]['mod_at_pageId'], 1) . '/' . $article[0]['cms_per_link'];
				
				return $url;
			}
		}
		
		return false;
	}
	
	/**
	 * Makes clever use of $_SERVER['HTTP_REFERER'] to find a back URL
	 */
	public function getBackUrl($match) {
		
		if (!empty($_SERVER['HTTP_REFERER'])) {
			
			$referer = $_SERVER['HTTP_REFERER'];
			$getMain = $this->cms['database']->prepare("SELECT * FROM `tbl_cms_websites` WHERE `cms_wb_id`=1");
			
			if (strpos(strtolower($referer), strtolower($match)) !== false) {
				
				return $referer;
			}
		}
		
		return false;
	}
	
	public function handlePlaceHolders($data) {

		// Find all page IDs
		preg_match_all('/{{pageId_([0-9]*)}}/', $data, $pageIds);
		
		if (isset($pageIds[1]) && count($pageIds[1]) > 0) {
			
			foreach ( $pageIds[1] as $key => $val ) {
				
				// Figure out the belonging permalink in the proper language
				$permaLink = $this->findPermalink($val);
				
				// Add baseURL
				if ($permaLink !== false) {
					
					$permaLink = $this->getBaseUrl() . $permaLink;
					
					// Replace it
					$data = str_replace($pageIds[0][$key], $permaLink, $data);
				}
				
				// Reset permalink
				$permaLink = false;
			}
		}
		
		// Find all article IDs
		preg_match_all('/{{articleId_(.*?)}}/', $data, $articleIds);
		
		if (isset($articleIds[1]) && count($articleIds[1]) > 0) {
			
			foreach ( $articleIds[1] as $key => $val ) {
				
				$data = str_replace($articleIds[0][$key], $this->getArticleUrl($val), $data);
			}
		}
		
		// Handle $dynamicRoot
		$data = str_replace('{{dynamicRoot}}', '/', $data);
		
		return $data;
	}
	
	public function findPermalink($pageId, $full = 0) {

		// See if it exists
		$checkLink = $this->cms['database']->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=? AND `cms_per_languageId`=? LIMIT 1", "ii", array(
			$pageId,
			$this->language
		));
		
		if (count($checkLink) > 0) {
			
			return ($full == 0) ? $checkLink[0]['cms_per_link'] : $this->getBaseUrl($this->language) . $checkLink[0]['cms_per_link'];
		}
		else {
			
			// If language is not default, try to see if default language exists
			if ($this->language != $this->cms['languages'][0]['cms_la_id']) {
				
				$checkLink = $this->cms['database']->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=? AND `cms_per_languageId`=? LIMIT 1", "ii", array(
					$pageId,
					$this->cms['languages'][0]['cms_la_id']
				));
				
				if (count($checkLink) > 0) {
					
					return ($full == 0) ? $checkLink[0]['cms_per_link'] : $this->getBaseUrl($this->language) . $checkLink[0]['cms_per_link'];
				}
				else {
					
					return false;
				}
			}
			else {
				
				return false;
			}
		}
	}
	
	/**
	 * Function added 2017-03-09 to build URLs based on link data
	 * 
	 * @param array $data
	 *        	Array containing the required data
	 * @return boolean|string Returns false if no URL found
	 */
	public function buildDynamicUrl($data, $type = 1) {

		// If type != 1, construct own array
		if ($type != 1) {
			
			$explode = explode(']][[', $data);
			
			if (count($explode) != 5)
				return false;
			
			// Bad, bad, overwrite value with an empty array
			$data = array();
			$data['inhoudTypeChoice'] = $explode[0];
			$data['linkType'] = $explode[1];
			$data['internalRedirect'] = $explode[2];
			$data['externUrl'] = $explode[3];
			$data['inhoudTypeHidden'] = $explode[4];
		}
		
		if ($data['linkType'] == 1) {
			
			$realUrl = $data['externUrl'];
		}
		elseif ($data['linkType'] == 0) {
			
			// Find permalink for banner
			$findLink = $this->cms['database']->prepare("SELECT * FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableField`='mod_pa_id' AND `cms_per_tableId`=? AND `cms_per_languageId` IN (0, ?) LIMIT 1", "ii", array(
				$data['internalRedirect'],
				$this->getCurrentLanguage()
			));
			
			if (count($findLink) > 0) {
				
				$realUrl = $this->getBaseURL() . $findLink[0]['cms_per_link'];
			}
		}
		elseif ($data['linkType'] == 2) {
			
			$realUrl = $this->getArticleUrl($data['inhoudTypeHidden']);
			
			// Find permaLink
// 			$permaLink = $this->cms['database']->prepare("SELECT * FROM `tbl_cms_permaLinks` WHERE `cms_per_tableId`=? AND `cms_per_TableName`=?", "is", array(
// 				$data['inhoudTypeHidden'],
// 				'tbl_mod_articleContent'
// 			));
			
// 			if (count($permaLink) > 0) {
				
// 				// Find the page it belongs to
// 				$page = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_articleContent` INNER JOIN `tbl_mod_articleTypes` ON `mod_at_id`=`mod_co_articletypeId` WHERE `mod_co_id`=?", "i", array(
// 					$data['inhoudTypeHidden']
// 				));
				
// 				if (count($page) > 0) {
					
// 					if (! is_null($page[0]['mod_at_pageId']))
// 						$realUrl = $this->findPermalink(6, 1) . '/' . $permaLink[0]['cms_per_link'];
// 				}
// 			}
		}
		elseif ($data['linkType'] != 3) {
			
			$realUrl = $this->findPermalink($data['internalRedirect'], 1);
		}
		
		if (! isset($realUrl)) {
			
			return false;
		}
		
		// Check if a mailto exists
		if (strpos($realUrl, 'mailto:')) {
			
			$realUrl = str_replace(array('http://', 'https://'), array('', ''), $realUrl);
		}
		
		return $realUrl;
	}
	
	public function insertWidget($value) {

		ob_start();
		include ('/cms/blocks/widgets/' . $value . '.php');
		return ob_get_clean();
	}
	
	public function handleNavigation($data, $extraData = '') {

		global $documentRoot;
		
		$usePageId = $this->pageId;
		
		// Fetch nav
		if (file_exists($documentRoot . 'cms/navigaties/' . $data[1] . '_' . $this->language . '.txt'))
			$navData = json_decode(file_get_contents($documentRoot . 'cms/navigaties/' . $data[1] . '_' . $this->language . '.txt'), true);
		
		if (isset($data[2]) && $data[2] == 'subnav') {
			
			$foundLeft = 0;
			$foundRight = 0;
			
			// Fetch left and right ID of subnav, find proper topnav
			foreach ( $navData as $key => $val ) {
				
				if ($val['mod_pa_id'] == $data[3]) {
					
					$foundLeft = $val['mod_pa_left'];
					$foundRight = $val['mod_pa_right'];
				}
			}
			
			// Now build the nav
			$ulStructure = '<ul class="sidebar-nav">' . PHP_EOL;
			
			$orderArray = array();
			
			$sortDepth = 0;
			$prevDepth = 1;
			
			foreach ( $navData as $key => $val ) {
				
				if ($val['mod_pa_left'] > $foundLeft && $val['mod_pa_right'] < $foundRight) {
				
					if ($prevDepth != $val['depth']) {
						
						$sortDepth ++;
						
						$prevDepth = $val['depth'];
					}
					
					$key = $val['mod_pa_sortOrder'];
						
					$orderArray[$sortDepth . '_' . $key] = $val;
				}
			}
			
			ksort($orderArray);
			
			foreach ( $orderArray as $key => $val ) {
				
				if (isset($this->realCrumbs))
					$usePageId = $this->realCrumbs;
				
				if (isset($data[4], $data[5]) && $data[4] == 'active')
					$usePageId = $data[5];
				
				$active = ($val['mod_pa_id'] == $usePageId) ? ' class="active"' : '';
				
				if (isset($val['mod_pa_redirectValue']) && ! empty($val['mod_pa_redirectValue']) && $val['mod_pa_type'] == 3 && $val['mod_pa_redirectValue'] == '#') {
					$url = '#';
					$target = '';
				}
				elseif (isset($val['mod_pa_redirectValue']) && ! empty($val['mod_pa_redirectValue']) && $val['mod_pa_type'] != 2) {
					if (substr($val['mod_pa_redirectValue'], 0, 5) == 'https') {
						
						$url = $val['mod_pa_redirectValue'];
					}
					else {
						
						$url = 'https://' . $val['mod_pa_redirectValue'];
					}
					
					$target = " target=\"_BLANK\"";
				}
				elseif (substr($val['permalink'], 0, 10) == 'javascript') {
					$url = $val['permalink'];
					$target = "";
				}
				else {
					$url = $this->getBaseUrl() . $val['permalink'];
					$target = "";
				}
				
				if ($val['depth'] == 2) {
					
					if (empty($active))
						$active = ' class="sub"';
					else
						$active = str_replace('active', 'active sub', $active);
				}
				
				$ulStructure .= '<li' . $active . '><a href="' . $url . '" title="' . $val['mod_pa_nav'] . '"' . $target . '>' . $val['mod_pa_nav'] . '</a></li>' . PHP_EOL;
			}
			
			$ulStructure .= '</ul>';
			
			return $ulStructure;
		
		}
		else {
			
			// Fetch nav
			if (file_exists($documentRoot . 'cms/navigaties/' . $data[1] . '_' . $this->language . '.txt'))
				$navData = json_decode(file_get_contents($documentRoot . 'cms/navigaties/' . $data[1] . '_' . $this->language . '.txt'), true);
			
			if (count($navData) > 0) {
				
				if (isset($data[2], $data[3]) && $data[2] == 'navType') {
					
					$navType = $data[3];
				}
				else {
					
					$navType = 'navMain';
				}
				
				// Basic set-up
				// $ulStructure = '<nav id="' . $navType . '" role="navigation">' . PHP_EOL;
				$ulStructure = '' . PHP_EOL;
				
				// Loop through
				$lastDepth = - 1;
				$oldDepth = 0;
				$counter = 0;
				$dataRel = array();
				
				foreach ( $navData as $key => $val ) {

					$extraClass = ($val['depth'] != 0) ? ' class="hover-menu-wrapper"' : '';
					
					if ($val['depth'] != $lastDepth) {
						
						if ($val['depth'] > $lastDepth) {
							
							$ulStructure .= '<ul' . $extraClass . '>';
							
							$dataRel[] = $val['mod_pa_id'];
						}
						else {
							
							$depthDifference = $lastDepth - $val['depth'];
							
							for($i = 1; $i <= $depthDifference; $i ++)
								$ulStructure .= '</ul></li>';
							array_pop($dataRel);
						}
						
						if ($val['depth'] == 1 && $lastDepth == 0)
							$oldDepth = 1;
						
						$lastDepth = $val['depth'];
					}
					elseif ($val['depth'] == $lastDepth) {
						
						array_pop($dataRel);
						$dataRel[] = $val['mod_pa_id'];
					}
					
					// "Predict" next node (to know if we close /li)
					if ((isset($navData[$counter + 1]) && $navData[$counter + 1]['depth'] <= $val['depth']) || ! isset($navData[$counter + 1])) {
						
						$isDir = true;
					}
					else {
						
						$isDir = false;
					}
					
					$classLi = "";
					
					$dirClass = (isset($isDir) && ! $isDir) ? 'dir' : '';
					
					if ((isset($data[2], $data[3]) && $data[2] == 'active') || (isset($data[4], $data[5]) && $data[4] == 'active')) {
						
						if ($data[2] == 'active')
							$usePageId = $data[3];
						else
							$usePageId = $data[5];
					}
					
					$active = ($val['mod_pa_id'] == $usePageId) ? 'active' : '';
					
					$fullClass = trim($dirClass);
					
					if (isset($val['mod_pa_redirectValue']) && ! empty($val['mod_pa_redirectValue']) && $val['mod_pa_type'] == 3 && $val['mod_pa_redirectValue'] == '#') {
						$url = '#';
						$target = '';
					}
					elseif (isset($val['mod_pa_redirectValue']) && ! empty($val['mod_pa_redirectValue']) && $val['mod_pa_type'] != 2) {
						if (substr($val['mod_pa_redirectValue'], 0, 5) == 'https') {
							
							$url = $val['mod_pa_redirectValue'];
						}
						else {
							
							$url = 'https://' . $val['mod_pa_redirectValue'];
						}
						
						$target = " target=\"_BLANK\"";
					}
					elseif (substr($val['permalink'], 0, 10) == 'javascript') {
						$url = $val['permalink'];
						$target = "";
					}
					else {
						$url = $this->getBaseUrl() . $val['permalink'];
						$target = "";
					}
					
					if (! $isDir && $val['depth'] == 0) {
						
						// $url = 'javascript:void(0);';
						$fullClass .= ' topLi';
					}
					
					if (! empty($fullClass)) {
						
						$classLi = ' class="' . $fullClass . '"';
					}
					
					// if ($val['depth'] == 0)
					// $url = 'javascript:void(0);';
					
					$extraAttr = '';
					
					// Handle some $extraData
					// "1" means the main parent navs are no links, but instead expand the subnav
					if ($extraData == 1) {
						
						if (! $isDir && $val['depth'] == 0) {
							
							// $extraAttr = ' data-type="expandNext"';
							// $url = 'javascript:void(0);';
						}
					}
					
					// If a class linked
					$classLi = '';
					
					if (isset($val['additional_data']['mod_np_addClass']) && ! empty($val['additional_data']['mod_np_addClass'])) {
						
						$classLi = ' class="' . $val['additional_data']['mod_np_addClass'] . ' ' . $active. '"';
					}
					else {
						
						$classLi = ' class="' . $active. '"';
					}
					
					$aAction = '';
					
					// If a special action linked
					if (isset($val['additional_data']['mod_np_addAction']) && ! empty($val['additional_data']['mod_np_addAction'])) {
						
						$aAction = $val['additional_data']['mod_np_addAction'];
					}
					
					if (isset($data[2], $data[3]) && $data[2] == 'special' && $data[3] == 'bubbles') {
						
						$ulStructure .= '<li' . $classLi . '>';
						
						$ulStructure .= '<a href="' . $url . $aAction . '" title="' . $val['mod_pa_nav'] . '"' . $target . '' . $extraAttr . '><span class="bubble"><span>&nbsp;</span></span><span class="text">' . $val['mod_pa_nav'] . '</span></a>';
					}
					else {
						
						$ulStructure .= '<li' . $classLi . '><a href="' . $url . $aAction . '" title="' . $val['mod_pa_nav'] . '"' . $target . '' . $extraAttr . '>';
						
						if (file_exists($documentRoot . 'inc/svg-nav/' . $val['mod_pa_admin_value'] . '.php')) {
							
							include($documentRoot . 'inc/svg-nav/' . $val['mod_pa_admin_value'] . '.php');
						}
						
						$ulStructure .= '&nbsp;' . $val['mod_pa_nav'] . '</a>';
					}
					
					if (isset($isDir) && $isDir)
						$ulStructure .= '</li>';
					
					$counter ++;
					$oldDepth = 0;
				}
				
				for($i = 1; $i <= $lastDepth; $i ++) {
					
					$ulStructure .= '</ul>';
					$ulStructure .= '</li>';
				}
				
				$ulStructure .= '</ul>'; // </nav>';
			}
			
			return $ulStructure;
		}
	}
	
	public function generateSitemap($navId) {

		global $documentRoot;
		
		// Fetch nav
		if (file_exists($documentRoot . 'cms/navigaties/' . $navId . '_' . $this->language . '.txt'))
			$navData = json_decode(file_get_contents($documentRoot . 'cms/navigaties/' . $navId . '_' . $this->language . '.txt'), true);
		
		if (count($navData) > 0) {
			
			// Basic set-up
			$ulStructure = '';
			
			// Loop through
			$lastDepth = - 1;
			$counter = 0;
			$dataRel = array();
			
			foreach ( $navData as $key => $val ) {
				
				if ($val['depth'] != $lastDepth) {
					
					if ($val['depth'] > $lastDepth) {
						
						if ($lastDepth == - 1)
							$class = ' class="sitemap"';
						else
							$class = '';
						
						$ulStructure .= '<ul' . $class . '>' . PHP_EOL;
						$dataRel[] = $val['mod_pa_id'];
					}
					else {
						$ulStructure .= '</ul></li>' . PHP_EOL;
						array_pop($dataRel);
					}
					
					$lastDepth = $val['depth'];
				}
				elseif ($val['depth'] == $lastDepth) {
					
					array_pop($dataRel);
					$dataRel[] = $val['mod_pa_id'];
				}
				
				// "Predict" next node (to know if we close /li)
				if ((isset($navData[$counter + 1]) && $navData[$counter + 1]['depth'] <= $val['depth']) || ! isset($navData[$counter + 1])) {
					
					$isDir = true;
				}
				else {
					
					$isDir = false;
				}
				
				$dirClass = (isset($isDir) && ! $isDir) ? 'dir' : '';
				
				if (isset($val['mod_pa_redirectValue']) && ! empty($val['mod_pa_redirectValue']) && $val['mod_pa_type'] == 3 && $val['mod_pa_redirectValue'] == '#') {
					$url = '#';
					$target = '';
				}
				elseif (isset($val['mod_pa_redirectValue']) && ! empty($val['mod_pa_redirectValue']) && $val['mod_pa_type'] != 2) {
					$url = 'https://' . $val['mod_pa_redirectValue'];
					$target = "_BLANK";
				}
				elseif (substr($val['permalink'], 0, 10) == 'javascript') {
					$url = $val['permalink'];
					$target = "";
				}
				else {
					
					if (strpos($val['permalink'], '/') !== false)
						$url = $this->getBaseUrl() . $val['permalink'];
					else
						$url = $this->getBaseUrl() . $val['permalink'] . '&ogType=' . $this->retrieveOGType();
					
					$target = "";
				}
				
				$ulStructure .= '<li class="' . $dirClass . '"><a href="' . $url . '" title="' . $val['mod_pa_nav'] . '" target="' . $target . '">' . $val['mod_pa_nav'] . '</a>' . PHP_EOL;
				
				if (isset($isDir) && $isDir)
					$ulStructure .= '</li>' . PHP_EOL;
				
				$counter ++;
			}
			
			for($i = 1; $i <= $lastDepth; $i ++) {
				
				$ulStructure .= '</ul>' . PHP_EOL;
				$ulStructure .= '</li>' . PHP_EOL;
			}
			
			$ulStructure .= '</ul>';
		}
		
		return $ulStructure;
	}
	
	public function getBaseUrl($lang = -1) {

		$languageId = ($lang == - 1) ? $this->language : $lang;
		
		// Grab current language ID (if multi-lang)
		$langCode = (count($this->cms['languages']) > 1) ? '/' . $this->cms['languages'][($languageId - 1)]['cms_la_shortName'] . '/' : '/';
		
		return $langCode;
	}
	
	public function getTemplateId() {
		
		if (isset($this->currentPage['mod_pa_templateId'])) {
			
			return $this->currentPage['mod_pa_templateId'];
		}
		
		return false;
	}
	
	public function getTemplateData($var) {

		return (isset($this->currentTemplate['mod_te_' . $var])) ? $this->currentTemplate['mod_te_' . $var] : 'Error 102.1: Template data niet gevonden.';
	}
	
	public function getCurrentLanguage() {

		return $this->language;
	}
	
	public function getPageId() {

		return $this->pageId;
	}
	
	public function getPermaLink($langId, $full = 0) {

		if ($full == 0)
			return (isset($this->permaLinks[$langId])) ? $this->permaLinks[$langId] : $this->permaLinks[$this->language];
		else
			return (isset($this->permaLinks[$langId])) ? $this->getBaseUrl($langId) . $this->permaLinks[$langId] : $this->getBaseUrl($langId) . $this->permaLinks[$this->language];
	}
	
	private function handleSection($data) {

		// First figure out how deep we're looking for data
		if (isset($data[2]) && $data[2] == 'block') {
			
			// We're looking for a specific block, fetch it by cmsLabel
			$findLabel = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplateBlockValues` WHERE `mod_bv_pageId`=? AND `mod_bv_name`='cmsLabel' AND `mod_bv_value`=? LIMIT 1", "is", array(
				$this->pageId,
				$data[3]
			));
			
			$blockData = '';
			
			if (count($findLabel) > 0) {
				
				// Fetch the proper block
				$fetchBlock = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplatePositionBlocks` LEFT JOIN `tbl_mod_pageTemplatePositionRows` ON `mod_tr_id`=`mod_tb_rowId` LEFT JOIN `tbl_mod_pageTemplateBlocks` ON `mod_tb_blockId`=`mod_bl_id` WHERE `mod_tb_id`=? LIMIT 1", "i", array(
					$findLabel[0]['mod_bv_blockId']
				));
				
				// Set block ID
				$blockId = $fetchBlock;
				
				// Add to static blockData array (if none existing)
				if (! isset($this->staticBlocks[$fetchBlock[0]['mod_bl_frontTemplate'] . '_' . $fetchBlock[0]['mod_tb_id']]))
					$this->staticBlocks[$fetchBlock[0]['mod_bl_frontTemplate'] . '_' . $fetchBlock[0]['mod_tb_id']] = $fetchBlock[0];
				
				$blockData .= $this->returnOutput("cms/blocks/" . $fetchBlock[0]['mod_bl_frontTemplate'] . ".php", $fetchBlock[0]['mod_tb_id'], '', array(
					$fetchBlock[0]['mod_tr_type'],
					$fetchBlock[0]['mod_tb_size']
				)) . PHP_EOL;
			}
			
			return $blockData;
		}
		elseif (isset($data[2]) && $data[2] == 'var') {
			
			// We're looking for a specific variable
			//$findLabel = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplateBlockValues` WHERE `mod_bv_pageId`=? AND `mod_bv_name`='cmsLabel' AND `mod_bv_value`=? LIMIT 1", "is", array($this->pageId, $data[3]));
			
			// First grab the row
			$fetchRow = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplatePositionRows` LEFT JOIN `tbl_mod_pageTemplatePositions` ON `mod_tr_positionId`=`mod_ts_id` WHERE `mod_ts_templateId`=? AND `mod_tr_name`=? LIMIT 1", "is", array($this->currentTemplate['mod_te_id'], $data[1]));
			
			if (count($fetchRow) > 0) {
				
				$fetchBlocks = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplatePositionBlocks` LEFT JOIN `tbl_mod_pageTemplateBlocks` ON `mod_tb_blockId`=`mod_bl_id` WHERE `mod_tb_rowId`=? AND `mod_tb_customId` IN (0, " . $this->pageId . ") ORDER BY `mod_tb_blockOrder` ASC, `mod_tb_customId` ASC", "i", array($fetchRow[0]['mod_tr_id']));
				
				if (count($fetchBlocks) > 0) {
					
					// Now fetch the variable
					$findVar = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplateBlockValues` WHERE `mod_bv_blockId`=? AND `mod_bv_pageId`=? AND `mod_bv_name`=? LIMIT 1", "iis", array($fetchBlocks[0]['mod_tb_id'], $this->pageId, $data[3]));
					
					if (count($findVar) > 0) {
						
						return $findVar[0]['mod_bv_value'];
					}
					else {
						
						return '';
					}
				}
			}
		}
		else {
			
			// We're looking for ALL blocks, loop through them, add them
			$fetchRow = Cache::get('template.class.fetchRow.' . $this->pageId . '.' . $data[1] . '.' . $this->language);
			
			if (! $fetchRow) {
				
				$fetchRow = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplatePositionRows` LEFT JOIN `tbl_mod_pageTemplatePositions` ON `mod_tr_positionId`=`mod_ts_id` WHERE `mod_ts_templateId`=? AND `mod_tr_name`=? LIMIT 1", "is", array(
					$this->currentTemplate['mod_te_id'],
					$data[1]
				));
				
				Cache::set($fetchRow, 'template.class.fetchRow.' . $this->pageId . '.' . $data[1] . '.' . $this->language, 60);
			}
			
			if (count($fetchRow) > 0) {
				
				// Fetch all blocks belonging to the row
				$fetchBlocks = Cache::get('template.class.fetchBlocks.' . $this->pageId . '.' . $fetchRow[0]['mod_tr_id'] . '.' . $this->language);
				
				if (! $fetchBlocks) {
					
					$fetchBlocks = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageTemplatePositionBlocks` LEFT JOIN `tbl_mod_pageTemplateBlocks` ON `mod_tb_blockId`=`mod_bl_id` WHERE `mod_tb_rowId`=? AND `mod_tb_customId` IN (0, " . $this->pageId . ") ORDER BY `mod_tb_blockOrder` ASC, `mod_tb_customId` ASC", "i", array(
						$fetchRow[0]['mod_tr_id']
					));
					
					Cache::set($fetchBlocks, 'template.class.fetchBlocks.' . $this->pageId . '.' . $fetchRow[0]['mod_tr_id'] . '.' . $this->language, 60);
				}
				
				if (count($fetchBlocks) > 0) {
					
					$blockData = '';
					
					// Create wrapper (if any)
					if ($fetchRow[0]['mod_tr_type'] == 0) {
						
						$blockData .= '<section class="' . $fetchRow[0]['mod_tr_idName'] . '">' . PHP_EOL;
					}
					elseif ($fetchRow[0]['mod_tr_type'] == 1) {
						
						$blockData .= '<div class="' . $fetchRow[0]['mod_tr_idName'] . '">' . PHP_EOL;
					}
					
					$totalSize = 0;
					$countData = 0;
					
					// Loop through blocks
					foreach ( $fetchBlocks as $key => $val ) {
						
						// Add to static blockData array (if none existing)
						if (! isset($this->staticBlocks[$val['mod_bl_frontTemplate'] . '_' . $val['mod_tb_id']]))
							$this->staticBlocks[$val['mod_bl_frontTemplate'] . '_' . $val['mod_tb_id']] = $val;
						
						$totalSize += $val['mod_tb_size'];
						
						if ($totalSize == 99 || $totalSize == 100) {
							
							$totalSize = 0;
							$lastClass = 'last';
						}
						else {
							
							$lastClass = '';
						}
						
						$supData = $this->returnOutput("cms/blocks/" . $val['mod_bl_frontTemplate'] . ".php", $val['mod_tb_id'], $lastClass, array(
							$fetchRow[0]['mod_tr_type'],
							$val['mod_tb_size']
						));
						
						if (! empty($supData)) {
							
							$blockData .= $this->returnOutput("cms/blocks/" . $val['mod_bl_frontTemplate'] . ".php", $val['mod_tb_id'], $lastClass, array(
								$fetchRow[0]['mod_tr_type'],
								$val['mod_tb_size']
							)) . PHP_EOL;
							$countData ++;
						}
					}
					
					// Close wrapper (if any)
					if ($fetchRow[0]['mod_tr_type'] == 0) {
						
						$blockData .= '</section>' . PHP_EOL;
					}
					elseif ($fetchRow[0]['mod_tr_type'] == 1) {
						
						$blockData .= '</div>' . PHP_EOL;
					}
					
					return ($countData > 0) ? $blockData : '';
				}
			
			}
			else
				return 'Error 101.3: Opgegeven section kon niet gevonden worden (' . implode('/', $data) . ').';
		}
	}
	
	private function returnOutput($file, $blockId, $lastClass = '', $rowType = array(2, 100)) {

		global $documentRoot;
		
		ob_start();
		include $documentRoot . $file;
		return ob_get_clean();
	}
}

?>