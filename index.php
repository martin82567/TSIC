<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

// if(isset($_SERVER['HTTPS'])){
//     $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
// }
// else{
//     $protocol = 'http';
// }

// $base_url = $protocol . "://" . $_SERVER['HTTP_HOST'];

// if($base_url == 'https://tsicmentorapp.org'){
// 	return header("Location: ".$base_url."/admin/login ");
// }



$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
