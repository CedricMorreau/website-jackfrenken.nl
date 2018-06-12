<?php

class Specialist {

	static function generateName($data, $titles = 1, $certs = 1, $aanhef = 1, $voorletters = 1, $nameSet = 1) {

		$name = '';

		if ($aanhef) {

			foreach ($data['med_aanhef'] as $key => $val) {

				$name .= $val . ' ';
			}
		}

		if ($titles) {

			if ($data['med_titels'] != '') {

				$name .= $data['med_titels'] . ' ';
			}
		}

		if ($voorletters) {

			if (!empty($data['med_voorletters'])) {

				$name .= $data['med_voorletters'] . ' ';
			}
		}

		if ($nameSet) {

			// Now the normal name
			$name .= ($voorletters && !empty($data['med_voorletters'])) ? '(' . $data['med_name'] . ') ' : $data['med_name'] . ' ';

			if (!empty($data['med_tussenvoegsels'])) {

				$name .= $data['med_tussenvoegsels'] . ' ';
			}

			$name .= $data['med_surname'] . ' ';
		}

		if ($certs) {

			if ($data['med_certificeringen'] != '') {

				$name .= $data['med_certificeringen'] . ' ';
			}
		}

		return $name;

		// Dhr. E.H.E.J. (Eric) Horsmans RMT/RTRMT/RT
	}
}