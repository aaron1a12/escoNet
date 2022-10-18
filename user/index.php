<?php

class customPage extends page {
    public $title = 'User Account';	
    public $private = true;
    
    function init()
    {
        
        header('Location: /user/account.php');
        die();
    }
}

new customPage();