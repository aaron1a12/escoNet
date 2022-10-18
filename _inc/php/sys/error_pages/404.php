<?php
header("HTTP/1.1 404 Not Found");

class customPage extends page {
    public $title = 'Home';
    function content() {
?>
<div class="widget">
    <h1>404: Not Found</h1>
    <p>The requested page could not be located on our server.</p>
</div>
<?php
    }
}

new customPage();