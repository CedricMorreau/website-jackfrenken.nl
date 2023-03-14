<?php
    /*  Friendly Urls
        ================================================
        RewriteEngine On
        RewriteCond %{SCRIPT_FILENAME} !-f [NC]
        RewriteCond %{SCRIPT_FILENAME} !-d [NC]
        RewriteRule ^(.+)$ /index.php?page=$1 [QSA,L]
        ================================================ */

    $root=__dir__;

    $uri=parse_url($_SERVER['REQUEST_URI'])['path'];
    $page=trim($uri,'/');   

    if (file_exists("$root/$page") && is_file("$root/$page")) {
        return false; // serve the requested resource as-is.
        exit;
    }

    function handle_images($content) {

        // Do a quick replace so images are loaded online, not locally
        $content = str_replace('"/og_media', '"https://www.jackfrenken.nl/og_media', $content);

        echo $content;
    }

    function on_die(){

        $content = ob_get_contents();
        ob_end_clean();
        handle_images($content);
    }
    
    register_shutdown_function('on_die');

    $_GET['page'] = $page;
    ob_start();
    include 'index.php';
    $content = ob_get_clean();

    handle_images($content);
?>