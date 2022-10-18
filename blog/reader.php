<?php
error_reporting(0);

class customPage extends page {
    public $title = 'Blog';

    public $post;

    public $bPostExists;

    public $cond;

    function init()
    {

        if(isset($_GET['id']) && intval($_GET['id'])!=0){
            $postID = intval($_GET['id']);
            $this->post = new stdClass;
            $this->post->id = $postID;

            $select = "SELECT * FROM esco_blog_posts WHERE id='$postID' ORDER BY `id` DESC";
            $result = mysqli_query($this->link, $select);
            $numrows = mysqli_num_rows($result);

            if($numrows==0){
                $this->bPostExists=false;
                header('HTTP/1.1 404 Not Found');
            }else{
                $this->bPostExists=true;
            }

            $row = mysqli_fetch_row($result);

            $this->post->author = $row[1];
            $this->post->time = $row[2];
            $this->post->year = $row[3];
            $this->post->month = $row[4];
            $this->post->title = $row[5];
            $this->post->content = $row[6];
            $this->post->media = $row[7];

            $this->post->category = $row[9];

            $this->post->categoryForced = $this->post->category;

            if(isset($_GET['in-cat']))
                $this->post->categoryForced = intval($_GET['in-cat']);

            if($this->post->categoryForced==0)
                $this->post->categoryForced = $this->post->category;




            //
            // Cat all category children
            //

            if($this->bPostExists){
                $children = array($this->post->categoryForced);
                getAllCategoryChildren($this->link, $this->post->categoryForced, $children);

                $this->cond = '';
                foreach($children as $cat){
                    $this->cond .= "`category`=$cat OR ";
                }
                $this->cond = substr($this->cond, 0, strlen($this->cond)-4);
                /*
                if(isset($_GET['in-cat'])){
                    $this->cond = substr($this->cond, 0, strlen($this->cond)-4);
                }else{
                    $this->cond = '`category`='. $this->post->category;
                }*/

                $this->title = $this->post->title . ' - Blog ';
            }
        }
        else
        {
            die();
        }

        if($_POST && $this->bPostExists)
        {
            if(isset($_POST['comment']) && $this->loggedIn == true)
            {
                //$postID = $postID;
                $comment = filter_var( htmlentities($_POST['comment']), FILTER_SANITIZE_MAGIC_QUOTES );
                $author = $this->escoID;
                $time = time();

                if($comment!='')
                {
                    $query = "INSERT INTO esco_blog_comments (post, author, time, comment) VALUES ('$postID', '$author', '$time', '$comment')";

                    mysqli_query($this->link, $query);

                    // Register Activity
                    logAction( $this, ACTION_COMMENT );

                    header('Location: ' . $_SERVER["REDIRECT_URL"]);
                    die();
                }

            }
        }

    }

    function content() {
?>
<?php if($this->bPostExists){ ?>

    <?php
    $authorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$this->post->author."' ") );
    ?>
<div class="widget">


    <?php

        //
        // SMART Navigation
        // ================
        // Shows next and previous posts in the post's category OR in the current category
        // which can be defined via the url query ?in-cat=(0-9).
        // It also automatically adds the ?in-cat to links if the next/prev. post is not in the
        // same category as this post.
        // (e.g., parent node has a post and has another child nodes with another post and we
        // are viewing a post in the parent node. In this case we should show nephew posts.)
        //

        $prevPost = mysqli_fetch_row(
            mysqli_query($this->link, "SELECT * FROM (SELECT id,year,title,category FROM esco_blog_posts WHERE id<".$this->post->id." AND (".$this->cond.") ORDER BY `id` DESC LIMIT 1) AS T1 ORDER BY id ASC")
        );
        $nextPost = mysqli_fetch_row(
            mysqli_query($this->link, "SELECT id,year,title,category FROM esco_blog_posts WHERE id>".$this->post->id." AND (".$this->cond.") ORDER BY `id` ASC LIMIT 1")
        );

