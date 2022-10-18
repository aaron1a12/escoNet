<?php
class customPage extends page {
    function init(){
        header('Content-type: application/json');
        //header('HTTP/1.1 500 Internal Server Error');

		//$_POST['recipient'] = 2;
		//$_POST['readall'] = 1;

        $data = new stdClass;
        $data->ok = false;

        if(!$_POST)
        {
            print( json_encode($data) );
            exit();
        }

        if(!$_POST['message'])
        {
            print( json_encode($data) );
            exit();
        }

        if($_POST['message']=='')
        {
            print( json_encode($data) );
            exit();
        }

        $data->ok = true;

        $message = strip_tags(filter_var( $_POST['message'], FILTER_SANITIZE_MAGIC_QUOTES));
        $escoID = $this->escoID;
  			$date = date('Y-m-d H:i:s', time());

  			$query = "INSERT INTO esco_shouts (`author`,`time`,`shout`) VALUES ('$escoID','$date','$message')";
  			mysqli_query($this->link, $query);

        print( json_encode($data) );
        exit();
    }
}

new customPage();
