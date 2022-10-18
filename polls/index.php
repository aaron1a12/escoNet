<?php

class customPage extends page {
    public $title = 'Blog';
    
    function init()
    {      
    }
    
    function content() {
?>


<div style="float:left; width:60%;">
    <div class="widget">
        <h3>All Polls</h3>
<?php
	{
		$select = "SELECT * FROM esco_polls ORDER BY `id` DESC";
		$result = mysqli_query($this->link, $select);

		$rows = array();

		while ($row = mysqli_fetch_row($result)) {
            ?>
            <?php
            $authorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$row[1]."' ") );
            ?>
            <a class="widget blog link" href="<?php echo '/polls/'.$row[0].'/'.urlify($row[3]);?>">
                <?php echo $row[3];?><br>
                <small>By <?php echo $authorInfo[3].' '.$authorInfo[4];?> | <?php echo escoDate($row[2]);?></small>
            </a>
        <?php            
        } // END OF POST LOOP
		
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