	
	<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyATO501GwK6eyvxPwA6TIdbmc_PcfKvPAg"></script>
	<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/cluster_google-maps.js"></script>
	<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/markerclusterer_compiled.js"></script>
	<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/scripting_google.js"></script>
	<script type="text/javascript">

		function showOverlay(type) {

			// Hide all others
			$('[id^="vestiging_"]').hide();

			// Show it
			$('#' + type).show();
		}

	<?php

	$counter = 0;

	foreach ($locaties as $sKey => $sVal) {

		?>

		arr_markerData[<?php echo $counter; ?>] = func_markerData(<?php echo $sVal['cl_coord_lat']; ?>,<?php echo $sVal['cl_coord_lon']; ?>, '<?php echo $sKey; ?>', 'vestiging', 1);
		totalBounds.extend(new google.maps.LatLng(<?php echo $sVal['cl_coord_lat']; ?>,<?php echo $sVal['cl_coord_lon']; ?>));

		<?php

		$counter++;
	}

	if (defined('EXCEPTIONALBS')) {

		?>

	arr_googleMapsData[0] = func_googleMapsData('map_canvas',51.1357792,5.9003766,11,'multiple_clustered',true);

		<?php
	}
	else {

		?>

	arr_googleMapsData[0] = func_googleMapsData('map_canvas',50.9107792,5.9403766,11,'multiple_clustered',true);

		<?php
	}

	?>

	
	func_openGoogleMaps(null,arr_googleMapsData,arr_markerData);

	<?php
	
	if (count($locaties) > 1) {
	
	?>
	//fitBounds('');
	<?php } ?>
	getPosition();
	</script>