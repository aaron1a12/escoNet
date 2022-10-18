<?php
class customPage extends page {
    public $private = true;

    function init(){
        header('Content-type: application/json');
        
        $bContinue = true;
        
        if( !(isset($_POST['photo']) && $_POST['photo']!='') ) $bContinue=false;
        if( !(isset($_POST['left']) && $_POST['left']!='') ) $bContinue=false;
        if( !(isset($_POST['top']) && $_POST['top']!='') ) $bContinue=false;
        if( !(isset($_POST['width']) && $_POST['width']!='') ) $bContinue=false;
        if( !(isset($_POST['height']) && $_POST['height']!='') ) $bContinue=false;
        if( !(isset($_POST['text']) && $_POST['text']!='') ) $bContinue=false;
        if( !(isset($_POST['imgtitle']) && $_POST['imgtitle']!='') ) $bContinue=false;
        
        
        if($bContinue)
        {
            $photo = intval($_POST['photo']);
            $author = $this->escoID;
            $time = time();
            $left = intval($_POST['left']);
            $top = intval($_POST['top']);
            $width = intval($_POST['width']);
            $height = intval($_POST['height']);
            $text = filter_var( htmlentities($_POST['text']), FILTER_SANITIZE_MAGIC_QUOTES );

            $query = "INSERT INTO esco_photo_notes (`photo`, `author`, `time`, `left`, `top`, `width`, `height`, `text`) VALUES ('$photo','$author','$time','$left','$top','$width','$height','$text')";
            mysqli_query($this->link, $query);


            $pageUrl = $_SERVER['HTTP_REFERER'];
            $title = filter_var( htmlentities($_POST['imgtitle']), FILTER_SANITIZE_MAGIC_QUOTES );;

            logAction($this, ACTION_PHOTO_NOTE, $title, $pageUrl);

            $data = new stdClass;
            $data->ok = true;
            $data->notes = array();
        }
        
        
        if( isset($_POST['delete']) ){
            $noteId = intval($_POST['delete']);
            
            
            $query = "DELETE FROM esco_photo_notes WHERE id='$noteId';";
            mysqli_query($this->link, $query);            
        }
        

        $data = new stdClass;
        $data->ok = true;
        $data->notes = array();
        
        if(!isset($_POST['photo'])){ $_POST['photo'] = 0; $data->ok = false;}
        
        $photo = intval($_POST['photo']);
        
        $noteResult = mysqli_query($this->link, "SELECT * FROM esco_photo_notes WHERE photo='$photo';");
        while($note=mysqli_fetch_row($noteResult)){
            $note[2] = mysqli_fetch_row( mysqli_query($this->link, "SELECT id, name, lastname FROM esco_users WHERE id='".$note[2]."' ") );
                    
            /*
            $httpPos = stripos($note[8], 'http://');
            if($httpPos !== false) {
                $everythingAfter = substr($note[8], $httpPos);
                $everythingAfter = explode(' ', $everythingAfter);
                $httpLink = $everythingAfter[0];
                $note[8] = str_replace($httpLink, "<a href=\"$httpLink\">$httpLink</a>", $note[8]);
            }*/
            $note[8] = addLinks(nl2br(trim($note[8])));
            
            array_push($data->notes, $note);
        }
        
        print( json_encode($data) );
        exit();
    }
}

new customPage();