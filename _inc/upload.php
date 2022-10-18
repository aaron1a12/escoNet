<?php

$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
var_dump($_FILES);
if ($_FILES)
{
     echo "File(s) received!";

     file_put_contents(
        $_FILES['upload-file']['name'][0],
        file_get_contents('php://input')
    );       
}
else
{
    echo "No file";
}
?>


<form action="" method="POST" enctype="multipart/form-data">
<input type="file" id="upload-file" name="upload-file[]" multiple="multiple" style="width:40%;float:left;background:#fff;padding:5px;height:25px;">
<input type="submit">
</form>