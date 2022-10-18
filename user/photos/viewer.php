<?php

class customPage extends page {
    
    public $user;
    public $img;
    public $profileLink;
    
    
    function init()
    {
        $this->img = new stdClass;
        $this->img->exists = true;
        

        
        //$this->profile =  mysqli_fetch_assoc(mysqli_query($this->link, "SELECT * FROM esco_user_profiles WHERE user='$userid';"));

        
        
           
        
        if(!isset($_GET['img-id']))
            $this->img->id = 0;
        else
            $this->img->id = intval($_GET['img-id']);
        
        
        if(  $this->img->id == 0 ) $this->img->exists = false;
        
        
        if( $this->img->exists )
        {
            $imgq = "SELECT * FROM esco_photos WHERE id='".$this->img->id."';";
            $imgr = mysqli_query($this->link, $imgq);

            $numrows = mysqli_num_rows( $imgr );
            
            $imgRow = mysqli_fetch_row($imgr);

            if($numrows==0){
                $this->img->exists = false;
            }else{
                
                //
                // Get user info
                //
                
                $userid = intval($imgRow[1]);
                //$userid = intval($_GET['usr-id']);

                $query = 'SELECT * FROM esco_users WHERE id="'.$userid.'"';
                $result = mysqli_query($this->link, $query);
                $num_rows = mysqli_num_rows($result);
                
                $this->user = mysqli_fetch_assoc($result);
                $this->user['photoBase'] = '/user/'.$this->user['id'].'/'.urlify($this->user['name'].'_'.$this->user['lastname']).'/photos';
                
                $this->title = 'Photo by '.$this->user['name'].' '.$this->user['lastname']; 
                
                
                
                $this->img->author = $imgRow[1];
                $this->img->time = $imgRow[2];
                $this->img->year = $imgRow[3];
                $this->img->month = $imgRow[4];
                $this->img->format = $imgRow[5];
                $this->img->views = intval($imgRow[6]);
                $this->img->album = $imgRow[7];
                $this->img->keywords = $imgRow[8];
                $this->img->title = $imgRow[9];
                $this->img->description = $imgRow[10];
                $this->img->name = $imgRow[11];
                $this->img->unlisted = $imgRow[12];
                
                $this->img->favs = mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_photo_favs WHERE photo='".$this->img->id."';"))[0];;
                
                if($this->img->title!=''){ $this->img->photoTitle = $this->img->title; }else{
                    $this->img->photoTitle = $this->img->name;
                    $this->img->photoTitle = explode('_', $this->img->photoTitle);
                    
                    array_pop($this->img->photoTitle);
                    array_pop($this->img->photoTitle);
                    
                    $this->img->photoTitle = implode('_', $this->img->photoTitle);
                }
                
                $this->title = $this->img->photoTitle;
                
                switch($this->img->format){
                    case 0:
                        $this->img->format = '.jpg';
                        break;
                    case 1:
                        $this->img->format = '.png';
                        break;
                    case 2:
                        $this->img->format = '.gif';
                        break;
                }

                if(isset($_GET['viewsize'])){
?>
<?php
{
    
    
}?>
<link href="/_inc/css/global.css" rel="stylesheet" type="text/css">
<div style="margin:30px;">
    
    <?php
    {
        $viewsize = intval($_GET['viewsize']);
        
        $linksHTML = array();
        $links = array(
            array('View Original Size', 'o'),
            array('View Large', 'l'),
            array('View Cover', 'c'),
            array('View Thumbnail', 't'),
            array('View Small', 's'),
        );
        
        
        
        $linkLength = count($links);
        if($viewsize>$linkLength-1) $viewsize = $linkLength-1;
        
        for($i=0; $i<$linkLength; $i++){            
            if($viewsize==$i)
            {
                array_push($linksHTML, array('<b>'.$links[$i][0].'</b>', $links[$i][1] ));
            }
            else
            {
                array_push($linksHTML, array('<a href="'.$_SERVER["REDIRECT_URL"].'?viewsize='.$i.'">'.$links[$i][0].'</a>', $links[$i][1] ));
            }
        }
        
        
        $i=0;
        foreach($linksHTML as $link){
            
            echo $link[0];
            
            if($i<$linkLength-1) echo ' | ';
            
            $i++;
        }
        
        $url = 'http://media.esco.net/img/social/photos/'.$this->img->author . '/'. $this->img->name . '_' . $links[$viewsize][1] . $this->img->format;
        
        //phpinfo();
    }
    ?>
    <br><br>
    <table>
        <tr>
            <td>Special Blog Code: </td>
            <td>
                <input value="<?php
                    $code = $this->img->id;
                    $code = gzcompress($code, 0);
                    $code = unpack('H*',$code);

                    print($code[1]);
                ?>" style="width:700px">                
            </td>
        </tr>
        <tr>
            <td>Image URL:</td>
            <td>
                <input value="<?php
                    print($url);
                ?>" style="width:700px">                   
            </td>
        </tr>
    </table>
    

    <br>
    <?php /*
                    $code = pack('H*','7801010100feff3800390039');
                    $code = gzuncompress($code, 0);
                    print($code);*/
                    ?>
    <br><br>
    <img src="<?php print($url);?>">
</div>    
<?php die(); ?>
<?php
                }

                
            }
        
        }
        
        $this->profileLink = '/user/'.$this->user['id'].'/'.urlify($this->user['name'].' '.$this->user['lastname']);
        
        
        
        if($this->img->exists)
        {
            if($_POST)
            {
                if(isset($_POST['comment']) && $this->loggedIn == true)
                {
                    $comment = filter_var( htmlentities($_POST['comment']), FILTER_SANITIZE_MAGIC_QUOTES );
                    $author = $this->escoID;
                    $time = time();

                    if($comment!='')
                    {
                        $query = "INSERT INTO esco_photo_comments (photo, author, time, comment) VALUES ('".$this->img->id."', '$author', '$time', '$comment')";

                        mysqli_query($this->link, $query);        

                        // Register Activity
                        logAction( $this, ACTION_COMMENT );

                        header('Location: ' . $_SERVER["REDIRECT_URL"]);
                        die();
                    }

                }
            }
            else
            {
				if($this->img->author!=$this->escoID)
				{
					// Update view
					$this->img->views++;
					mysqli_query($this->link, "UPDATE esco_photos SET `views`=".$this->img->views." WHERE `id`=".$this->img->id );
				}
            }
        }
        else
        {
            header('HTTP/1.1 404 Not Found');
        }
        
        
        //
        // Fetch image information
        //
        
    }
    
