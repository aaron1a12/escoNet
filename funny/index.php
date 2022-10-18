<?php

/*
function isRowInArray($link, )
{
    SELECT name FROM esco_funny_pic_list ORDER BY rand() LIMIT 1;
}
*/

class customPage extends page {
    public $title = 'Funny Pic of the Day';
    public $funnyPic;

    function init() {
        if($_POST)
        {
            if(isset($_POST['comment']) && $this->loggedIn == true)
            {
                $comment = filter_var( htmlentities($_POST['comment']), FILTER_SANITIZE_MAGIC_QUOTES );
                $author = $this->escoID;
                $time = time();
                
                if($comment!='')
                {           
                    // Get the current pic id
                    $currentFunnyPicID = mysqli_fetch_row( mysqli_query($this->link, "SELECT data FROM esco_funny_pic_recent WHERE id='2' ") )[0];
                    
                    $query = "INSERT INTO esco_funny_pic_comments (pic, author, time, comment) VALUES ('$currentFunnyPicID', '$author', '$time', '$comment')";
                    mysqli_query($this->link, $query);
                    
                    // Register Activity
                    logAction( $this, ACTION_COMMENT );
                    
                    header('Location: /funny/');
                    die();
                }
                
            }
        }
    }
    
    function content() {
?>
<div class="widget">
    <div style="position:relative;">
        <h1>Funny Pic of the Day</h1>
        
<!--        <a class="btn" href="/funny/submit.php" style="position:absolute;top:0; right:0;">Submit your funny pic</a>-->
    </div>
    
    <div style="background-color:#000; text-align:center;font-size:0;">
        <img src="http://www.esco.net/funny/pic_of_the_day.jpg?rd=<?php echo date('z');?>">
    </div>
    
    <br>
    
    <h2>Comments</h2>
    
    <?php
    {
        
        if($this->loggedIn)
        {
            include($this->siteDirectory . '/_inc/php/comment-box.php');
        }

        // Get the current pic id
        $currentFunnyPicID = mysqli_fetch_row( mysqli_query($this->link, "SELECT data FROM esco_funny_pic_recent WHERE id='2' ") )[0];

        
        {
            // Table
            $COMMENTS_TABLE = 'esco_funny_pic_comments';
            $COMMENTS_FOR_ROW = 'pic';
            $COMMENTS_FOR_VALUE = $currentFunnyPicID;
            
            // How many items to show per page
            $RESULTS_PER_PAGE = 15;
            // How pages to show until the ellipsis (...)
            // Must be an odd number (not evenly divisable by 2. E.g., 1, 3, 5, 7, 9, 11, 13, etc)
            $MAX_PAGE_GROUP = 9;
            
            include($this->siteDirectory . '/_inc/php/comments.php');
        }
        
        /*
        // Get the comments from the pic
        $select = "SELECT * FROM esco_funny_pic_comments WHERE pic='".$currentFunnyPicID."' ORDER BY `id` DESC";
        $result = mysqli_query($this->link, $select);

        echo '<div class="comments" style="background-color:#f2f2f7; margin-bottom:20px;">';
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <?php
            {
                $authorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$row['author']."' ") );
                
            }
            ?>
            <div style="padding:20px; border-bottom:2px solid #eee;">
                <img src="http://media.esco.net/img/social/<?php echo $row['author'];?>/profile_small.jpg" style="width:50px; float:left;">
                <div style="float:left; margin-left:20px; width:600px;">
                    <b>
                        <a href="<?php echo '/user/'.$row['author'].'/'.urlify( $authorInfo[3].'_'.$authorInfo[4] );?>"><?php echo $authorInfo[3].' '.$authorInfo[4];?></b></a>
                        <span style="color:#bbb;">
                        |
                        <small><?php echo escoDate($row['time']);?></small>
                        <?php
                        {
                            if($row['author']==$this->escoID && time()-$row['time'] < 604800){ // a week is the max
                                echo '| <small><a href="/api/remove-comment.php?from=2&id='.$row['id'].'&return='.base64_encode($_SERVER['REDIRECT_URL']).'" style="color:#ffaaaa;">Remove</a></small>';
                            }
                        }
                        ?>
                        </span>
                    <br>
                    <?php echo $row['comment'];?>
                </div>
                <div class="cf"></div>
            </div>
        <?php
        }
        echo '</div>';
             */   
    }
    ?>
</div>
<?php
    }
}

new customPage();