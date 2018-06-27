<?php

include($_SERVER['DOCUMENT_ROOT'] . "/cms/classes/pp_mailer.class.php");

// Process POST data etc.
if ($_SERVER['REQUEST_METHOD'] == "POST") {

	// Not empty
	$array = array('voornaam', 'achternaam', 'contactStraat', 'contactHuisnummer', 'contactPostcode', 'contactPlaats', 'contactTelefoon', 'contactEmail');

	$error = false;

	foreach ($array as $key => $val) {

		if (empty($_POST[$val])) {

			$error = true;
		}
	}

	if (!$error) {

		$mail_template = file_get_contents('mailtemplates/process_zoekersFormulier.html');
		
// 		$mail_ontvanger = 'info@jackfrenken.nl';
		$mail_ontvanger = 'sander@pixelplus.nl';
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

		if ($test) {
			
			echo $output;
		}
		else {
			
			$mail = new PP_Mailer();
			
			$mail->addField('apiKey', '8b7442195ca347fe7b36d77fd7289a17');
			$mail->addField('base64', 1);
			$mail->addField('type', 'send');
			$mail->addField('from', 'info@jackfrenken.nl');
			$mail->addField('fromName', 'Jackfrenken.nl');
			$mail->addField('to', $mail_ontvanger);
			$mail->addField('subject', $mail_subject);
			$mail->addField('message', base64_encode($mail_template));
			
			$mail->send();
			
			echo 1;
		}
	}
	else {
		
		echo '0';
	}
}

?>