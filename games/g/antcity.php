<?php

class customPage extends page {
    public $title = 'Ant City';
    function content() {
?>
<div class="widget" style="text-align:center; position:relative;">
    <embed s width="840" height="560" src="http://media.esco.net/swf/antcity.swf" type="application/x-shockwave-flash"></embed>
 
    <div class="cf"></div>
</div>
<?php
    }
}

new customPage();