<?php

if ($totalRows > 0) {

?>

<div class="paging-wrapper">

	<?php
	
	$current = (($perPage) * ($currentPage - 1)) + 1;
	$last = $perPage * $currentPage;
	
	if ($last > $totalRows)
		$last = $totalRows;
	
	?>
	
	<div class="text-wrapper">
		Objecten <span class="bold"><?php echo $current; ?> t/m <?php echo $last; ?></span> van <span class="bold"><?php echo $totalRows; ?></span>
	</div>
	
	<?php
	
	if ($totalRows > $perPage) {
		
		$pagingClass = new PP_Paging($totalPages, $perPage, $currentPage, $pagingUrl);
	
	?>
	
	<div class="numbers-wrapper">
	
		<?php echo $pagingClass->displayLeft('&xlarr;'); ?>
		<?php echo $pagingClass->displayPages(); ?>
		<?php echo $pagingClass->displayRight('&xrarr;'); ?>		
		
	</div>

	<?php } ?>
	
</div>

<?php } ?>