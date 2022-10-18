<?php

class customPage extends page {
    public $title = 'Edit Categories';	
    public $private = true;
    
    public $category;
    public $newAlbum;
    
    public $errors = array();
    

    function init()
    {
        $this->newAlbum = new stdClass;
        $this->newAlbum->name = '';
        
        $this->album = new stdClass;
        $this->album->id = 0;
        $this->album->name = '';
        
        if(isset($_GET['new']))
            $this->album->id = intval($_GET['new']);
        
        if(isset($_GET['delete'])){
    
            $this->album->id = intval($_GET['delete']);

            mysqli_query($this->link, "DELETE FROM esco_photo_albums WHERE `id`=".$this->album->id." and `owner`=".$this->escoID.";");
            header('Location: /user/photo-uploader/albums.php');
            die();        
        }

        
        if($_POST){
            if(isset($_POST['name']) && $_POST['name']!=''){
                $this->newAlbum->name = filter_var( htmlentities($_POST['name']), FILTER_SANITIZE_MAGIC_QUOTES );
                $id = $this->escoID;
                $name = $this->newAlbum->name;
                $query = "INSERT INTO esco_photo_albums (owner, title) VALUES($id,'$name')";
                mysqli_query($this->link, $query);

                
                header('Location: /user/photo-uploader/albums.php');
                die();
                
            }
        }
        //filter_var( htmlentities($title, FILTER_SANITIZE_MAGIC_QUOTES ));
    }
    
    function head(){
?>
<script>
</script>
<?php
    }

    function content() {
?>
<?php if(isset($_GET['new'])) { ?>
<div class="widget">
    <h1>New Album</h1>
    <form action="" method="POST">
        <table class="table">
            <tr>
                <td>Title</td>
                <td style="width:500px;"><input style="width:100%;" type="text" name="name" value="<?php echo $this->newAlbum->name;?>" maxlength="100"></td>
            </tr>
        </table>


        <br><br><br><br>


        <button type="submit">Save</button>
        <button type="button" onclick="location.href='/user/blog/categories.php';">Cancel</button>
    </form>   
    <?php
        if(count($this->errors)>0){
            echo '<div class="error" style="margin-top:20px;"><ul>';
            foreach($this->errors as &$error){
                echo '<li>'.$error.'</li>';
            }
            echo '</ul></div>';
        }
    ?>
</div>    
<?php }elseif(isset($_GET['delete'])) { ?>
<div class="widget">
    <h1>Cannot delete Category</h1>
    The selected category has pages in it. Try removing the pages first.
</div>    
<?php }else{ ?>

<div class="widget" style="position:relative;">
    <h1>My Photo albums</h1>
    <button onclick="location.href='<?php echo $this->escoProfileURL.'/photos';?>';">&laquo; Back to my photos</button>
    <button onclick="location.href='/user/photo-uploader/albums.php?new=0';">Create New Album</button>
    <br>
    <br>
    
    <?php
	{
		$select = "SELECT * FROM esco_photo_albums WHERE owner=".$this->escoID." ORDER BY `id` ASC";
		$result = mysqli_query($this->link, $select);
        
        $d = new DOMDocument;
        $d->loadHTML('<div id="categoriesDiv"></div>', LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        
        $categoriesDiv = $d->getElementById( 'categoriesDiv' );
        
        while ($row = mysqli_fetch_row($result)) {
            $id = $row[0];
            $parentId = $row[1];
            $name = $row[2];
            
            $parent = $d->getElementById( 'cat_'.$parentId );
            
            $div = $d->createElement('div');
            $div->setAttribute('id', 'cat_' . $id );
            $div->setAttribute('class', 'category');
            
            $divBoldName = $d->createElement('b');
            $divBoldName->nodeValue = $name;
            
            $small = $d->createElement('small');
            
            
            $divLinkDelete = $d->createElement('a');
            $divLinkDelete->nodeValue = 'Delete';
            $divLinkDelete->setAttribute('href', 'albums.php?delete='.$id);
            
            
            
            $div->appendChild( $divBoldName );
            $div->appendChild( $d->createTextNode(' ― ') );
            $div->appendChild( $small );

            $small->appendChild($divLinkDelete);
            
            
            
            if($parent){
                $div->setAttribute('style', 'margin-left:20px;');
                $parent->appendChild($div);
            }
            else{
                $categoriesDiv->appendChild($div);
            }
        }
        
        print( $d->saveHTML() );
	}
?>
</div>
<?php } ?>
<?php
    }
}

new customPage();