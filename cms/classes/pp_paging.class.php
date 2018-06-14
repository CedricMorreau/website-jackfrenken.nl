<?php

class PP_Paging {
	
	private $totalPages;
	private $perPage;
	private $currentPage;
	private $templateData = array();
	private $pageUrl;
	
	public function __construct($totalPages, $perPage, $currentPage, $pageUrl) {
		
		$this->totalPages = $totalPages;
		$this->perPage = $perPage;
		$this->currentPage = $currentPage;
		$this->pageUrl = $pageUrl;
		
		$this->templateData['left'] = '<div class="next"><a href="{{pageUrl}}" class="paging-button left{{hiddenClass}}" title="Vorige pagina">{{lang}}</a></div>';
		$this->templateData['right'] = '<div class="next"><a href="{{pageUrl}}" class="paging-button right{{hiddenClass}}" title="Volgende pagina">{{lang}}</a></div>';
		$this->templateData['pages'] = '{{pageData}}';
	}
	
	public function displayLeft($lang) {
		
		if ($this->currentPage <= 1)
			return '';
			
			$url = $this->pageUrl . '&p=' . ($this->currentPage - 1);
			
			$hiddenClass = ($this->currentPage > 1) ? '' : ' hide';
			
			if ($lang == 'fa-long-arrow-left') {
				
				$hiddenClass .= ' fa fa-long-arrow-left';
				$lang = '&nbsp;';
			}
			
			return str_replace(array('{{pageUrl}}', '{{lang}}', '{{hiddenClass}}'), array($url, $lang, $hiddenClass), $this->templateData['left']);
	}
	
	public function displayRight($lang) {
		
		if ($this->currentPage >= $this->totalPages)
			return '';
			
			$url = $this->pageUrl . '&p=' . ($this->currentPage + 1);
			
			$hiddenClass = ($this->currentPage < $this->totalPages) ? '' : ' hide';
			
			if ($lang == 'fa-long-arrow-right') {
				
				$hiddenClass .= ' fa fa-long-arrow-right';
				$lang = '&nbsp;';
			}
			
			return str_replace(array('{{pageUrl}}', '{{lang}}', '{{hiddenClass}}'), array($url, $lang, $hiddenClass), $this->templateData['right']);
	}
	
	public function displayPages() {
		
		$pages = '';
		$url = $this->pageUrl . '&p=';
		
		// Always display first page
		$activeFirst = ($this->currentPage == 1) ? true : false;
		
		if ($activeFirst)
			$pages .= '<span class="active">1</span>';
		else
			$pages .= '<span><a href="' . $url . '1" class="button" title="1">1</a></span>';
		
		// Calculate next pages
		$nextPages = array(($this->currentPage - 4), ($this->currentPage - 3), ($this->currentPage - 2), ($this->currentPage - 1), $this->currentPage, ($this->currentPage + 1), ($this->currentPage + 2), ($this->currentPage + 3), ($this->currentPage + 4));
		
		// ... start
		if (!in_array(2, $nextPages))
			$pages .= '<span class="no-link">...</span>';
			
			// Loop through $nextPages
			foreach ($nextPages as $key => $val) {
				
				if ($val > 1 && $val < $this->totalPages) {
					
					$active = ($val == $this->currentPage) ? true : false;
					
					if ($active)
						$pages .= '<span class="active">' . $val . '</span>';
					else
						$pages .= '<span><a href="' . $url . $val . '" title="Naar pagina ' . $val . '">' . $val . '</a></span>';
				}
			}
			
			// ... end
			if (!in_array(($this->totalPages - 1), $nextPages))
				$pages .= '<a href="#"><span>...</span></a>';
				
				// Always display last page
				$activeLast = ($this->currentPage == $this->totalPages) ? true : false;
				
				if ($activeLast)
					$pages .= '<span class="active">' . $this->totalPages . '</span>';
				else 
					$pages .= '<span><a href="' . $url . $this->totalPages . '" class="button" title="' . $this->totalPages . '">' . $this->totalPages . '</a></span>';
				
				return str_replace('{{pageData}}', $pages, $this->templateData['pages']);
				
	}
}

?>