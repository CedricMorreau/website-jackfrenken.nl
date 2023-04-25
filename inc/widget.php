<?php

// Extra widget for "Linnerpark, Linne"
if (($template->getPageId() == 40 && $val['id'] == 44) || ($template->getPageId() == 74 && ($val['id'] >= 518 && $val['id'] <= 529))) {

	?>

<div class="sidebar-widget" style="background: #2CBCAF;">
	<a href="<?php echo $template->findPermalink(70, 1); ?>">
	<h3>
		Financieel meer ruimte dankzij de groenverklaring en NOM
	</h3>
	<p>
		Meer weten? Neem contact op &rsaquo;
	</p>
	</a>
</div>

	<?php
}

if ($template->getPageId() == 42) {
	
	?>
	
<div class="sidebar-widget">
	<a href="<?php echo $template->findPermalink(57, 1); ?>">
	<h3>
		Zoekt u een bedrijfspand?
	</h3>
	<p>
		Lees meer over onze bedrijfsmakelaardij &rsaquo;
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
		De woning van uw dromen nog niet kunnen vinden? Wij zoeken graag met u mee
	</p>
	</a>
</div>

<div class="sidebar-widget ghost-white">
	<a href="<?php echo $template->findPermalink(64, 1); ?>">
	<h3>
		Maar wat <strong>betalen</strong><br>
		We dan per maand aan<br>
		<strong>hypotheek?</strong>
	</h3>
	<p>
		Bereken uw hypotheeklasten &rsaquo;
	</p>
	</a>
</div>

<?php } ?>