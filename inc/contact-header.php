<section class="column-header">
	<div class="content-wrapper">
		<div class="header-main-content">
			<div class="header-title-wrapper animated fadeInLeft">
				<div class="header-title">
					<h1><strong>Goed</strong> en <strong>vertrouwd</strong> <br>voor huis en hypotheek.</h1>
					
					<?php
					
					if (count($locaties) > 0) {
						
						foreach ($locaties as $key => $val) {
							
							$uniId = strtolower(str_replace(' ', '-', $val['cl_name']));
							
							?>
							
					<a href="javascript:void(0);" onclick="javascript:$.scrollTo('#<?php echo $uniId; ?>', 1000)" class="scroll-down"><span class="scroll-text"><?php echo $val['cl_name']; ?></span><span class="arrow">&rsaquo;</span></a>
							
							<?php
						}
					}
					
					?>
				</div>
			</div>

			<div class="content-image animated fadeInRight">
			
				<div id="map_canvas" style="-webkit-transform: translateZ(0px); height: 100%;">
					&nbsp;
				</div>
			
				<!-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d11895.070427079785!2d5.98123814876528!3d51.18903252708682!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c0b4b408f5d97d%3A0x91c92d6f4d90920c!2sGraaf+Reinaldstraat+1%2C+6041+XB+Roermond!5e0!3m2!1snl!2snl!4v1528892968635" width="100%" height="600" frameborder="0" style="border:0" allowfullscreen></iframe> -->
			</div>
		</div>

		<div class="breadcrumbs-row-wrapper">
			<div class="breadcrumbs-spacer"></div>
			<div class="breadcrumbs-wrapper">
				<ul>
					<li>home &rsaquo;</li>
					<li class="active">contact &rsaquo;</li>
				</ul>
			</div>

		</div>

	</div>

</section>