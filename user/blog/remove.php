<?php

class customPage extends page {
    
    function init()
    {

        if(isset($_GET['id']) && intval($_GET['id'])!=0)
        {
            $id  = intval($_GET['id']);
            
            $author = $this->escoID;
            $numrows = intval(mysqli_fetch_row( mysqli_query($this->link, "SELECT COUNT(id) FROM esco_blog_posts WHERE id='$id' AND author='$author';") )[0]);
            
            if($numrows!=0)
            {
                $query = "DELETE FROM esco_blog_posts WHERE id='$id'";
                $query2 = "DELETE FROM esco_blog_comments WHERE post='$id'";

                mysqli_query($this->link, $query);
                mysqli_query($this->link, $query2);
                
                
                transfer($this, $this->escoID, 0, 5);
                
                header("Location: /user/blog/"); 
                die();
            }
            else
            {
                die('Invalid post.');
            }
        }
        
    }
}

new customPage();