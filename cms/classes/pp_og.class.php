<?php

class PP_OG {

	private function grabValues($ogType) {

		// Define the tables and such for ogtypes
		$mainArray = array();

		// Wonen
		$mainArray['wonen'] = array();
		$mainArray['wonen']['table'] = 'tbl_OG_wonen';
		$mainArray['wonen']['status'] = 'objectDetails_StatusBeschikbaarheid_Status';

		// ALV
		$mainArray['alv'] = array();
		$mainArray['alv']['table'] = 'tbl_OG_alv';
		$mainArray['alv']['status'] = 'object_ObjectDetails_Status_StatusType';

		// BOG
		$mainArray['bog'] = array();
		$mainArray['bog']['table'] = 'tbl_OG_bog';
		$mainArray['bog']['status'] = 'objectDetails_Status_StatusType';

		// Nieuwbouw
		$mainArray['nieuwbouw'] = array();
		$mainArray['nieuwbouw']['table'] = 'tbl_OG_nieuwbouw_bouwNummers';
		$mainArray['nieuwbouw']['status'] = 'Status_ObjectStatus';

		return $mainArray[$ogType];
	}

	static function countObjects($ogType, $append = false, $bold = false) {

		global $documentRoot;

		$appendArr = array(
			'wonen' => array('woning', 'woningen'),
			'bog' => array('bedrijf', 'bedrijven'),
			'alv' => array('agrarisch', 'agrarisch'),
			'buitenstate' => array('buitenstate', 'buitenstates'),
			'nieuwbouw' => array('nieuwbouw', 'nieuwbouw'),
		);

		if (file_exists($documentRoot . 'data/cache/og/count_' . $ogType . '.txt')) {

			$data = unserialize(file_get_contents($documentRoot . 'data/cache/og/count_' . $ogType . '.txt'));

			// Exception, if wonen, then also add buitenstate
			if ($ogType == 'wonen')
				$data = $data + unserialize(file_get_contents($documentRoot . 'data/cache/og/count_buitenstate.txt'));

			if ($append) {

				if ($bold) {

					if ($data != 1)
						$data = '<b>' . $data . '</b> ' . $appendArr[$ogType][1];
					else
						$data = '<b>' . $data . '</b> ' . $appendArr[$ogType][0];
				}
				else {

					if ($data != 1)
						$data .= ' ' . $appendArr[$ogType][1];
					else
						$data .= ' ' . $appendArr[$ogType][0];
				}
			}
			
			return $data;
		}

		return 0;
	}
}

?>