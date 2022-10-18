<?php
require_once( 'sharpen_image.php' );


class customPage extends page {
    public $private = true;
    
    function init(){


        
        ini_set('max_execution_time', 300);	

        // Folder to look for images.
        // Must include trailing slash.
        $uploadFolder = 'uploads/';

        // Find the specific image in the custom header
        $HTTPheaders = getallheaders();

        if(!array_key_exists('X-IMAGE-ID',$HTTPheaders))
            exit();


        // Original Sized Image
        $imageFile = $HTTPheaders['X-IMAGE-ID'];
        
        // Must include trailing slash
        $uploadFolder = dirname($this->siteDirectory).'/media.esco.net/_httpdocs/img/$funny/';

        
        
        if(!file_exists($uploadFolder.$imageFile))
            die('Invalid Request. Will not make thumbnails');

        /*
        ========================================================================
            Time to make the thumbnails!
            ============================
            Steps:
                1-Create a large version with '_o' as the suffix
                2-Create a small version with '_s' as the suffix
                3-Delete the original
                4-Move both versions to /resources/img/hotels/(HOTEL-ID)/

        ========================================================================
        */

        function resizeImage($inputImgPath, $outputImgPath, $quality=100)
        {
            // Get the original size of the image
            list($oWidth, $oHeight) = getimagesize($inputImgPath);
            
            $maxWidth = 960;
            
            
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
            $sourceImage = imagecreatefromjpeg($inputImgPath);

            // Copy from the source image on to the new image, cropping.
            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $oWidth, $oHeight);

            // Slightly Sharpen the image
            //UnsharpMask($newImage, "50", "0.5", "3");

            imagejpeg($newImage, $outputImgPath, $quality);
        }

        
        function makeThumbnail($inputImgPath, $outputImgPath, $width, $height, $quality=100)
        {
            // Get the original size of the image
            list($oWidth, $oHeight) = getimagesize($inputImgPath);


            $wr = $oWidth / $width;             // Width Ratio (source:destination)
            $hr = $oHeight / $height;             // Height Ratio (source:destination)

            $cx = 0;                     // Crop X (source offset left)
            $cy = 0;                     // Crop Y (source offset top)

            if ($hr < $wr)               // Height is the limiting dimension; adjust Width
            {
            $ow = $oWidth;               // Old Source Width (temp)
            $oWidth = $width * $hr;         // New virtual Source Width
            $cx = ($ow - $oWidth) / 2;   // Crops source width; focus remains centered
            }
            if ($wr < $hr)               // Width is the limiting dimension; adjust Height
            {
            $oh = $oHeight;               // Old Source Height (temp)
            $oHeight = $height * $wr;         // New virtual Source Height
            $cy = ($oh - $oHeight) / 2;   // Crops source height; focus remains centered
            }
            // If the width ratio equals the height ratio, the dimensions stay the same.

            // Create a new image
            $newImage = imagecreatetruecolor($width, $height); // Destination image

            // Extract from source image
            $sourceImage = imagecreatefromjpeg($inputImgPath);

            // Copy from the source image on to the new image, cropping.
            imagecopyresampled($newImage, $sourceImage, 0, 0, $cx, $cy, $width, $height, $oWidth, $oHeight);

            // Slightly Sharpen the image
            //UnsharpMask($newImage, "50", "0.5", "3");

            imagejpeg($newImage, $outputImgPath, $quality);
        }
        
        //$imageID = pathinfo($imageToProcess)['filename'];
        $imageID = explode('.', $imageFile)[0];

        // Small Thumb
        
        //Large Version
        resizeImage($uploadFolder.$imageFile, $uploadFolder.$imageID.'_o.jpg', 70);
        unlink( $uploadFolder.$imageFile );
        makeThumbnail($uploadFolder.$imageID.'_o.jpg', $uploadFolder.$imageID.'_s.jpg', 330, 220, 70);
        
        
        //
        // Add it to the database
        //
        
        
        mysqli_query($this->link, 'INSERT INTO esco_funny_pic_list (name) VALUES (\''.$imageID.'\')');
        
        die();
        /*
        
        resizeImage($imageFile, $uploadFolder.$imageID.'_s.jpg', 100, 80, 65);

        

        // Delete Original
        unlink( $imageFile );

        // Move Small
        rename($uploadFolder.$imageID.'/'.$imageID.'_s.jpg', $pathToFinalRestingPlace . $imageID.'_s.jpg');

        // Move Large
        rename($uploadFolder.$imageID.'/'.$imageID.'_o.jpg', $pathToFinalRestingPlace . $imageID.'_o.jpg');

        // Goodbye Folder
        rmdir( $uploadFolder.pathinfo($imageToProcess)['filename'] );
        
        */
    }
}

new customPage();



