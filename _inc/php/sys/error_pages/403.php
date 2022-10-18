<?php

header("HTTP/1.1 403 Forbidden");

class customPage extends page {
    public $title = 'Home';
    function content() {
?>
<div class="widget">
    <h1>403: Forbidden</h1>
    <p>You're not supposed to go looking in these folders.</p>
</div>

<?php
    }
}

new customPage();