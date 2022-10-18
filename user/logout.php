<?php

class customPage extends page {
    
    function init()
    {
       // echo '?';
        
        if(substr(strtolower($_SERVER["HTTP_HOST"]), 0, 4)=='www.')
                    $domain = substr($_SERVER["HTTP_HOST"], 4);
                else
                    $domain = $_SERVER["HTTP_HOST"];
        
        setcookie("esco_user", "", time() - 3600, '/', $domain);
        setcookie("esco_pass", "", time() - 3600, '/', $domain);
        setcookie("esco_name", "", time() - 3600, '/', $domain);
        /*
        $_SESSION['esco_user']='';
        unset($_SESSION['esco_user']);
        $_SESSION['esco_pass']='';
        unset($_SESSION['esco_pass']);
        */
        $_SESSION['PHPSESSID']='';
        unset($_SESSION['PHPSESSID']);

        header("Location: /"); 
        die();
    }
}

new customPage();