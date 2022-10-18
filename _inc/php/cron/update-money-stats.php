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


$userID = 2;

// TODO: Loop?

$result = mysqli_query($link, "SELECT funds FROM esco_bank_accounts WHERE owner=$userID LIMIT 1");

if(mysqli_num_rows($result)>0){
    $funds = mysqli_fetch_row($result)[0];
    mysqli_query($link, "INSERT INTO esco_money_stats (user, funds) VALUES ($userID, $funds)");
}

echo "User: $userID, Funds: $funds";

exit("\n");