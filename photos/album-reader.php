<?php

class customPage extends page {
    
    public $user;
    public $album;
    public $profileLink;
    
    function init()
    {
        $this->album = new stdClass;
        $this->album->id = intval($_GET['id']);
        
        $result = mysqli_query($this->link, 'SELECT owner,title FROM esco_photo_albums WHERE id='.$this->album->id);
        
        $albumRow = mysqli_fetch_row($result);
        
        $this->album->owner = $albumRow[0];
        $this->album->title = $albumRow[1];

        $this->user = mysqli_fetch_assoc(mysqli_query($this->link, 'SELECT * FROM esco_users WHERE id="'.$this->album->owner.'"'));        
        $this->user['photoBase'] = '/user/'.$this->user['id'].'/'.urlify($this->user['name'].'_'.$this->user['lastname']).'/photos';

        $this->title = 'Photo Album "'.$this->album->title.'"';    
    }
    
    function content()
    {
?>
<div class="widget nopadding">
    
    <div class="paddedContent">
        <div>
        <h1><?php echo $this->album->title;?></h1>
            <small>Album created by <?php echo '<a href="'.$this->user['photoBase'].'">'.$this->user['name'].' '.$this->user['lastname'].'</a>';?></small>
        </div>    
                
        <div style="margin-top:20px;">
            <?php
            {
                $this->document = new DOMDocument;

                $this->table = $this->document->createElement('table');    

                $this->table->setAttribute('cellspacing', 0);
                $this->table->setAttribute('cellpadding', 00);        
                $this->table->setAttribute('style', '');

                $this->tr = $this->document->createElement('tr');
                $this->i = 0;
                
                if($this->loggedIn)
                    $unlistedCond = "AND (`photo_unlisted`=0 OR (`photo_owner`=".$this->escoID." AND `photo_unlisted`=1))";
                else
                    $unlistedCond = "AND `photo_unlisted`=0";

                $photoPages = new paginator($this, "
                    SELECT SQL_CALC_FOUND_ROWS id,author,time,format,views,album,title,name FROM  esco_photos
                    LEFT JOIN (SELECT DISTINCT `photo` FROM esco_photo_album_assoc WHERE `album`=".$this->album->id."  $unlistedCond ORDER BY `order` ASC) AS T1
                    ON esco_photos.id = T1.photo WHERE T1.photo is not null                    
                    ", 42, 9,
                    function($photoRow){
                        $this->i++;

                        $query = 'SELECT * FROM esco_users WHERE id="'.$photoRow[1].'"';
                        $result = mysqli_query($this->link, $query);
                        $num_rows = mysqli_num_rows($result);

                        $this->user = mysqli_fetch_assoc($result);
                        $this->user['photoBase'] = '/user/'.$this->user['id'].'/'.urlify($this->user['name'].'_'.$this->user['lastname']).'/photos';                



                        $photoTitle = $photoRow[6];

                        if($photoTitle!=''){ $photoTitle = $photoRow[6]; }else{
                            $photoTitle = $photoRow[7];
                            $photoTitle = explode('_', $photoTitle);

                            array_pop($photoTitle);
                            array_pop($photoTitle);

                            $photoTitle = implode('_', $photoTitle);
                        }

                        $photoURL = $this->user['photoBase'] . '/'. $photoRow[0] . '/' . urlify($photoTitle);


                        if( $this->i==7 ){
                            $this->i = 1;
                            // Add the row now and create another row
                            $this->table->appendChild($this->tr);
                            $this->tr = $this->document->createElement('tr');
                        }

                        $br = $this->document->createElement('br');

                        $td = $this->document->createElement('td');

                        $a = $this->document->createElement('a');
                        $a->setAttribute('href', $photoURL);

                        $img = $this->document->createElement('img');
                        $img->setAttribute('style', 'width:156px; height:117px;');

                        $a->appendChild($img);         
                        $td->appendChild($a);
    
                        $imageName = $photoRow[7];

                        $format = $photoRow[3]; 
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
                        
                        $imageFile = $imageName . '_t' . $format;

                        $parts = explode('_', $imageName);
                        $serverID = $parts[ count($parts)-2 ];

                        $photo = 'http://media.esco.net/img/social/photos/'.$photoRow[1].'/'.$imageFile;

                        $img->setAttribute('src', $photo);
                        $img->setAttribute('alt', $photoTitle);
                        //$img->setAttribute('style', 'width:200px; height:150px;');

                        // Add the cell
                        $this->tr->appendChild( $td );                

                }, false);
                

                $this->table->appendChild($this->tr);
                $this->document->appendChild( $this->table );
                
                $this->table->setAttribute('class', 'image-grid-all');


                echo '<div style="float:right;">';
                echo $photoPages->showPages();
                echo '</div><div class="cf"></div>';
                
                echo $this->document->saveHTML();
                        
                echo '<div style="float:right;margin-top:20px;">';
                echo $photoPages->showPages();
                echo '</div><div class="cf"></div>';
            }
     
            if($this->i==0){
                echo '<h2>No photos have been added yet.</h2>'; 
            }
            ?>
        </div>
        
    </div>
</div>
<?php
    }
}

new customPage();