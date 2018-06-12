<?php

// FIXIT: NL etc. in URL

class Languages {

	private static $lang;

	static function fillLanguage($languageId) {

		global $documentRoot, $template;

		// Fetch language file
		include($documentRoot . 'cms/templates/languages/language_' . $languageId . '.php');

		if (isset(Languages::$lang))
			Languages::$lang = $lang;
	}

	static function returnLang($key = '') {

		if (empty($key))
			return self::$lang;
		else {

			// Key is built in two vars, seperated by /
			$keySplit = explode('/', $key);

			return (isset(self::$lang[$keySplit[0]][$keySplit[1]])) ? self::$lang[$keySplit[0]][$keySplit[1]] : 'Undefined language';
		}
	}
}

?>