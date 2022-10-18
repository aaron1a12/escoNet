<?php

class customPage extends page {
    public $title = 'User Account';	
    public $private = false;
    
    public $user;
    public $bUserExists = false;
    
    function init()
    {
        //if($this->escoID!=20)
        //   die('<h1>Temporarily Unavailable</h1><p>Please check back in 15-20 mins.</p>');
    }
    
    function head()
    {
        echo '<script src="/_inc/js/profile-uploader.js"></script>';
    }
    

    
	
    function content() {
?>

<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>
<div class="widget">

    <h2>Profile Picture</h2>
    
    
    <div class="widget">
        <p>Please upload images in <b>.JPG</b> and no larger than <b>5 MB</b>.</p>

        <form id="upload-form" action="/api/upload-profile.php" method="POST" enctype="multipart/form-data" style="position:relative; height:100px;">

            <input type="hidden" name="id" value="<?php print($this->escoID); ?>">

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
    
    <br><br>

    <h2>Profile Banner</h2>
    
    <div class="widget">
        <p>Please upload images in <b>.JPG</b> and no larger than <b>5 MB</b>. Super widscreen size: <b>1000 &times; 210</b></p>

        <form id="upload-banner-form" action="/api/upload-profile.php" method="POST" enctype="multipart/form-data" style="position:relative; height:100px;">

            <input type="hidden" name="id" value="<?php print($this->escoID); ?>">

            <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="2147483648">
            <input type="file" id="upload-banner-file" name="upload-file[]" style="width:40%;float:left;background:#fff;padding:5px;height:25px;">

            <div id="banner-progess-container" style="float:right; width:58%; position:relative;">
                <progress id="upload-banner-progress" value="0" max="100" style="width:100%;height:35px;"></progress>
                <span id="banner-upload-state"></span>
                <span id="banner-progress-error"></span>

            </div>

            <div style="clear:both"></div>
        </form>
    </div>
</div>
<?php
    }
}

new customPage();