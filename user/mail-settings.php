<?php
//if($_SERVER['REMOTE_ADDR']!='192.168.0.100') die('This section is being upgraded.<br><br>&mdash; Aaron');

class customPage extends page {
    public $title = 'MAIL @ escoNet';
	
	public $private = true;
	
	public $pageIsFullscreen = true; 
    
    public $errors = array();
	public $messages = array();
	
	public $userInfo;
	public $bHasEmail;
	public $userEmail;
	public $userEmail_old;
    
    function init() {
        
        //if($this->loggedIn)
         //   header('Location: /user/account.php');
        //die();
		
		// Check if the user has an EMAIL account
		
		$this->bHasEmail = false;
		$this->userEmail = '';

        $result = mysqli_query($this->link, "SELECT email FROM esco_mail_virtual_users WHERE owner=" . $this->escoID);
        $rows = mysqli_num_rows($result);

        if($rows>0) $this->bHasEmail = true;

        if($this->bHasEmail){
            $this->userEmail_old = mysqli_fetch_row($result)[0];
			$this->userEmail = explode('@', $this->userEmail_old );
			
			if(isset($_GET['enable'])) {
				header('Location: mail-settings.php');
				exit();
			}
			
        }elseif(isset($_GET['enable'])) {
	
		}

		
		
		$this->userInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$this->escoID."' ") ); 

        if($_POST){
		
			if(isset($_GET['enable']) || isset($_GET['disable'])){
		
				if(!isset($_POST['password'])){
					array_push($this->errors, 'Bad request');
				}else{      
					$_POST['password'] = strip_tags(filter_var( $_POST['password'], FILTER_SANITIZE_MAGIC_QUOTES));
				
					if($_POST['password'] == '') {
						array_push($this->errors, 'You have to confirm the password that you sign into escoNet with to continue.');
					}else{
					
						//
						// Check the password
						//
						
						$password = $_POST['password'];
						
						$hash = sha1( encrypt( sha1( md5($password) ) ) );
							
						$query = 'SELECT * FROM esco_users WHERE id=\'' . $this->escoID . '\' AND password=\'' . $hash . '\'';

						$result = mysqli_query($this->link, $query);

						if(mysqli_num_rows($result)==0)
						{
							array_push($this->errors, 'That is not the password you use for escoNet.');
						}				
					}
					if(count($this->errors)==0){
						if(isset($_GET['enable'])) {
						
							//
							// Register the email account
							//
							
							$username = strtolower(mysqli_fetch_row(mysqli_query($this->link, 'SELECT `username` FROM `esco_users` WHERE id=\''.$this->escoID.'\';'))[0]);
							$username = preg_replace("/[^.A-Za-z0-9_\(\)]/", "", $username);
							$escoID = $this->escoID;
							
							
							
							$insertQuery = "
							INSERT INTO `esco`.`esco_mail_virtual_users`
							(`owner`, `domain_id`, `password` , `email`)
							VALUES
							('$escoID', '1', ENCRYPT('$password', CONCAT('$6$', SUBSTRING(SHA(RAND()), -16))), '$username@esco.net');";
							
							//die($insertQuery);
							mysqli_query($this->link, $insertQuery);
	
							header('Location: mail-settings.php');
							exit();
		
						}elseif(isset($_GET['disable'])){


							
							if(!$this->bHasEmail){
								die('Error: You have no email account to disable.');
							}else{
								//
								// Delete the email account
								//	
								
								$oldEmailParts = explode('@', $this->userEmail_old);
								
								// Erase the old mailbox folder
								exec('sudo rm -r -f /var/mail/vhosts/'.$oldEmailParts[1].'/'.$oldEmailParts[0].'/');		
								
								$deleteQuery = 'DELETE FROM `esco_mail_virtual_users` WHERE  `owner`='.$this->escoID.';';
								mysqli_query($this->link, $deleteQuery);
								
								header('Location: mail-settings.php');
								exit();								
							}
							/*
							
							$username =  mysqli_fetch_row(mysqli_query($this->link, 'SELECT `username` FROM `esco_users` WHERE id=\''.$this->escoID.'\';'))[0];
							$escoID = $this->escoID;
							
							$insertQuery = "
							INSERT INTO `esco`.`esco_mail_virtual_users`
							(`owner`, `domain_id`, `password` , `email`)
							VALUES
							('$escoID', '1', ENCRYPT('$password', CONCAT('$6$', SUBSTRING(SHA(RAND()), -16))), '$username@esco.net');";
							
							mysqli_query($this->link, $insertQuery);
	
							header('Location: mail-settings.php');
							exit();	
							*/
						}
					} // END OF ZERO ERRORS
					
				
				}
				
			}else{
				$this->userEmail[0] = strtolower(strip_tags(filter_var( $_POST['username'], FILTER_SANITIZE_MAGIC_QUOTES)));
				$this->userEmail[1] = strtolower(strip_tags(filter_var( $_POST['domain'], FILTER_SANITIZE_MAGIC_QUOTES)));
				
				// Filter the username
				$this->userEmail[0] = preg_replace("/[^.A-Za-z0-9_\(\)]/", "", $this->userEmail[0]);
				
				$finalEmail = $this->userEmail[0].'@'.$this->userEmail[1];
				
				if($finalEmail != $this->userEmail_old) {
				
					// See if the domain is available for registration and find its ID
					$bDomainIsOkay = false;
					
					$selectDomains = mysqli_query($this->link, 'SELECT * FROM `esco_mail_virtual_domains`');
					while ($row = mysqli_fetch_row($selectDomains)) {
						$domain = $row[1];
						if($domain!='raspberrypi' && $domain!='raspberrypi.esco.net' && $domain!='localhost.esco.net') {
							if($domain==$this->userEmail[1]) {
								$bDomainIsOkay = true;
								$domainID = $row[0];
								break;
							}
						}
					}
					
					if($bDomainIsOkay) {
						$oldEmailParts = explode('@', $this->userEmail_old);
						
						// Erase the old mailbox folder
						exec('sudo rm -r -f /var/mail/vhosts/'.$oldEmailParts[1].'/'.$oldEmailParts[0].'/');				
						
						
						$updateQuery = 'UPDATE `esco_mail_virtual_users` SET `domain_id`='.$domainID.', `email`=\''.$finalEmail.'\' WHERE  `owner`='.$this->escoID.';';
						mysqli_query($this->link, $updateQuery);
						
						array_push($this->messages, 'Email updated.');
					}
				

				}else{
					array_push($this->errors, 'Nothing has changed.');
				}
			}
        } // END IF POST
    }
	
	function head() { ?>
	<script>
	function updateEmailPreview() {
		var emailPreview = document.getElementById('emailPreview');
		var mailform_username = document.getElementById('mailform_username');
		var mailform_domain = document.getElementById('mailform_domain');
		
		emailPreview.innerHTML = mailform_username.value + '@' + mailform_domain.value;
	}
	
	$(function() {
		var mailform_username = document.getElementById('mailform_username');
		var mailform_domain = document.getElementById('mailform_domain');
		
		$(mailform_username).bind("change", updateEmailPreview);
		$(mailform_username).bind("keyup", updateEmailPreview);
		$(mailform_username).bind("keypress", updateEmailPreview);
		
		$(mailform_domain).bind("change", updateEmailPreview);	
		
		updateEmailPreview();
	});
	</script>
	<?php }
    
    function content() {
?>
<div class="siteWidth center">
<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>
</div>
<div class="siteWidth center" style="position:relative;">

	<?php if(!isset($_GET['enable'])) { ?>
	<div style="position:absolute;height:100%;width:520px;background:url(/_inc/img/mail-side.jpg) no-repeat #742ab5; box-shadow: 0 -4px 0 rgba(0,0,0,.2) inset">
	</div>
	<?php } ?>
	
	<?php
	if($this->bHasEmail && !isset($_GET['disable'])){
	?>

	<div class="widget" style="float:right;height:100%;width: 440px; min-height:380px;  margin-bottom:0;">
		<h1>Mail Settings</h1>
		<form id="mailSettingsForm" action="" method="post">
			<table border="0" cellpadding="0" cellspacing="20" class="form" align="center">
				<tr>
					<td style="text-align:right;">Email Name:</td>
					<td><input id="mailform_username" name="username" value="<?php print($this->userEmail[0]); ?>" type="text" style="width:225px;"></td>
				</tr>
				<tr>
					<td style="text-align:right;">Domain:</td>
					<td>
						<select id="mailform_domain" name="domain" style="width:225px;">
							<?php
							$selectDomains = mysqli_query($this->link, 'SELECT name FROM `esco_mail_virtual_domains`');
							while ($domain = mysqli_fetch_row($selectDomains)[0]) {
								if($domain!='raspberrypi' && $domain!='raspberrypi.esco.net' && $domain!='localhost.esco.net')
								{
									if($domain==$this->userEmail[1])
										echo '<option selected="selected">'.$domain.'</option>';
									else
										echo '<option>'.$domain.'</option>';
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;">Preview:</td>
					<td><span id="emailPreview"></span></td>
				</tr>
			</table>
			
			
			<!-- <p>TIP: Having trouble connecting? Try reseting your main account password to synchronize the systems.</p> -->
			
			<div style="position:absolute;bottom:20px;right:0">
				<a class="btn" href="#" onclick="document.getElementById('mailSettingsForm').submit()">Save Changes</a>
				<a class="btn" href="mail-settings.php?disable">Disable Mail</a>
			</div>
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
	}elseif(!isset($_GET['enable']) && !isset($_GET['disable'])) { //}elseif($this->bHasEmail==false){
	?>
	<div class="widget" style="float:right;height:100%;width: 440px; min-height:380px;  margin-bottom:0;">
		<h1>Mail Settings</h1>
		
		<p>It appears your account is not yet enabled for emails.</p>
		<p>You are just seconds away from enjoying free mail @ escoNet with minimal spam!</p>
		
		<p style="text-align:center;padding-top:40px;"><a href="mail-settings.php?enable" class="btn">Enable Mail</a></p>
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
	<?php }elseif(isset($_GET['enable'])) { ?>
	<div class="widget" style="height:100%;margin-bottom:0;">
		<h2>Please confirm your password to continue</h2>
		
		<form method="post">
		<input type="password" name="password" style="width:250px;">
		
		
		<button>Submit</button>
		</form>
		<br>
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
	<?php }elseif(isset($_GET['disable'])) { ?>
	<div class="widget" style="height:100%;margin-bottom:0;">
		<h2>Please confirm your password to disable email</h2>
		
		<p>Note that all your emails will get deleted.</p>
		
		<form method="post">
		<input type="password" name="password" style="width:250px;">
		
		
		<button>Submit</button>
		</form>
		<br>
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
	<?php } ?>	

	<div class="cf"></div>
</div>
<?php
    }
}

new customPage();