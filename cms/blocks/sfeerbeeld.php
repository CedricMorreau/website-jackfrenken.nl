<?php

if ($this->getData('blockStatus', $blockId) == 1) {

	$arrData = array();
	$arrData['mediaLink'] = $this->getData('mediaLink', $blockId);

	// 	echo ' data-pp-background="' . $arrData['mediaLink'] . '"';
	
	$this->setCustomVar("sfeerbeeld", $arrData['mediaLink']);
}

?>