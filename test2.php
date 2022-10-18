<?php
//header('Content-type: text/plain');

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;


$img = '/home/pi/www/media.esco.net/_httpdocs/img/social/1/photos/mixer2_0_44632360e3dd77602253_o.jpg';
$img = '/home/pi/www/media.esco.net/test.jpg';

//$image->resizeImage(1000, 666, imagick::FILTER_POINT, 8);

$image = new Imagick( $img );
//$image->adaptiveResizeImage(1500, 999);
//$image->resizeImage(1500, 999, imagick::FILTER_POINT, 8);
$image->resizeImage(1250, 832, imagick::FILTER_POINT, 8);
$image->resizeImage(700, 466, imagick::FILTER_TRIANGLE, 1);
$image->sharpenimage(1,4);
$image->writeImage('imagick.jpg');
$gdImage = imagecreatefromjpeg('imagick.jpg');
imagejpeg($gdImage, 'imagick.jpg', 90);





//header('Content-type: image/jpg');
//die($image);

header('Content-type: text/plain');
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo "Render time: $total_time";


die();