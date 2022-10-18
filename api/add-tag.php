<?php
class customPage extends page {
    public $private = true;

    function init(){
        header('Content-type: application/json');
        
        if( !(isset($_POST['id']) && $_POST['id']!='') ) die();    
        if( !(isset($_POST['text']) && $_POST['text']!='') ) die();    
        
        $data = new stdClass;
        $data->ok = true;
        
        $id = intval($_POST['id']); 
        
        $imgr = mysqli_query($this->link, "SELECT keywords FROM esco_photos WHERE id=$id;");
        $keywords = mysqli_fetch_row($imgr)[0];

        $keywords .= ' '.filter_var( htmlentities($_POST['text'], FILTER_SANITIZE_MAGIC_QUOTES ));

        //
        // Clean keywords
        //

        $keywords = explode(',', $keywords);
        $keywords = implode(' ', $keywords);
        $keywords = explode(' ', $keywords);

        $i=0;
        foreach($keywords as &$keyword){
            if($keyword=='')
                unset($keywords[$i]);
            $i++;
        }
        
        $data->tags = array();
        
        foreach($keywords as &$keyword){
            array_push($data->tags, $keyword);
        }    
        

        mysqli_query($this->link, "UPDATE esco_photos SET `keywords`='".implode(' ', $keywords)."' WHERE id=$id;");
        

        print( json_encode($data) );
        exit();
    }
}

new customPage();