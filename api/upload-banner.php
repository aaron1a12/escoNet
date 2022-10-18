<?php
/*
    Original Code by Craig Buckler (@craigbuckler) of OptimalWorks.net
    Ported by Aaron Escobar
*/


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
        $log = dirname($this->siteDirectory).'/htdocs_media_esconet/img/social/62/log.txt';
        $fh = fopen($log, 'a');
        $entry =  $str."\r\n";
        fwrite($fh, $entry);
        fclose($fh);        
    }    
    
    function init(){
        //$this->log('Hello World!');
        
        $fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
        
        if ($fn)
        {
            $id = get_random_string('0123456789abcdef', 12);

            // Must include trailing slash
            $uploadFolder = dirname($this->siteDirectory).'/htdocs_media_esconet/img/social/' .  $this->escoID . '/';


            // First let's dedicate a tmp folder for the image
            if(!file_exists($uploadFolder . $this->escoID))
                mkdir( $uploadFolder . $this->escoID );
            

            //$this->log($uploadFolder);
            //mkdir( $uploadFolder . $id);

            $ext = strtolower(pathinfo($fn)['extension']);

            /*
               We place the image in the tmp folder like this
               /[id]/[id].jpg
            */
            
            if($ext!='jpg' && $ext!='jpeg'){
                header('HTTP/1.1 400 Bad Request');
                die();
            }

            
            $newBanner = 'banner_'.$id.'.' . $ext;
            
            
            // Only works for AJAX calls
            file_put_contents(
                $uploadFolder . $newBanner,
                file_get_contents('php://input')
            );        

            echo $newBanner;          
            
            
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