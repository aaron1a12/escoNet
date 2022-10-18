<?php

class customPage extends page {
    public $title = 'Home';

    function content() {?>
<div class="widget">
    <h1>QR Code Generator</h1>
    <p>Generate a <a href="http://en.wikipedia.org/A/QR_code.html">QR code</a> with this simple tool.</p>
	<form action="" method="POST">
		<input name="code"><button>Encode</button>
	</form>
	<br>
	<code style="padding:0;">
	Hello World!<br>Foobar
	</code>
	<br>
	<br>
	<img src="http://www.esco.net/api/qr.php?code=<?php
		if(isset($_POST['code'])) echo urlencode($_POST['code']);
	?>">
</div>
    <?php }
}

new customPage();