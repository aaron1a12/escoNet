<?php
//
// Command-line script to resize an image
//

if(!isset($argv))
    die('This script is meant for command line use only.');

// Load Settings
define( 'SITE_BASE' , dirname(dirname(dirname(__FILE__))) );
require_once( SITE_BASE . '/php/sys/settings.php');

// Connect the database
$link = mysqli_connect($_ENV['db_server'], $_ENV['db_user'], $_ENV['db_pass']);
if(!$link) die('Failed to connect to the database. Error: '.mysqli_connect_errno());
if(!mysqli_select_db( $link, $_ENV['db_name'] )) die('Database does not exist.');

// Headers
$headers = 'From: escoNet <esconet@esco.net>' . "\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-Type: multipart/related;'."\r\n\t".'type="multipart/alternative";'."\r\n\t".'boundary="===NEXT_SECTION==="' . "\r\n";
$headers .= 'This is a multi-part message in MIME format.';
//$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";

//
// BEGIN MESSAGE
//

$message = '--===NEXT_SECTION===
Content-Type: multipart/alternative; boundary="===NEXT_SECTION1==="


--===NEXT_SECTION1===
Content-Type: text/plain;
	charset="iso-8859-1"
Content-Transfer-Encoding: quoted-printable


--===NEXT_SECTION1===
Content-Type: text/html;
	charset="iso-8859-1"
