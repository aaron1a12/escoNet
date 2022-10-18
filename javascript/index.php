<?php

class customPage extends page {
    public $title = 'Javascript';
	
    function head() {
?>
<style>
    #chatwindow {
        background-color:#000;
        height:400px; border-radius:12px;border:4px solid #ccc; padding:15px; 
        overflow-y:hidden;
        white-space:pre-line;
		word-wrap: break-word;
        word-break: break-all;
		font-family:Monospace;
    }
</style>
<?php }	
	
    function content() {
?>
<h1>Javascript</h1>

<a href="/javascript/chat.php">Web Socket Chat</a>
<?php
    }
}

new customPage();