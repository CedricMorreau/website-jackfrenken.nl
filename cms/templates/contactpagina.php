<?php

// First of all grab all locations
$locaties = $cms['database']->prepare("SELECT * FROM `tbl_cms_locaties` WHERE `cl_status`=1 ORDER BY `cl_sortOrder` ASC");

?>

<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>

	</head>

	<body>
		<div class="page-wrapper contact">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/contact-header.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">
				
					<?php include($documentRoot . "inc/contact-widget.php"); ?>


				</div>

				<div class="column-content">

					<div class="content-wrapper">
					
						<?php
						
						if (count($locaties) > 0) {
							
							foreach ($locaties as $key => $val) {
								
								$uniId = strtolower(str_replace(' ', '-', $val['cl_name']));
								
								?>
								
						<a id="<?php echo $uniId; ?>" class="anchor"></a>
						
						<div class="location-wrapper">
							<div class="location-details">
								<h2><?php echo $val['cl_name']; ?></h2>
								<p>
									<a href="<?php echo $val['cl_googleLink']; ?>" target="_blank">
									<?php echo $val['cl_adres_straat']; ?> <?php echo $val['cl_adres_huisnummer']; ?><br>
									<?php echo $val['cl_adres_postcode']; ?> <?php echo $val['cl_adres_plaats']; ?>
									</a>
								</p>

								<p>
									<?php if (!empty($val['cl_telefoon'])) { ?>
									Tel: <?php echo $val['cl_telefoon']; ?><br>
									<?php } ?>
									<?php if (!empty($val['cl_fax'])) { ?>
									Fax: <?php echo $val['cl_fax']; ?><br>
									<?php } ?>
									<?php if (!empty($val['cl_email'])) { ?>
									<a href="mailto:<?php echo $val['cl_email']; ?>">
										<?php echo $val['cl_email']; ?>
									</a>
									<?php } ?>
								</p>
								
								<?php
					
								$times = $cms['database']->prepare("SELECT * FROM `tbl_cms_locatieOpening` WHERE `clo_locatieId`=? ORDER BY `clo_sortOrder` ASC, `clo_day` ASC", "i", array($val['cl_id']));
								
								if (count($times) > 0) {
									
									?>
									
								<table class="times-table">
								
									<?php
									
									foreach ($times as $sKey => $sVal) {
										
										$today = ($sVal['clo_day'] == date('w')) ? ' class="today"' : '';
										
										?>
										
									<tr<?php echo $today; ?>>
										<td><?php echo $sVal['clo_dayName']; ?></td>
										<td><?php echo $sVal['clo_openTime']; ?></td>
									</tr>	
										
										<?php
									}
									
									?>
								
								</table>
									
									<?php	
								}
								
								?>
							</div>
							<div class="location-photo">
								<img src="<?php echo $val['cl_photo']; ?>" alt="<?php echo $val['cl_name']; ?>">
							</div>
						</div>
								
								<?php
							}
						}
						
						?>

						<div class="social-wrapper">
							<div>Kvk-nummer: <?php echo $locaties[0]['cl_kvk']; ?></div>
							<div class="social-items"><span>Volg ons:</span>
								<a href="https://www.facebook.com/jackfrenken" title="Facebook"><img src="<?php echo $dynamicRoot; ?>resources/social_facebook.svg" alt="Facebook"></a>

								<a href="https://twitter.com/JackFrenkenNVM"><img src="<?php echo $dynamicRoot; ?>resources/social_twitter.svg" alt="Twitter" title="Twitter"></a>
								
								<a href="https://www.linkedin.com/company/1680615?trk=tyah&trkInfo=tarId%3A1410275372935%2Ctas%3Ajack%20frenken%2Cidx%3A1-1-1"><img src="<?php echo $dynamicRoot; ?>resources/social_linkedin.svg" alt="LinkedIn" title="LinkedIn"></a>
							</div>
						</div>
					</div>


				</div>
			</div>

			<?php include($documentRoot . "inc/footer.php"); ?>
		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

	</body>

</html>