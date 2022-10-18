<?php

if(!function_exists('outputSliderImg')){
    function outputSliderImg($link, $img){
        $format = $img[2]; 
        switch($format){
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
        $imageFile = $img[4] . '_s' . $format;
        $title = $img[3];

        if($title==''){
            $title = $img[4];
            $title = explode('_', $title);

            array_pop($title);
            array_pop($title);

            $title = implode('_', $title);
        }
        

        $authorName = mysqli_fetch_row( mysqli_query($link, "SELECT name,lastname FROM esco_users WHERE id='".$img[1]."' ") );

        echo '<a href="/user/'.$img[1].'/'.urlify($authorName[0].'_'.$authorName[1]).'/photos/'.$img[0].'/'.urlify($title).'"><img style="width:50px; height:50px;" src="http://media.esco.net/img/social/photos/'.$img[1].'/'.$imageFile.'"></a>';        
    }
}


function main($link, $imgId, $imgAuthor, $imgName, $imgFormat, $loggedUser){
    
    if($loggedUser==NULL)
        $unlistedCond = "AND `photo_unlisted`=0";
    else
        $unlistedCond = "AND (`photo_unlisted`=0 OR (`photo_owner`=$loggedUser AND `photo_unlisted`=1))";
        
    
    $sql = 'SELECT * FROM esco_photo_album_assoc WHERE `photo`='.$imgId;
    //echo $sql;
    
    $albumQuery = mysqli_query($link, $sql);
    while($albumAssocRow=mysqli_fetch_row($albumQuery)){
    ?>
    <?php
        $albumRow = mysqli_fetch_row(mysqli_query($link, 'SELECT owner,title FROM esco_photo_albums WHERE id='.$albumAssocRow[2]));
    ?>
    <div class="photoviewer-box">
        In album "<?php echo '<a href="/photos/albums/'.$albumAssocRow[2].'/'.urlify($albumRow[1]).'">'.$albumRow[1].'</a>';?>"

        <div class="img-slider" style="margin-top:5px;">
            <img src="/_inc/img/img-slider-older.png" style="height:50px;">
            <?php
            {
                /*

                */
                $prevSlideResult = mysqli_query($link,
                    "
                    SELECT id,author,format,title,name FROM esco_photos
                    LEFT JOIN (SELECT DISTINCT `photo`,`order` FROM esco_photo_album_assoc WHERE `album`=".$albumAssocRow[2]." $unlistedCond ORDER BY `order` DESC) AS T1
                    ON esco_photos.id = T1.photo WHERE T1.photo is not null AND T1.order<".$albumAssocRow[3]." LIMIT 1
                    "
                );
                $nextSlideResult = mysqli_query($link,
                    "
                    SELECT id,author,format,title,name FROM esco_photos
                    LEFT JOIN (SELECT DISTINCT `photo`,`order` FROM esco_photo_album_assoc WHERE `album`=".$albumAssocRow[2]." $unlistedCond ORDER BY `order` ASC) AS T1
                    ON esco_photos.id = T1.photo WHERE T1.photo is not null AND T1.order>".$albumAssocRow[3]." LIMIT 1
                    "                                 
                );

                $endHTML = '<img src="/_inc/img/img-slider-end.png" style="width:50px;height:50px;">';

                if(mysqli_num_rows($prevSlideResult)==0){
                    echo $endHTML;
                }else{
                    while($slideImgRow = mysqli_fetch_row($prevSlideResult)){
                        outputSliderImg( $link, $slideImgRow );
                    }
                }

                print( '<img title="'.$imgName.'" src="http://media.esco.net/img/social/photos/'.$imgAuthor . '/'. $imgName . '_s' . $imgFormat . '" style="width:50px; height:50px; filter: brightness(0.5);">' );

                if(mysqli_num_rows($nextSlideResult)==0){
                    echo $endHTML;
                }else{
                    while($slideImgRow = mysqli_fetch_row($nextSlideResult)){
                        outputSliderImg( $link, $slideImgRow );
                    }
                }

            }
            ?>
            <img src="/_inc/img/img-slider-newer.png" style="height:50px;">
        </div>    
    </div>        
    <?php
    }
}

if(isset($this)){
    main($this->link, $this->img->id, $this->img->author, $this->img->name, $this->img->format, $this->escoID);
}else{
    class customPage extends page {

        function init(){
            if(!isset($_POST['img']) || !isset($_POST['author']) || !isset($_POST['name']) || !isset($_POST['format']) )
                die('Bad input');
            
            $imgID = intval($_POST['img']);
            $imgAuthor = intval($_POST['author']);
            $imgName = $_POST['name'];
            $imgFormat = $_POST['format'];
            
            main($this->link, $imgID, $imgAuthor, $imgName, $imgFormat, $this->escoID);
            exit;
        }

    }
    new customPage();
}