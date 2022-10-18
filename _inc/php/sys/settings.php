<?php
/*
settings.php | v1.0.2 | (c) 2014 Escobar Studios. All rights reserved.
Loads the entries in settings.ini and put's them in $_ENV[]
*/

if(!defined('SITE_BASE'))
    define('SITE_BASE', dirname(dirname(__FILE__)) );

$ConfigFile = SITE_BASE . '/_config/settings.ini';

foreach( parse_ini_file( $ConfigFile ) as $key=>$value )
    $_ENV[$key] = $value;

$_ENV['SETTINGS_LOADED'] = 1;