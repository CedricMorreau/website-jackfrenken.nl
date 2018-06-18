<!doctype html>

<html lang="nl">
	<head>
		<meta charset="utf-8">
		<title>Jack Frenken Makelaars en Adviseurs</title>
		<meta property="og:site_name" content="Jack Frenken Makelaars en Adviseurs">
		<meta property="og:title" content="Jack Frenken Makelaars en Adviseurs">
		<meta property="og:description" content="Jack Frenken Makelaars en Adviseurs">
	  	<meta name="description" content="Jack Frenken Makelaars en Adviseurs">
	  	<meta name="author" content="Pixelplus Interactieve Media">
		
		<?php include($documentRoot . "inc/head.php"); ?>

	</head>

	<body>
		<div class="page-wrapper aanbod-overzicht">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">

				<?php include($documentRoot . "inc/aanbod-filtering.php"); ?>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
					
					<?php $x = 1;  while($x <= 5) {
						include ($documentRoot . "inc/aanbod-blok.php");
					$x++;	} ?>
					
					<?php include($documentRoot . "inc/aanbod-banner.php"); ?>
					
					<?php include($documentRoot . "inc/paging.php"); ?>
					</div>


				</div>
			</div>

			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

		<script>

		</script>
	</body>

</html>