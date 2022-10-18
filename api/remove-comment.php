<?php
class customPage extends page {    
    function init(){
        if($this->loggedIn){      
            
            
            //$table = intval( $_GET['from'] );
            $table = base64_decode($_GET['from']);
            $commentID = intval( $_GET['id'] );
            $return = base64_decode($_GET['return']);
            
            /*
            switch($table)
            {
                case 0:
                    $table = 'esco_blog_comments';
                    break;
                case 1:
                    $table = 'esco_wall_comments';
                    break;
                case 2:
                    $table = 'esco_funny_pic_comments';
                    break;
                    
            }
            */
            $pos = stripos($table, 'comments');
            if ($pos === false) {
                die('Comments table not found');
            }
            
            
            
            // See if the author is the one who's doing the removing
            
            $result = mysqli_query($this->link, "SELECT author FROM $table WHERE id='$commentID';");
            $numrows = mysqli_num_rows($result);
            $author = mysqli_fetch_row($result)[0];
            
            if($numrows!=0)
            {
                if($author==$this->escoID)
                {
                    mysqli_query($this->link, "DELETE FROM $table WHERE id='$commentID';");
                }
            }
            
            header("Location: $return");
            die();
        }
    }
}

new customPage();