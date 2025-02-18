<?php

header('Content-Type: text/plain');

include($_SERVER['DOCUMENT_ROOT'] . "/cms/classes/pp_mailer.class.php");
include($_SERVER['DOCUMENT_ROOT'] . "/cms/classes/realworkssearchform.class.php");

// Process POST data etc.
if ($_SERVER['REQUEST_METHOD'] == "POST") {

	// Not empty
	$array = array('voornaam', 'achternaam', 'contactStraat', 'contactHuisnummer', 'contactPostcode', 'contactPlaats', 'contactTelefoon', 'contactEmail', 'plaatsnaam', /*'prijsVanaf',*/ 'prijsTot', 'soortAankoop');

	// Set prijsVanaf to 0 if empty
	if (empty($_POST['prijsVanaf']))
		$_POST['prijsVanaf'] = 0;

	$error = false;

	foreach ($array as $key => $val) {
		if (empty($_POST[$val])) {
			$error = true;
			break;
		}
	}

	if (!$error) {

		// Initialize search form for Realworks
		$search_form = new RealworksSearchForm('e2ed5b0a-d544-409b-aa06-7f3a875c2403', 44003, '884311');
		$search_form->fetch_locations();

		$gender = [
			'Dhr' => RealworksSearchForm::FIELD_GENDER_MALE,
			'Mevr' => RealworksSearchForm::FIELD_GENDER_FEMALE,
			'Fam' => RealworksSearchForm::FIELD_GENDER_OTHER,
		];

		$gender = $gender[$_POST['aanhef']];

		$search_form->set_contact_info($_POST['voornaam'], !empty($_POST['tussenvoegsel']) ? $_POST['tussenvoegsel'] : null, $_POST['achternaam'], $_POST['contactTelefoon'],
			!empty($_POST['contactMobiel']) ? $_POST['contactMobiel'] : null, $_POST['contactEmail'], $gender);

		$search_form->set_contact_address($_POST['contactStraat'], intval($_POST['contactHuisnummer']), null, $_POST['contactPostcode'], $_POST['contactPlaats']);

		$search_form->set_min_perceeloppervlakte(intval($_POST['objectPerceelOpp']));
		$search_form->set_min_slaapkamers(intval($_POST['objectSlaapkamers']));

		if (strtolower($_POST['objectSoort']) === 'appartement') {

			$search_form->set_all_appartementsoorten();
			$search_form->set_objectsoort('APPARTEMENT');

		} elseif (strtolower($_POST['objectSoort']) === 'bouwgrond') {

			$search_form->set_woningsoorten([]);
			$search_form->set_objectsoort('BOUWGROND');

		} else {
			if (!empty($_POST['objectSoort'])) {
				$search_form->set_woningsoorten([$_POST['objectSoort']]);

				if (!empty($_POST['objectBouwvorm']))
					$search_form->set_woningtype($_POST['objectBouwvorm']);

				$search_form->set_objectsoort('WOONHUIS');
			}
		}

		$min_object_price = intval($_POST['prijsVanaf']);
		$max_object_price = intval($_POST['prijsTot']);

		if (!in_array(strtolower($_POST['soortAankoop']), ['huren', 'kopen']))
			die(0);

		if (strtolower($_POST['soortAankoop']) === 'huren') {
			$search_form->set_rent_range($min_object_price, $max_object_price);
		} else {
			$search_form->set_purchase_range($min_object_price, $max_object_price);
		}

		if (is_array($_POST['plaatsnaam'])) {
			$search_form->set_locations('plaatsen', $_POST['plaatsnaam']);
		} else {
			$search_form->set_all_locations('plaatsen');
		}

		$success = $search_form->send();

		if ($success === false)
			die(0);

		if (isset($test)) {
			
			echo $output;
		}
		else {
			$mail_template = file_get_contents('mailtemplates/process_zoekersFormulier.html');
			
			// $mail_ontvanger = 'info@jackfrenken.nl';
			$mail_ontvanger = $_POST['contactEmail'];
			// $mail_ontvanger = "luca@pixelplus.nl";
			$mail_subject = 'Jack Frenken makelaars en adviseurs: Uw zoekopdracht';

			$_POST['plaatsnaam']=(!empty($_POST['plaatsnaam']))?$_POST['plaatsnaam']:'Geen voorkeur';
			$_POST['soortAankoop']=(!empty($_POST['soortAankoop']))?$_POST['soortAankoop']:'Geen voorkeur';
			$_POST['contactStraat']=(!empty($_POST['contactStraat']))?$_POST['contactStraat']:' -';
			$_POST['contactHuisnummer']=(!empty($_POST['contactHuisnummer']))?$_POST['contactHuisnummer']:' -';
			$_POST['contactPostcode']=(!empty($_POST['contactPostcode']))?$_POST['contactPostcode']:' -';
			$_POST['contactPlaats']=(!empty($_POST['contactPlaats']))?$_POST['contactPlaats']:' -';
			$_POST['contactTelefoon']=(!empty($_POST['contactTelefoon']))?$_POST['contactTelefoon']:' -';
			$_POST['contactMobiel']=(!empty($_POST['contactMobiel']))?$_POST['contactMobiel']:' -';
			$_POST['contactEmail']=(!empty($_POST['contactEmail']))?$_POST['contactEmail']:' -';

			// Replace placeholders
			foreach ($_POST as $key => $val) {

				if ($key == 'plaatsnaam')
					$val = implode(', ', array_map('ucfirst', $val));

				if ($key == 'achternaam' && !empty($_POST['tussenvoegsel']))
					$val = $_POST['tussenvoegsel'] . ' ' . $val;

				$mail_template = str_replace('{{' . $key . '}}', $val, $mail_template);
			}
			$mail_template = str_replace('Dhr', 'heer', $mail_template);
			$mail_template = str_replace('Mevr', 'mevrouw', $mail_template);
			$mail_template = str_replace('Fam', 'familie', $mail_template);
			
			$mail = new PP_Mailer();
			
			$mail->addField('apiKey', '8b7442195ca347fe7b36d77fd7289a17');
			$mail->addField('base64', 1);
			$mail->addField('type', 'send');
			$mail->addField('from', 'info@jackfrenken.nl');
			$mail->addField('fromName', 'Jackfrenken.nl');
			$mail->addField('to', $mail_ontvanger);
			// $mail->addField('bcc', 'info@jackfrenken.nl');
			$mail->addField('subject', 'Jack Frenken makelaars en adviseurs: Uw zoekopdracht');
			$mail->addField('message', base64_encode($mail_template));
			$mail->send();
			
			// Add another email to Jack Frenken
			$mail = new PP_Mailer();
			
			$mail->addField('apiKey', '8b7442195ca347fe7b36d77fd7289a17');
			$mail->addField('base64', 1);
			$mail->addField('type', 'send');
			$mail->addField('from', 'info@jackfrenken.nl');
			$mail->addField('fromName', 'Jackfrenken.nl');
			$mail->addField('to', 'info@jackfrenken.nl');
			// $mail->addField('bcc', 'info@jackfrenken.nl');
			$mail->addField('subject', 'Nieuwe zoekopdracht via Jackfrenken.nl');
			$mail->addField('message', base64_encode($mail_template));
			$mail->send();

			echo 1;
		}
	}
	else {
		
		echo 0;
	}
}

?>