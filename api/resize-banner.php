<?php


function log2($str)
{
    $log = 'C:\\xampp\\sites\\htdocs_media_esconet\\img\\social\\62\\log.txt';
    $fh = fopen($log, 'a');
    $entry =  $str."\r\n";
    fwrite($fh, $entry);
    fclose($fh);        
}

require_once( 'sharpen_image.php' );


class customPage extends page {
    public $private = true;



    
    function init(){

        //ini_set('max_execution_time', 300);	

        // Folder to look for images.
        // Must include trailing slash.

        // Find the specific image in the custom header
        $HTTPheaders = getallheaders();

        if(!array_key_exists('X-IMAGE-ID',$HTTPheaders))
            exit();


        // Original Sized Image
        $newBanner = $HTTPheaders['X-IMAGE-ID'];
        
       // die("Received: $newBanner");
       
        
        
        
        // Must include trailing slash
        $uploadFolder = dirname($this->siteDirectory).'/htdocs_media_esconet/img/social/'.$this->escoID.'/';

        
        log2("The new banner is " . $uploadFolder. $newBanner);
        if(!file_exists($uploadFolder. $newBanner))
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
        //$imageID = explode('.', $newBanner)[0];

        // Small Thumb
        
        //Large Version
        //resizeImage($uploadFolder.$newBanner, $uploadFolder.$imageID.'_o.jpg', 70);
        //unlink( $uploadFolder.$newBanner );
        makeThumbnail($uploadFolder. $newBanner, $uploadFolder. $newBanner, 1000, 210, 70);
        
        // Delete Original
       // unlink( $uploadFolder.'profile.jpg' );
        
        
        //
        // Update db
        //



        $previousBanner =  mysqli_fetch_row(mysqli_query($this->link, "SELECT banner FROM esco_user_profiles WHERE user='".$this->escoID."';"))[0];

        if($previousBanner!='')
            unlink( $uploadFolder . $previousBanner );


        $query = "UPDATE esco_user_profiles SET banner='$newBanner' WHERE user='".$this->escoID."';";
        mysqli_query($this->link, $query);
        
        
        die();
        
        
        /*
        
        resizeImage($newBanner, $uploadFolder.$imageID.'_s.jpg', 100, 80, 65);

        

       
        

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