<?php

class customPage extends page {
    public $title = 'Editing Image Details';
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
            $this->img->unlisted = intval($imgRow[12]);

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

            if($_POST)
            {
                if($this->img->author==$this->escoID)
                {
                    $title = $_POST['title'];
                    $description = $_POST['description'];
                    $keywords = $_POST['keywords'];

                    $title = filter_var( htmlentities($title, FILTER_SANITIZE_MAGIC_QUOTES ));
                    $description = filter_var( htmlentities($description, FILTER_SANITIZE_MAGIC_QUOTES ));
                    $keywords = filter_var( htmlentities($keywords, FILTER_SANITIZE_MAGIC_QUOTES ));
                    $unlisted = intval($_POST['unlisted']);

                    //
                    // Clean keywords
                    //

                    $keywords = explode(',', $keywords);
                    $keywords = implode(' ', $keywords);
                    $keywords = explode(' ', $keywords);

                    $i=0;
                    foreach($keywords as &$keyword){
                        if($keyword=='')
                            unset($keywords[$i]);
                        $i++;
                    }

                    $keywords = implode(' ', $keywords);

                    $query = "UPDATE esco_photos SET `keywords`='$keywords',`title`='$title',`description`='$description',`unlisted`=$unlisted WHERE id='".$this->img->id."';";
                    mysqli_query($this->link, $query);

                    $secondQuery = "UPDATE esco_photo_album_assoc SET `photo_unlisted`=$unlisted WHERE `photo`=". $this->img->id;
                    mysqli_query($this->link, $secondQuery);


                    $goto = '/user/'.$this->img->author.'/'.urlify($this->img->user[3].'_'.$this->img->user[4]).'/photos/' . $this->img->id . '/' . urlify( $title );

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
    <h1>Editing Image Details</h1>


    <form action="" method="POST">
        <table class="table">
            <tr>
                <td>Title</td>
                <td style="width:500px;"><input style="width:100%;" type="text" name="title" value="<?php echo $this->img->photoTitle; ?>" maxlength="100"></td>
            </tr>

            <tr>
                <td>Description</td>
                <td><textarea style="width:100%;height:100px;" name="description"><?php echo $this->img->description; ?></textarea></td>
            </tr>

            <tr>
                <td>Keywords</td>
                <td><input style="width:100%;" type="text" name="keywords" value="<?php echo $this->img->keywords; ?>"></td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td></td>
            </tr>

            <tr>
                <td style="vertical-align:top;">Unlisted</td>
                <td><b id="unlisted-display"><?php if($this->img->unlisted==0) echo 'No'; else echo 'Yes'; ?></b> &mdash; <a href="javascript:void(0);" onclick="toggleUnlisted();">Change</a></td>
            </tr>
        </table>

        <input id="unlisted-input" type="hidden" name="unlisted" value="<?php echo $this->img->unlisted; ?>">
        <script>
            function toggleUnlisted(){
                var unlistedInput = document.getElementById('unlisted-input');
                var unlistedDisplay = document.getElementById('unlisted-display');

                if(unlistedInput.value=='1'){
                    unlistedInput.value = '0';
                    unlistedDisplay.innerHTML = 'No';
                }
                else{
                    unlistedInput.value = '1';
                    unlistedDisplay.innerHTML = 'Yes';
                }
            }
        </script>

        <br><br><br><br>


        <button type="submit">Save</button>
        <button type="button" onclick="location.href='<?php echo '/user/'.$this->img->author.'/'.urlify($this->img->user[3].'_'.$this->img->user[4]).'/photos/'.$this->img->id.'/'.urlify($this->img->photoTitle); ?>';">Cancel</button>
    </form>

</div>
<?php
}
}

new customPage();
