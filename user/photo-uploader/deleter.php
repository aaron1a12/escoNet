<?php

class customPage extends page {
    public $title = 'Deleting Image';	
    public $private = true;

    public $img;

    function init()
    {
        //function

        $this->img = new stdClass;
        $this->img->id = intval(base64_decode($_GET['image']));

        $imgq = "SELECT * FROM esco_photos WHERE id='".$this->img->id."';";
        $imgr = mysqli_query($this->link, $imgq);

        $numrows = mysqli_num_rows( $imgr );

        $imgRow = mysqli_fetch_row($imgr);

        if($numrows==0){
            $this->img->exists = false;
        }else{

            $this->img->author = $imgRow[1];
            $this->img->time = $imgRow[2];
            $this->img->year = $imgRow[3];
            $this->img->month = $imgRow[4];
            $this->img->format = $imgRow[5];
            $this->img->views = intval($imgRow[6]);
            $this->img->album = $imgRow[7];
            $this->img->keywords = $imgRow[8];
            $this->img->title = $imgRow[9];
            $this->img->description = $imgRow[10];
            $this->img->name = $imgRow[11];

            if($this->img->title!=''){ $this->img->photoTitle = $this->img->title; }else{
                $this->img->photoTitle = $this->img->name;
                $this->img->photoTitle = explode('_', $this->img->photoTitle);

                array_pop($this->img->photoTitle);
                array_pop($this->img->photoTitle);

                $this->img->photoTitle = implode('_', $this->img->photoTitle);
            }

            switch($this->img->format){
                case 0:
                    $this->img->format = '.jpg';
                    break;
                case 1:
                    $this->img->format = '.png';
                    break;
                case 2:
                    $this->img->format = '.gif';
                    break;
            }



            $this->img->fullURL = 'http://media.esco.net/img/social/photos/'.$this->img->author . '/'. $this->img->name . '_t' . $this->img->format;

            $this->img->user = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$this->img->author."' ") );

            if(isset($_GET['confirm'])&&intval($_GET['confirm'])==1)
            {
                if($this->img->author==$this->escoID)
                {
                    $queries = array();

                    array_push($queries, "DELETE FROM esco_photos WHERE id='".$this->img->id."';" );
                    array_push($queries, "DELETE FROM esco_photo_comments WHERE photo='".$this->img->id."';" );
                    array_push($queries, "DELETE FROM esco_photo_notes WHERE photo='".$this->img->id."';" );
					array_push($queries, "DELETE FROM esco_photo_album_assoc WHERE photo='".$this->img->id."';" );

                    if($this->img->format == '.jpg')
                        array_push($queries, "DELETE FROM esco_photo_exif WHERE photo='".$this->img->id."';" );

                    foreach($queries as $query){
                        mysqli_query($this->link, $query);
                    }


                    $photoBase = dirname($this->siteDirectory).'/media.esco.net/_httpdocs/img/social/'.$this->img->author.'/photos/';

                    if( file_exists($photoBase.$this->img->name.'_o'.$this->img->format) )
                        unlink( $photoBase.$this->img->name.'_o'.$this->img->format );

                    if( file_exists($photoBase.$this->img->name.'_l'.$this->img->format) )
                        unlink( $photoBase.$this->img->name.'_l'.$this->img->format );

                    if( file_exists($photoBase.$this->img->name.'_c'.$this->img->format) )
                        unlink( $photoBase.$this->img->name.'_c'.$this->img->format );

                    if( file_exists($photoBase.$this->img->name.'_t'.$this->img->format) )
                        unlink( $photoBase.$this->img->name.'_t'.$this->img->format );

                    if( file_exists($photoBase.$this->img->name.'_s'.$this->img->format) )
                        unlink( $photoBase.$this->img->name.'_s'.$this->img->format );

                    transfer($this, $this->escoID, 0, 5);

                    $goto = '/user/'.$this->img->author.'/'.urlify($this->img->user[3].'_'.$this->img->user[4]).'/photos/';

                    header("Location: $goto");
                    die();
                }
                else
                {
                    header("HTTP/1.1 401 Unauthorized");
                    die('<h1>401 Unauthorized<h1>You do not own this photo.');
                }
            }


        }
    }

function content()
{
?>
<?php

?>
<div class="widget">
    <img style="float:right; margin:10px;" src="<?php echo $this->img->fullURL;?>">
    <h1>Please confirm...</h1>
    You are about to <u>erase</u> the image "<?php echo $this->img->photoTitle;?>" from your account. This is PERMANENT.
    <br><br>
    Continue?<br><br><br><br>

    <a class="btn" href="<?php echo '/user/'.$this->img->author.'/'.urlify($this->img->user[3].'_'.$this->img->user[4]).'/photos/'.$this->img->id.'/'.urlify($this->img->photoTitle); ?>">NO!!! Go back!</a>
    <a class="btn" href="<?php echo $_SERVER["REQUEST_URI"].'&confirm=1';?>">ERASE IT!</a>
    <br><br>

</div>
<?php
}
}

new customPage();
