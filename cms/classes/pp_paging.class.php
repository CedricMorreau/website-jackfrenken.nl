<?php

class PP_Paging {
	
	private $db;
	private $paging = array();
	private $currentPage;
	private $query = array();
	private $templateData = array();
	private $filters = array();
	private $joins = array();
	private $order;
	private $pageUrl = '';
	
	public function __construct(Database $db, $query, $params, $values, $pageUrl, $perPage = 10, $currentPage = 1) {
		
		$this->db = $db;
		
		$this->pageUrl = $pageUrl;
		
		$this->query['q'] = $query;
		$this->query['p'] = $params;
		$this->query['v'] = $values;
		
		$this->paging['perPage'] = (!empty($_GET['pageSize'])) ? $_GET['pageSize'] : $perPage;
		
		if (!FormValidate::numeric($this->paging['perPage']))
			$this->paging['perPage'] = 10;
			
			$this->currentPage = $currentPage;
			
			// Append the page size
			$this->pageUrl = $this->appendUrl($this->pageUrl, 'pageSize=' . $this->paging['perPage']);
	}
	
	public function addFilter($uniqueName, $filter, $pageVal) {
		
		// If $uniqueName exists, overwrite it
		$this->filters[$uniqueName] = $filter;
		
		// Don't forget to append
		$this->pageUrl = $this->appendUrlArray($this->pageUrl, $uniqueName, $pageVal);
		
		// Return $this for chaining
		return $this;
	}
	
	public function addJoin($uniqueName, $join) {
		
		// If $uniqueName exists, overwrite it
		$this->joins[$uniqueName] = $join;
		
		// Return $this for chaining
		return $this;
	}
	
	public function addOrder($uniqueName, $order) {
		
		// If $uniqueName exists, overwrite it
		$this->order[$uniqueName] = $order;
		
		// Return this for chaining
		return $this;
	}
	
	public function getValue($value) {
		
		if (isset($this->paging[$value]))
			return false;
			
			return $this->paging[$value];
	}
	
	public function countFilters() {
		
		$counter = 0;
		
		foreach ($_GET as $key => $val) {
			
			if ($key != 'paging' && $key != 'pageSize' && $key != 'page' && $key != 'order' && $key != 'wisFilter')
				$counter++;
		}
		
		return $counter;
	}
	
	public function loadFilters($type) {
		
		// Manually filtered?
		if (count($_GET) > 1) {
			
			return $_GET;
		}
		else {
			
			// Load filters from database
			$filters = $this->db->prepare("SELECT * FROM `sys_accountFilters` WHERE `saf_filterType`=? AND `saf_accountId`=?", "si", array($type, $_SESSION['wamis']['common']['Id']));
			
			if (count($filters) > 0) {
				
				$filterData = unserialize($filters[0]['saf_filters']);
				
				unset($filterData['paging']);
				
				if (count($filterData) > 0)
					return $filterData;
			}
		}
		
		return false;
	}
	
	public function saveFilters($type) {
		
		$filters = $this->db->prepare("SELECT * FROM `sys_accountFilters` WHERE `saf_filterType`=? AND `saf_accountId`=?", "si", array($type, $_SESSION['wamis']['common']['Id']));
		
		if (count($filters) == 0) {
			
			// Insert filters
			$this->db->prepare("INSERT INTO `sys_accountFilters` (`saf_accountId`, `saf_filterType`, `saf_filters`) VALUES(?, ?, ?)", "iss", array($_SESSION['wamis']['common']['Id'], $type, serialize($_GET)));
		}
		else {
			
			// Update filters
			$this->db->prepare("UPDATE `sys_accountFilters` SET `saf_filters`=? WHERE `saf_id`=?", "si", array(serialize($_GET), $filters[0]['saf_id']));
		}
	}
	
	public function clearFilters($type) {
		
		$filters = $this->db->prepare("SELECT * FROM `sys_accountFilters` WHERE `saf_filterType`=? AND `saf_accountId`=?", "si", array($type, $_SESSION['wamis']['common']['Id']));
		
		if (count($filters) > 0) {
			
			// Set empty array
			$this->db->prepare("UPDATE `sys_accountFilters` SET `saf_filters`=? WHERE `saf_id`=?", "si", array(serialize(array()), $filters[0]['saf_id']));
		}
	}
	
	public function generateInputs($ignore = array()) {
		
		$returnData = '';
		
		// Start splitting
		$split = explode('?', $this->pageUrl);
		$blocks = explode('&', $split[1]);
		
		foreach ($blocks as $key => $val) {
			
			$values = explode('=', $val);
			
			$key = $values[0];
			unset($values[0]);
			$value = implode('=', $values);
			
			if (!in_array($key, $ignore))
				$returnData .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
		}
		
		return $returnData;
	}
	
