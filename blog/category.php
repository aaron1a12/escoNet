<?php

class customPage extends page {
    public $title = 'Blog';
    
    public $categoryName;
    public $categoryId;
    
    
    function init()
    {
        $this->categoryId = intval($_GET['id']);
        $this->categoryName = mysqli_fetch_row(mysqli_query($this->link, "SELECT name FROM esco_blog_categories  WHERE id='".$this->categoryId."';"))[0];
    }
    
    function content() {
?>
<?php
{
    //
    // Cat all category children
    //

    $this->categoryId = intval($_GET['id']);
    $children = array($this->categoryId);
    getAllCategoryChildren($this->link, $this->categoryId, $children);

    $cond = '';
    foreach($children as $cat){
        $cond .= "`category`=$cat OR ";
    }
    $cond = substr($cond, 0, strlen($cond)-4);
}
?>
<div style="float:left; width:70%;">
    <div class="widget">
        <?php
        {

            //----------------------------------------------------------

            $bPostHasImage = false;
            $bPostImageExists = false;


            $cselect = "SELECT * FROM (SELECT id,author,time,year,month,title,cover FROM esco_blog_posts WHERE cover!=0 AND ($cond) ORDER BY `id` DESC LIMIT 1) AS T1 LIMIT 1"; //ORDER BY rand()

            $cresult = mysqli_query($this->link, $cselect);
            $numrows = mysqli_num_rows($cresult);

            if($numrows>0){
                $bPostHasImage = true;

                $coverRow = mysqli_fetch_row($cresult);

                $postLink = '/blog/'.$coverRow[3].'/'.$coverRow[0].'/'.urlify($coverRow[5]);

                $imgq = "SELECT author,format,name FROM esco_photos WHERE id='".$coverRow[6]."';";
                $imgr = mysqli_query($this->link, $imgq);

                $numrows = mysqli_num_rows( $imgr );

                $imgRow = mysqli_fetch_row($imgr);

                if($numrows!=0)
                    $bPostImageExists = true;


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

            }





                if(true){
                ?>
                <div style="position:relative; height:150px; overflow:hidden; width:100%;">
                    <div style="position:absolute; width:100%; height:100%; top:0; left:0; z-index:99; background:url('http://www.esco.net/_inc/img/overlay.png');">
                        <table style="width:100%; height:100%; color:#fff;  text-align:center;  font-size:50px; font-weight:100;">
                            <tr>
                                <td style="vertical-align:middle; text-align:center;"><?php echo $this->categoryName;?></td>
                            </tr>
                        </table>
                    </div>
                    <a href="<?php echo $postLink;?>">
                        <?php if ($bPostImageExists) { ?>
                        <img src="<?php echo 'http://media.esco.net/img/social/photos/'.$imgRow[0].'/'.$imgRow[2].'_c'.$format; ?>" style=" filter: blur(4px);  width:100%; margin-top:-10%; background-color:#000;">
                        <?php } ?>
                    </a>
                </div>
                <?php
                }        


            echo '<br>';

            new paginator($this, "
            SELECT SQL_CALC_FOUND_ROWS * FROM  esco_blog_posts WHERE $cond ORDER BY `id` DESC
            ", 10, 9,
            function($row){
            ?>
                <?php
                $authorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$row[1]."' ") );

                if($row[9]!=$this->categoryId)
                    $urlSuffix = '?in-cat='.$this->categoryId;
                else
                    $urlSuffix = '';

                $postLink = '/blog/'.$row[3].'/'.$row[0].'/'.urlify($row[5]).$urlSuffix;
                
                if (intval($row[7])!=0)
                    $bHasImage = true;
                else
                    $bHasImage = false;
                ?>
        
                <div class="widget blog link" style="cursor:pointer;" onclick="location.href='<?php echo $postLink;?>';">
                    <?php if($bHasImage){
                        $imgq = "SELECT author,format,name FROM esco_photos WHERE id='".$row[7]."';";
                        $imgr = mysqli_query($this->link, $imgq);
                        $numrows = mysqli_num_rows( $imgr );
                        $imgRow = mysqli_fetch_row($imgr);

                        if($numrows!=0)
                            $bPostImageExists = true;
                        else
                            $bPostImageExists = false;

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
                    
                        if($bPostImageExists)
                            echo '<img src="http://media.esco.net/img/social/photos/'.$imgRow[0].'/'.$imgRow[2].'_c'.$format.'" style="width:200px; height:100px; float:left; background-color:#000;">';
                        else
                            echo '<img src="http://media.esco.net/img/social/photos/c.jpg" style="width:200px; height:100px; float:left; background-color:#000;">';
                    
                        $divWidth = '60%';
                        $maxText = 100;
                    }
                    else
                    {
                        $divWidth = '90%';
                        $maxText = 180;
                    }
                    ?>
                    
                    <div style="float:left; margin-left:20px; width:<?php echo $divWidth;?>;">
                        <h3 style="font-weight:300;margin-bottom:0;"><a href="<?php echo $postLink;?>"><?php echo $row[5];?></a></h3>
                        <small>By <?php echo $authorInfo[3].' '.$authorInfo[4];?> | <?php echo escoDate($row[2]);?></small>
                        <div style="font-size:10pt;">
                            <?php 
                            {
                                $textDocument = new DOMDocument();
                                $textDocument->loadHTML('<?xml encoding="UTF-8">'.$row[6], LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);

                                //$textDocument->removeChild( $textDocument->childNodes->item(0) );

                                print(trimText($textDocument->textContent, $maxText));
                            }
                            ?>
                        </div>
                    </div>
                    <div class="cf"></div>
                </div>
            <?php
            });        

        }      
        ?>
    </div>
</div>


<div style="float:right; width:29%;">
    <div class="widget">
        <h2>Categories</h2>
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
            
            $div = $d->createElement('div');
            $div->setAttribute('id', 'cat_' . $id );
            $div->setAttribute('class', 'category');
            
            $divLink = $d->createElement('a');
            $divLink->setAttribute('href', '/blog/category/'.$id.'/'.urlify($name));
            $divLink->setAttribute('style', 'background-color:transparent;font-weight:bold;');
            $divLink->nodeValue = $name;

            $div->appendChild( $divLink );

            
            if($parent){
                $div->setAttribute('style', 'margin-left:7px;');
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
</div>

<div class="cf"></div>
<?php
    }
}

new customPage();