<?php

if ($this->getData('blockStatus', $blockId) == 1) {

	if (!$this->getData('cmsLabel', $blockId))
		$additionalClass = '';
	else
		$additionalClass = $this->getData('cmsLabel', $blockId);

	// Surrounding divs
	if ($rowType[0] != 2) {

		echo '<div class="col size' . $rowType[1] . ' ' . $lastClass . ' ' . $additionalClass . '">';
	}

?>

	<?php 
	
	$custVar = $this->getCustomVar('hasOptionalSubTitle');

	if ($this->getData('optionalSubTitle', $blockId) && !$custVar) { 

		$optionalTitle = $this->getData('optionalSubTitle', $blockId);

	?>

		<h2><?php echo $optionalTitle; ?></h2>

	<?php

	} 

	$data = $this->getData('tekstValue', $blockId);
	
	echo $this->handlePlaceholders($data);

	if ($rowType[0] != 2) {

		echo '</div>';
	}

?>

<?php } ?>