<?php
//
// Command-line script to update the top user
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


$pastTopUser = intval(mysqli_fetch_row(mysqli_query($link, "SELECT user FROM esco_user_activity_top"))[0]);
$nextTopUser = intval(mysqli_fetch_row(mysqli_query($link, 'SELECT user, COUNT(*) c FROM esco_user_activity WHERE `user`!=0 GROUP BY user ORDER BY c DESC LIMIT 1'))[0]);

if($nextTopUser!=$pastTopUser){
    $query = "UPDATE esco_user_activity_top SET user=$nextTopUser WHERE id=1;";
    mysqli_query($link, $query);
	
	//mysqli_query($link, "UPDATE `esco`.`esco_user_profiles` SET `badge_cache`='hello' WHERE  `user`=1;");
}

echo $nextTopUser;


exit("\n");