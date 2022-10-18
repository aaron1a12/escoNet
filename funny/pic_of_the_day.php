<?php

class customPage extends page {    
    function init() {
        function fetchRandom( $link )
        {
            return mysqli_fetch_row( mysqli_query($link, "SELECT * FROM esco_funny_pic_list ORDER BY rand() LIMIT 1 ") );
        }
        
        
        //$currentDayAndHour = date('zH');
        $currentDayAndHour = date('z');
        $lastTimeChecked = mysqli_fetch_row( mysqli_query($this->link, "SELECT data FROM esco_funny_pic_recent WHERE id='1' ") )[0];
        
        
        if($lastTimeChecked!=$currentDayAndHour)
        {   
            // Time to fetch a new funny pic
            
            // Recent Pics
            $recentPics = json_decode( mysqli_fetch_row( mysqli_query($this->link, "SELECT data FROM esco_funny_pic_recent WHERE id='3' ") )[0] );
            
            $newPic_array = fetchRandom( $this->link );
            $newPic = $newPic_array[1];
            
            //
            // As long as the 'random' pic is in the recentPics list we will keep fetching random ones
            //
            
            while( in_array( $newPic, $recentPics ) )
            {
                $newPic_array = fetchRandom( $this->link );
                $newPic = $newPic_array[1];
            }
            
            array_shift($recentPics);
            array_push($recentPics, $newPic);
            
            $json = json_encode($recentPics);
            
            $writeQuery = "UPDATE esco_funny_pic_recent SET data='$json'  WHERE id='3' ";
            mysqli_query($this->link, $writeQuery);
            
            $writeQuery = "UPDATE esco_funny_pic_recent SET data='".$newPic_array[0]."'  WHERE id='2' ";
            mysqli_query($this->link, $writeQuery);
            
            $writeQuery = "UPDATE esco_funny_pic_recent SET data='$currentDayAndHour'  WHERE id='1' ";
            mysqli_query($this->link, $writeQuery);
            
            $this->funnyPic = $newPic;
        }
        else
        {
            // Get the current funny pic
            $funnypicID = mysqli_fetch_row( mysqli_query($this->link, "SELECT data FROM esco_funny_pic_recent WHERE id='2' ") )[0];
            $this->funnyPic = mysqli_fetch_row( mysqli_query($this->link, "SELECT name FROM esco_funny_pic_list WHERE id='$funnypicID' ") )[0];
        }
        
        
        if(!isset($_GET['size']))
            $_GET['size'] = 1;
        else
            $_GET['size'] = intval($_GET['size']);
        
        
        if( $_GET['size'] == 0 )
            $suffix = '_s';
        elseif( $_GET['size'] == 1 )
            $suffix = '_o';
        
        //die($this->funnyPic);
        header('Content-Type: image/jpg');
        readfile('/home/pi/www/media.esco.net/_httpdocs/img/$funny/' . $this->funnyPic . $suffix . '.jpg');
        
        die();
        
        //die($this->funnyPic);
    }
}

new customPage();