<?php
header('Content-type: image/png');

$base = dirname(dirname(dirname(__FILE__)));
$file = $base .'/'. $_GET['icon'] . '/site.png';
$nofile = dirname(dirname(__FILE__)).'/_inc/img/site.png';

if(file_exists($file)){
    readfile($file);
}
else
{
    readfile($nofile);
}

exit();