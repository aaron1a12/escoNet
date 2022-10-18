<?php

class customPage extends page {
    public $title = 'Add your funny pic';
    function head() {
?>
<script src="/_inc/js/upload.js"></script>
<?php
    }
    
    function content() {
?>
<div class="widget">
    <div style="position:relative;">
        <h1>Add your funny pic</h1>
    </div>
    
    
    <p>Please upload images in <b>.JPG</b> and no larger than <b>5 MB</b>.</p>
		
    <form id="upload-form" action="/api/upload.php" method="POST" enctype="multipart/form-data" style="position:relative; height:100px;">

        <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="2147483648">
<!--        <input type="file" id="upload-file" name="upload-file[]" multiple="multiple" style="width:40%;float:left;background:#fff;padding:5px;height:25px;">-->
        <input type="file" id="upload-file" name="upload-file[]" style="width:40%;float:left;background:#fff;padding:5px;height:25px;">

        <div id="progess-container" style="float:right; width:58%; position:relative;">
            <progress id="upload-progress" value="0" max="100" style="width:100%;height:35px;"></progress>
            <span id="upload-state"></span>
            <span id="progress-error"></span>

        </div>

        <div style="clear:both"></div>
    </form>
</div>
<?php
    }
}

new customPage();