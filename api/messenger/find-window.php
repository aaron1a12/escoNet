<?php
class customPage extends page {    
    function init(){         
        header('Content-type: application/json');
        //header('HTTP/1.1 500 Internal Server Error');
		
		//$_POST['recipient'] = 2;

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
		
		if( !(isset($_POST['recipient'])&&$_POST['recipient']!='') )
        {
            print( json_encode($data) );
            exit();
        }
        
		$recipient = intval( $_POST['recipient'] );

        $data->username = strip_tags(filter_var( $_POST['username'], FILTER_SANITIZE_MAGIC_QUOTES));
        $data->password = strip_tags(filter_var( $_POST['password'], FILTER_SANITIZE_MAGIC_QUOTES));
        
        $hash = sha1( encrypt( sha1( md5($_POST['password']) ) ) );

        $q = 'SELECT * FROM esco_users WHERE username=\'' . $_POST['username'] . '\' AND password=\'' . $hash . '\'';
        $r = mysqli_query($this->link, $q);

        if(mysqli_num_rows($r)>0)
        {
            $data->ok = true;
			$escoID = mysqli_fetch_row($r)[0];
			
			//
			// Find the window
			//
			
			$data->window = 0;
			
            $query = "SELECT * FROM esco_chat_windows WHERE recipient='$recipient' AND owner='$escoID';";
			$result = mysqli_query($this->link, $query);
			$numrows = mysqli_num_rows($result);
			
			if($numrows==0){
				// Create the window
				$qu= "INSERT INTO esco_chat_windows (recipient, owner, open) VALUES ('$recipient', '$escoID', '1')";
				$qr = mysqli_query($this->link, $qu);
				$data->window = mysqli_insert_id($this->link);
			}
			else
			{
				$row = mysqli_fetch_row($result);
				$data->window = $row[0];
				
				// Set window as open
                mysqli_query($this->link, "UPDATE esco_chat_windows SET open='1' WHERE id='".$data->window."';");
				
				//echo "UPDATE esco_chat_windows SET open='1' WHERE id='".$data->window."';";
			}

        }
        print( json_encode($data) );
        exit();
    }
}

new customPage();