    function head(){
?>
<style>
    .ui-resizable-helper {
        border:4px solid #fff;
    }
</style>
<script>
    esco.imageId = <?php echo $this->img->id; ?>;
    esco.imageAuthor = <?php echo $this->img->author; ?>;
    esco.bImageFavorite = <?php
        $favCount = mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_photo_favs WHERE user='".$this->escoID."' AND photo='".$this->img->id."';"))[0];
        if($favCount==0) echo 'false'; else echo 'true';
    ?>;
    esco.imageName = '<?php echo $this->img->name;?>';
    esco.imageFormat = '<?php echo $this->img->format;?>';
    
    var imageURL = '<?php echo addslashes($_SERVER['REDIRECT_URL']);?>';
    var imageTitle = '<?php echo addslashes($this->img->photoTitle);?>';
    
    function favorite(){
        esco.bImageFavorite = !esco.bImageFavorite;
        
        $.ajax({
            type: "GET",
            url : "/api/favorite-photo.php",
            data: { id:esco.imageId, url:imageURL, title:imageTitle },
            dataType : 'json'
        }).done(function(data){
            esco.bImageFavorite = data.favorite;
            
            updateFavorite();
        }); 
        
        
    }
    
    function updateFavorite(){
        var favBtn = document.getElementById('fav-btn');
        
        if(favBtn!=null){
            var img = favBtn.getElementsByTagName('img')[0];

            if(esco.bImageFavorite){
                img.src = '/_inc/img/img-toolbtn-fav-on.png';
            }else{
                img.src = '/_inc/img/img-toolbtn-fav.png';
            }
        }
    }
    
    var notes = new Object();
    notes.offsets = [0,0];
    notes.workingNotes = [];
    
    notes.initCurOffsetLeft = 0;
    notes.initCurOffsetTop = 0;
    
    notes.bDisableTrigger = false;
    
    function cancelNote()
    {
        var notesTemp = document.getElementById('notes-temp');
        while(notesTemp.firstChild){
            notesTemp.removeChild( notesTemp.firstChild );
        }
        notes.workingNotes.pop();
    }
    
    function refreshNotes(data){
        if(typeof data=='undefined'){ 
            $.ajax({
                type: "POST",
                url : "/api/note-api.php",
                data: {  photo: esco.imageId },
                dataType : 'json'
            }).done(function(data){
                createNoteElements(data);
            });             
        }
        else{
            createNoteElements(data);
        }
    }
    
    function createNoteElements(data){
        var notesContainer = document.getElementById('notes-container');
        
        while(notesContainer.firstChild){
            notesContainer.removeChild( notesContainer.firstChild );
        }
        
        var notesLength = data.notes.length;
        for(var i=0; i<notesLength; i++){
            //alert(data.notes[i][4]);
            var noteAuthor = data.notes[i][2];
            var noteId = data.notes[i][0];
            var noteLeft = data.notes[i][4];
            var noteTop = data.notes[i][5];
            var noteWidth = data.notes[i][6];
            var noteHeight = data.notes[i][7];
            var noteText = data.notes[i][8];
            
            var noteDiv = document.createElement('div');
            noteDiv.id = 'noteDiv_'+noteId;
            noteDiv.style.position = 'absolute';
            noteDiv.style.left = noteLeft+'px';
            noteDiv.style.top = noteTop+'px';
            noteDiv.setAttribute('data-note-id', 'note_'+noteId);
            //noteDiv.style.backgroundColor = "rgba(0,255,255, 0.5)";
            noteDiv.onmouseover = function(){
                if(!notes.bDisableTrigger){
                    this.style.zIndex = '999';
                    document.getElementById( this.getAttribute('data-note-id') ).style.display='block';
                }
            };
            noteDiv.onmouseout = function(){
                this.style.zIndex = '1';
                document.getElementById( this.getAttribute('data-note-id') ).style.display='none';
            };
            
            noteDivBox = document.createElement('div');
            noteDivBox.className = 'notebox';
            noteDivBox.style.width = noteWidth+'px';
            noteDivBox.style.height = noteHeight+'px';
            noteDivBox.style.float = 'left';
            
            noteDivOutTrigger = document.createElement('div');
            noteDivOutTrigger.setAttribute('data-note-id', noteId);
            noteDivOutTrigger.style.marginLeft = Number(noteWidth)+4+'px';
            noteDivOutTrigger.style.height = Number(noteHeight)+2+'px';
            //noteDivOutTrigger.style.backgroundColor = "rgba(0,0,255, 0.5)";
            noteDivOutTrigger.onmouseover = function(){
                
                // Prevent noteDiv from triggering its mouseover event
                notes.bDisableTrigger = true;
                
                var noteId = this.getAttribute('data-note-id');
                
                var noteDiv = document.getElementById('noteDiv_'+noteId);
                var noteDivText = document.getElementById('note_'+noteId);
                
                noteDiv.style.zIndex = '1';
                noteDivText.style.display='none';
                //noteDiv.style.display='none';
            };
            noteDivOutTrigger.onmouseout = function(){
                // Enable noteDiv to trigger its mouseover event again
                notes.bDisableTrigger = false;
            };
            
            noteDivText = document.createElement('div');
            noteDivText.id = 'note_'+noteId;
            noteDivText.style.display = 'none';
            noteDivText.style.backgroundColor = '#FFEFD5';
            noteDivText.style.boxShadow = '0 3px 4px rgba(0,0,0,0.3)';
            noteDivText.style.padding = '5px';
            noteDivText.style.marginTop = '5px';
            noteDivText.style.zIndex = '999';
            noteDivText.style.borderRadius = '6px';
            noteDivText.style.borderTopLeftRadius = '0';
            noteDivText.style.maxWidth = '400px';
            
            noteDivTextCommentSpan = document.createElement('span');
            noteDivTextCommentSpan.innerHTML = noteText;

            noteDivText.appendChild( noteDivTextCommentSpan );
            noteDivTextCredit = document.createElement('div');
            noteDivTextCredit.style.wordBreak = 'keep-all';
            noteDivTextCredit.innerHTML = '&mdash; ' + noteAuthor[1] + ' ' + noteAuthor[2];
            
            //if(esco.escoID==)
            if(esco.escoID==esco.imageAuthor || esco.escoID==Number(noteAuthor[0])){
                noteDivDeleteLink = document.createElement('a');
                noteDivDeleteLink.setAttribute('data-note-id', noteId);
                noteDivDeleteLink.href = 'javascript:void(0);';
                noteDivDeleteLink.innerHTML = 'X';
                noteDivDeleteLink.onclick = function(){
                    
                    $.ajax({
                        type: "POST",
                        url : "/api/note-api.php",
                        data: {  photo: esco.imageId, delete:this.getAttribute('data-note-id') },
                        dataType : 'json'
                    }).done(function(data){
                        refreshNotes(data);
                    });                    
                };
                
 

                noteDivTextCredit.appendChild( document.createTextNode(' (') );
                noteDivTextCredit.appendChild( noteDivDeleteLink );
                noteDivTextCredit.appendChild( document.createTextNode(')') );
            }
            
            noteDivText.appendChild(noteDivTextCredit);
            
            noteDiv.appendChild(noteDivBox);
            noteDiv.appendChild(noteDivOutTrigger);
            noteDiv.appendChild(noteDivText);
            
            notesContainer.appendChild(noteDiv);
            //11,67,1,Aaron,Escobar,1444149854,48,31,150,65,s
        }
                   
    }
    
    
    function addNote()
    {
		var defaultNoteSize = 150;
		
        if(notes.workingNotes.length==0)
        {
            var notesContainer = document.getElementById('notes-container');
            var notesTemp = document.getElementById('notes-temp');

            var off = $( document.getElementById('note-and-image-holder') ).offset();
            notes.offsets = [ off.left, off.top ];


            var newNoteDiv = document.createElement('div');
            newNoteDiv.style.position = 'absolute';
            newNoteDiv.style.left = '0px';
            newNoteDiv.style.top = '0px';
            newNoteDiv.style.width = '150px';
            newNoteDiv.style.height = '150px';
            newNoteDiv.style.backgroundColor = 'rgba(255,255,0,0.3)';
            newNoteDiv.style.border = '1px dotted white';
            newNoteDiv.style.boxShadow = '0 0 2px rgba(0,0,0, 0.4)';

            var newNoteExtra = document.createElement('div');
            newNoteExtra.style.position = 'absolute';
            newNoteExtra.style.left = '0px';
            newNoteExtra.style.top = '160px';
            newNoteExtra.style.background = '#F2F2F7';
            newNoteExtra.style.padding = '10px';
            //newNoteExtra.style.border = '1px solid #AAC';
            newNoteExtra.style.boxShadow = '0 0 4px rgba(0,0,0, 0.4)';
            newNoteExtra.style.borderRadius = '6px';
            newNoteExtra.style.textAlign = 'left';

            newNoteExtraInput = document.createElement('input');
            newNoteExtraInput.style.width = '200px';
            newNoteExtraInput.value = 'New Note';
            newNoteExtraInput.maxLength = 150;

             newNoteExtraInput.onkeydown = function(){
                if(this.value=='New Note'){
                    this.value = '';
                }
            };
            

            
            var br = document.createElement('br');

            newNoteExtraBtnSave = document.createElement('button');
            newNoteExtraBtnSave.innerHTML = 'Save';
            newNoteExtraBtnSave.onclick = function(){
                
                if(notes.workingNotes[0][6]==''){
                    alert('No text for your note?');
                }
                else{
                    $.ajax({
                        type: "POST",
                        url : "/api/note-api.php",
                        data: {
                            photo: esco.imageId,
                            left: notes.workingNotes[0][4],
                            top: notes.workingNotes[0][5],
                            width: notes.workingNotes[0][2],
                            height: notes.workingNotes[0][3],
                            text: notes.workingNotes[0][6],
                            imgtitle: imageTitle
                        },
                        dataType : 'json'
                    }).done(function(data){
                        cancelNote();
                        
                        refreshNotes(data);
                    }).fail(function(){
                        cancelNote();
                    }); 
                }
                
            };
            newNoteExtraBtnCancel = document.createElement('button');
            newNoteExtraBtnCancel.innerHTML = 'Cancel';
            newNoteExtraBtnCancel.onclick = function(){
                cancelNote();
            };

            notesTemp.appendChild( newNoteDiv );
            notesTemp.appendChild( newNoteExtra );

            newNoteExtra.appendChild(newNoteExtraInput);
            newNoteExtra.appendChild(br);
            newNoteExtra.appendChild(newNoteExtraBtnSave);
            newNoteExtra.appendChild(newNoteExtraBtnCancel);

            notes.workingNotes.push([newNoteDiv, newNoteExtra, 150, 150, 0, 0, '']);
            
            newNoteExtraInput.onchange = function(){
                notes.workingNotes[0][6] = this.value;
            };
            
            $( newNoteDiv ).draggable({
				containment: '#zImage',
                drag: function(event, ui){
                    notes.workingNotes[0][1].style.top = ui.position.top+ notes.workingNotes[0][3]+10+'px';
                    notes.workingNotes[0][1].style.left = ui.position.left+'px';
                },
                stop: function(event, ui){
                    notes.workingNotes[0][1].style.top = ui.position.top+ notes.workingNotes[0][3]+10+'px';
                    notes.workingNotes[0][1].style.left = ui.position.left+'px';
                    
                    notes.workingNotes[0][4] = ui.position.left;
                    notes.workingNotes[0][5] = ui.position.top;
                }
            });

            $( newNoteDiv ).resizable({
				containment: '#zImage',
                resize: function(event, ui){
                    notes.workingNotes[0][3] = ui.size.height;
                    notes.workingNotes[0][1].style.top = ui.position.top+ notes.workingNotes[0][3]+10+'px';
                },
                stop: function(event, ui){
                    notes.workingNotes[0][3] = ui.size.height;
                    notes.workingNotes[0][1].style.top = ui.position.top+ notes.workingNotes[0][3]+10+'px';
                    
                    notes.workingNotes[0][2] = ui.size.width;
                }
            });
            
            newNoteExtraInput.focus();
        
        }        
    }
    
    function addTag(tagText){
        var tagList = document.getElementById('tags');

        $.ajax({
            type: "POST",
            url : "/api/add-tag.php",
            data: { id:esco.imageId, text:tagText },
            dataType : 'json'
        }).done(function(data){
            while(tagList.firstChild){
                tagList.removeChild( tagList.firstChild );
            }

            var tagsLength = data.tags.length;

            for(var i=0; i<tagsLength; i++){

                var liTag = document.createElement('li');
                liTag.innerHTML = data.tags[i];
                tagList.appendChild(liTag);
            }
        }).fail(function(){alert('FAILED!');});
    }
    
    
    $(document).ready(function(){
        $( "#tagAddInput" ).keypress(function(event) {
            if ( event.which == 13 ) {
                var tagAddInput = document.getElementById('tagAddInput');
                addTag( tagAddInput.value );
                tagAddInput.value = '';
            }
        }); 
        
        updateFavorite();
        refreshNotes();
    });    
</script>
<?php
    }
    
    
    
