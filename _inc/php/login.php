<?php

if(!isset($_ENV['alreadyChecked']))
{ $_ENV['alreadyChecked'] = '';   // We check only once, since this file gets requested twice: once for the title, and a second time for the content.

 // Send out the status code
 header("HTTP/1.1 401 Unauthorized");

    if($_POST)
    {
        if(isset($_POST['username'])&&$_POST['username']!='')
        {
            $_POST['username'] = strip_tags(filter_var( $_POST['username'], FILTER_SANITIZE_MAGIC_QUOTES));
            $_POST['password'] = strip_tags(filter_var( $_POST['password'], FILTER_SANITIZE_MAGIC_QUOTES));

            $hash = sha1( encrypt( sha1( md5($_POST['password']) ) ) );

            $query = 'SELECT * FROM esco_users WHERE username=\'' . $_POST['username'] . '\' AND password=\'' . $hash . '\'';

            $result = mysqli_query($this->link, $query);

            if(mysqli_num_rows($result)==0)
            {
                $_ENV['error'] = '<div style="padding:15px;"><b>Incorrect login details.</b></div>';
            }
            else
            {
                //$_SESSION['esco_user'] = $_POST['username'];
                //$_SESSION['esco_pass'] = encrypt( $hash );

                if(substr(strtolower($_SERVER["HTTP_HOST"]), 0, 4)=='www.')
                    $domain = substr($_SERVER["HTTP_HOST"], 4);
                else
                    $domain = $_SERVER["HTTP_HOST"];

                setcookie("esco_user", $_POST['username'], time()+60*60*24*30, '/', $domain);
                setcookie("esco_pass", encrypt( $hash ), time()+60*60*24*30, '/', $domain );



                $data =  mysqli_fetch_array(mysqli_query($this->link, 'SELECT * FROM esco_users WHERE username=\''.$_POST['username'].'\''));

                //die($data[3]);

                setcookie("esco_name", $data[3], time()+60*60*24*30, '/', $domain);
                //setcookie("esco_id", $data[0], time()+60*60*24*30, '/', $domain);


                header("Location: /user/account.php");
                die();
            }
        }
    }

}


switch($outputType)
{
    case 'title':
    echo 'Not Logged In';
    break;
    case 'content':
?>
<div class="widget" style="float:left; width:49%; height:250px; position:relative; ">
    <h1>Sign in to ESCO.NET</h1>
    <form action="" method="post">
        <table border="0" cellpadding="0" cellspacing="10" class="form" align="center">
            <tr>
                <td>Username:</td>
                <td><input name="username" value="<?php if(isset($_POST['username']))echo $_POST['username'];?>" type="text" style="width:225px;"></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input name="password" value="" type="password" style="width:225px;"></td>
            </tr>
            <tr>
                <td colspan="2" align="right"><button type="submit" style="position:absolute; right:0; bottom:0;">Login To Esco.net</button></td>
            </tr>
        </table>
    </form>
</div>
<div class="widget" style="float:right; position:relative;width:40%; height:250px;">
    <h1>Not Yet Registered?</h1>
    Strange... Did something happen to your account?

    <br>

    <button type="button" onclick="location.href='/register/';" style="position:absolute; right:0; bottom:0;">Register Here</button>
</div>
<div class="cf"></div>
<?php if(isset($_ENV['error'])){echo '<div class="error"><div>'.$_ENV['error'].'</div></div>'; }?>
<?php
    break;
}
