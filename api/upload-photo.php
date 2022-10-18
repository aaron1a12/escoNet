<?php
/*
    Original Code by Craig Buckler (@craigbuckler) of OptimalWorks.net
    Ported by Aaron Escobar
*/

define('CURRENT_UPLOAD_SERVER_ID', 0);

function get_random_string($valid_chars, $length)
{
    // start with an empty random string
    $random_string = "";

    // count the number of chars in the valid chars string so we know how many choices we have
    $num_valid_chars = strlen($valid_chars);

    // repeat the steps until we've created a string of the right length
    for ($i = 0; $i < $length; $i++)
    {
        // pick a random number from 1 up to the number of valid chars
        $random_pick = mt_rand(1, $num_valid_chars);

        // take the random character out of the string of valid chars
        // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
        $random_char = $valid_chars[$random_pick-1];

        // add the randomly-chosen char onto the end of our string so far
        $random_string .= $random_char;
    }

    // return our finished random string
    return $random_string;
}


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
    
    
    function changeExtension( $fileNameStr, $newExt ){
        $parts = explode('.', $fileNameStr);
        $parts[ count($parts)-1 ] = $newExt;
        return implode('.', $parts);
    }
    
    
    function init(){
        
        $fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
        
        if ($fn)
        {
            $id = CURRENT_UPLOAD_SERVER_ID .'_'. get_random_string('0123456789abcdef', 20);

            // Must include trailing slash
            $uploadFolder = dirname($this->siteDirectory).'/htdocs_media_esconet/img/social/' .  $this->escoID . '/photos/tmp/';

            //log($uploadFolder);

            // First let's dedicate a tmp folder for the image
            if(!file_exists($uploadFolder . $this->escoID))
                mkdir( $uploadFolder . $this->escoID );
            
            //mkdir( $uploadFolder . $id);
            
            $ext = strtolower(pathinfo($fn)['extension']);

            /*
               We place the image in the tmp folder like this
               /[id]/[id].jpg
            */
            /*
            if($ext!='jpg' && $ext!='jpeg'){
                header('HTTP/1.1 400 Bad Request');
                die();
            }
            */
            
            $newPhoto = urlify(strtolower(substr(pathinfo($fn)['filename'],0,40))).'_'.$id.'.' . $ext;
            //$newPhoto = substr($newPhoto,0, 50);

            
            
            // Only works for AJAX calls
            file_put_contents(
                $uploadFolder . $newPhoto,
                file_get_contents('php://input')
            );        

            $fileSize = filesize( $uploadFolder . $newPhoto );
            
            //
            // Check the image format
            //
            
            $realFormat = exif_imagetype( $uploadFolder . $newPhoto );

            
            if( $realFormat != IMAGETYPE_JPEG && $realFormat != IMAGETYPE_PNG && $realFormat != IMAGETYPE_GIF)
            {
                echo 'Invalid image format';
                header('HTTP/1.1 400 Bad Request');
                die();
            }
            
            $oldName = $newPhoto;
            
            switch($realFormat){
                case IMAGETYPE_JPEG:
                    if($ext!='jpg')
                        $newPhoto = $this->changeExtension($newPhoto, 'jpg');
                        rename($uploadFolder.$oldName, $uploadFolder.$newPhoto);
                    break;
                case IMAGETYPE_PNG:
                    if($ext!='png')
                        $newPhoto = $this->changeExtension($newPhoto, 'png');
                        rename($uploadFolder.$oldName, $uploadFolder.$newPhoto);
                    break;   
                case IMAGETYPE_GIF:
                    if($ext!='gif')
                        $newPhoto = $this->changeExtension($newPhoto, 'gif');
                        rename($uploadFolder.$oldName, $uploadFolder.$newPhoto);
                    break;                       
            }
            
            $this->log("Photo: $newPhoto, Image Type: $realFormat, Extension: $ext, Size: $fileSize");
            
            // Return to Javascript
            echo $newPhoto;          
            
            
            if($fileSize>6000000){
                header('HTTP/1.1 400 Bad Request'); //<-Causes a fatal error in the uploader and stop uploading the rest
                unlink( $uploadFolder.$newPhoto );
            }
            
            exit();
        }
        else
        {
            echo 'No File...';
            exit();
        }
       
    }
}

new customPage();