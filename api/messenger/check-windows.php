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

        $q = 'SELECT * FROM esco_users WHERE username=\'' . $_POST['username'] . '\' AND password=\'' . $hash . '\'';
        $r = mysqli_query($this->link, $q);

        if(mysqli_num_rows($r)>0)
        {
            $data->ok = true; unset($data->username); unset($data->password);
			$escoID = mysqli_fetch_row($r)[0];
			
			$data->unread = array();
			
            $query = "SELECT `from` FROM esco_chat_text WHERE `to`='$escoID' AND `read`='0';";
			$result = mysqli_query($this->link, $query);
			
			//die($query);
			
			while ($row = mysqli_fetch_row($result)) {
				array_push($data->unread, array(
					'id'=>$row[0],
				));
			}

        }
        print( json_encode($data) );
        exit();
    }
}

new customPage();