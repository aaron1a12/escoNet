<?php


class customPage extends page {
    public $title = 'Photo Uploader';	
    public $private = true;
    
    public $profile;    

   
    function init()
    {
        /*
        if( $this->escoID != 1 )    
        {
           print('<h1>The Photo uploader is currently being upgraded. Check back later.</h1>');
            die();
        }
        */
        
        
        $userPhotoFolder = dirname($this->siteDirectory).'/htdocs_media_esconet/img/social/' .  $this->escoID . '/photos/';
        $userPhotoFolderTMP = $userPhotoFolder . 'tmp/' ;

        //echo $userPhotoFolderTMP;
        //echo file_put_contents($userPhotoFolderTMP . 'test.txt',"Hello World. Testing!");

        
        //
        // Make sure the proper folders exists
        //
        
        
        if(!file_exists($userPhotoFolder)) mkdir($userPhotoFolder);
        if(!file_exists($userPhotoFolderTMP)) mkdir($userPhotoFolderTMP);

        //
        // Cleanup any prev leftovers
        //

        $tmpFiles = glob( $userPhotoFolderTMP . '*' );

        foreach($tmpFiles as $tmpFile) {
            if(is_file($tmpFile))
                unlink($tmpFile);
        }
    }
    
    function head()
    {
?>
<script>
    var photoUploaderWizard;
</script>
<script src="/_inc/js/pageslider.min.js"></script>
<script src="/_inc/js/photo-uploader.js"></script>
<script>
    $( document ).ready(function() {
        photoUploaderWizard = new pageSlider('photoUploaderWizard');
        photoUploaderWizard.addPage( 'page1', function(){
            //hideClientErrors();
        } );
        
        photoUploaderWizard.addPage( 'page2' );
        photoUploaderWizard.addPage( 'page3', function(){
            window.onbeforeunload = function(){ return 'Don\'t leave now!'; };
            UploadFiles();
        } );
        photoUploaderWizard.addPage( 'success', function(){
            window.onbeforeunload = function(){};
        } );
        photoUploaderWizard.addPage( 'error', function(){
            window.onbeforeunload = function(){};
        } );
        
        photoUploaderWizard.show( 'page1' );
    });
    
    window.onbeforeunload = function(){
    };
    
    
</script>
<?php
    }
    
	
    function content() {
?>
<?php //include($this->siteDirectory . '/_inc/php/user-header.php');?>
<div class="widget" style="padding-top:100px; padding-bottom:20px; overflow:none;">
    <h1 style="font-family:Roboto;font-size:60px;">Photo Uploader</h1>

    <div id="photoUploaderWizard">
        <div id="page1" class="pageSlider">
<!--             <input type="file" id="upload-file" name="upload-file[]" multiple="multiple" style="display:none; width:40%;float:left;background:#fff;padding:5px;height:25px;">-->
            <div class="center" style="width:70%;">
                <p>Select photos to upload from your computer or device. Images larger than <b>2 MB</b> will be compressed and anything larger than <b>5 MB</b> will not be allowed.</p>
                <p style="color:#aaa;">(Please bear in mind our server has a 4-core 1GHz ARM processor with only 1GB ram so resizing the images will take some time.)</p>
                <br>
                <a class="btn" href="#" onclick="$('#upload-file').click();">Select Photos</a>
                <input type="file" id="upload-file" name="upload-file[]" multiple="multiple" style="display:none; width:40%;float:left;background:#fff;padding:5px;height:25px;">
                
                <br><br><br>
                <div id="clientErrorMessages" class="error" style="font-size:8pt; border-radius:3px;"></div>
            </div>
        </div>
        
        <div id="page2" class="pageSlider">
            <div class="center" style="width:70%;">
                <div id="filesList">                
                </div>
                <p>If these are not the files you want then click on "Start Over". Otherwise, click on "Upload".</p>
                <br>
                <a class="btn" href="#" onclick="photoUploaderWizard.show( 'page3' );">Upload</a>
                <a class="btn" href="#" onclick="photoUploaderWizard.show( 'page1' ); document.getElementById('upload-file').value='';">Start Over</a>
            </div>
        </div>
        
        <div id="page3" class="pageSlider">
            <div class="center" style="width:70%;">
                <h3 id="upload-status">Uploading...</h3>
                <div style="overflow:hidden; width:100%;height:4px;">
                    <div style="background-color:#7722bb; width:0%;height:100%;" id="progress-bar-div"></div>
                </div>
            </div>
        </div>
        
        <div id="success" class="pageSlider">
            <div class="center" style="width:70%;">
                <h3>All right!</h3>
                <p>Your pictures were successfully uploaded to your photostream!</p>
                
                <br><br>
                
                <a href="<?php echo $this->escoProfileURL;?>/photos" class="btn">My Photostream</a>
                <a href="#" class="btn">Manage Albums</a>
                <a href="#" onclick="photoUploaderWizard.show( 'page1' ); document.getElementById('upload-file').value='';" class="btn">Upload Again</a>
            </div>
        </div>        
        
        <div id="error" class="pageSlider">
            <div class="center" style="width:70%;">
                <h3><img src="/_inc/img/icons/exclamation.png"> &nbsp;Houston, we have a problem!</h3>
                <p>We received the following from the server and had to stop uploading:</p>
                
                <div id="serverErrorMessages" class="error"></div>
                <br><br>
                
                <a href="#" onclick="photoUploaderWizard.show( 'page1' ); document.getElementById('upload-file').value='';" class="btn">Try Again</a>
                <a href="<?php echo $this->escoProfileURL;?>/photos" class="btn">My Photostream</a>
            </div>
        </div>  
    </div>
</div>
<?php
    }
}

new customPage();