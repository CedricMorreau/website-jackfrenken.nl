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

    $_GET['page']=$page;
    require_once 'index.php';
?>