    function content()
    {
?>
<?php if($this->img->exists){ ?>
<div class="widget">
    <h1 class="wrap"><?php print($this->img->photoTitle);?></h1>
    
    <div style="width:700px;float:left;">
        <div class="img-toolbar">
            <?php if($this->loggedIn && $this->img->author!=$this->escoID){ ?>
            <a id="fav-btn" onclick="favorite();" href="javascript:void(0);"><img src="/_inc/img/img-toolbtn-fav.png"></a>  
            <?php } ?>
            <?php if($this->loggedIn){ ?>
            <a href="javascript:void(0);" onclick="addNote();"><img src="/_inc/img/img-toolbtn-note.png"></a>  
            <?php } ?>
            <a rel="nofollow" href="<?php print(urlify($this->img->photoTitle));?>?viewsize=0"><img src="/_inc/img/img-toolbtn-zoom.png"></a>  
            
            <?php if($this->img->author==$this->escoID){ ?>
            <a href="/user/photo-uploader/editor.php?image=<?php echo urlencode(base64_encode($this->img->id));?>" style="float:right;"><img src="/_inc/img/img-toolbtn-edit.png"></a>   
            <a href="/user/photo-uploader/deleter.php?image=<?php echo urlencode(base64_encode($this->img->id));?>"  style="float:right;"><img src="/_inc/img/img-toolbtn-delete.png"></a>   
            <?php } ?>
        </div>
        
<!--        cursor:zoom-in;-->

        <div id="image-container" style="background-color:#555;font-size:0; width:700px;display:table-cell; text-align:center; vertical-align:middle; height:300px; position:relative;">
            
            
            <div id="note-and-image-holder" style="backgound-color:#ccc; display:inline-block; position:relative; width:auto;">
                <div id="notes-container" style="display:none; position:absolute;  width:100%; height:100%; text-align:left; z-index:10; font-size:12px;" onmouseover="document.getElementById('notes-container').style.display='block';" onmouseout="document.getElementById('notes-container').style.display='none';">
                </div>
                
                <div id="notes-temp" style="position:absolute; display:block; cursor:move; font-size:12px; z-index:20;">
                </div>
                
                
                <div style="position:absolute; width:100%; height:100%; z-index:0;" onmouseover="document.getElementById('notes-container').style.display='block';">
                </div>
                <img id="zImage" style="-moz-user-select: none;" src="<?php print( 'http://media.esco.net/img/social/photos/'.$this->img->author . '/'. $this->img->name . '_l' . $this->img->format );?>">
            </div>
            
            
        </div>    
    
        <br>
        
        <?php 
        {
            $description = $this->img->description;
            $description = addLinks( nl2br($description), true );
            
            /*
            
            $httpPos = stripos($description, 'http://');

            if($httpPos !== false) {

                $everythingAfter = substr($description, $httpPos);
                $everythingAfter = explode(' ', $everythingAfter);

                $httpLink = $everythingAfter[0];

                $description = str_replace($httpLink, "<a href=\"$httpLink\">$httpLink</a>", $description);
            }
                    */
            
            print( $description );
        }
        ?>
    
    
    
        <br><br><br>
    
        <!-- COMMENTS BEGIN -->
        

        <!-- POST BOX -->
        <?php
        if($this->loggedIn)
        {
            echo '<h2 style="margin-top:30px;">Add your comment</h2>';
            include($this->siteDirectory . '/_inc/php/comment-box.php');
        }
        else
        {
            echo '<h2 style="margin-top:30px;">Comments</h2>'; 
        }
        ?>
        <!-- POST BOX -->

        <?php
        {
            // Table
            $COMMENTS_TABLE = 'esco_photo_comments';
            $COMMENTS_FOR_ROW = 'photo';
            $COMMENTS_FOR_VALUE = $this->img->id;      
            
            $ENABLE_VIEW_PHOTOS = true;
            
            // How many items to show per page
            $RESULTS_PER_PAGE = 15;
            // How pages to show until the ellipsis (...)
            // Must be an odd number (not evenly divisable by 2. E.g., 1, 3, 5, 7, 9, 11, 13, etc)
            $MAX_PAGE_GROUP = 9;
            
            include($this->siteDirectory . '/_inc/php/comments.php');
        }
        ?>     

        <!-- COMMENTS END -->
    </div>
    
    
    <div style="float:left; margin-left:15px; width:220px; font-size:13px;">
        
        <div class="photoviewer-box">
            <img src="http://media.esco.net/img/social/<?php echo $this->user['id'];?>/profile_small.jpg" style="width:40px;height:40px; float:left; vertical-align:middle;">
            <div style="float:left; margin-left:10px;">
                <span style="font-family:Tahoma, verdana, Arial, sans-serif ;font-size:11px;">Uploaded <?php echo date('F j, Y', $this->img->time);?> by</span><br>
                <b><a href="<?php echo '/user/'.$this->user['id'].'/'.urlify( $this->user['name'].'_'.$this->user['lastname'] ).'/photos';?>"><?php echo $this->user['name'].' '.$this->user['lastname'];?></b></a>
            </div>
            <div class="cf"></div>
        </div>
        
        <div class="photoviewer-box">
            In photostream	
            
            <div class="img-slider" style="margin-top:5px;">
                <img src="/_inc/img/img-slider-older.png" style="height:50px;">
                <?php {

                function outputSliderImg($link, $img){
                    $format = $img[2]; 
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
                    $imageFile = $img[4] . '_s' . $format;
                    $title = $img[3];
                    
                    if($title==''){
                        $title = $img[4];
                        $title = explode('_', $title);

                        array_pop($title);
                        array_pop($title);

                        $title = implode('_', $title);
                    }
                    

                    $authorName = mysqli_fetch_row( mysqli_query($link, "SELECT name,lastname FROM esco_users WHERE id='".$img[1]."' ") );

                    //echo 'http://media.esco.net/img/social/'.$img[1].'/photos/'.$imageFile;
                    echo '<a href="/user/'.$img[1].'/'.urlify($authorName[0].'_'.$authorName[1]).'/photos/'.$img[0].'/'.urlify($title).'"><img style="width:50px; height:50px;" src="http://media.esco.net/img/social/photos/'.$img[1].'/'.$imageFile.'"></a>';
                }

                if($this->escoID!=$this->img->author)
                    $unlistedCond = 'AND `unlisted`=0';
                else
                    $unlistedCond = '';            

                $prevSlideResult = mysqli_query($this->link, "SELECT * FROM (SELECT id,author,format,title,name FROM esco_photos WHERE id<".$this->img->id." AND `author`=".$this->img->author." $unlistedCond ORDER BY `id` DESC LIMIT 1) AS T1 ORDER BY id ASC");
                $nextSlideResult = mysqli_query($this->link, "SELECT id,author,format,title,name FROM esco_photos WHERE id>".$this->img->id." AND `author`=".$this->img->author." $unlistedCond ORDER BY `id` ASC LIMIT 1");

                $endHTML = '<img src="/_inc/img/img-slider-end.png" style="width:50px;height:50px;">';

                if(mysqli_num_rows($prevSlideResult)==0){
                    echo $endHTML;
                }else{
                    while($slideImgRow = mysqli_fetch_row($prevSlideResult)){
                        outputSliderImg( $this->link, $slideImgRow );
                    }
                }

                print( '<img src="http://media.esco.net/img/social/photos/'.$this->img->author . '/'. $this->img->name . '_s' . $this->img->format . '" style="width:50px; height:50px; filter: brightness(0.5);">' );

                if(mysqli_num_rows($nextSlideResult)==0){
                    echo $endHTML;
                }else{
                    while($slideImgRow = mysqli_fetch_row($nextSlideResult)){
                        outputSliderImg( $this->link, $slideImgRow );
                    }
                }



                } ?>
                <img src="/_inc/img/img-slider-newer.png" style="height:50px;">
            </div>    
        </div>
        
        
        <div id="album-boxes">
        <?php include($this->siteDirectory.'/api/photo-album-boxes.php'); ?>
        </div>    
        
        
        
        
        
        
        
        <?php if($this->loggedIn && $this->img->author==$this->escoID){ ?>
        <a href="javascript:void(0);" id="addAlbumLink" style="display:block;" onclick="document.getElementById('addAlbumLink').style.display='none'; document.getElementById('albums').style.display='block'; document.getElementById('cancelAlbum').style.display='block';">Add/Remove to albums</a>
        <a href="javascript:void(0);" id="cancelAlbum" style="display:none;" onclick="document.getElementById('albums').style.display='none'; document.getElementById('cancelAlbum').style.display='none'; document.getElementById('addAlbumLink').style.display='block';">Cancel</a>

        <div id="albums" style="display:none;">
        <?php
        {
			$ownerID = $this->img->author;
            $select = "SELECT * FROM esco_photo_albums WHERE `owner`=0 OR `owner`=$ownerID ORDER BY `owner` ASC, `title` ASC";
            $result = mysqli_query($this->link, $select);

            while ($row = mysqli_fetch_row($result)) {
                echo '<div class="category">';
                echo $row[2].' | <a href="javascript:void(0);" onclick="addRemoveAlbum('.$row[0].', 1);">Add</a> | <a href="javascript:void(0);" onclick="addRemoveAlbum('.$row[0].', 0);">Remove</a>';
                echo '</div>';
            }
        }
        ?>
        </div> 
        <script>
            
            function refreshAlbums(){
                $.ajax({
                    type: "POST",
                    url : "/api/photo-album-boxes.php",
                    data: {  img:esco.imageId, author:esco.imageAuthor, name:esco.imageName, format:esco.imageFormat },
                }).done(function(html){
                    document.getElementById('album-boxes').innerHTML = html;
                });                
            }
            

            
            function addRemoveAlbum(albumID, actionCode){
                $.ajax({
                    type: "POST",
                    url : "/api/photo-album-manage.php",
                    data: {  img:esco.imageId, album:albumID, action:actionCode },
                }).done(function(){
                    refreshAlbums();
                });                
            }            
        </script>
        <br><br>
        <?php } ?>
        
        
        
        <h3>Tags</h3>
        <ul id="tags" class="metalist taglist">
            <?php {
            $tags = explode(' ', $this->img->keywords);
            foreach($tags as &$tag){
                echo "<li>$tag</li>";
            }
            } ?>
            
        </ul>
        <div id="tagAddForm" style="display:none; margin-bottom:5px;">
            <input id="tagAddInput" style="font-weight:bold; font-family:Roboto;width:100%;background-color:#F2F2F7;" onblur="this.parentNode.style.display='none'; document.getElementById('addTagLink').style.display='block';">
        </div>
        <div>
        <a href="javascript:void(0);" id="addTagLink" onclick="document.getElementById('addTagLink').style.display='none'; document.getElementById('tagAddForm').style.display='block'; document.getElementById('tagAddInput').focus();">Add new tag</a>
        </div>    
        <br>
        <br>
        <br>
        <h3>Additional information</h3>
        <ul class="metalist">
            <?php {
            $r=mysqli_fetch_row(mysqli_query($this->link, "SELECT json FROM esco_photo_exif WHERE photo='".$this->img->id."';"))[0];
            $data = json_decode( $r );
			
			//var_dump($data);
            
            if(isset($data->DateTimeOriginal))
                echo '<li>Date taken:<br><b>'.escoDate(strtotime($data->DateTimeOriginal)).'</b></li>';
            elseif(isset($data->DateTime))
                echo '<li>Date taken:<br><b>'.escoDate(strtotime($data->DateTime)).'</b></li>';
            
            if(isset($data->Make) && isset($data->Model)){
                $makePos = stripos($data->Model, $data->Make);
                if ($makePos === false)
                    echo '<li>Shot with a <b>'.$data->Make.' '.$data->Model.'</b></li>';
                else
                    echo '<li>Shot with a <b>'.$data->Model.'</b></li>';
            }
            
            if(isset($data->Artist))
                echo '<li>Author:<br><b>'.$data->Artist.'</b></li>';
            
            if(isset($data->Software))
                echo '<li>Edited with  <b>'.$data->Software.'</b></li>';            
            }?>
            <li>Unlisted: <b><?php if($this->img->unlisted==0) echo 'No';else echo 'Yes';?></b></li>
            <li>Favorites: <b><?php echo number_format($this->img->favs);?></b></li>
            <li>Views: <b><?php echo number_format($this->img->views);?></b></li>
        </ul>
        
    </div>
    
    <div class="cf"></div>
    
</div>
<?php }else{ ?>
<div class="widget">
    <h1>Photo does not exist</h1>
</div>
<?php } ?>
<?php
    }
}

new customPage();