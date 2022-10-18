<?php
//$_POST['img'] = 74;
//$_POST['album'] = 5;
//$_POST['action'] = 0;

class customPage extends page {
    function init(){        
        header('Content-type: application/json');
        
        $data = new stdClass;
        $data->ok = true;
        
        if(!isset($_POST['img']) || !isset($_POST['album']) || !isset($_POST['action']) ){
            $data->ok = false;
            print(json_encode($data));
            die();        
        }
        
        $img = intval($_POST['img']);
        $album = intval($_POST['album']);
        $action = intval($_POST['action']);
        
        
        //
        // Verify image owner
        //
        
        $imgInfo = mysqli_fetch_row(mysqli_query($this->link, "SELECT `author`,`unlisted` FROM esco_photos WHERE id=$img"));
        $imgAuthor = intval($imgInfo[0]);
        $imgUnlisted = intval($imgInfo[1]);
        
        if($this->escoID != $imgAuthor)
            exit;
        
        //
        // Read album
        //
        
        //$albumInfo = mysqli_fetch_row(mysqli_query($this->link, "SELECT `unlisted` FROM `esco_photo_albums` WHERE id=$album"));
        //$iUnlisted = intval($albumInfo[0]);
        
        //
        // See if it's already added or not
        //
        
        $albumResult = mysqli_query($this->link, "SELECT * FROM esco_photo_album_assoc WHERE `photo`=$img AND `album`=$album");
        $count = intval(mysqli_num_rows($albumResult));
        
        if($action==1){
            //
            // Add
            //
            
            if($count>0) {
                $data->ok = false;
                print(json_encode($data));
                die();                
            }
            
            
            $maxOrder = mysqli_fetch_row(mysqli_query($this->link, "SELECT `order` FROM esco_photo_album_assoc WHERE `album`=$album ORDER BY `order` DESC LIMIT 1"))[0];
            
            if($maxOrder!='')
                $order = intval($maxOrder)+1;
            else
                $order = 0;
            
            
            $query = "INSERT INTO esco_photo_album_assoc (`photo`, `album`, `order`,`photo_owner`,`photo_unlisted`) VALUES ($img, $album, $order, $imgAuthor, $imgUnlisted)";
               
            mysqli_query($this->link, $query);
            
        }elseif($action==0){
            //
            // Remove
            //
            
            if($count<1) {
                $data->ok = false;
                print(json_encode($data));
                die();
            }            
            
            $assocInfo = mysqli_fetch_row($albumResult);
            
            
            $queries = array();
            array_push($queries, "DELETE FROM esco_photo_album_assoc WHERE `id`=".$assocInfo[0].";" );
            
            $result = mysqli_query($this->link, "SELECT * FROM esco_photo_album_assoc WHERE `order`>". $assocInfo[3]);
            
            $order = $assocInfo[3];
            while($assoc=mysqli_fetch_row($result)){
                array_push($queries, "UPDATE esco_photo_album_assoc SET `order`=$order WHERE `id`=".$assoc[0].";");                
                $order++;
            }

            foreach($queries as $query){
                mysqli_query($this->link, $query);
            }
        }
        
        //
        // Output JSON result
        //
        
        print(json_encode($data));
        die();        
    }
}
new customPage();