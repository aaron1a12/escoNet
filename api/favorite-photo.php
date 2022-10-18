<?php
class customPage extends page {    
    function init(){         
        header('Content-type: application/json');
        //header('HTTP/1.1 500 Internal Server Error');
        error_reporting(0);

        if(!isset($_GET['id'])) die();
        if(!isset($_GET['title'])) die();
        if(!isset($_GET['url'])) die();
        
        $userID = $this->escoID;
        $id = intval($_GET['id']);
        $imgTitle = filter_var( $_GET['title'], FILTER_SANITIZE_MAGIC_QUOTES );
        $imgURL = filter_var( $_GET['url'], FILTER_SANITIZE_MAGIC_QUOTES );
                
        $data = new stdClass;
        $data->favorite = false;

        
        $favCount = mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_photo_favs WHERE user='$userID' AND photo='$id';"))[0];
        
        if($favCount==0)
        {
            // Favorite it!
            $data->favorite = true;
            
            mysqli_query($this->link, "INSERT INTO esco_photo_favs (user, photo) VALUES($userID, $id)");
            
            logAction($this, ACTION_PHOTO_FAV, $imgTitle, $imgURL);
        }
        else
        {
            // Unfavorite!
            $data->favorite = false;
            
            mysqli_query($this->link, "DELETE FROM esco_photo_favs WHERE user='$userID' AND photo='$id';");
        }
        
        print( json_encode($data) );
        exit();
    }
}

new customPage();

