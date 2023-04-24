<?php

include($_SERVER['DOCUMENT_ROOT'] . "/cms/classes/pp_mailer.class.php");

if(!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['phone'])) {

	$test = false;

	$first_name = (empty($_POST['first_name'])) ? '' : $_POST['first_name'];
	$last_name = (empty($_POST['last_name'])) ? '' : $_POST['last_name'];
	$email = (empty($_POST['email'])) ? '-' : $_POST['email'];
	$phone = (empty($_POST['phone'])) ? '-' : $_POST['phone'];
	
	$mail_template = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/inc/mailtemplates/algemeen/mail_template.txt');

	// $mail_ontvanger = 'sander@pixelplus.nl';
	$mail_ontvanger = 'info@jackfrenken.nl';
	
	$mail_subject = 'Verduurzamen Jackfrenken.nl';

	$placeholders = array('{{placeHolder_title}}', '{{placeHolder_content}}');
	$output = array('Contactformulier', 'Onderstaand bericht is binnengekomen via de pagina \'Verduurzamen\' op Jackfrenken.nl.<br><br><b>Naam:</b> ' . $first_name . ' ' . $last_name . '<br><b>E-mailadres:</b> ' . $email . '<br><b>Telefoonnummer:</b> ' . $phone);
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