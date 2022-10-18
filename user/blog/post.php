<?php

class customPage extends page {
    public $title = 'Edit Post';	
    public $private = true;
    public $errors = array();
    
    public $post;
    public $bNewPost = false;

    function init()
    {
        $this->post = new stdClass;
        $this->post->id = 0;
        $this->post->author = $this->escoID;
        $this->post->time = time();
        $this->post->year = date('Y');
        $this->post->month = date('n');
        $this->post->title = "";
        $this->post->content = "";
        $this->post->cover = "";
        $this->post->draft = 0;
        $this->post->category = 1;
        
        
        
        if(isset($_GET['id']) && intval($_GET['id'])!=0)
            $this->post->id = intval($_GET['id']);
        else
            $this->bNewPost = true;
        
        
        if( !$this->bNewPost ) // IF EDITING POST
        {
            // Fetch Post Data From DB
            
            $result = mysqli_query( $this->link, 'SELECT * FROM esco_blog_posts WHERE id=' . $this->post->id . ' AND author=' . $this->post->author );
            $count = mysqli_num_rows( $result );
            
            if($count!=0){
                $row = mysqli_fetch_row( $result );
                
                if(intval($row[1]) == $this->escoID)
                    $this->post->author = intval($row[1]);
                
                $this->post->author = $row[1];
                $this->post->time = intval($row[2]);
                $this->post->year = intval($row[3]);
                $this->post->month = intval($row[4]);
                $this->post->title = $row[5];
                $this->post->content = $row[6];
                $this->post->cover = $row[7];
                $this->post->draft = intval($row[8]);
                $this->post->category = intval($row[9]);
                
                               
            }
            else
            {
                // If the requested post could not be found for the current user
                // then create a new post under this user's name
                
                $this->bNewPost = true;
            }
            
        }

        
        $this->title = 'Editing post "'.trimText($this->post->title, 20).'"';
        
        
        $this->post->categoryTitle = '';
        
        $catTitleResult = mysqli_query($this->link, "SELECT name FROM esco_blog_categories WHERE id='".$this->post->category."';");
        
        if(mysqli_num_rows($catTitleResult)!=0){
            $this->post->categoryTitle = mysqli_fetch_row($catTitleResult)[0];
        }
        
        //die('ID:' . $this->post->id);
        
        
        if($_POST){
            if(!isset($_POST['title']) || !isset($_POST['content']) || !isset($_POST['category']) ){
                array_push($this->errors, 'Bad request');
            }
            else{
                $this->post->title = strip_tags(filter_var( $_POST['title'], FILTER_SANITIZE_MAGIC_QUOTES));
                $this->post->content = filter_var( $_POST['content'], FILTER_SANITIZE_MAGIC_QUOTES);
                
                if($this->post->title == '')
                    array_push($this->errors, 'Your post needs a title');
                
                
                $this->post->category = intval($_POST['category']);
                
                //
                // Find cover
                //
                
                if($this->post->content != ''){
                    $d = new DOMDocument;
                    $d->loadHTML( $_POST['content'] );
                    
                    $imgs = $d->getElementsByTagName('img');
                    
                    $bFoundCover = false;
                    
                    foreach ($imgs as $img) {
                        if($bFoundCover==false){
                            $imgBlogCode = $img->getAttribute('data-blog-code');
                            if($imgBlogCode!=''){
                                $bFoundCover = true;
                                $this->post->cover = $imgBlogCode;
                            }
                        }
                    }
                    //show($this->post);
                    //die();
                }
                
                
                //
                // Get the post cover id for db row
                //


                if($this->post->cover!='' && $this->post->cover!='0' && $this->post->cover!=0){
                    $this->post->cover = pack('H*', $this->post->cover);
                    $this->post->cover = gzuncompress($this->post->cover, 0);
                }
                $this->post->cover = intval($this->post->cover);

                //
                // Cleanup
                //

                $this->post->content = trim($this->post->content);

                // Remove hideous new lines at the beginning and ending in the article

                $emptyP = '<p>&nbsp;</p>';
                $emptyPLen = strlen($emptyP);

                while(substr($this->post->content, 0, $emptyPLen)==$emptyP){
                    $this->post->content = trim(substr($this->post->content, $emptyPLen));
                }
                while(substr($this->post->content, strlen($this->post->content)-$emptyPLen)==$emptyP){
                    $this->post->content = trim(substr($this->post->content, 0, strlen($this->post->content)-$emptyPLen));
                }                
				
				//
				// Correct videos with fixed sizing.
				// TODO: Make your own video plugin for tinymce
				//
                
				//$videos = $d->getElementsByTagName('img');
				
                //
                // Last minute check
                //
                
                if($this->post->content == '')
                    array_push($this->errors, 'A blog post with no content!?');
                
                //
                // Change the db
                //
                
                if(count($this->errors)==0){
                    
                    if($this->bNewPost){
                        $query = "INSERT INTO esco_blog_posts (author, time, year, month, title, content, cover, draft, category) VALUES ('".$this->post->author."','".$this->post->time."', '".$this->post->year."', '".$this->post->month."','".$this->post->title."','".$this->post->content."','".$this->post->cover."','".$this->post->draft."','".$this->post->category."') "; 
                        transfer($this, 0, $this->escoID, 5);
                    }else{
                        $query = "UPDATE esco_blog_posts SET title='".$this->post->title."', content='".$this->post->content."', cover='".$this->post->cover."', category='".$this->post->category."' WHERE id='".$this->post->id."';";
                    }
                    

                    
                    mysqli_query($this->link, $query);
                    
                    $postId = mysqli_insert_id($this->link);
                    
                    if($this->bNewPost) logAction( $this, ACTION_BLOG_POST, $this->post->title, '/blog/'.$this->post->year.'/'.$postId.'/'.urlify($this->post->title) );
                    
                    
                    header("Location: /user/blog/");
                    die();
                }
                
            }
        }
    }
    