        if(count($prevPost)!=0 || count($nextPost)!=0){
            echo '<div class="category-box"><div style="height:20px;">';

            $inCat = 0;
            if(isset($_GET['in-cat'])){
                $inCat = intval($_GET['in-cat']);
            }

            $urlPrevQueryArr = array();
            $urlNextQueryArr = array();

            if($inCat!=0){
                if($prevPost[3]!=$inCat) array_push($urlPrevQueryArr, "in-cat=$inCat");
                if($nextPost[3]!=$inCat) array_push($urlNextQueryArr, "in-cat=$inCat");
            }else{
                if($prevPost[3]!=$this->post->category){
                    array_push($urlPrevQueryArr, 'in-cat='.$this->post->category);
                    array_push($urlNextQueryArr, 'in-cat='.$this->post->category);
                }
            }

            $urlPrevQuery = '';
            $urlNextQuery = '';

            if( count($urlPrevQueryArr)>0 ) $urlPrevQuery = '?'.implode('&', $urlPrevQueryArr);
            if( count($urlNextQueryArr)>0 ) $urlNextQuery = '?'.implode('&', $urlNextQueryArr);

            if(count($prevPost)!=0)
                echo '<a href="/blog/'.$prevPost[1].'/'.$prevPost[0].'/'.urlify($prevPost[2]).$urlPrevQuery.'" > &laquo; '.trimText($prevPost[2],50).'</a>';
            if(count($nextPost)!=0)
                echo '<a href="/blog/'.$nextPost[1].'/'.$nextPost[0].'/'.urlify($nextPost[2]).$urlNextQuery.'" style="float:right;">'.trimText($nextPost[2],50).' &raquo;</a>';

            echo '<a style="clear:both;"></a></div></div>';
        }

    ?>




    <div style="width:560px; padding-top:50px;" class="center">
        <h1 style="margin-bottom:0; font-weight:bold; font-size:26pt;"><?php echo $this->post->title;?></h1>
        <ul class="news-headlines"><li>By <?php echo '<a href="/blog/author/'.$this->post->author.'/'.urlify($authorInfo[3].' '.$authorInfo[4]).'">'.$authorInfo[3].' '.$authorInfo[4].'</a>';?> <span style="color:#bbb;">| <small><?php echo escoDate($this->post->time);?></small><?php if($this->post->author==$this->escoID) echo '&nbsp;| <a href="/user/blog/post.php?id='.$this->post->id.'">Edit Post</a>'; ?></span></li></ul>

        <div class="post-text">
            <?php
            $d = new DOMDocument;

            $d->loadHTML( '<?xml encoding="UTF-8">'.$this->post->content, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );

            // Remove xml tag
            $d->removeChild( $d->childNodes->item(0) );


            $postImgs = $d->getElementsByTagName('img');
            $imgCount = $postImgs->length;


