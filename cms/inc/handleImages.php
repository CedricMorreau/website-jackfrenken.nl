<?php

include('config.php');

if (isset($_GET['hash'])) {
    
    // Query the database; find the image
    $image = $cms['database']->prepare("SELECT * FROM `tbl_mod_media` WHERE `mod_me_hash`=? LIMIT 1", "s", array($_GET['hash']));
    
    if (count($image) > 0) {
    	
    	if (isset($_GET['thumb']) && is_numeric($_GET['thumb']))
    		$fileName = $documentRoot . 'upload/media/' . $image[0]['mod_me_folderId'] . '/' . $image[0]['mod_me_hash'] . '_thumb_' . $_GET['thumb'] . '.' . $image[0]['mod_me_extension'];
    	else 
    		$fileName = $documentRoot . 'upload/media/' . $image[0]['mod_me_folderId'] . '/' . $image[0]['mod_me_hash'] . '.' . $image[0]['mod_me_extension'];
        
    	if (file_exists($fileName)) {

            // Grab last modified
    		$lastModified = filemtime($fileName);
            $lastModifiedRead = date("D, d M Y H:i:s \G\M\T", $lastModified);
        
            // Kick in headers
            switch(strtolower($image[0]['mod_me_extension'])) {

                case "gif":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24 * 365)));
                    header("Content-type: image/gif");
                    break;

                case "jpg":
                case "jpeg":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24 * 365)));
                    header("Content-type: image/jpeg");
                    break;

                case "png":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24 * 365)));
                    header("Content-type: image/png");
                    break;

                case "bmp":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24 * 365)));
                    header("Content-type: image/bmp");
                    break;

                case "svg":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24 * 365)));
                    header("Content-type: image/svg+xml");
                    break;

                case "pdf":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24)));
                    header("Content-type: application/pdf");
                    break;

                case "vcf":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24)));
                    header("Content-type: text/vcard");
                    break;

                case "xls":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24)));
                    header("Content-type: application/vnd.ms-excel");
                    break;

                case "xlsx":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24)));
                    header("Content-type: application/vnd.ms-excel");
                    break;

                case "doc":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24)));
                    header("Content-type: application/msword");
                    break;

                case "docx":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24)));
                    header("Content-type: application/msword");
                    break;

                case "eps":

                    $expireDate = date("D, d M Y H:i:s \G\M\T", (time() + (3600 * 24)));
                    header("Content-type: application/postscript");
                    break;
            }

            header("Accept-Ranges: bytes");
            header('Content-Length: ' . $image[0]['mod_me_size']);
            header("Last-Modified: " . $lastModifiedRead);
            header("Expires: " . $expireDate);
            header_remove("Cache-Control");
            header_remove("Pragma");
            
            $arrImgTypes = array('jpg', 'jpeg', 'gif', 'png');
            
            if (in_array(strtolower($image[0]['mod_me_extension']), $arrImgTypes)) {
            	
            	if (!empty($image[0]['mod_me_extraOne']) || !empty($image[0]['mod_me_extraTwo'])) {
            
            		if (strtolower($image[0]['mod_me_extension']) == 'jpg' || strtolower($image[0]['mod_me_extension']) == 'jpeg')
            			$im = imagecreatefromjpeg($fileName);
	            	elseif (strtolower($image[0]['mod_me_extension']) == 'png')
	            		$im = imagecreatefrompng($fileName);
            		elseif (strtolower($image[0]['mod_me_extension']) == 'gif')
            			$im = imagecreatefromgif($fileName);
		            
		            function shadow_text($im, $size, $x, $y, $font, $text)
		            {
		            	$black = imagecolorallocate($im, 0, 0, 0);
		            	$white = imagecolorallocate($im, 255, 255, 255);
		            	imagettftext($im, $size, 0, $x + 1, $y + 1, $black, $font, $text);
		            	imagettftext($im, $size, 0, $x + 0, $y + 1, $black, $font, $text);
		            	imagettftext($im, $size, 0, $x + 0, $y + 0, $white, $font, $text);
		            }
		            
		            $font = $documentRoot . 'fonts/Museo500-Regular.ttf';
		            $size = 10;
		            
		            if (!empty($image[0]['mod_me_extraOne'])) {
		            
		            	$text = $image[0]['mod_me_extraOne'];
		            
			            # calculate maximum height of a character
			            $bbox = imagettfbbox($size, 0, $font, $text);
			            
			            $textWidth = abs($bbox[4] - $bbox[0]);
			            $x = (imagesx($im) - $textWidth) - 8;
			            
			            $y = imagesy($im) - 8;
		            }
		            
		            if (!empty($image[0]['mod_me_extraTwo'])) {
		            	
		            	$text2 = $image[0]['mod_me_extraTwo'];
		            	
		            	# calculate maximum height of a character
		            	$bbox2 = imagettfbbox($size, 0, $font, $text2);
		            	
		            	$textWidth = abs($bbox2[4] - $bbox2[0]);
		            	$x2 = (imagesx($im) - $textWidth) - 8;
		            	
		            	$y2 = imagesy($im) - 8;
		            	
		            	if (!empty($text))
		            		$y = $y - 16;
		            }
		            
		            if (!empty($text))
		            	shadow_text($im, $size, $x, $y, $font, $text);
		            
	            	if (!empty($text2))
		            	shadow_text($im, $size, $x2, $y2, $font, $text2);
            	
	            	if (strtolower($image[0]['mod_me_extension']) == 'jpg' || strtolower($image[0]['mod_me_extension']) == 'jpeg')
	            		imagejpeg($im, null, 90);
            		elseif (strtolower($image[0]['mod_me_extension']) == 'png')
	            		imagepng($im);
            		elseif (strtolower($image[0]['mod_me_extension']) == 'gif')
	            		imagegif($im);
		            
		            die();
            	}
            }

            readfile($fileName);
        }
    }
}