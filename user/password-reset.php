<?php

class customPage extends page {
    public $title = 'Password Reset';
	public $private = true;
    
    public $errors = array();
	public $messages = array();
    
    function init() {
        

        if($_POST){
            if( !isset($_POST['password']) && !isset($_POST['confirm']) ){
                array_push($this->errors, 'Bad request');
            }else{
			
				if(!isset($_POST['confirm'])) $_POST['confirm'] = '';
                
                $_POST['password'] = strip_tags(filter_var( $_POST['password'], FILTER_SANITIZE_MAGIC_QUOTES));
				$_POST['confirm'] = strip_tags(filter_var( $_POST['confirm'], FILTER_SANITIZE_MAGIC_QUOTES));
            
                if($_POST['password'] == '')
                    array_push($this->errors, 'A password is necessary to secure your account');
                
				if($_POST['confirm'] == '')
                    array_push($this->errors, 'Please enter your password twice to confirm it.');
				elseif($_POST['password']!=$_POST['confirm'])
					array_push($this->errors, 'Your passwords do not match.');
                
                if(count($this->errors)==0){
                    
                    //
                    // Update password
                    //
                                     
                    $password = sha1( encrypt( sha1( md5($_POST['password']) ) ) );
					
					
					
					$pwdQuery = 'UPDATE `esco`.`esco_users` SET `password`=\''.$password.'\' WHERE  `id`='.$this->escoID.';';
					mysqli_query($this->link, $pwdQuery);
					
					
					// See if user has an email account

					$result = mysqli_query($this->link, "SELECT 1 FROM esco_mail_virtual_users WHERE owner=" . $this->escoID);
					$rows = mysqli_num_rows($result);

					if($rows>0){
						$emQuery = 'UPDATE `esco`.`esco_mail_virtual_users` SET `password`=ENCRYPT(\''.$_POST['confirm'].'\', CONCAT(\'$6$\', SUBSTRING(SHA(RAND()), -16))) WHERE  `owner`='.$this->escoID.';';
						mysqli_query($this->link, $emQuery);
					}					
					
					array_push($this->messages, 'Success.');

                }
                
            }
        }
    }
    
    function content() {
?>
<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>
<div class="widget">
    <h1>Reset your password</h1>
    <form action="" method="post">
        <table border="0" cellpadding="0" cellspacing="10" class="form" align="center">
            <tr>
                <td>Your password:</td>
                <td><input name="password" type="password" style="width:225px;"></td>
            </tr>
			<tr>
                <td>Confirm:</td>
                <td><input name="confirm" type="password" style="width:225px;"></td>
            </tr>
            <tr>
                <td colspan="2" align="right"><button type="submit">Reset</button></td>
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
		
        if(count($this->messages)>0){
            echo '<div class="success"><ul>';
            foreach($this->messages as &$message){
                echo '<li>'.$message.'</li>';
            }
            echo '</ul></div>';
        }		
    ?>
</div>
<?php
    }
}

new customPage();