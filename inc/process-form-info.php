<?php

include($_SERVER['DOCUMENT_ROOT'] . "/cms/classes/pp_mailer.class.php");

var_dump($_POST);
die();

if(!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['phone']) && !empty($_POST['message'])) {
	
	$test = false;
	
	// Hieronder de name="" van de input invullen
	$objectPlaatsnaam = (empty($_POST['object_plaatsnaam'])) ? '' : $_POST['object_plaatsnaam'];
	$objectAdres = (empty($_POST['object_adres'])) ? '' : $_POST['object_adres'];
	$objectUrl = (empty($_POST['object_url'])) ? '' : $_POST['object_url'];
	$name = (empty($_POST['name'])) ? '' : $_POST['name'];
	$city = (empty($_POST['city'])) ? '-' : $_POST['city'];
	$email = (empty($_POST['email'])) ? '-' : $_POST['email'];
	$phone = (empty($_POST['phone'])) ? '-' : $_POST['phone'];
	$message = nl2br($_POST['message']);
	
	$mail_template = file_get_contents('mailtemplates/aanbod_reactieformulier.html');
	
	$mail_ontvanger = 'info@jackfrenken.nl';
// 	$mail_ontvanger = 'sander@pixelplus.nl';
	
	$mail_subject = 'Aanvraag informatie: '.$objectPlaatsnaam.' '. $objectAdres;
	
	$placeholders = array('{{objectPlaatsnaam}}', '{{objectAdres}}', '{{objectUrl}}', '{{name}}', '{{city}}', '{{phone}}', '{{email}}', '{{message}}');
	$output = array($objectPlaatsnaam, $objectAdres, $objectUrl, $name, $city, $phone, $email, $message);
	$output	= str_replace($placeholders, $output, $mail_template);
	
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
		$mail->addField('message', base64_encode($output));
		
		$mail->send();
		
		echo 1;
	}
}
else {
	
	echo 0;
}

?>