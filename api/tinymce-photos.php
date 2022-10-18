<?php
class customPage extends page {    
    public $private = true;
    
    
    
    function init(){
        $bOk = true;
        
        if($bOk)
        {
?>
<link href="/_inc/css/global.css" rel="stylesheet" type="text/css">
<style>
.mce-gallery img {
    filter: brightness(0.8) grayscale(1); transition: all 0.2s ease-out;
}
.mce-gallery img:hover {
    cursor:pointer; filter: brightness(1.2);
}
    
label, label input {
    cursor: pointer;
}
    
.placement-table {
    width:100%;
}
    
.placement-table td {
    text-align:center;
}

.blog-img-placement {
    width:24px; height:24px; background: url("/_inc/img/blog-img-placement.png") no-repeat; 
    margin-left: auto;
    margin-right: auto;
}
.blog-img-placement.left {
    background-position: 0px 0px !important;
}
.blog-img-placement.middle {
    background-position: 0px -24px !important;
}
.blog-img-placement.right {
    background-position: 0px -48px !important;
}
</style>
<div style="margin:20px;">
    
    <table style="width:100%;">
        <tr>
            <td>Enter the special blog code here: </td>
            <td><input name="title" id="blog-code" style="width:70%;background:#d5d5e6;"><button onclick="insertImage();">ADD IMAGE</button></td>
        </tr>
        <tr>
            <td>Choose placement:</td>
            <td>
                <table class="placement-table">
                    <tr>
                        <td>
                            <label title="Place image on left of text">
                                <input id="radioPlaceLeft" name="input-placement" type="radio" style="height:auto;">
                                <div class="blog-img-placement left"></div>
                            </label>
                        </td>
                        <td>
                            <label title="Place image in middle with full width">
                                <input id="radioPlaceMiddle" name="input-placement" type="radio" style="height:auto;" checked="checked">
                                <div class="blog-img-placement middle"></div>
                            </label>                            
                        </td>
                        <td>
                            <label title="Place image on right of text">
                                <input id="radioPlaceRight" name="input-placement" type="radio" style="height:auto;">
                                <div class="blog-img-placement right"></div>
                            </label>                            
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    
    <div style="border-bottom:2px solid #333388; margin-bottom:5px; margin-top:20px;"></div>
    
    <p style="margin-bottom:15px;font-weight:bold;">Your most recent photos</p>
    <div class="mce-gallery">
    <?php
    $collumns = 5;
    $rows = 14;
            
    $query = "SELECT * FROM esco_photos WHERE author='".$this->escoID."' ORDER BY id DESC LIMIT ".$collumns*$rows.";";
    $result = mysqli_query($this->link, $query);

    while($row=mysqli_fetch_row($result))
    {
        $imageName = $row[11];

        $format = $row[5]; 
        switch($format){
            case 0:
                $format = '.jpg';
                break;
            case 1:
                $format = '.png';
                break;
            case 2:
                $format = '.gif';
                break;
        }

        $parts = explode('_', $imageName);
        $serverID = $parts[ count($parts)-2 ];
        
        $title = $row[9];
        
        $blogCode = gzcompress($row[0], 0);
        $blogCode = unpack('H*',$blogCode)[1];

        $photo = 'http://media.esco.net/img/social/photos/'.$row[1].'/'.$imageName . '_s' . $format;  
        $largePhoto = 'http://media.esco.net/img/social/photos/'.$row[1].'/'.$imageName . '_l' . $format;
        
        echo "<img src=\"$photo\" title=\"$title\" onclick=\"addThis(this);\" data-large-photo=\"$largePhoto\" data-blog-code=\"$blogCode\">";
    }
    ?>
    </div>    
    
    
</div>
<script src="http://media.esco.net/global/jquery.js"></script>
<script>
    var imageURL = '';
    function insertImage(){
        
        var sClass = 'blog-img-left';
        var iPlacement = 1;
        var sWidthAttr = 'width="160"';
        
        var blogCode = document.getElementById('blog-code').value;
        
        $.ajax({
            type: "GET",
            url : "/api/tinymce-get-photo.php",
            data: { id:blogCode },
            dataType : 'json'
        }).done(function(data){
            if( data.ok ){
                
                if(document.getElementById('radioPlaceLeft').checked){
                    sClass = 'blog-img-left'; iPlacement = '0';
                }

                if(document.getElementById('radioPlaceMiddle').checked){
                    sClass = 'blog-img-middle'; iPlacement = '1'; sWidthAttr = '';
                }

                if(document.getElementById('radioPlaceRight').checked){
                    sClass = 'blog-img-right'; iPlacement = '2';
                }        
                
                top.tinymce.activeEditor.insertContent('<img class="'+sClass+'" src="'+data.image+'" data-placement="'+iPlacement+'" '+sWidthAttr+' data-blog-code="'+blogCode+'">');
                top.tinymce.activeEditor.windowManager.close();
            }
        });        
        
        if(imageURL!='')
        {

            
            
            
            
            
            //top.tinymce.activeEditor.insertContent('<img class="'+sClass+'" src="'+imageURL+'" data-placement="'+iPlacement+'" '+sWidthAttr+' data-blog-code="'+blogCode+'">');
            //top.tinymce.activeEditor.windowManager.close();
        }
    }
    
    
    function addThis( element ){
        imageURL = element.getAttribute('data-large-photo');
        
        var input = document.getElementById('blog-code');
        
        input.value = element.getAttribute('data-blog-code');
    }
</script>
<?php
        }
    
        die();
    }
    
}

new customPage();