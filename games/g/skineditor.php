<?php

class customPage extends page {
    public $title = 'Minecraft Skin Editor';
    function content() {
?>
<div class="widget" style="text-align:center; position:relative;">
	<iframe src="http://www.minecraft.com/" frameborder="0" style="overflow:hidden; width:1000px; height:600px" scrolling="no" seamless>
	</iframe>
	<div class="cf"></div>
</div>
<?php
    }
}

new customPage();