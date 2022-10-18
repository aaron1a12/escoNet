<?php

class customPage extends page {
    
    function init()
    {

        if(isset($_GET['id']) && intval($_GET['id'])!=0)
        {
            $id  = intval($_GET['id']);
            
            $author = $this->escoID;
            $numrows = intval(mysqli_fetch_row( mysqli_query($this->link, "SELECT COUNT(id) FROM esco_polls WHERE id='$id' AND author='$author';") )[0]);
            
            if($numrows!=0)
            {            
                $query = "DELETE FROM esco_polls WHERE id='$id'";
                $query2 = "DELETE FROM esco_poll_results WHERE poll='$id'";
                $query3 = "DELETE FROM esco_poll_comments WHERE poll='$id'";

                mysqli_query($this->link, $query);
                mysqli_query($this->link, $query2);
                mysqli_query($this->link, $query3);
                
                header("Location: /user/polls/"); 
                die();
            }
            else
            {
                die('Invalid poll.');
            }
        }
        
    }
}

new customPage();