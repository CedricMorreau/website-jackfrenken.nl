<?php
	
if (isset($injectTitle)) {

	$pageDisplayTitle = $injectTitle;
}
else {

	$pageDisplayTitle = $template->getPageDataMulti('pageTitle');
}

if (isset($injectDescription)) {

	$pageDisplayDescription = $injectDescription;
}
else {

	$pageDisplayDescription = $template->getPageDataMulti('metaTag_1');
}

?>

<meta charset="utf-8">
<title><?php echo $pageDisplayTitle; ?></title>
<meta property="og:site_name" content="Jack Frenken Makelaars en Adviseurs">
<meta property="og:title" content="<?php echo $pageDisplayTitle; ?>">
<meta property="og:description" content="<?php echo $pageDisplayDescription; ?>">
<meta name="description" content="<?php echo $pageDisplayDescription; ?>">
<meta name="web_author" content="Pixelplus Interactieve Media">
	  	
<!-- Favicon -->
<link rel="shortcut icon" href="<?php echo $dynamicRoot; ?>favicon.ico?v=1" type="image/ico">
<!-- Enable responsive view -->
<meta name="viewport" content="width=device-width">
<!-- Force IE to behave -->
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- normalize browser behavior -->
<link rel="stylesheet" href="<?php echo $dynamicRoot; ?>css/normalize.css">
<!-- animation library -->
<link rel="stylesheet" href="<?php echo $dynamicRoot; ?>css/animate.css">
<!-- Google fonts -->
<link href="https://fonts.googleapis.com/css?family=Titillium+Web:200,400,400i,600,700" rel="stylesheet">
<!-- custom styling -->
<link rel="stylesheet" href="<?php echo $dynamicRoot; ?>css/style.css">

<!--[if lt IE 9]>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
<![endif]-->

<!-- Jquery 3.3.1 minified -->
<script
  src="http://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous">
</script>