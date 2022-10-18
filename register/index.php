<?php

class customPage extends page {
    public $title = 'Links';
    
    public $errors = array();
    
    function init() {
        
        //if($this->loggedIn)
         //   header('Location: /user/account.php');
        //die();

        if($_POST){
            if(!isset($_POST['name']) || !isset($_POST['lastname']) || !isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['confirm'])){
                array_push($this->errors, 'Bad request');
            }else{
                
                $_POST['name'] = strip_tags(filter_var( $_POST['name'], FILTER_SANITIZE_MAGIC_QUOTES));
                $_POST['lastname'] = strip_tags(filter_var( $_POST['lastname'], FILTER_SANITIZE_MAGIC_QUOTES));
                $_POST['username'] = strip_tags(filter_var( $_POST['username'], FILTER_SANITIZE_MAGIC_QUOTES));
                $_POST['password'] = strip_tags(filter_var( $_POST['password'], FILTER_SANITIZE_MAGIC_QUOTES));
                $_POST['confirm'] = strip_tags(filter_var( $_POST['confirm'], FILTER_SANITIZE_MAGIC_QUOTES));
            
                if($_POST['name'] == '')
                    array_push($this->errors, 'You need a name');
                if($_POST['lastname'] == '')
                    array_push($this->errors, 'No last name?');
                if($_POST['username'] == '')
                    array_push($this->errors, 'Please enter the username you want to login with');
                if($_POST['password'] == '')
                    array_push($this->errors, 'A password is necessary to secure your account');
                
                if( $_POST['confirm'] != $_POST['password'] )
                    array_push($this->errors, 'The two passwords do not match. Try remembering your new password better.');

                //if($_POST['lastname'] != 'Escobar')
                //    array_push($this->errors, 'Last name hacking?');
                
                
                //
                // Check to see if the user already exists
                //
                
                if($_POST['username'] != '') {
                    
                    
                    $result = mysqli_query( $this->link, 'SELECT id FROM esco_users WHERE username=\''.$_POST['username'].'\'' );
                    $count = mysqli_num_rows($result);
                    if($count>0)
                        array_push($this->errors, 'Username is already registered');
                }
                
                if(count($this->errors)==0){
                    
                    //
                    // Register the user
                    //
                    
                    $username = $_POST['username'];                    
                    $password = sha1( encrypt( sha1( md5($_POST['password']) ) ) );
                    $name = $_POST['name'];
                    $lastname = $_POST['lastname'];
					$time = time();
                    $query = "INSERT INTO esco_users (username, password, name, lastname, time) VALUES ('$username','$password', '$name', '$lastname', '$time') "; 

                    //die($query);
                    
                    // Execute
                    mysqli_query( $this->link, $query );

                    // Quickly get the last generate user id
                    $newID = mysqli_insert_id($this->link);
                    
                    // Create a new profile
                    
                    $query = "INSERT INTO esco_user_profiles (user, updated, banner, birth, tagline) VALUES ('$newID','0', '', '1800-00-00', 'Welcome to my escoNet Profile!') "; 
                    mysqli_query( $this->link, $query );

                    
                   // die($query);
                    
                    // Create the default profile pic
                                  
                    
                    //$baseProfileFolder = '/home/pi/www/media.esco.net/_httpdocs/img/social/';
                    $baseProfileFolder = dirname($this->siteDirectory).'/htdocs_media_esconet/img/social/';
                    $userProfileFolder = $baseProfileFolder . $newID . '/';
                    
                    mkdir( $userProfileFolder );
                    
                    copy( $baseProfileFolder . 'default-profile-large.jpg', $userProfileFolder . 'profile_large.jpg');
                    copy( $baseProfileFolder . 'default-profile-small.jpg', $userProfileFolder . 'profile_small.jpg');
                    
                    // Login
                    header('Location: /user/');
                    die($query);
                }
                
            }
        }
    }
    
    function content() {
?>
<div class="widget">
    <h1>Register a new ESCO.NET account</h1>
    <form action="" method="post">
        <table border="0" cellpadding="0" cellspacing="10" class="form" align="center">
            <tr>
                <td>First Name:</td>
                <td><input name="name" value="<?php if(isset($_POST['name']))echo $_POST['name'];?>" type="text" style="width:225px;"></td>
            </tr>
            <tr>
                <td>Last Name:</td>
                <td><input name="lastname" type="text" value="Escobar" style="width:225px;"></td>
            </tr>
            <tr>
                <td>Username:</td>
                <td><input name="username" value="<?php if(isset($_POST['username']))echo $_POST['username'];?>" type="text" style="width:225px;"></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input name="password" type="password" style="width:225px;"></td>
            </tr>
            <tr>
                <td>Password (Confirm):</td>
                <td><input name="confirm" type="password" style="width:225px;"></td>
            </tr>
            <tr>
                <td colspan="2" align="right"><button type="submit">Create a new account</button></td>
            </tr>
        </table>
    </form>
    <?php
        if(count($this->errors)>0){
            echo '<div class="error"><ul>';
            foreach($this->errors as &$error){
                echo '<li>'.$error.'</li>';
            }
            echo '</ul></div>';
        }
    ?>
</div>
<?php
    }
}

new customPage();