<div class="column-header">
	
	<div class="header-title-wrapper">
		<div class="header-title">
			<h1><?php echo $val['project_ProjectDetails_Adres_Woonplaats']; ?><br>
			<?php echo utf8_encode($val['project_ProjectDetails_Projectnaam']); ?></h1>
			<p class="title-price"><?php echo $priceText; ?></p>

			<a href="javascript:void(0);" onclick="javascript:$.scrollTo('#content', 1000)" class="scroll-down">Meer over dit project &darr;</a>
		</div>
	</div>
	
	<?php
	
	if (count($mediaList) > 0) {
	
	?>

	<div class="content-image" style="background: none;">
		<div class="image-container">
		
			<div id="royal-slider" class="royalSlider rsMinW visibleNearby">
			
				<?php
				
				$i = 0;
				
				foreach ($mediaList as $key => $media) {
					
					$fullImage = $dynamicRoot . 'og_media/nieuwbouw__' . $val['project_NVMVestigingNR'] . '_' . $val['project_ObjectTiaraID']. '/' . $media['bestandsnaam'];
					$tnImage = $dynamicRoot . 'og_media/nieuwbouw__' . $val['project_NVMVestigingNR'] . '_' . $val['project_ObjectTiaraID']. '/' . $media['bestandsnaam_medium'];
					
					echo '<a class="rsImg" href="' . $tnImage . '" data-rsTmb="' . $tnImage . '" data-rsBigImg="' . $fullImage . '"></a>' . PHP_EOL;
					
					$i++;
				}
				
				?>
			
			</div>
		
		</div>
		
	</div>
	
	<?php } ?>

</div>