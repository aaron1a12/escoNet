<?php

class customPage extends page {
    public $title = 'Home';
    function content() {
?>
<div class="widget">
<h1>escoMessenger<sup>PRE-ALPHA</sup></h1>
    
    
    <div class="error"><div style="margin:10px;">escoMessenger is in the PRE-ALPHA stage and, as such, is currently lacking basic features. Report any errors to Aaron.</div></div>
    
    <br><br>
    
    <div style="text-align:center;">
        <a class="btn" href="http://media.esco.net/messenger/releases/0.1pa/escoMessenger_setup.exe">
            <img src="/_inc/img/down.png" style="vertical-align:middle;margin-bottom:10px;">
            Download
        </a>
    </div>    
    
    
    <p>Finally a way for Escobars to stay in touch! Simply download and sign in with your escoNet account!</p>
    <br><br>
    
    <h2>Releases</h2>
    
    <p>
        <a href="http://media.esco.net/messenger/releases/0.1pa/escoMessenger_setup.exe">escoMessenger_setup.exe - 0.1 PRE-ALPHA</a>
    <p>
</div>
<?php
    }
}

new customPage();