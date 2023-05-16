<div class="filter-list-wrapper">
	<p class="filter-head"><a href="<?php echo $template->findPermalink(88, 1); ?>">Hypotheek <span class="arrow">&rsaquo;</span></a></p>

	<?php if ($template->getPageId() == 33) { $class = ' active'; } else { $class = ''; } ?>
	<p class="filter-head<?php echo $class; ?>"><a href="<?php echo $template->findPermalink(33, 1); ?>">Koopwoningen <span class="arrow">&rsaquo;</span></a></p>
	
	<?php if ($template->getPageId() == 34) { $class = ' active'; } else { $class = ''; } ?>
	<p class="filter-head<?php echo $class; ?>"><a href="<?php echo $template->findPermalink(34, 1); ?>">Huurwoningen <span class="arrow">&rsaquo;</span></a></p>
	
	<?php if ($template->getPageId() == 35) { $class = ' active'; } else { $class = ''; } ?>
	<p class="filter-head<?php echo $class; ?>"><a href="<?php echo $template->findPermalink(35, 1); ?>">Nieuwbouw <span class="arrow">&rsaquo;</span></a></p>
	
	<?php if ($template->getPageId() == 36) { $class = ' active'; } else { $class = ''; } ?>
	<p class="filter-head<?php echo $class; ?>"><a href="<?php echo $template->findPermalink(36, 1); ?>">Bouwkavels <span class="arrow">&rsaquo;</span></a></p>
	
	<?php if ($template->getPageId() == 37) { $class = ' active'; } else { $class = ''; } ?>
	<p class="filter-head<?php echo $class; ?>"><a href="<?php echo $template->findPermalink(37, 1); ?>">Bedrijfspanden <span class="arrow">&rsaquo;</span></a></p>
	
	<?php if ($template->getPageId() == 73) { $class = ' active'; } else { $class = ''; } ?>
	<p class="filter-head<?php echo $class; ?>"><a href="<?php echo $template->findPermalink(73, 1); ?>">Recent verkocht <span class="arrow">&rsaquo;</span></a></p>
</div>