<?php

class customPage extends page {
    public $title = 'User Account';	
    public $private = true;
    
    public $profile;    
	
    function content() {
?>
<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>
<?php
    }
}

new customPage();