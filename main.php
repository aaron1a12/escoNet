<?php

//
// Find out the page file we need to load
//

define('RANTRA_INCLUDE', '_inc/php/' );

$path = dirname(__FILE__) . '/' . $_GET['page'];

// Search for .php
$pos = stripos($path, '.php');

if ($pos === false) {
    // So we're dealing with a directory. Let's find index.php
    if(substr($path, -1)!='/'){
        $path .= '/';
    }
    
    $path .= 'index.php';
}


require( RANTRA_INCLUDE . 'sys/page.class.php' );


if(file_exists($path))
    include( $path );
else
    include( RANTRA_INCLUDE . 'sys/error_pages/404.php' );
