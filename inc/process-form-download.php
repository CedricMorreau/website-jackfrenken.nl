<?php

include($_SERVER['DOCUMENT_ROOT'] . "/cms/inc/config.php");

if(!empty($_POST['name']) && !empty($_POST['telnr'])) {

	if (empty($_POST['page']))
		die();

	$test = false;

	// Grab the proper download
	$article = $cms['database']->prepare("SELECT * FROM `tbl_mod_articleContent` WHERE EXISTS(SELECT * FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=28 AND `mod_cv_value`=?)", "s", array($_POST['page']));

	if (count($article) == 0)
		die();

	$dataArray = Content::getArticleValues($article[0]['mod_co_id'], $cms, 1);

	if (!isset($dataArray['dl_status'][11]))
		die();

    // Hieronder de name="" van de input invullen
    $name = (empty($_POST['name'])) ? '' : $_POST['name'];
    $telnr = (empty($_POST['telnr'])) ? '-' : $_POST['telnr'];
    $email = (empty($_POST['email'])) ? '-' : $_POST['email'];

    $mail_template = file_get_contents('mailtemplates/download-form.html');

    $mail_ontvanger = $dataArray['dl_receiver'];

    $mail_subject = 'Er is een bestand gedownload via de website';

    $placeholders = array('{{name}}', '{{telnr}}', '{{email}}', '{{file}}');
    $output = array($name, $telnr, $email, $dataArray['dl_title']);
    $output	= str_replace($placeholders, $output, $mail_template);

    if ($test) {

        echo $output;
    }
    else {

        $mail = new PP_Mailer();

        $mail->addField('apiKey', 'dc3c99308282564676a51dfc6d656653');
        $mail->addField('base64', 1);
        $mail->addField('type', 'send');
        $mail->addField('from', 'info@jackfrenken.nl');
        $mail->addField('fromName', 'Jackfrenken.nl');
        $mail->addField('to', $mail_ontvanger);
        $mail->addField('subject', $mail_subject);
        $mail->addField('message', base64_encode($output));

		$mail->send();
		
		?>

		<h2>Bedankt!</h2>

		<p>U kunt het bestand nu downloaden.

		<div class="buttonWrapper">
			<a href="<?php echo $dataArray['dl_download']; ?>" target="_blank" class="btn">Downloaden</a>
		</div>

		<?php
    }
}
else {

    echo 0;
}

?>