    function head(){
?>
<script src="/_inc/js/tinymce/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: "textarea",
        plugins: ["link", "image", "media", "emoticons", "imagetools", "escoimage"],
        //plugins: ["media"],
        toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media emoticons | insertimage",
        content_css: "/_inc/css/editor.css",
        document_base_url: "http://www.esco.net/",
        relative_urls: false
    });
</script>
<?php
    }

    function content() {
?>
<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>
<div class="widget" style="position:relative;">
    <a class="btn" style="position:absolute; top:0; left:0;" href="/user/blog/">Back to your posts</a>
    
    <br><br><br><br>
    
        <?php
            if(count($this->errors)>0){
                echo '<div class="error"><ul>';
                foreach($this->errors as &$error){
                    echo '<li>'.$error.'</li>';
                }
                echo '</ul></div><br><br>';
            }
        ?>
    
    <h1>Editing "<?php  if($this->post->title=='')
                            echo 'New Article';
                        else
                            echo $this->post->title; ?>"</h1>
    
    <br>
    
    <form action="" method="POST">
        <p style="text-align:center;">Title: <input name="title" value="<?php echo $this->post->title;?>" style="width:500px;"><br></p>

        <textarea name="content" style="height:500px;"><?php echo htmlspecialchars($this->post->content);?></textarea>

        <input type="hidden" name="id" value="<?php echo $this->post->id;?>">
        
        <BR><BR>

            
        <div>
            <div class="category"> 
                File under <b id="file-under-ui">"<?php echo $this->post->categoryTitle;?>"</b> &nbsp;<button type="button" onclick="toggleCats();">Change</button>
                <div id="catsDiv" style="display:none;">
<?php
{
    $select = "SELECT * FROM esco_blog_categories ORDER BY `id` ASC";
    $result = mysqli_query($this->link, $select);

    $d = new DOMDocument;
    $d->loadHTML('<div id="categoriesDiv"></div>', LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);

    $categoriesDiv = $d->getElementById( 'categoriesDiv' );

    while ($row = mysqli_fetch_row($result)) {
        $id = $row[0];
        $parentId = $row[1];
        $name = $row[2];

        $parent = $d->getElementById( 'cat_'.$parentId );
        $parentChildCats = $d->getElementById( 'cat_childCats_'.$parentId );

        $div = $d->createElement('div');
        $div->setAttribute('id', 'cat_' . $id );
        $div->setAttribute('class', 'category');

        $divBoldName = $d->createElement('b');
        $divBoldName->nodeValue = $name;

        $small = $d->createElement('small');

        $divLinkFile = $d->createElement('a');
        $divLinkFile->nodeValue = 'File under here';
        $divLinkFile->setAttribute('href', 'javascript:void(0);');
        $divLinkFile->setAttribute('data-cat-id', $id);
        $divLinkFile->setAttribute('data-cat-name', htmlentities($name));
        $divLinkFile->setAttribute('onclick', "setCat(this);");

        $divLinkShow = $d->createElement('a');
        $divLinkShow->nodeValue = 'Show Contents';
        $divLinkShow->setAttribute('href', 'javascript:void(0);');
        $divLinkShow->setAttribute('onclick', "if(document.getElementById('cat_childCats_".$id."').style.display=='none'){document.getElementById('cat_childCats_".$id."').style.display='block'}else{document.getElementById('cat_childCats_".$id."').style.display='none'}");
        
        $divChildCats = $d->createElement('div');
        $divChildCats->setAttribute('id', 'cat_childCats_' . $id);
        $divChildCats->setAttribute('style', 'display:none;');



        $div->appendChild( $divBoldName );
        $div->appendChild( $d->createTextNode(' ― ') );
        $div->appendChild( $small );
        $div->appendChild( $divChildCats );

        $small->appendChild($divLinkFile);
        $small->appendChild( $d->createTextNode(' | ') );
        $small->appendChild($divLinkShow);


        if($parent){
            $div->setAttribute('style', 'margin-left:20px;');
            $parentChildCats->appendChild($div);
        }
        else{
            $categoriesDiv->appendChild($div);
        }
    }

    print( $d->saveHTML() );
}
?>
                </div>
            </div>
        </div>  
            
        <input type="hidden" name="category" value="<?php echo $this->post->category;?>" id="category-hidden">    
            
        <script>
            var catState = false;
            function toggleCats(){
                var catsDiv = document.getElementById('catsDiv');
                if(catState){
                    catsDiv.style.display = 'none';
                }else{
                    catsDiv.style.display = 'block';
                }
                catState = !catState;
            }
            function setCat(element){
                var id = element.getAttribute('data-cat-id');
                var name = element.getAttribute('data-cat-name');
                
                document.getElementById('category-hidden').value = id;
                document.getElementById('file-under-ui').innerHTML = '"'+name+'"';
                toggleCats();
            }
        </script>
            
            
        <br><br>
            
        <button>Save and Submit</button>
        
        <?php if($this->bNewPost) { ?>
        <small>&nbsp; You will earn &euro;5 for this post.</small>
        <?php } ?>

    </form>
</div>
<?php
    }
}

new customPage();