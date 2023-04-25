<?php

if (!isset($_COOKIE['sellPopup']) || (isset($_COOKIE['sellPopup']) && $_COOKIE['sellPopup'] != 1)) {

?>

<div class="popup-wrapper sell">
	<div class="popup-close" title="Sluiten">
		<?php include($documentRoot . "resources/icon-close.svg"); ?>	
	</div>

	<h4>De woning van uw dromen <br>nog niet kunnen vinden?</h4>
	<p>
		<a href="<?php echo $template->findPermalink(58, 1); ?>">
			Inschrijven als zoeker &rsaquo;
		</a>
	</p>
</div>

<script>

// Show popup after 10 seconds
setTimeout(function() {
	showPopup();
}, 10000);

</script>

<?php } ?>