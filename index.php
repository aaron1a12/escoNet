<?php
class customPage extends page {
    public $title = 'Home';
    //public $pageIsFullscreen = true;


    function init() {
        //echo   strtotime('August 27, 12:00 AM');
        //die();
    }

    // TODO: Fix this!
    // NOTE: This is a temporary fix
    function content() {
?>
<div id="homePage-leftCollumn" style="float:left; width:60%;">
    <?php
		$iCoverId = 0;
        $bPostHasImage = false;
        $bPostImageExists = false;

        $cselect = "SELECT * FROM (SELECT id,author,time,year,month,title,cover FROM esco_blog_posts WHERE cover!=0 ORDER BY `id` DESC LIMIT 3) AS T1 LIMIT 1"; //ORDER BY rand()

        $cresult = mysqli_query($this->link, $cselect);
        $numrows = mysqli_num_rows($cresult);

        if($numrows>0){
            $bPostHasImage = true;

            $coverRow = mysqli_fetch_row($cresult);
			$iCoverId = $coverRow[0];

            $postLink = '/blog/'.$coverRow[3].'/'.$coverRow[0].'/'.urlify($coverRow[5]);

            $imgq = "SELECT author,format,name FROM esco_photos WHERE id='".$coverRow[6]."';";
            $imgr = mysqli_query($this->link, $imgq);

            $numrows = mysqli_num_rows( $imgr );

            $imgRow = mysqli_fetch_row($imgr);

            if($numrows!=0){
                $bPostImageExists = true;
            }

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

            if($bPostHasImage){
    ?>

    <div style="position:relative; height:300px; width:600px;">
        <div style="position:absolute; width:100%; bottom:0; left:0; background:url('http://www.esco.net/_inc/img/overlay.png');">
            <div style="margin:15px; color:#fff; font-size:25px;font-weight:300;">
                <a href="<?php echo $postLink;?>" style="color:#fff;"><?php echo $coverRow[5];?></a>
            </div>
        </div>
        <a href="<?php echo $postLink;?>">
            <?php if ($bPostImageExists) { ?>
            <img src="<?php echo 'http://media.esco.net/img/social/photos/'.$imgRow[0].'/'.$imgRow[2].'_c'.$format; ?>" style="width:600px; height:300px; background-color:#000;">
            <?php }else{ ?>
            <img src="http://media.esco.net/img/social/photos/c.jpg" style="width:600px; height:300px; background-color:#000;">
            <?php } ?>
        </a>
    </div>
    <?php
            }
        }
    ?>
    <div class="widget">
        <h3>Latest Blog Posts</h3>

        <?php
            {
                $select = "SELECT id,author,time,year,month,title FROM esco_blog_posts WHERE `id`!=$iCoverId ORDER BY `id` DESC LIMIT 3";
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
       <a class="widget blog link" href="/blog" style="float:right;padding:0 30px;"><div style="margin:5px;">More &raquo;</div></a>
       <div class="cf"></div>
<!--        <button style="float:right;padding:0 30px;">More</button>-->

    </div>



    <div class="widget nopadding">
        <div style="font-family:'Clavika'; color:#333388; border-bottom:4px solid #eeeef5;padding:8px;">News Headlines</div>
            <ul class="news-headlines">
                <?php
                {
                    $select = "SELECT * FROM esco_news ORDER BY `id` DESC";
                    $result = mysqli_query($this->link, $select);

                    $rows = array();

                    while ($row = mysqli_fetch_row($result)) {
                        echo '<li>'.$row[1].'</li>';
                    }
                }
                ?>
            </ul>
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
