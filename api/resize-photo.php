<?php
  
define('ORIGINAL_SIZE', 0);
define('LARGE_SIZE', 1);
define('COVER_SIZE', 2);
define('THUMB_SIZE', 3);
define('SMALL_SIZE', 4);

require_once( 'sharpen_image.php' );


class customPage extends page {
    public $private = true;
	
    function log($str)
    {
        $log = dirname($this->siteDirectory).'/htdocs_media_esconet/img/social/1/photos/log.txt';
        $fh = fopen($log, 'a');
        $entry =  $str."\r\n";
        fwrite($fh, $entry);
        fclose($fh);        
    }	
    
    function init(){

        ini_set('max_execution_time', 300);	

        // Folder to look for images.
        // Must include trailing slash.
        $uploadFolder = 'uploads/';

        // Find the specific image in the custom header
        $HTTPheaders = getallheaders();

        if(!array_key_exists('X-IMAGE-ID',$HTTPheaders))
            exit();
        if(!array_key_exists('X-SIZE',$HTTPheaders))
            exit();


        // Original Sized Image
        $imageFile = $HTTPheaders['X-IMAGE-ID'];
        $sizeCode = $HTTPheaders['X-SIZE'];
        
        $fileParts = explode('.', $imageFile);
        $fileName = $fileParts[0];
        $fileExt = strtolower($fileParts[count($fileParts)-1]);
        
        
        
        // Must include trailing slash
        $imageFolder = dirname($this->siteDirectory).'/htdocs_media_esconet/img/social/'.$this->escoID.'/photos/';
        $uploadFolder = $imageFolder .  'tmp/';

    
        
        
        if(!file_exists($uploadFolder))
            die('Invalid Request. Will not make thumbnails');

        
        
        
        function resizeImage($inputImgPath, $outputImgPath, $maxWidth, $quality=100)
        {
            // Get the original size of the image
            list($oWidth, $oHeight) = getimagesize($inputImgPath);
            
            if($maxWidth<1)
                $maxWidth = $oWidth;
            
            
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
            
            $fileParts = explode('.', $inputImgPath);
            $fileName = $fileParts[0];
            $fileExt = strtolower($fileParts[count($fileParts)-1]);
            
            if($fileExt=='jpg')
                $sourceImage = imagecreatefromjpeg($inputImgPath);
            elseif($fileExt=='gif')
                $sourceImage = imagecreatefromgif($inputImgPath);
            

            // Copy from the source image on to the new image, cropping.
            imagecopyresampled($newImage, $sourceImage, 0, 0, $cx, $cy, $width, $height, $oWidth, $oHeight);

            // Slightly Sharpen the image
            //UnsharpMask($newImage, "50", "0.5", "3");

            
            
            if($fileExt=='jpg')
                imagejpeg($newImage, $outputImgPath, $quality);
            elseif($fileExt=='gif')
                imagegif($newImage, $outputImgPath);
        }        
        
        function getResized($oWidth, $oHeight, $maxWidth){
            if($maxWidth<1)
                $maxWidth = $oWidth;

            $newWidth = $oWidth;
            $newHeight = $oHeight;

            // Retain aspect
            if($oWidth>$maxWidth)
            {
                $newWidth = $maxWidth;
                $newHeight = $oHeight * ($maxWidth / $oWidth);
            }            
            
            return array($newWidth, $newHeight);
        }
        
        function resizeImageFast($inputImgPath, $outputImgPath, $maxWidth, $quality=100){
            // Get the original size of the image
            list($oWidth, $oHeight) = getimagesize($inputImgPath);
            
            $newSize = getResized($oWidth, $oHeight, $maxWidth);
            
            $image = new Imagick( $inputImgPath );
            
            if($oWidth>2000 && $oHeight>2000) // Image too large to resize fast enough
            {
                if($maxWidth > 1250 ) {
                    // Make a fast and cheap resize to 1250
                    $tempSize = getResized($oWidth, $oHeight, 1250);
                    $image->resizeImage($tempSize[0], $tempSize[1], imagick::FILTER_POINT, 8);
                }
            }
            
            $image->resizeImage($newSize[0], $newSize[1], imagick::FILTER_TRIANGLE, 1);
            $image->sharpenimage(1,4);
            $image->setCompression(100); // Better than not compressing at all?
            $image->writeImage($outputImgPath);
            
            // Compress with slow GD because it's better at it than imagick
            $gdImage = imagecreatefromjpeg($outputImgPath);
            imagejpeg($gdImage, $outputImgPath, $quality);
        }
        
        function compressImage($source, $output, $level)
        {
            //$image = new Imagick( $source );
            //$image->setImageCompressionQuality($level);
            //$image->writeImage( $output );
            
            $img = imagecreatefromjpeg($source);
            imagejpeg($img, $output, $level);            
        }
        

        
        
        switch($sizeCode){
            case ORIGINAL_SIZE:
                
                if($fileExt=='jpg'){
                    copy($uploadFolder.$fileName.'.'.$fileExt, $uploadFolder.$fileName.'_o.'.$fileExt);
                    
                    if( filesize($uploadFolder.$fileName.'_o.'.$fileExt) > 2097152 ){
                        compressImage($uploadFolder.$fileName.'_o.'.$fileExt, $uploadFolder.$fileName.'_o.'.$fileExt, 85);
                    }
                }
                break;
                
            case LARGE_SIZE:
                
                if($fileExt=='jpg'){
                    resizeImageFast($uploadFolder.$fileName.'.jpg', $uploadFolder.$fileName.'_l.jpg', 700, 90);
                }elseif($fileExt=='gif'){
                    list($oWidth, $oHeight) = getimagesize($uploadFolder.$fileName.'.'.$fileExt);
                    
                    if($oWidth>700)
                        resizeImageFast($uploadFolder.$fileName.'.'.$fileExt, $uploadFolder.$fileName.'_l.'.$fileExt, 700, 90);
                    else
                        copy($uploadFolder.$fileName.'.'.$fileExt, $uploadFolder.$fileName.'_l.'.$fileExt);
                }
                break;
                
            case COVER_SIZE:
                
                if($fileExt=='jpg')
                    makeThumbnail($uploadFolder.$fileName.'_l.jpg', $uploadFolder.$fileName.'_c.jpg', 600, 300, 85);
                elseif($fileExt=='gif')
                    makeThumbnail($uploadFolder.$fileName.'_l.gif', $uploadFolder.$fileName.'_c.gif', 600, 300, 85);
                
                break;
                
            case THUMB_SIZE:
                
                if($fileExt=='jpg')
                    makeThumbnail($uploadFolder.$fileName.'_l.jpg', $uploadFolder.$fileName.'_t.jpg', 200, 150, 80);
                elseif($fileExt=='gif')
                    makeThumbnail($uploadFolder.$fileName.'_l.gif', $uploadFolder.$fileName.'_t.gif', 200, 150, 80);
                
                break;
                
            case SMALL_SIZE:
                
                if($fileExt=='jpg')
                    makeThumbnail($uploadFolder.$fileName.'_l.jpg', $uploadFolder.$fileName.'_s.jpg', 50, 50, 75);
                elseif($fileExt=='gif')
                    makeThumbnail($uploadFolder.$fileName.'_l.gif', $uploadFolder.$fileName.'_s.gif', 50, 50, 75);
                
                

                //
                // Move from tmp and add to database
                //
                
                {
                    
                    if($fileExt!='gif'){
                        copy($uploadFolder.$fileName.'_o.'.$fileExt, $imageFolder.$fileName.'_o.'.$fileExt);
                        unlink($uploadFolder.$fileName.'_o.'.$fileExt);
                    }
                    
                    copy($uploadFolder.$fileName.'_l.'.$fileExt, $imageFolder.$fileName.'_l.'.$fileExt);
                    unlink($uploadFolder.$fileName.'_l.'.$fileExt);
                    
                    copy($uploadFolder.$fileName.'_c.'.$fileExt, $imageFolder.$fileName.'_c.'.$fileExt);
                    unlink($uploadFolder.$fileName.'_c.'.$fileExt);
                    
                    copy($uploadFolder.$fileName.'_t.'.$fileExt, $imageFolder.$fileName.'_t.'.$fileExt);
                    unlink($uploadFolder.$fileName.'_t.'.$fileExt);
                    
                    copy($uploadFolder.$fileName.'_s.'.$fileExt, $imageFolder.$fileName.'_s.'.$fileExt);
                    unlink($uploadFolder.$fileName.'_s.'.$fileExt);                        
                        
                        /*
                    copy($uploadFolder.$fileName.'_o.jpg', $imageFolder.$fileName.'_o.jpg');
                    unlink($uploadFolder.$fileName.'_o.jpg');
                    
                    copy($uploadFolder.$fileName.'_l.jpg', $imageFolder.$fileName.'_l.jpg');
                    unlink($uploadFolder.$fileName.'_l.jpg');
                    
                    copy($uploadFolder.$fileName.'_c.jpg', $imageFolder.$fileName.'_c.jpg');
                    unlink($uploadFolder.$fileName.'_c.jpg');
                    
                    copy($uploadFolder.$fileName.'_t.jpg', $imageFolder.$fileName.'_t.jpg');
                    unlink($uploadFolder.$fileName.'_t.jpg');
                    
                    copy($uploadFolder.$fileName.'_s.jpg', $imageFolder.$fileName.'_s.jpg');
                    unlink($uploadFolder.$fileName.'_s.jpg');
                    */
                    
                    $extCode = 0;
                    switch($fileExt){
                        case 'jpg':
                            $extCode = 0;
                            break;
                        case 'png':
                            $extCode = 1;
                            break;
                        case 'gif':
                            $extCode = 2;
                            break;
                    }

                    $author = $this->escoID;
                    $time = time();
                    $year = date('Y');
                    $month = date('n');
                    $format = $extCode;
                    $views = 0;
                    $album = 0;
                    $keywords = '';
                    $title = '';
                    $description = '';
                    $name = $fileName;
                    
                    
                    
                    $title = $name;
                    $title = explode('_', $title);

                    array_pop($title);
                    array_pop($title);

                    $title = implode('_', $title);

                    
                    $photo_query = "INSERT INTO esco_photos (author, time, year, month, format, views, album, keywords, title, description, name) VALUES ('$author', '$time', '$year', '$month', '$format', '$views', '$album', '$keywords', '$title', '$description', '$name')";
                    mysqli_query($this->link, $photo_query);
                
                    $id = mysqli_insert_id($this->link);
					
					transfer($this, 0, $this->escoID, 5);
                    
                    if($title!=''){ $photoTitle = $title; }else{
                        $photoTitle = $name;
                        $photoTitle = explode('_', $photoTitle);

                        array_pop($photoTitle);
                        array_pop($photoTitle);

                        $photoTitle = implode('_', $photoTitle);
                    }
                    
                    //logAction( $this, ACTION_PHOTO_UPLOAD, $photoTitle, $this->escoProfileURL.'/photos/'.$id.'/'.$photoTitle );
					
					
                    
                    if($fileExt=='jpg')
                    {
                        require( $this->siteDirectory . '/_inc/php/thirdparty/exif.php' );
                        
                        $exif = new Exif( $uploadFolder.$fileName.'.jpg' );
                        $exifData = $exif->getFilteredData();
						
						
                        
                        $photo = $id;
                        $json = json_encode($exifData);
						
						//$this->log('Photo ID: '.$photo);

                        $exif_query = "INSERT INTO esco_photo_exif (photo, json) VALUES ('$photo', '$json')";
                        mysqli_query($this->link, $exif_query);

                        // Date: Y-m-d H:i:s
                        
                        if(isset($exifData['DateTimeOriginal']))
                            $dateTime = $exifData['DateTimeOriginal'];
                        elseif(isset($exifData['DateTime']))
                            $dateTime = $exifData['DateTime'];
                        
                        mysqli_query($this->link, "UPDATE `esco_photos` SET `datetaken`='$dateTime' WHERE `id`=$id;");
                        
                        
                    }
                    
                    unlink($uploadFolder.$fileName.'.'.$fileExt);
                        
                }
                
                break;
                
            default:
                die();
        }
        
        echo 'Done Resizing';
            


        
        //$imageID = pathinfo($imageToProcess)['filename'];
        //$imageID = explode('.', $imageFile)[0];

        // Small Thumb
        
        //Large Version
        //resizeImage($uploadFolder.$imageFile, $uploadFolder.$imageID.'_o.jpg', 70);
        //unlink( $uploadFolder.$imageFile );
        //makeThumbnail($uploadFolder. 'profile.jpg', $uploadFolder. 'profile_large.jpg', 230, 230, 90);
        //makeThumbnail($uploadFolder. 'profile_large.jpg', $uploadFolder. 'profile_small.jpg', 50, 50, 100);
        
        // Delete Original
        //unlink( $uploadFolder.'profile.jpg' );
        
        die();
        
        
        /*
        
        resizeImage($imageFile, $uploadFolder.$imageID.'_s.jpg', 100, 80, 65);

        

       
        

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