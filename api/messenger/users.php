<?php
class customPage extends page {    
    function init(){         
        header('Content-type: application/json');
        //header('HTTP/1.1 500 Internal Server Error');

        $data = new stdClass;

        $select = "SELECT * FROM esco_users ORDER BY `name` ASC";
        $result = mysqli_query($this->link, $select);
        
        $data->users = array();
        $data->users[0] = array();
        $data->users[1] = array();     

        
        while ($user = mysqli_fetch_assoc($result)) {
            unset($user['password']);
            
            $user['profileURL'] = 'http://www.esco.net/user/'.$user['id'].'/'. urlify($user['name'].' '.$user['lastname']);
            
            if(strtolower($user['lastname'])=='escobar' || strtolower($user['lastname'])=='tubella')
                array_push($data->users[0], $user);
            else
                array_push($data->users[1], $user);
        }         

        print( json_encode($data) );
        exit();
    }
}

new customPage();