<?php
class customPage extends page {
    public $private = true;
    
    
    function init(){
        if($_POST)
        {            
            if(!isset($_POST['action']) || !isset($_POST['data']) )
                exit();
            
            $_POST['action'] = intval( $_POST['action'] );
            
            if($_POST['action']==1)
            {            
                // Check limit
                if(mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_trending"))[0] >= 12)
                {
                    header('HTTP/1.1 400 Bad Request');
                    die();                    
                }

                $data = strip_tags(filter_var( $_POST['data'], FILTER_SANITIZE_MAGIC_QUOTES));

                $query = "INSERT INTO esco_trending (keyword) VALUES ('$data')";
                mysqli_query($this->link, $query);
            }
            elseif ($_POST['action']==2)
            {
                $data = intval($_POST['data']);

                $query = "DELETE FROM esco_trending WHERE id='$data'";
                mysqli_query($this->link, $query);
            }
            
            
            $select = "SELECT * FROM esco_trending ORDER BY `id` DESC";
            $result = mysqli_query($this->link, $select);
            
            $rows = array();
        
            while ($row = mysqli_fetch_row($result)) {
                array_push($rows, $row);
            }
            
            
            header('Content-type: application/json');
            echo json_encode( $rows );

            exit();
        }
        else
        {
            exit();
        }
       
    }
}

new customPage();