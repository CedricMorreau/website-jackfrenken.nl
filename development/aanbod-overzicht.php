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

			<?php include("../inc/primary-nav.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">

				<?php include("../inc/aanbod-filtering.php"); ?>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
					
					<?php $x = 1;  while($x <= 5) {
						include ("../inc/aanbod-blok.php");
					$x++;	} ?>
					
					<?php include("../inc/aanbod-banner.php"); ?>
					
					<?php include("../inc/paging.php"); ?>
					</div>


				</div>
			</div>

			<?php include("../inc/footer.php"); ?>

		</div>
		
		<?php include("../inc/footer-scripting.php"); ?>

		<script>

		</script>
	</body>

</html>