<?php

if ($template->getPageId() == 42) {
	
	?>
	
<div class="sidebar-widget">
	<a href="<?php echo $template->findPermalink(57, 1); ?>">
	<h3>
		Zoekt u een bedrijfspand?
	</h3>
	<p>
		Lees meer over onze bedrijfsmakelaardij &xrarr;
	</p>
	</a>
</div>
	
	<?php
}
else {

?>

<div class="sidebar-widget">
	<a href="<?php echo $template->findPermalink(58, 1); ?>">
	<h3>
		Schrijf u in als zoeker!
	</h3>
	<p>
		De woning van uw dromen nog niet kunnen vinden? Wij zoeken graag met u mee &xrarr;
	</p>
	</a>
</div>

<?php } ?>