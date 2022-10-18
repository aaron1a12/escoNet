<?php

class customPage extends page {
    public $title = 'Manage blog posts';	
    public $private = true;

    function init()
    {
        
    }
    
    function head(){
?>
<script>
function confirm_delete_post(postID, postTitle)
{
	var bDeletePost = confirm('Are you sure you wish to delete the following post?\n\n\t"'+postTitle+'"');
	
	if(bDeletePost)
	{
		location.href='/user/blog/remove.php?id=' + postID;
	}
}
</script>
<?php
    }

    function content() {
?>
<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>
<div class="widget" style="position:relative;">
    <h1>My Blog Posts</h1>
    <button onclick="location.href='/user/blog/post.php';">Add New Post</button> <button onclick="location.href='/user/blog/categories.php';">Manage Categories</button>
    <br><br>
    
    <?php
        {                
            new paginator($this, "
            SELECT SQL_CALC_FOUND_ROWS * FROM esco_blog_posts WHERE author='".$this->escoID."' ORDER BY `id` DESC
            ", 10, 9,
            function($row){
            ?>
                <div class="widget link" style="display:block; text-decoration:none;">
                    <div style="float:right;">

                        <button onclick="confirm_delete_post(<?php echo intval($row[0]);?>, '<?php echo addslashes(htmlentities($row[5]));?>');">&times; Delete</button>
                        &nbsp;
                        <button onclick="location.href='/user/blog/post.php?id=<?php echo intval($row[0]);?>';">Edit</button>
                        <button onclick="location.href='<?php echo '/blog/'.$row[3].'/'.$row[0].'/'.urlify($row[5]);?>';">View</button>
                    </div>
                    <?php echo $row[5];?>
                    <br>
                    <span style="color:#bbb;"><small><?php echo date('F jS, Y  g:i A', $row[2]);?></small></span>
                </div>
            <?php
            });
        }
    ?>    

</div>
<?php
    }
}

new customPage();