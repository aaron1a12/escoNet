<?php

class customPage extends page {
    
    public $user;
    public $img;
    public $profileLink;
    
    function init()
    {
        $this->img = new stdClass;
        
        if(!isset($_GET['usr-id']))
            die();
        
        $userid = intval($_GET['usr-id']);
        
        
        $query = 'SELECT * FROM esco_users WHERE id="'.$userid.'"';
        $result = mysqli_query($this->link, $query);
        $num_rows = mysqli_num_rows($result);
        $this->user = mysqli_fetch_assoc($result);
        
        $this->profile =  mysqli_fetch_assoc(mysqli_query($this->link, "SELECT * FROM esco_user_profiles WHERE user='$userid';"));
        
        $this->user['photoBase'] = '/user/'.$this->user['id'].'/'.urlify($this->user['name'].'_'.$this->user['lastname']).'/photos';

        $this->title = 'Photos by '.$this->user['name'].' '.$this->user['lastname'];    
        
        if(!isset($_GET['img-id']))
            $this->img->id = 0;
        else
            $this->img->id = intval($_GET['img-id']);
        
        $this->profileLink = '/user/'.$this->user['id'].'/'.urlify($this->user['name'].' '.$this->user['lastname']);
    }
    
    function head(){ ?>
<script>
    function deleteImage( imgCode ){
        var ok = confirm('Really delete?');
        
        if(ok)
        {
            $.ajax({
                type: "GET",
                url : "/user/photo-uploader/deleter.php",
                data: { confirm:1, image:imgCode }
            }).done(function(data){
                location.reload();
            }); 
        }
        
    }
</script>
<?php
    }
    
    function content()
    {
?>
<div class="widget nopadding">
    <?php
     include($this->siteDirectory.'/_inc/php/user-photo-header.php');
    ?>
    
    <div class="paddedContent">
        <h2>Photostream</h2>
        <div style="float:left;">
            <?php
            {
                $this->document = new DOMDocument;

                $this->table = $this->document->createElement('table');    

                $this->table->setAttribute('cellspacing', 0);
                $this->table->setAttribute('cellpadding', 20);        
                $this->table->setAttribute('style', 'margin-left:-20px;');

                $this->tr = $this->document->createElement('tr');
                $this->i = 0;
                
                
                if($this->escoID!=$this->user['id'])
                    $unlistedCond = 'AND `unlisted`=0';
                else
                    $unlistedCond = '';

                $photoPages = new paginator($this, "
                    SELECT SQL_CALC_FOUND_ROWS id,author,time,format,views,album,title,name,unlisted FROM esco_photos WHERE author=".$this->user['id']." $unlistedCond ORDER BY id DESC
                    ", 24, 9,
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


                        if( $this->i==4 ){
                            $this->i = 1;
                            // Add the row now and create another row
                            $this->table->appendChild($this->tr);
                            $this->tr = $this->document->createElement('tr');
                        }

                        $br = $this->document->createElement('br');

                        $td = $this->document->createElement('td');
                        $td->setAttribute('style', 'width:200px; font-size:12px;vertical-align:top;');

                        $a = $this->document->createElement('a');
                        $a->setAttribute('href', $photoURL);

                        $img = $this->document->createElement('img');

                        $bLabel = $this->document->createElement('div');
                        $bLabel->setAttribute('class', 'wrap');
                        $bLabel->setAttribute('style', 'font-weight:bold;');
                        $bLabel->nodeValue = $photoTitle;



                        $a->appendChild($img);         
                        $td->appendChild($a);
                        $td->appendChild($br);
                        $td->appendChild($bLabel);


                        if($photoRow[1]==$this->escoID){
                            $aEditLink = $this->document->createElement('a');
                            $aEditLink->nodeValue = 'Edit Details';
                            $aEditLink->setAttribute('href', '/user/photo-uploader/editor.php?image='. urlencode(base64_encode($photoRow[0])));

                            $aDeleteLink = $this->document->createElement('a');
                            $aDeleteLink->nodeValue = 'Delete';
                            $aDeleteLink->setAttribute('style', 'color:#aa1100;cursor:pointer;');
                            $aDeleteLink->setAttribute('onclick', 'deleteImage(\''. urlencode(base64_encode($photoRow[0])).'\');');
                            $td->appendChild($br->cloneNode());
                            $td->appendChild($aEditLink);
                            $td->appendChild($this->document->createTextNode(' | '));
                            $td->appendChild($aDeleteLink);
                        }                              



                        $extraDiv = $this->document->createElement('div');
                        $extraDiv->setAttribute('style', 'color:#aaa;');
                        $extraDiv->appendChild($this->document->createTextNode('Views: '));

                        $bViews = $this->document->createElement('b');
                        $bViews->nodeValue = number_format(intval($photoRow[4]));


                        $extraDiv->appendChild($bViews);

                        $td->appendChild($extraDiv);

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

                        
                        $style = 'width:200px; height:150px;';
                        
                        if(intval($photoRow[8])==1)
                            $style .= 'opacity:0.65;';
                        
                        $img->setAttribute('src', $photo);
                        $img->setAttribute('alt', $photoTitle);
                        $img->setAttribute('style', $style);
                        
                        

                        // Add the cell
                        $this->tr->appendChild( $td );                

                }, false);

                $this->table->appendChild($this->tr);
                $this->document->appendChild( $this->table );


                echo $this->document->saveHTML();
                $photoPages->showPages();        
            }
            ?>
        </div>
        
        <div style="float:right;">
            <?php
            {
                $result = mysqli_query($this->link, "SELECT * FROM esco_photo_albums WHERE owner=0 OR owner=".$this->user['id']." ORDER BY `owner` ASC, `title` ASC;");
                while($row=mysqli_fetch_row($result)){
                    echo '<div class="photoviewer-box" style="text-align:center; width:170px;font-size:12px;">';
                    echo '<a href="/photos/albums/'.$row[0].'/'.urlify($row[2]).'">'.$row[2].'</a>';
                    echo '</div>';
                }
                
                if($this->user['id']==$this->escoID){
                    echo '<button style="width:192px;" onclick="location.href=\'/user/photo-uploader/albums.php\';">Manage Albums</button>';
                }
            }
            ?>
        </div>
        
        <div class="cf"></div>
        
    </div>
</div>
<?php
    }
}

new customPage();