	public function getPageUrl($ignore = 'nothing') {
		
		// Start splitting
		$split = explode('?', $this->pageUrl);
		$blocks = explode('&', $split[1]);
		
		$returnData = '?';
		
		foreach ($blocks as $key => $val) {
			
			$values = explode('=', $val);
			
			$key = $values[0];
			unset($values[0]);
			$value = implode('=', $values);
			
			if ($key != $ignore)
				$returnData .= '&' . $key . '=' . $value;
		}
		
		return $returnData;
	}
	
	public function returnRows() {
		
		$this->checkVars();
		
		if ($this->paging['totalRecords'] > 0) {
			
			if (!empty($this->query['p']))
				$data = $this->db->prepare($this->replaceQuery($this->query['q'], true) . " LIMIT " . $this->paging['start'] . ", " . $this->paging['perPage'], $this->query['p'], $this->query['v']);
				else
					$data = $this->db->prepare($this->replaceQuery($this->query['q'], true) . " LIMIT " . $this->paging['start'] . ", " . $this->paging['perPage']);
					
					// 			if (count($data) > 0)
					return $data;
		}
		
		return array();
	}
	
	public function returnTotal() {
		
		return $this->paging['totalRecords'];
	}
	
	public function returnCurrentPos() {
		
		return ($this->paging['start'] + 1);
	}
	
	public function returnNextPos() {
		
		$pos = $this->paging['start'] + $this->paging['perPage'];
		
		return ($pos > $this->returnTotal()) ? $this->returnTotal() : $pos;
	}
	
	public function showPaging() {
		
		$this->checkVars();
		
		if ($this->paging['maxPage'] > 1) {
			
			$this->templateData['left'] = '<li><a href="{{pageUrl}}" class="left{{hiddenClass}}" title="Vorige pagina">{{lang}}</a></li>';
			$this->templateData['right'] = '<li><a href="{{pageUrl}}" class="right{{hiddenClass}}" title="Volgende pagina">{{lang}}</a></li>';
			$this->templateData['pages'] = '{{pageData}}';
			
			$returnData = '';
			
			$returnData .= $this->displayLeft('&laquo;');
			$returnData .= $this->displayPages();
			$returnData .= $this->displayRight('&raquo;');
			
			return '<ul class="pagination">' . $returnData . '</div>';
		}
		
		return false;
	}
	
	public function showDisplaySize($action) {
		
		if ($this->paging['totalRecords'] > 5) {
			
			$arrSizes = array(5, 10, 25, 50, 100, 250);
			
			$returnData = '<div class="row-count"><div><form action="' . $this->pageUrl . '" method="get">';
			
			// Start splitting
			$split = explode('?', $this->pageUrl);
			$blocks = explode('&', $split[1]);
			
			foreach ($blocks as $key => $val) {
				
				$values = explode('=', $val);
				
				$key = $values[0];
				unset($values[0]);
				$value = implode('=', $values);
				
				if ($key != 'pageSize')
					$returnData .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
			}
			
			$returnData .= '<select name="pageSize" id="row_count" class="form-control" onchange="this.form.submit();">';
			
			foreach ($arrSizes as $key => $val) {
				
				$active = ($val == $this->paging['perPage']) ? ' selected="selected"' : '';
				
				$returnData .= '<option value="' . $val . '"' . $active . '>' . $val . ' rijen</option>';
			}
			
			$returnData .= '</select></form></div></div>';
			
			return $returnData;
		}
		
		return false;
	}
	
	public function getQuery() {
		
		return $this->replaceQuery($this->query['q'], true);
	}
	
	private function replaceQuery($query, $orderBy = false) {
		
		$filters = implode($this->filters);
		$joins = implode($this->joins);
		
		if ($orderBy && count($this->order) > 0)
			$order = implode(',', $this->order);
			
			if (isset($order))
				return str_replace(array('{{joins}}', '{{filters}}', '{{orderby}}'), array($joins, $filters, ' ORDER BY ' . $order), $query);
				else
					return str_replace(array('{{joins}}', '{{filters}}', '{{orderby}}'), array($joins, $filters, ''), $query);
	}
	
	private function appendUrl($url, $add) {
		
		if(strpos($url, '?') !== false) {
			
			return $url . '&' . $add;
		}
		else {
			
			return $url . '?' . $add;
		}
	}
	
	// Appends an array of data
	private function appendUrlArray($url, $name, $data) {
		
		if(strpos($url, '?') !== false) {
			
			$sign = '&';
		}
		else {
			
			$sign = '?';
		}
		
		$fullUrl = $url;
		
		if (is_array($data)) {
			
			foreach ($data as $key => $val) {
				
				$fullUrl .= $sign . $name . '[]=' . $val;
				
				// On first, auto update the sign
				$sign = '&';
			}
		}
		else {
			
			$fullUrl .= $sign . $name . '=' . $data;
		}
		
		return $fullUrl;
	}
	
