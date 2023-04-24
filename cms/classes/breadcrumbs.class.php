<?php

class Breadcrumbs {

	private $pageId;
	private $pageTitle;
	private $db;
	private $template;
	private $ignore;
	private $extraCrumb;

	public function __construct($pageId, $pageTitle, $db, $template, $extraCrumb = array(), $ignore = 0) {

		$this->pageId = $pageId;
		$this->pageTitle = $pageTitle;
		$this->db = $db;
		$this->template = $template;
		$this->ignore = $ignore;
		$this->extraCrumb = $extraCrumb;
	}

	public function displayCrumbs() {

		global $template;

		// First of all, grab the single page structure of our page
		$tree = new NestedTree('tbl_mod_pages', 'mod_pa_', $this->db);
		$singlePath = $tree->singlePath($this->pageId);

		if ($this->ignore) {

			array_pop($singlePath);
		}

		$crumbReturn = '<ul class="breadcrumbs">' . PHP_EOL;

		foreach ($singlePath as $key => $val) {

			$start = ($key == 0) ? 'start' : '';
			$current = ($val['id'] == $this->pageId && count($this->extraCrumb) == 0) ? 'active' : '';

			if ($val['id'] == $this->pageId && $this->ignore != 1) {

				$title = $val['mod_pa_nav'];
			}
			else {

				$title = $val['mod_pa_nav'];
			}

			// FIXIT: TEMP FIX: DA seems to redirect ALL pages to NL/404.shtml
			if ($key == 0)
				$url = '/';
			else
				$url = $this->template->findPermalink($val['id'], 1);
			// END TEMP FIX

			if ($val['id'] == 1) $val['id'] = 9;

			$link = ($val['id'] == $this->pageId && $this->ignore == 0) ? 'javascript: void(0);' : $url;

			if ($val['id'] == $this->pageId && $this->ignore == 0) {

				$crumbReturn .= '<li class="' . $current . '">' . $title . ' &rsaquo;</li>' . PHP_EOL;
			}
			else {

				$crumbReturn .= '<li class="' . $current . '"><a href="' . $link . '" class="' . $start . '" title="' . $title .'" alt="' . $title . '">' . $title . ' &rsaquo;</a></li>' . PHP_EOL;	
			}
		}

		if (count($this->extraCrumb) > 0) {

			foreach ($this->extraCrumb as $key => $val) {

				$crumbReturn .= '<li class="active">' . $key . ' &rsaquo;</li>' . PHP_EOL;
			}
		}

		$crumbReturn .= '</ul>';

		return $template->handlePlaceholders($crumbReturn);
	}
}

?>