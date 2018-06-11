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

			<?php include("../inc/2-col-header.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">

					<ul class="sidebar-nav">
						<li><a href="#">Een huis verkopen</a></li>
						<li><a href="#">Een huis verkopen</a></li>
						<li class="active"><a href="">Een huis verkopen &xrarr;</a></li>
						<li><a href="#">Een huis verkopen</a></li>
						<li><a href="#">Een huis verkopen</a></li>
					</ul>

					<div class="sidebar-widget">
						<h3>
							<a href="">Schrijf u in als zoeker!</a>
						</h3>
						<p>
							<a href="">De woning van uw dromen nog niet kunnen vinden? Wij zoeken graag met u mee &xrarr;</a>
						</p>
					</div>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
						<section class="items-wrapper">
							
							<div class="items-container">
								<?php $x = 1;  while($x <= 2) { ?>
									<div class="item-container">
										<div class="item-image-wrapper">
											<div class="item-image">
												&nbsp;
											</div>
											<a href="#" class="item-button">&xrarr;</a>
										</div>
										
										<p class="item-title"><a href="#">Verkoop Neldervelt gestart!</a></p>

									</div>

									<div class="item-container">
										<div class="item-image-wrapper">
											<div class="item-image">
												&nbsp;
											</div>
											<a href="#" class="item-button">&xrarr;</a>
										</div>
										<p class="item-title"><a href="#">Nog enkele appartementen te huur!</a></p>

									</div>
								<?php $x++;	} ?>
							</div>
				
						</section>

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