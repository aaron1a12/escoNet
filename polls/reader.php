<?php

class customPage extends page {
    public $title = 'Blog';
    
    public $poll;
    
    public $bPollExists;
    
    function init()
    {
        if(isset($_GET['id']) && intval($_GET['id'])!=0){
            $pollID = intval($_GET['id']);
            $this->poll = new stdClass;
            $this->poll->id = $pollID;
            
            $select = "SELECT * FROM esco_polls WHERE id='$pollID' ORDER BY `id` DESC";
            $result = mysqli_query($this->link, $select);
            $numrows = mysqli_num_rows($result);
            
            if($numrows==0){
                $this->bPollExists=false;
                header('HTTP/1.1 404 Not Found');
            }else{
                $this->bPollExists=true;
            }
            
            $row = mysqli_fetch_row($result);
            
            $this->poll->author = $row[1];
            $this->poll->time = $row[2];
            $this->poll->title = $row[3];
            $this->poll->choices = $row[4];

            
            $this->title = $this->poll->title . ' - Poll ';
        }
        else
        {
            die('Invalid Poll');
        }
        
        
        if($_POST && $this->bPollExists)
        {
            if(isset($_POST['choice']))
            {
                if($this->escoLastName != 'Escobar' && $this->escoLastName != 'Tubella')
                    die('Sorry. Only Escobars are allowed to vote');
                
                $pollVotesByUser = mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_poll_results WHERE poll='".$this->poll->id."' AND author='".$this->escoID."';"))[0];
                
                if($pollVotesByUser>0)
                    die('You already voted for this poll.');
                
                $choice = intval($_POST['choice']);
                $choicesAvailable = json_decode($this->poll->choices);
                
                if($choice >= $choicesAvailable || $choice < 0)
                    die('Invalid Choice');
                
                
                $author = $this->escoID;
                $insertQuery = "INSERT INTO esco_poll_results (poll, author, answer) VALUES ('$pollID', '$author', '$choice')";
                
                mysqli_query($this->link, $insertQuery);   
                
                header('Location: ' . $_SERVER["REDIRECT_URL"]);
                die();
            }
            elseif(isset($_POST['comment']) && $this->loggedIn == true)
            {
                $comment = filter_var( htmlentities($_POST['comment']), FILTER_SANITIZE_MAGIC_QUOTES );
                $author = $this->escoID;
                $time = time();
                
                if($comment!='')
                {
                    $query = "INSERT INTO esco_poll_comments (poll, author, time, comment) VALUES ('$pollID', '$author', '$time', '$comment')";
                    
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
<?php if($this->bPollExists){ ?>

<div style="float:left; width:60%;">
    <?php
    $authorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$this->poll->author."' ") );
    ?>
    <div class="widget">
        <h1 style="margin-bottom:0;"><?php echo $this->poll->title;?></h1>
        <ul class="news-headlines"><li>By <?php echo '<a href="/user/'.$this->poll->author.'/'.urlify($authorInfo[3].' '.$authorInfo[4]).'">'.$authorInfo[3].' '.$authorInfo[4].'</a>';?> <span style="color:#bbb;">| <small><?php echo escoDate($this->poll->time);?></small></span></li></ul>
        
        
            <?php
            {
                echo '<form action="" method="POST">';
                $pollChoices = json_decode($this->poll->choices);
                $pollChoicesCount = count($pollChoices);
                
                
                $pollTotalResults = mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_poll_results WHERE poll='".$this->poll->id."';"))[0];
                
                if(mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_poll_results WHERE poll='".$this->poll->id."' AND author='".$this->escoID."';"))[0]>0 || $this->loggedIn==false)
                {
                    //
                    // Show Results
                    //
                    
                    for($i=0; $i<$pollChoicesCount; $i++)
                    {
                        echo '<b>'.$pollChoices[$i].'</b>';
                        $number = mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_poll_results WHERE poll='".$this->poll->id."' AND answer='$i';"))[0];
                        $percentage = round( ($number/$pollTotalResults)*100, 2 );
                        
                        if($number==1) $votes = 'vote'; else $votes = 'votes';

                        echo '<div style="background-color:#ccc; width:100%; height:4px;">';
                        echo '<div style="width:'.$percentage.'%; background-color:#7722bb; height:4px;"></div>';
                        echo '</div>';


                        echo "<span style=\"font-size:8pt;\"> ($percentage%, $number $votes)</span>";
                        echo '<br>';
                    }                    
                }
                else
                {
                    //
                    // Show Form
                    //
                    
                    for($i=0; $i<$pollChoicesCount; $i++)
                    {
                        echo '<label style="font-size:10pt; cursor:pointer;"><input type="radio" name="choice" value="'.$i.'" style="height:auto;vertical-align:middle;cursor:pointer;"> &nbsp;'.$pollChoices[$i].'</label><br>';
                    }
                    echo '<br><button>Vote</button>';
                }

                echo '</form>';
            }
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
            $COMMENTS_TABLE = 'esco_poll_comments';
            $COMMENTS_FOR_ROW = 'poll';
            $COMMENTS_FOR_VALUE = $this->poll->id;            
            
            // How many items to show per page
            $RESULTS_PER_PAGE = 15;
            // How pages to show until the ellipsis (...)
            // Must be an odd number (not evenly divisable by 2. E.g., 1, 3, 5, 7, 9, 11, 13, etc)
            $MAX_PAGE_GROUP = 9;
            
            include($this->siteDirectory . '/_inc/php/comments.php');
        }
        ?>
        
        <?php
        {
            /*
            $commentSelect = "SELECT * FROM esco_blog_comments WHERE post='".$this->post->id."' ORDER BY `id` DESC";
            $commentResult = mysqli_query($this->link, $commentSelect);
            echo '<div class="comments" style="background-color:#f2f2f7; margin-bottom:20px;">';
            while ($commentRow = mysqli_fetch_assoc($commentResult)) {
            ?>
                <?php $commentAuthorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$commentRow['author']."' ") ); ?>
                <div style="padding:20px; border-bottom:2px solid #eee;">
                    <img src="http://media.esco.net/img/social/<?php echo $commentRow['author'];?>/profile_small.jpg" style="width:50px; float:left;">
                    <div style="float:left; margin-left:20px; width:450px;">
                        <b><a href="<?php echo '/user/'.$commentRow['author'].'/'.urlify( $commentAuthorInfo[3].'_'.$commentAuthorInfo[4] );?>"><?php echo $commentAuthorInfo[3].' '.$commentAuthorInfo[4];?></b></a>
                        
                        <span style="color:#bbb;">
                        |
                        <small><?php echo escoDate($commentRow['time']);?></small>
                        <?php
                        {
                            if($commentRow['author']==$this->escoID && time()-$commentRow['time'] < 604800){ // a week is the max
                                echo '| <small><a href="/api/remove-comment.php?from=0&id='.$commentRow['id'].'&return='.base64_encode($_SERVER['REDIRECT_URL']).'" style="color:#ffaaaa;">Remove</a></small>';
                            }
                        }
                        ?>
                        </span>
                        
                        <br>
                        <?php echo $commentRow['comment'];?>
                    </div>
                    <div class="cf"></div>
                </div>
            <?php
            }
            echo '</div>';
            */
        }
        ?>        

        <!-- COMMENTS END -->

    </div>
</div>



<div id="homePage-rightCollumn" style="float:right; width:39%;">
    <?php include($this->siteDirectory.'/_inc/php/theme-sidebar.php'); ?>
</div>
<div class="cf"></div>
<?php }else{ ?>
<div class="widget">
    <h1>Poll Not Found</h1>
</div>
<?php } ?>
<?php
    }
}

new customPage();