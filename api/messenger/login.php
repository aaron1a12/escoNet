<?php
class customPage extends page {    
    function init(){         
        header('Content-type: application/json');
        //header('HTTP/1.1 500 Internal Server Error');

        $data = new stdClass;
        $data->ok = false;

        if(!$_POST)
        {
            print( json_encode($data) );
            exit();
        }
        
        if( !(isset($_POST['username'])&&$_POST['username']!='') )
        {
            print( json_encode($data) );
            exit();
        }
        
        if( !(isset($_POST['password'])&&$_POST['password']!='') )
        {
            print( json_encode($data) );
            exit();
        }
        

        $data->username = strip_tags(filter_var( $_POST['username'], FILTER_SANITIZE_MAGIC_QUOTES));
        $data->password = strip_tags(filter_var( $_POST['password'], FILTER_SANITIZE_MAGIC_QUOTES));
        
        $hash = sha1( encrypt( sha1( md5($_POST['password']) ) ) );

        $query = 'SELECT * FROM esco_users WHERE username=\'' . $_POST['username'] . '\' AND password=\'' . $hash . '\'';
        $result = mysqli_query($this->link, $query);

        if(mysqli_num_rows($result)>0)
        {
            $data->ok = true; unset($data->username); unset($data->password);
            
            $row = mysqli_fetch_row($result);
            $data->escoID = $row[0];
            $data->name = $row[3];
            $data->lastname = $row[4];
            $data->profileURL = 'http://www.esco.net/user/'.$data->escoID.'/'.urlify($row[3].' '.$row[4]);
        }
        print( json_encode($data) );
        exit();
    }
}

new customPage();