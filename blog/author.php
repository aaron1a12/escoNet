<?php

class customPage extends page {
    public $title = 'Blog';
    
    public $userID;
    public $author;
    
    function init()
    {
        $this->userID = intval($_GET['usr-id']);
        
        $this->author = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$this->userID."' ") );
    }
    
    function content() {
?>


<div style="float:left; width:60%;">
    <div class="widget">
        <h3>Posts by <?php echo $this->author[3].' '.$this->author[4]; ?></h3>
<?php
	{                
        new paginator($this, "
        SELECT SQL_CALC_FOUND_ROWS id,author,time,year,month,title FROM esco_blog_posts WHERE `author`='".$this->userID."' ORDER BY `id` DESC
        ", 5, 9,
        function($row){
        ?>
            <?php
            $authorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$row[1]."' ") );
            ?>
            <a class="widget blog link" href="<?php echo '/blog/'.$row[3].'/'.$row[0].'/'.urlify($row[5]);?>">
                <?php echo $row[5];?><br>
                <small>By <?php echo $authorInfo[3].' '.$authorInfo[4];?> | <?php echo escoDate($row[2]);?></small>
            </a>
        <?php
        });
	}
?>
    </div>
</div>



<div id="homePage-rightCollumn" style="float:right; width:39%;">
    <?php include($this->siteDirectory.'/_inc/php/theme-sidebar.php'); ?>
</div>
<div class="cf"></div>

<?php
    }
}

new customPage();