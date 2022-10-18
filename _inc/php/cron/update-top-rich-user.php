<?php
//
// Command-line script to resize an image
//

if(!isset($argv))
    die('This script is meant for command line use only.');


    
// Load Settings
define( 'SITE_BASE' , dirname(dirname(dirname(__FILE__))) );
require_once( SITE_BASE . '/php/sys/settings.php');

// Connect the database
$link = mysqli_connect($_ENV['db_server'], $_ENV['db_user'], $_ENV['db_pass']);
if(!$link) die('Failed to connect to the database. Error: '.mysqli_connect_errno());
if(!mysqli_select_db( $link, $_ENV['db_name'] )) die('Database does not exist.');


$query = 'SELECT owner FROM esco_bank_accounts WHERE owner!=0 ORDER BY funds DESC LIMIT 1';

$pastTopUser = intval(mysqli_fetch_row(mysqli_query($link, 'SELECT user FROM esco_user_activity_top_rich'))[0]);
$nextTopUser = intval(mysqli_fetch_row(mysqli_query($link, $query))[0]);

if($nextTopUser!=$pastTopUser){
    $query = "UPDATE esco_user_activity_top_rich SET user=$nextTopUser WHERE id=1;";
    mysqli_query($link, $query);
}

echo 'Richest user: '.$nextTopUser;

exit("\n");