Content-Transfer-Encoding: quoted-printable

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<style>
body {margin:0;padding:0;font-family:Verdana;background-color:#FFFFF;}
#mainBody {margin:50px;}
</style>
</head>
<body>

<TABLE width=3D"100%" cellspacing=20 cellpadding=20 border=3D0 bgColor=3D#333388>
	<TBODY>
		<TR>
			<TD width=3D1050>
				<div style=3D"margin:20px;"><IMG border=0 hspace=0 alt="" align=3Dbaseline src=3D"cid:escoLogo"></div>
			</TD>
		</TR>
	</TBODY>
</TABLE>

<div id=3D"mainBody">
	Hello %fullname%. %CHECK_ONLINE%
</div>
	  
<hr style=3D"border-color:#000000;">

<div style=3D"margin:10px;">
	<small>&copy; Copyright '.date('Y').' escoNet. All rights reserved.</small>
</div>




</body>
</html>

--===NEXT_SECTION1===--

--===NEXT_SECTION===
Content-Type: image/gif; name="logo.gif"
Content-Transfer-Encoding: base64
Content-ID: <escoLogo>

R0lGODlhlgAcAPcAAAAAAP///+6qVY5tb45tcI1scItrcIhpcIlqcYdpcYVncoBkc39jc35jdHpg
dXlfdnhfdXZedXFadm5ZeGpWeWlVeWlWeWhVemdUemVTe2FQfGJRe2BQe11NfV5PfFhLflZJf1FG
f01DgU5FgUtCgktCgUc/gkdAgkhAgklBgklBgUQ+hEQ+gzo3hjs4hjs4hT05hjw5hT46hTQzhzY1
hzc2hzk3hjQ0iTc3ijg4izk5jDs7jTw8jT09jj8/j0BAkEBAj0JCkUVFkkZGk0dHlEhIlEpKlkpK
lU1Nl05OmE5Ol1FRmlFRmVRUm1ZWnFlZnltboF1doF5eoV9fomJio2FhomNjpGVlpWZmpmlpp2tr
qWxsqW1tqnBwrHBwq3R0rnNzrXZ2r3d3sHh4sHt7sn19tH19s39/tISEt4ODtoWFuIeHuYmJuouL
u42NvI+PvZGRv5SUwZOTwJiYw5eXwpycxpubxZ6exqGhyKOjyaenzKamy6Wlyqmpzaurzqqqza2t
z7Cw0a+v0LOz07W11LS007e31bi41ru72Lq617292cDA2sPD3MLC28TE3cbG3snJ4MjI383N4svL
4M/P49LS5dHR5NTU5tfX6NnZ6dvb6tra6d3d6+Dg7d/f7OHh7uTk8OLi7uTk7+jo8ubm8Ovr9Orq
8+3t9fDw9+/v9u/v9fPz+PX1+fn5/Pf3+vb2+fv7/fn5+/7+//39/vz8/e2pVuypVeuoVemnVual
V+WkV+SkWOOjV+OjWOSjV+GiWd6gWd2fWtueWtqdW9mdW9ecW9abXNWaXNSZXNKYXdGYXdCXXdCX
Xs2VXsuUX8qTX8mTX8iSYMeRX8WQYMSPYMOPYcCNYb+MYryKYrmJY7qJZLmIZLeHZLWGZbSFZbOF
Za2BZ6p/Z6p/aKh+aKh9aKd8aKV7aaJ6aaF5a594a553a513a5t2bJl0bZhzbZdybZVxbZJwbpFv
b////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAPMALAAAAACWABwA
AAj/AOcJHEiwoMGDCBMqXMiwocOHEAfOYOGBwTpsHiJq3Mixo8ePAlH4EkCS5AWQKFOqXMmQRMmS
J1nKnElzo8uXAmLW3Mmzp8CbL3Ui1HGESZAbD3UMYdIEiQ+HOogwIYKjYY4iSZ7yjCGCxUMbKETI
WAgU5sEcaRqdCsA2wKtLc4ogvFGGUSlZbdnG+rSIiUEkcSaNwtv2lKQ2Bo3EeYQqbytMf7Qg1fjt
mmVuNeaRcDDuWa+SxBQklIEAG7CXv64pgFFQ3LVpOAU0s3ztm0Amo/LqdrvFIA5Hu3f7JYgGVvC2
fAqmMX6cbSMdGk+XHMDNVuySCw5u0HW9pLCMA5V1/3+JbN4OT80LCymIJj3bWdAHZqHlfg1BLvTd
B3CUI6L08Tj18kJBKvwCIEm9lBDegQI4M88g+rEFyWQCWaKfKgQJsYp+VAyERGMRBtCHfwziRAFB
NUBTogDgLHjgNVEQllcso+SXVxQExbKbKqXoyFYmBL2hnyw9DHRHiO8N59B/KxpAEAXX4SJMLTjh
4tU84h34jR+6pbIFUjwkotsbAwGh2yhJDJRDElloQZAiux0yBhZbjCGHHgR1olspbmABxReQ7Ebm
Q0y+VEswu8SWDkHoBHgBUitUg1N282hwwQOxEXDBpiBoohseBFWhWyADCaEbJAzpmVcjCv0gI1tf
EP+0Q255HQJRoQIUY8AJ89Ag6UvlEEQNTu8QBAFOwQ5UlkkEpaIbGwQlodsiA/2gmytqrJfQWnmF
odATusnCQ0F96CbJrTjFYwNB7eA0DkHJ4AQBQRjgpA1By+ZE0Cy6kXLJv5dsotsjBLGymyyb3DEF
hQO5otsUCl2hG4YFwaEbJui+ZEFB7+BEDkHD4ISMNCRLE+9L1uAbm1D8IknwQJSkp4kXBfnYlhQK
YdGlQRbnBSShOG1MUMcvvTtQyCsKUI3KOLGMZAAvCzQGc80JUpVANrMFcUI654UKz7r9vGTQHLsL
ctJKMx0UQU9DXdAVobgnxkBZB4Aj17qdArbPGZf/JPRARJdktEBIr7i0sisT9Kp+UQ+kgxZ7XNJK
cIbQrRvOeOeVyt5tid1QoX8LFDhJg89TeImH/5R4w7q1scPrsMceH0I5gBF3XpoM9IpuVihkhW6r
GBTHxX2TFPo8owvw8UDF4OSAC9BHL/26iDdNECm6yfHREbqJMpApuqmhkBO7aTsQIeYS9E027LM/
QUGgl100Qc3glMBD+Qo1ycAJza6QqXnpxEAyoRtTFGkoLWvLHAjCBKqxZRAEyQVOREOQ+A3NYwTJ
Bk6oMQOEZEZtJakAQeiwm0Jg4QhCQAIUwnAHS1SiIIQIhBmY0J953KBnbbHEQAyxm0y4YQxneMMg
/3Q4EE7oZhaBIAMX3AC+MUVwgvAj2wVfsjyBwCM24eAAClgggg5EYB3T8EZBUhCbc5ggBA9gARMM
FqFW7GAgN+BWAGaxilNsSDd4EkgaImS+OTytFUqaR6JeQsGBWBBwZhtIB5JmjILIAEAi9GOInjAQ
aUWoCgPZgSr0g4WBBEGO+rFDQXgBxQpKEZFULAg3VmSLsRAkS9fJDg54GCEzDGSP+qEWQaLgrPS4
gSBSaGJ6ZIGHqw1kkCUppEAOKbpEDkQGv2JQBgrigPGwQyA3dFh6XHGGgdghgceBxA8MsgRMpEcQ
BVFCzJrDCjIchDuEjKLG5FeSKg6kBei4xYF6IYUBgxzAOrERY6nSAAhKaAIUncCEIwDRhiv4bx5D
6IIcAhGJTHwiFArNQxYYVpAqzIEQltCEKDhhCUTUwU0GkcIcEHEJTogCE5HQwxjGdZACyOOmN+1A
QRCA05uOoCAZ6Kk83ncQEijAHNVoxjGWEY1uqKMBHmgBQj7gDmww4xjR2AY8NBAQsssADs=

--===NEXT_SECTION===--
';

//
// END OF MESSAGE
//

$selectEmails = mysqli_query($link, 'SELECT owner,email FROM `esco_mail_virtual_users`');
while ($row = mysqli_fetch_row($selectEmails)) {
	$escoID = $row[0];
	$email = $row[1];
	
	$bUserOnline = false;
	
	//if($email=='aaron@esco.net')
	//{
	$selectUser = mysqli_query($link, 'SELECT name,lastname FROM `esco_users` WHERE id='.$escoID);
	$userInfo = mysqli_fetch_row($selectUser);
	
	$profile =  mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM esco_user_profiles WHERE user='$escoID';"));

	$timeDifference = time()-strtotime($profile['heartbeat']);

	if($timeDifference < 10) // If the last heartbeat was 10 seconds ago.
	  $bUserOnline = true;	
	
	$fullName = $userInfo[0].' '.$userInfo[1];
	//echo "Hello $fullName";
	
	$finalMsg = str_replace('%fullname%', $fullName, $message); 
	
	
	$subject = "Hello $fullName" ;
	
	if($bUserOnline){
		$finalMsg = str_replace('%CHECK_ONLINE%', 'Thank you for being online and signed into escoNet.', $finalMsg); 
	}else{
		$finalMsg = str_replace('%CHECK_ONLINE%', 'WHY ARE YOU OFFLINE??? I DON\'T LIKE IT WHEN USERS AREN\'T CONNECTED TO ME!', $finalMsg);
		$subject = strtoupper($userInfo[0]).', YOU ARE OFFLINE ON ESCONET!';
	}
	
	
	// Send
	mail("$fullName <$email>", $subject, $finalMsg, $headers); //."To: $fullName <$email>"
	sleep(1/2);
	//} // END OF if($email=='aaron@esco.net')
}

exit("\n");