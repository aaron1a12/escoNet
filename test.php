<?php

class customPage extends page {
    public $title = 'Home';

    function content() {?>
<div class="widget">
    <h1>Test <i>Test</i></h1>
    <p style="font-weight:300;">Hello World! <i>Hello World!</i></p>
    <?php {
        /*

        //cho date('Y-m-d H:i:s');
        
        
        $query = "SELECT `id`,`time`,`datetaken` FROM `esco_photos`";
        
        $r = mysqli_query($this->link, $query);
        
        while($pr=mysqli_fetch_row($r))
        {
            $id = $pr[0];
            $time = intval($pr[1]);
            $datetaken = $pr[2];
            
            if($datetaken=='0000-00-00 00:00:00'){
                $photoRow = mysqli_fetch_row(mysqli_query($this->link, "SELECT * FROM `esco_photo_exif` WHERE `photo`=$id"));
                
                $exifData=json_decode($photoRow[1]);

                if(isset($exifData->DateTimeOriginal))
                    $realDate = $exifData->DateTimeOriginal;
                elseif(isset($exifData->DateTime))
                    $realDate = $exifData->DateTime;
                else
                    $realDate = date('Y-m-d H:i:s', $time);
                
                echo 'Photo:'.$id.', '.$datetaken . ', SCANNED DATE: '.$realDate.'<br>';
                
                //mysqli_query($this->link, "UPDATE `esco_photos` SET `datetaken`='$realDate' WHERE `id`=$id;");
            }
        }
        
        
        
        /*
        $insert = 'INSERT INTO `esco_photo_comments` (`photo`, `author`, `time`, `comment`) VALUES';
        $values = array();
        
        $result = mysqli_query($this->link, 'SELECT id,name FROM esco_funny_pic_list'); 
        
        while($row=mysqli_fetch_row($result)){
            $pic = $row[1];
            $picId = $row[0];
            
            $newPic = intval(mysqli_fetch_row(mysqli_query($this->link, 'SELECT `id` FROM `esco_photos` WHERE `title`=\''.$pic.'\''))[0]);
            //echo $pic.' New pic: '.$newPic.'<br>';
            

            if($newPic!=0)
            {
                $cresult = mysqli_query($this->link, "SELECT * FROM `esco_funny_pic_comments` WHERE `pic`=$picId;");
                
                while($commentRow=mysqli_fetch_row($cresult)){
                    $id = $commentRow[0];
                    $pic = $commentRow[1];
                    $author = $commentRow[2];
                    $time = $commentRow[3];
                    $comment = filter_var( $commentRow[4], FILTER_SANITIZE_MAGIC_QUOTES);
                    
                    
                    array_push($values, "\n\t($newPic, $author, $time, '$comment')");
                    
                    //echo $insert . '<br>';
                }
            }
            
           // echo '<hr>';
            

        }
        
        $full_sql = $insert.implode(',', $values).';';
        
        echo '<pre>';
        echo '</pre>';      
        
        //mysqli_query($this->link, $full_sql);
        */
    } ?>
</div>
    <?php }
}

new customPage();