	private function checkVars() {
		
		if (!isset($this->paging['totalRecords'])) {
			
			$this->paging['totalRecords'] = $this->count();
			$this->paging['maxPage'] = $this->maxPage();
			$this->paging['currentPage'] = (!empty($_GET['paging'])) ? $this->current($_GET['paging']) : $this->current($this->currentPage);
			$this->paging['start'] = $this->start();
			$this->paging['end'] = $this->end();
		}
	}
	
	private function count() {
		
		if (!empty($this->query['p']))
			$count = $this->db->prepare("SELECT COUNT(*) AS `count` FROM (" . $this->replaceQuery($this->query['q']) . ") AS `output`", $this->query['p'], $this->query['v']);
			else
				$count = $this->db->prepare("SELECT COUNT(*) AS `count` FROM (" . $this->replaceQuery($this->query['q']) . ") AS `output`");
				
				return $count[0]['count'];
	}
	
	private function current($page) {
		
		if ($page > $this->paging['maxPage'])
			$page = $this->paging['maxPage'];
			elseif ($page <= 1)
			$page = 1;
			
			return $page;
	}
	
	private function end() {
		
		return ($this->paging['start'] + $this->paging['perPage']);
	}
	
	private function maxPage() {
		
		return ceil($this->paging['totalRecords'] / $this->paging['perPage']);
	}
	
	private function start() {
		
		// Get the start of the paging
		return ($this->paging['currentPage'] - 1) * $this->paging['perPage'];
	}
	
	// Build paging functions
	private function displayLeft($lang) {
		
		$url = $this->pageUrl . '&paging=' . ($this->paging['currentPage'] - 1);
		
		$hiddenClass = ($this->paging['currentPage'] > 1) ? '' : ' hidden';
		
		if ($lang == 'fa-long-arrow-left') {
			
			$hiddenClass .= ' fa fa-long-arrow-left';
			$lang = '&nbsp;';
		}
		
		return str_replace(array('{{pageUrl}}', '{{lang}}', '{{hiddenClass}}'), array($url, $lang, $hiddenClass), $this->templateData['left']);
	}
	
	private function displayRight($lang) {
		
		$url = $this->pageUrl . '&paging=' . ($this->paging['currentPage'] + 1);
		
		$hiddenClass = ($this->paging['currentPage'] < $this->paging['maxPage']) ? '' : ' hidden';
		
		if ($lang == 'fa-long-arrow-right') {
			
			$hiddenClass .= ' fa fa-long-arrow-right';
			$lang = '&nbsp;';
		}
		
		return str_replace(array('{{pageUrl}}', '{{lang}}', '{{hiddenClass}}'), array($url, $lang, $hiddenClass), $this->templateData['right']);
	}
	
	private function displayPages() {
		
		$pages = '';
		$url = $this->pageUrl . '&paging=';
		
		// Always display first page
		$activeFirst = ($this->paging['currentPage'] == 1) ? 'active' : '';
		$pages .= '<li class="' . $activeFirst . '"><a href="' . $url . '1" class="button" title="1">1</a></li>';
		
		// Calculate next pages
		$nextPages = array(($this->paging['currentPage'] - 4), ($this->paging['currentPage'] - 3), ($this->paging['currentPage'] - 2), ($this->paging['currentPage'] - 1), $this->paging['currentPage'], ($this->paging['currentPage'] + 1), ($this->paging['currentPage'] + 2), ($this->paging['currentPage'] + 3), ($this->paging['currentPage'] + 4));
		
		// ... start
		if (!in_array(2, $nextPages))
			$pages .= '<span>&hellip;</span>';
			
			// Loop through $nextPages
			foreach ($nextPages as $key => $val) {
				
				if ($val > 1 && $val < $this->paging['maxPage']) {
					
					$active = ($val == $this->paging['currentPage']) ? 'active' : '';
					
					$pages .= '<li class="' . $active . '"><a href="' . $url . $val . '" title="Naar pagina ' . $val . '">' . $val . '</a></li>';
				}
			}
			
			// ... end
			if (!in_array(($this->paging['maxPage'] - 1), $nextPages))
				$pages .= '<span>&hellip;</span>';
				
				// Always display last page
				$activeLast = ($this->paging['currentPage'] == $this->paging['maxPage']) ? 'active' : '';
				$pages .= '<li class="' . $activeLast . '"><a href="' . $url . $this->paging['maxPage'] . '" class="button" title="' . $this->paging['maxPage'] . '">' . $this->paging['maxPage'] . '</a></li>';
				
				return str_replace('{{pageData}}', $pages, $this->templateData['pages']);
				
	}
}