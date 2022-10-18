<?php
class customPage extends page {    
    function init(){         
        header('Content-type: application/json');
        //header('HTTP/1.1 500 Internal Server Error');
        error_reporting(0);

        $data = new stdClass;
        
        $data->ok = false;

        $id = rtrim(strtolower($_GET['id']), ' ');
        $id = preg_replace("/[^a-f0-9]/", "", $id);        
        
        $data->id = $id;
        
        $id = pack('H*',$id);
        $id = intval(gzuncompress($id, 0));
        
        //
        // Get _l.jpg
        //
        
        $imgr = mysqli_query($this->link, "SELECT author,format,name FROM esco_photos WHERE id='$id';");
        $numrows = mysqli_num_rows( $imgr );
        $imgRow = mysqli_fetch_row($imgr);


        if($numrows!=0){
            $format = '.jpg';

            switch($imgRow[1]){
                case 0:
                    $format = '.jpg';
                    break;
                case 1:
                    $format = '.png';
                    break;
                case 2:
                    $format = '.gif';
                    break;
            }
            
            $data->ok = true;
            $data->image = 'http://media.esco.net/img/social/photos/'.$imgRow[0].'/'.$imgRow[2].'_l'.$format;
        }
        
        print( json_encode($data) );
        exit();
    }
}

new customPage();