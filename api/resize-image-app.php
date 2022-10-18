<?php
//
// Command-line script to resize an image
//

if(!isset($argv))
    die('This script is meant for command line use only.');//die('This script cannot be run in the browser.');

if(count($argv)!=7)
    die("\n".'Bad usage. Example: resize-image-app.php input.jpg output.jpg width=500 height=300 crop=false quality=70' . "\n\n");


$inputIMG = $argv[1];
$outputIMG = $argv[2];

$width = 0;
$height = 0;
$quality = 0;

$crop = false;

array_shift($argv);
array_shift($argv);
array_shift($argv);

foreach($argv as $arg)
{
    $argParts = explode('=', strtolower($arg));
    switch($argParts[0])
    {
        case 'width':
            $width = intval($argParts[1]);
            break;
        case 'height':
            $height = intval($argParts[1]);
            break;
        case 'quality':
            $quality = intval($argParts[1]);
            break;
        case 'crop':
            if($argParts[1]=='true' || $argParts[1]=='1')
                $crop = true;
            break;
    }
}


if(!$crop)
{
    list($oWidth, $oHeight) = getimagesize($inputIMG);

    $maxWidth = $width;


    $newWidth = $oWidth;
    $newHeight = $oHeight;

    // Retain aspect
    if($oWidth>$maxWidth)
    {
        $newWidth = $maxWidth;
        $newHeight = $oHeight * ($maxWidth / $oWidth);
    }


    // Create a new image
    $newImage = imagecreatetruecolor($newWidth, $newHeight); // Destination image

    // Extract from source image
    $sourceImage = imagecreatefromjpeg($inputIMG);

    // Copy from the source image on to the new image, cropping.
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $oWidth, $oHeight);

    // Slightly Sharpen the image
    //UnsharpMask($newImage, "50", "0.5", "3");

    imagejpeg($newImage, $outputIMG, $quality);
}



//echo "Input: $inputIMG\nOutput: $outputIMG\nWidth: $width\nHeight: $height\nQuality: $quality\nCrop: ";
//var_dump($crop);

exit();