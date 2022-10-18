<?php
class customPage extends page {    
    public $title = 'All Photos';
    
    function init(){
    }
    
    function content(){
?>
<div class="widget">
    <?php if($this->loggedIn){ ?>
    <div style="position:absolute;right:0;top:0;">
        <a class="btn" href="<?php echo $this->escoProfileURL.'/photos/';?>" style="float:right"><img src="/_inc/img/icons/picture.png"> My Photos</a>
        <a class="btn" href="<?php echo $this->escoProfileURL.'/photos/favorites';?>" style="float:right;margin-right:5px;"><img src="/_inc/img/icons/fav.png"> My Favorites</a>
    </div>
    <?php } ?>
    
    <h1>Photos</h1>
    
    <form action="" method="GET">
        <input name="q" style="width:400px;" value="<?php if(isset($_GET['q'])) echo htmlentities($_GET['q']);?>"><button>Search Photos</button><br>
        <small style="color:#ccc;">Tip: Add "<b>+</b>" before your keywords to force-include them. E.g., "+tropical +landscape"</small>
    </form>


    
    <br> 
    
    
    <div style="margin-top:20px;">
        <div style="float:left;text-align:left;">
            <small>
                Sort by:
                <?php if(isset($_GET['sort']) && $_GET['sort']=='1'){ ?>
                Photo Date (oldest first)
                <?php }else{ ?>
                <a href="/photos/?sort=1">Photo Date (oldest first)</a>
                <?php } ?>
                |
                <?php if(!isset($_GET['sort']) || $_GET['sort']!='1'){ ?>
                Upload Date (newest first)
                <?php }else{ ?>
                <a href="/photos/">Upload Date (newest first)</a>
                <?php } ?>
            </small>
        </div>
        <?php
        {
            
            $SORTBY = 'ORDER BY `id` DESC';
            if(isset($_GET['sort'])){
                $sort = intval($_GET['sort']);
                if($sort==1)
                    $SORTBY = 'ORDER BY `datetaken` ASC';
            }            
            
            
            $this->document = new DOMDocument;

            $this->table = $this->document->createElement('table');    

            $this->table->setAttribute('cellspacing', 0);
            $this->table->setAttribute('cellpadding', 00);        
            $this->table->setAttribute('style', '');

            $this->tr = $this->document->createElement('tr');
            $this->i = 0;
            
            if(isset($_GET['q']) && $_GET['q']!=''){
                $sq = filter_var( $_GET['q'], FILTER_SANITIZE_MAGIC_QUOTES );
                $SEARCH = "MATCH(`keywords`, `title`) AGAINST (\"$sq\" IN BOOLEAN MODE) AND";
            }else{
                $SEARCH = '';
            }

            $QUERY = "
            SELECT SQL_CALC_FOUND_ROWS id,author,time,format,views,album,title,name FROM `esco_photos` WHERE $SEARCH `unlisted`=0  $SORTBY 
            ";

            $photoPages = new paginator($this, $QUERY, 42, 9,
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

                    $imageFile = $imageName . '_c' . $format;

                   

                    $parts = explode('_', $imageName);
                    //var_dump($imageName);
                    $serverID = $parts[ count($parts)-2 ];

                    $photo = 'http://media.esco.net/img/social/photos/'.$photoRow[1].'/'.$imageFile;

                    $img->setAttribute('src', $photo);
                    $img->setAttribute('alt', $photoTitle);
                    $img->setAttribute('style', 'width:320px; height:160px;');

                    // Add the cell
                    $this->tr->appendChild( $td );                

            }, false);

            echo '<div style="float: left;text-align: left;margin-left: 200px;margin-top: -90px;"><small>Photos found: <b>'.number_format($photoPages->getCount()).'</b></div></div>';


            $this->table->appendChild($this->tr);
            $this->document->appendChild( $this->table );

            $this->table->setAttribute('class', 'image-grid-all');

            
            
            echo '<div style="float:right;">';
            echo $photoPages->showPages();
            echo '</div>';
            
            
            
            echo '<div class="cf"></div>';
            
            

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
<?php
    }
}

new customPage();

