<!doctype html>

<html lang="nl">
	<head>
		<meta charset="utf-8">
		<title>Jack Frenken Makelaars en adviseurs</title>
		<meta property="og:site_name" content="Jack Frenken Makelaars en adviseurs">
		<meta property="og:title" content="Jack Frenken Makelaars en adviseurs">
		<meta property="og:description" content="Jack Frenken Makelaars en adviseurs">
	  	<meta name="description" content="Jack Frenken Makelaars en adviseurs">
	  	<meta name="author" content="Pixelplus Interactieve Media">
		
		<?php include("../inc/head.php"); ?>

	</head>

	<body>
		<div class="page-wrapper actueel-overzicht">

			<?php include("../inc_dev/primary-nav.php"); ?>

			<?php include("../inc/2-col-header.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">

				<?php include("../inc/sidebar-nav.php"); ?>

				<?php include("../inc/widget.php"); ?>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
						<section class="items-wrapper">
							
							<div class="items-container">
								
								<?php $x = 1;  while($x <= 5) {

									include("../inc/actueel-blokken.php");
								
								$x++;	} ?>

							</div>

						</section>

					<?php include("../inc/paging.php"); ?>
					</div>


				</div>
			</div>

			<?php include("../inc_dev/footer.php"); ?>

		</div>
		
		<?php include("../inc/footer-scripting.php"); ?>

		<script>

		</script>
	</body>

</html>