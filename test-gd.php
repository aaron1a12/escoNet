<?php
//header('Content-type: text/plain');

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;


$img = '/home/pi/www/media.esco.net/_httpdocs/img/social/1/photos/mixer2_0_44632360e3dd77602253_o.jpg';
$img = '/home/pi/www/media.esco.net/test.jpg';

header('Content-type: image/jpg');

// GD VERSION
// Create a new image
$newImage = imagecreatetruecolor(700, 466); // Destination image

// Extract from source image
$sourceImage = imagecreatefromjpeg($img);

// Copy from the source image on to the new image, cropping.
imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, 700, 466, 5184, 3456);


//header('Content-type: text/plain');

imagejpeg($newImage, null, 90);
die();


/*
IMAGIK
$image = new Imagick( $img );
$image->adaptiveResizeImage(1500, 999);
$image->resizeImage(700, 466, imagick::FILTER_TRIANGLE, 1);
$image->sharpenimage(1,4);
$image->setImageCompressionQuality(90);

//$image->resizeImage(1000, 666, imagick::FILTER_POINT, 8);
//$image->sharpenimage(1,4);
*/
//
//die($image);


$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo "Render time: $total_time";


die();