            for($i=$imgCount-1; $i>-1; --$i)
            {
                $imgTag =  $postImgs->item($i);

                $imgBlogCode = $imgTag->getAttribute('data-blog-code');
                $imgBlogCode = preg_replace("/[^a-f0-9]/", "", $imgBlogCode);

                if($imgBlogCode!=''){

                    $imgBlogCode = pack('H*',$imgBlogCode);
                    $imgBlogCode = intval(gzuncompress($imgBlogCode, 0));

                    //
                    // Find the image
                    //

                    $imgr = mysqli_query($this->link, "SELECT author,format,title,name FROM esco_photos WHERE id='$imgBlogCode';");
                    $numrows = mysqli_num_rows( $imgr );
                    $imgRow = mysqli_fetch_row($imgr);

                    if($numrows!=0){
                        $format = '.jpg';

                        switch($imgRow[1]){
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

                        $authorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$imgRow[0]."' ") );
                        $authorName = $authorInfo[3].' '.$authorInfo[4];

                        if($imgRow[2]==''){
                            $imgRow[2] = explode('_', $imgRow[3]);

                            array_pop($imgRow[2]);
                            array_pop($imgRow[2]);

                            $imgRow[2] = implode('_', $imgRow[2]);
                        }


                        $imgURL = 'http://media.esco.net/img/social/photos/'.$imgRow[0].'/'.$imgRow[3].'_l'.$format;
                        $imgLINK = 'http://www.esco.net/user/'.$imgRow[0].'/'.urlify($authorInfo[3].' '.$authorInfo[4]).'/photos/'.$imgBlogCode.'/'.urlify($imgRow[2]);

                    }
                    else
                    {
                        $imgURL = 'http://media.esco.net/img/social/photos/l.jpg';
                        $imgLINK = 'http://www.esco.net/photos/';

                        $authorInfo = null;
                        $authorName = 'who knows';
                    }

                    $divOverlayID = create_guid();

                    $div = $d->createElement('span');

                    $imgParent = $postImgs->item($i)->parentNode;


                    /*
                    if($imgParent->nodeName=='p'){
                        // <DIV>s can't go inside <P>s so we must replace the <P> with a <DIV>
                        // Convert the parent of the image from P to DIV

                        $newImgParent = $d->createElement('div');

                        // Get all of the img's parents children and add them to the new img parent
                        foreach($imgParent->childNodes as $imgPChild){
                            $node = $d->importNode($imgPChild, true);
                            $newImgParent->appendChild($node);
                        }

                        $imgParent->parentNode->replaceChild( $newImgParent, $imgParent );
                    }
                    else{
                        $imgParent->replaceChild( $div, $postImgs->item($i) );
                    }*/


                    $imgParent->replaceChild( $div, $postImgs->item($i) );


                    $div->setAttribute('style', 'display:block; text-align:left; position:relative;font-size:0;');

                    $a = $d->createElement('a');
                    $div->appendChild( $a );
                    $a->setAttribute('href', $imgLINK);

                    $img = $d->createElement('img');
                    $a->appendChild( $img );
                    $img->setAttribute('src', $imgURL);
                    $img->setAttribute('onmouseover', 'document.getElementById("'.$divOverlayID.'").style.display="block";');
                    $img->setAttribute('onmouseout', 'document.getElementById("'.$divOverlayID.'").style.display="none";');


                    $divOverlay = $d->createElement('span');
                    $div->appendChild( $divOverlay );
                    $divOverlay->setAttribute('style','display:block; position:absolute; pointer-events:none; display:none; width:100%;overflow:hidden;max-height:'.$imgTag->getAttribute('height').'px; bottom:0; left:0; background:url(\'http://www.esco.net/_inc/img/overlay.png\');');
                    $divOverlay->setAttribute('id', $divOverlayID);
                    $divOverlay->setAttribute('class', 'wrap');


                    $divOverlayText = $d->createElement('span');
                    $divOverlay->appendChild( $divOverlayText );
                    $divOverlayText->setAttribute('style', 'display:block; margin:10px; color:#fff; font-size:12px;font-weight:300;');


                    $divOverlayTextLink = $d->createElement('a');
                    $divOverlayText->appendChild( $divOverlayTextLink );
                    $divOverlayTextLink->setAttribute('href', $imgLINK);
                    $divOverlayTextLink->setAttribute('style', 'color:#fff;');
                    $divOverlayTextLink->nodeValue = '"'.$imgRow[2] . '"';


                    $authorBlock = $d->createElement('span');
                    $divOverlayTextLink->appendChild( $authorBlock );
                    $authorBlock->nodeValue = "by $authorName";
                    $authorBlock->setAttribute('style', 'padding-left:3px;display:inline-block;word-break:keep-all;');

                    $placement = intval($imgTag->getAttribute('data-placement'));

                    if($placement==1){
                        $img->setAttribute('style', 'width:100%;');
                    }else{
                        $img->setAttribute('style', 'width:'.$imgTag->getAttribute('width').'px; height:'.$imgTag->getAttribute('height').'px;');
                    }

                    switch($placement){
                        case 0:
                            $div->setAttribute('class', 'blog-img-left');
                        break;
                        case 1:
                            $div->setAttribute('class', 'blog-img-middle');
                        break;
                        case 2:
                            $div->setAttribute('class', 'blog-img-right');
                        break;
                    }
                }
            } // End of image loop


            print( $d->saveHTML() );
            ?>
            <div class="cf"></div>
        </div>

        <?php
        //
        // Categories
        //

        $parentCats = array();

        getAllCategoryParents($this->link, $this->post->category, $parentCats);

        krsort($parentCats);


        echo '<br><br>';
        echo '<div class="category-box"><div>';

        $catLength = count($parentCats);
        $i = 0;
        foreach($parentCats as $category){

            echo '<a href="/blog/category/'.$category[0].'/'.urlify($category[1]).'">'.$category[1].'</a>';

            $i++; if($i < $catLength) echo ' &raquo; ';
        }

        echo '</div></div>';


        ?>

        <!-- COMMENTS BEGIN -->
        <h2 style="margin-top:30px;">Comments</h2>

        <!-- POST BOX -->
        <?php
        if($this->loggedIn)
        {
            include($this->siteDirectory . '/_inc/php/comment-box.php');
        }
        ?>
        <!-- POST BOX -->

        <?php
        {
            // Table
            $COMMENTS_TABLE = 'esco_blog_comments';
            $COMMENTS_FOR_ROW = 'post';
            $COMMENTS_FOR_VALUE = $this->post->id;

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
</div>



<div class="widget">
    <h3>Latest Blog Posts</h3>
    <?php
        {
            $select = "SELECT id,author,time,year,month,title FROM esco_blog_posts ORDER BY `id` DESC LIMIT 6";
            $result = mysqli_query($this->link, $select);

            $rows = array();

            while ($row = mysqli_fetch_row($result)) {
                ?>
                <?php
                $authorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$row[1]."' ") );
                ?>
                <a class="widget blog link" href="<?php echo '/blog/'.$row[3].'/'.$row[0].'/'.urlify($row[5]);?>">
                    <?php echo $row[5];?><br>
                    <small>By <?php echo $authorInfo[3].' '.$authorInfo[4];?> | <?php echo escoDate($row[2]);?></small>
                </a>
            <?php
            } // END OF POST LOOP

        }
    ?>
</div>

<?php }else{ ?>
<div class="widget">
    <h1>Post Not Found</h1>
</div>
<?php } ?>
<?php
    }
}

new customPage();
