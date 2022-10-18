<?php

class customPage extends page {
    public $title = 'Edit Home Sections';	
    public $private = true;

    function init()
    {
        
    }
    
    function head(){
?>
<script>
</script>
<?php
    }

    function content() {
?>
<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>
<div class="widget" style="position:relative;">
    <h1>My Polls</h1>
    <button onclick="location.href='/user/polls/new.php';">Add New Poll</button>
    <br><br>
    
    <?php
	{
		$select = "SELECT * FROM esco_polls WHERE author='".$this->escoID."' ORDER BY `id` DESC";
		$result = mysqli_query($this->link, $select);

		$rows = array();

		while ($row = mysqli_fetch_row($result)) {
            ?>
<div class="widget link" style="display:block; text-decoration:none;">
    <div style="float:right;">
        <button onclick="location.href='/user/polls/remove.php?id=<?php echo intval($row[0]);?>';">Remove</button>
    </div>
    <?php echo $row[3];?>
    <br>
    <span style="color:#bbb;"><small><?php echo date('F jS, Y  g:i A', $row[2]);?></small></span>
</div>
        <?php
        }
		
	}
?>
</div>
<?php
    }
}

new customPage();