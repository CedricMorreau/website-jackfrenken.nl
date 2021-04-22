<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include($_SERVER['DOCUMENT_ROOT'] . "/cms/classes/pp_mailer.class.php");
include($_SERVER['DOCUMENT_ROOT'] . "/cms/classes/realworkssearchform.class.php");

// Process POST data etc.
if ($_SERVER['REQUEST_METHOD'] == "POST") {

	// Not empty
	$array = array('voornaam', 'achternaam', 'contactStraat', 'contactHuisnummer', 'contactPostcode', 'contactPlaats', 'contactTelefoon', 'contactEmail', 'plaatsnaam', 'prijsVanaf', 'prijsTot', 'soortAankoop');

	$error = false;

	foreach ($array as $key => $val) {

		if (empty($_POST[$val])) {

			$error = true;
		}
	}
	
	if (!$error) {

		$mail_template = file_get_contents('mailtemplates/process_zoekersFormulier.html');
		
		$mail_ontvanger = 'info@jackfrenken.nl';
// 		$mail_ontvanger = 'sander@pixelplus.nl';
		$mail_subject = 'Verstuurd via Jackfrenken.nl';

		$_POST['plaatsnaam']=(!empty($_POST['plaatsnaam']))?$_POST['plaatsnaam']:'Geen voorkeur';
		$_POST['soortAankoop']=(!empty($_POST['soortAankoop']))?$_POST['soortAankoop']:'Geen voorkeur';

		// Replace placeholders
		foreach ($_POST as $key => $val) {

			if ($key == 'plaatsnaam')
				$val = implode(', ', $val);

			if ($key == 'soortAankoop')
				$val = implode(', ', $val);

			$mail_template = str_replace('{{' . $key . '}}', $val, $mail_template);
		}

		// Initialize search form for Realworks
		$search_form = new RealworksSearchForm('e2ed5b0a-d544-409b-aa06-7f3a875c2403', 44003, 884311);
		$search_form->fetch_locations();

		$gender = [
			'DHR' => RealworksSearchForm::FIELD_GENDER_MALE,
			'MEVR' => RealworksSearchForm::FIELD_GENDER_FEMALE,
			'FAM' => RealworksSearchForm::FIELD_GENDER_OTHER,
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

			$search_form->set_woningsoorten([$_POST['objectSoort']]);
			$search_form->set_woningtype($_POST['objectBouwvorm']);
			
			$search_form->set_objectsoort('WOONHUIS');
		}

		$min_object_price = intval($_POST['prijsVanaf']);
		$max_object_price = intval($_POST['prijsTot']);

		if (is_array($_POST['soortAankoop'])) {

			if (in_array('Huren', $_POST['soortAankoop']))
				$search_form->set_rent_range($min_object_price, $max_object_price);

			if (in_array('Kopen', $_POST['soortAankoop']))
				$search_form->set_purchase_range($min_object_price, $max_object_price);

		} else {
			$search_form->set_rent_range($min_object_price, $max_object_price);
			$search_form->set_purchase_range($min_object_price, $max_object_price);
		}

		if (is_array($_POST['plaatsnaam'])) {
			$search_form->set_locations('plaatsen', $_POST['plaatsnaam']);
		} else {
			$search_form->set_all_locations('plaatsen');
		}



		$mail_template_wrapper = '
		<!DOCTYPE html>
			<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>Jack Frencken zoek registratie</title>
					<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
				</head>
				<body style="margin: 0; padding: 0;">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td style="display:flex; justify-content: center; align-items: center; padding: 40px 0 30px 0; background-color: white; width: 100%; height: 100%;">
								<img src="/resources/logo.svg" alt="Jack Frenken zoek registratie" style="width: 500px; height: 500px; display: block;">
							</td>
						</tr>
						<tr>
							<td>
								'.$mail_template.'
							</td>
							<td>
								'.$mail_template[0][1].'
							</td>
						</tr>
						<tr>
							<td>
								'.$mail_template[2].'
							</td>
							<td>
								'.$mail_template[3].'
							</td>
						</tr>
						<tr style="background-color: #003064; padding: 20px;">
						</tr>
					</table>
				</body>
			</html>
		';


		$search_form->send();

		
		if (isset($test)) {
			
			echo $output;
		}
		else {
			
			$mail = new PP_Mailer();
			
			$mail->addField('apiKey', '8b7442195ca347fe7b36d77fd7289a17');
			$mail->addField('base64', 1);
			$mail->addField('type', 'send');
			$mail->addField('from', 'info@jackfrenken.nl');
			$mail->addField('fromName', 'Jackfrenken.nl');
			// $mail->addField('to', $mail_ontvanger);
			$mail->addField('to', "luca@pixelplus.nl");
			// $mail->addField('BCC', 'info@jackfrenken.nl');
			$mail->addField('subject', $mail_subject);
			$mail->addField('message', base64_encode($mail_template_wrapper));
			
			$mail->send();
			
			echo 1;
		}
	}
	else {
		
		echo '0';
	}
}

?>