<?php
//if($_SERVER['REMOTE_ADDR']!='192.168.0.100') die('This section is being upgraded.<br><br>&mdash; Aaron');
?>
<div id="accountSettingsWidget" class="widget" style="position:relative;<?php if(isset($this->badge)){print('display:none;');}?>">
    <h2>User Account</h2>
	<div style="line-height:50px;text-align:justify;">
		<button onclick="location.href='/user/password-reset.php';">Reset Password</button>
		<button onclick="location.href='/user/profile.php';">Edit Profile</button>
		<button onclick="location.href='/user/profile-pictures.php';">Change Profile Pictures</button>
		<button onclick="location.href='/edit/';">Edit Home Sections</button>
		<button onclick="location.href='/user/polls/';">Polls</button>
		<button onclick="location.href='/user/blog/';">Manage Blog Posts</button>
		<button onclick="location.href='/user/mail-settings.php';">Email</button>
	</div>
    <a class="btn" href="/user/logout.php" style="position:absolute; right:0; top:0;">Log out</a> 
</div>