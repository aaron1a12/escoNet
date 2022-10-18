<?php
//
// Command-line script to resize an image
//

if(!isset($argv))
    die('This script is meant for command line use only.');

// Load Settings
define( 'SITE_BASE' , dirname(dirname(dirname(dirname(__FILE__)))) );
require_once( SITE_BASE . '/php/sys/settings.php');

// Connect the database
$link = mysqli_connect($_ENV['db_server'], $_ENV['db_user'], $_ENV['db_pass']);
if(!$link) die('Failed to connect to the database. Error: '.mysqli_connect_errno());
if(!mysqli_select_db( $link, $_ENV['db_name'] )) die('Database does not exist.');


// Names
$names = array(
'Michael',
'Christopher',
'Matthew',
'Joshua',
'Andrew',
'Brandon',
'Daniel',
'Tyler',
'James',
'David',
'Joseph',
'Nicholas',
'Ryan',
'John',
'Zachary',
'Robert',
'Justin',
'Anthony',
'William',
'Kyle',
'Alexander',
'Cody',
'Kevin',
'Eric',
'Thomas',
'Dylan',
'Steven',
'Aaron',
'Brian',
'Jordan',
'Benjamin',
'Timothy',
'Christian',
'Adam',
'Jose',
'Austin',
'Patrick',
'Samuel',
'Richard',
'Sean',
'Charles',
'Nathan',
'Stephen',
'Jason',
'Jeremy',
'Travis',
'Mark',
'Jesse',
'Jeffrey',
'Cameron',
'Alex',
'Paul',
'Juan',
'Bryan',
'Dustin',
'Kenneth',
'Gregory',
'Scott',
'Derek',
'Trevor',
'Luis',
'Corey',
'Evan',
'Ethan',
'Jared',
'Ian',
'Carlos',
'Edward',
'Shawn',
'Bradley',
'Peter',
'Marcus',
'Gabriel',
'Victor',
'Garrett',
'Logan',
'Miguel',
'Mitchell',
'Vincent',
'Luke',
'Seth',
'George',
'Adrian',
'Brett',
'Erik',
'Spencer',
'Joel',
'Josh',
'Martin',
'Jean',
'Max',
'Tom',
'Thomas',
'Edd',
'Eddy',
'Steve',
'Steven',
'Alan',
'Charles',
'Charlie',
'Paul',
'William',
'Stanley',
'Zach',
'Isaac',
'Jacob',
'Ashley',
'Jessica',
'Amanda',
'Brittany',
'Sarah',
'Samantha',
'Emily',
'Stephanie',
'Elizabeth',
'Megan',
'Jennifer',
'Lauren',
'Kayla',
'Chelsea',
'Rachel',
'Taylor',
'Danielle',
'Amber',
'Rebecca',
'Courtney',
'Victoria',
'Kelsey',
'Melissa',
'Michelle',
'Hannah',
'Katherine',
'Jasmine',
'Alexandra',
'Alyssa',
'Heather',
'Tiffany',
'Christina',
'Shelby',
'Kimberly',
'Mary',
'Sara',
'Laura',
'Andrea',
'Alexis',
'Morgan',
'Kaitlyn',
'Brianna',
'Erica',
'Betty',
'Maria',
'Erin',
'Kelly',
'Allison',
'Anna',
'Crystal',
'Amy',
'Cassandra',
'Kristen',
'Katie',
'Vanessa',
'Haley',
'Lindsey',
'Olivia',
'Brooke',
'Kathryn',
'Caitlin',
'Jordan',
'Paige',
'Shannon',
'Katelyn',
'Jenna',
'Brittney',
'Angela',
'Julia',
'Hilary',
'Alcia',
'Marissa',
'Alexandria',
'Mariah',
'Jamie',
'Jacqueline',
'Monica',
'Catherine',
'Briana',
'Erika',
'Lindsay',
'Christine',
'Molly',
'Gabrielle',
'Whitney',
'Abigail',
'Ariel',
'Lisa',
'Miranda',
'Kristin',
'Meghan',
'Cynthia',
'Kristina',
'Breanna',
'Veronica',
'Leah',
'Cindy',
'Bianca',
'Melanie',
'Holly',
'Angelica',
'Pauline',
'Emma',
'Janet',
'Susan',
'Susannah',
'Lily',
'Birgit',
'Julianna'
);

$lastnames = array(
'Smith',
'Johnson',
'Williams',
'Jones',
'Brown',
'Davis',
'Miller',
'Wilson',
'Moore',
'Taylor',
'Anderson',
'Thomas',
'Jackson',
'White',
'Harris',
'Martin',
'Thompson',
'Garcia',
'Martinez',
'Robinson',
'Clark',
'Rodriguez',
'Lewis',
'Lee',
'Walker',
'Hall',
'Allen',
'Young',
'Hernandez',
'King',
'Wright',
'Lopez',
'Hill',
'Scott',
'Green',
'Adams',
'Baker',
'Gonzalez',
'Nelson',
'Carter',
'Michel',
'Perez',
'Roberts',
'Turner',
'Phillips',
'Parker',
'Evans',
'Edwards',
'Collins',
'Stewart',
'Sanchez',
'Morris',
'Rogers',
'Reed',
'Cook',
'Morgan',
'Bell',
'Murphy',
'Baily',
'Rivera',
'Cooper',
'Richardson',
'Cox',
'Howard',
'Ward',
'Torres',
'Peterson',
'Gray',
'Ramirez',
'James',
'Watson',
'Brooks',
'Kelly',
'Sanders',
'Price',
'Bennett',
'Wood',
'Barnes',
'Ross',
'Henderson',
'Coleman',
'Jenkins',
'Perry',
'Powell',
'Long',
'Patterson',
'Hughes',
'Flores',
'Washington',
'Butler',
'Simmons',
'Foster',
'Gonzales',
'Bryant',
'Alexander',
'Russell',
'Griffin',
'Diaz',
'Hayes',
'Myers',
'Ford',
'Hamilton',
'Graham',
'Sullivan',
'Wallace',
'Woods',
'Cole',
'West',
'Jordan',
'Owens',
'Reynolds',
'Fisher',
'Ellis',
'Harrison',
'Gibson',
'McDonald',
'Cruz',
'Marshall',
'Ortiz',
'Gomez',
'Murray',
'Freeman',
'Wells',
'Webb',
'Simpson',
'Stevens',
'Tucker',
'Porter',
'Hunter',
'Hicks',
'Crawford',
'Henry',
'Boyd',
'Mason',
'Morales',
'Kennedy',
'Warren',
'Dixon',
'Ramos',
'Reyes',
'Burns',
'Gordon',
'Shaw',
'Holmes',
'Rice',
'Robertson',
'Hunt',
'Black',
'Daniels',
'Palmer',
'Mills',
'Nichols',
'Grant',
'Knight',
'Ferguson',
'Rose',
'Stone',
'Mortimer',
'Hawkins',
'Dunn',
'Perkins',
'Hudson',
'Spencer',
'Gardner',
'Stephens',
'Payne',
'Pierce',
'Berry',
'Mathews',
'Arnold',
'Wagner',
'Willis',
'Ray',
'Watkins',
'Olson',
'Carroll',
'Duncan',
'Snyder',
'Hart',
'Cunningham',
'Bradley',
'Lane',
'Andrews',
'Ruiz',
'Harper',
'Fox',
'Riley',
'Armstrong',
'Carpenter',
'Weaver',
'Greene',
'Lawrence',
'Elliott',
'Chavez',
'Sims',
'Austin',
'Peters',
'Kelley',
'Franklin',
'Lawson',
'Fields',
'Gutierrez',
'Ryan',
'Schmidt',
'Carr',
'Vasquez',
'Castillo',
'Wheeler',
'Chapman',
'Oliver',
'Montgomery',
'Richards',
'Williamson',
'Johnston',
'Banks',
'Meyer',
'Bishop',
'Mccoy',
'Howell',
'Alvarez',
'Morrison',
'Hansen',
'Fernandez',
'Garza',
'Harvey',
'Little',
'Burton',
'Stanley',
'Dubois',
'Richard',
'Petit',
'Lefebvre',
'Fournier',
'Dupont',
'Lambert',
'Torres',
'Navarro',
'Ramos',
'Blanco',
'Castro',
'Ortega',
'Rubio',
'Delgado',
'Morales',
'Jimenez',
'Johansson',
'Larsson',
'Magnusson',
'Gustafsson',
'Pavlov',
'Ivanov',
'Silva',
'Santos',
'Costa',
'Hoffmann',
'Escobar'
);

$botname = $names[array_rand($names)];
$botfullname = $botname.' '.$lastnames[array_rand($lastnames)];



$randomSubjects = array(
'Hello, check out this article',
'This is my favorite article on Wikipedia',
'%name%, what do you think about this?',
'Hey %name%, check this out',
'From your friend %botname%',
'This article changed my life',
'Did you know about this?',
'I can\'t believe I didn\'t know this article',
'This article can change your life!',
'Hey %name%, this article is very important',
'Most americans don\'t know about this',
'Wikipedia article',
'I know %name% will LOVE this article',
'Forget everything and read this NOW',
'Drop what you\'re doing and click this link',
'This is quite interesting',
'Unbelievable',
'I thought I knew everything',
'Now this is incredible',
'Just a random article on Wikipedia',
'%name%, did you know about this?',
'I just found out about this article...',
'From your best friend, %botname%',
'Hola amigo, did you read this?',
'This might not seem important but...',
'Good read here',
'You should\'ve read this long ago',
'Could you please read this %name%?',
'I think this article should be taught in schools',
'Award-winning article here. Must read!',
'You should REALLY read this',
'I know I\'ve sent these emails before but this one\'s different!',
'Article\'s like this one makes me want to cry',
'Weird but cool article',
'%name%, read this NOW',
'This might not seem important but...',
'IMPORTANT ARTICLE ON WIKIPEDIA',
'Amazing read',
'This could change your life',
'You think you know everything?',
'Don\'t erase this message!',
'Please read this lovely article',
'Wikipedia would not be the same without this page',
'This article\'s not so bad',
'Read this and tell me what you think',
'Pretty cool article',
'This article makes me feel stupid',
'%name%, your friend %botname% wants you to read this!',
'Read this and never be the same person!',
'Wikipedia wants to delete this page, I don\'t know why',
);

$randomMessages = array(
'Hello %name%. Hope you don\'t mind my spam (lol). <a href=3D"%randomLink%">Check out this article on Wikipedia </a>
<br><br>
Best Regards,
%botname%
',
'<a href=3D"%randomLink%">Check out this article on Wikipedia</a>'
,
'<a href=3D"%randomLink%">%randomLink%</a>'
,
'<a href=3D"%randomLink%">OMG. This article is amazing. Click to read!</a>'
,
'<a href=3D"%randomLink%">Wikipedia</a> &lt;--- click.  You must read to all your friends!<br><br>BTW: my vacation is almost over :('
,
'<a href=3D"%randomLink%">Important article</a>'
,
'Hi %name%, sorry I haven\'t been writing but I found this very cool article on Wikipedia and I just had to send it to you
<br>
<a href=3D"%randomLink%">%randomLink%</a>
'
,
'This article, on <a href=3D"%randomLink%">Wikipedia</a>, reminds me of you in strange and weird way. Does it mean anything to you?'
,
'<a href=3D"%randomLink%">wikipedia article</a><br><br>--%botname%'
,
'<a href=3D"%randomLink%">Wikipedia</a><br><br>Please don\'t say it\'s stupid. <br><br>--Your stupid friend, %botname%'
,
'<a href=3D"%randomLink%">%randomLink%</a><br><br>Read it and tell me what you think! <br><br>--%botname%'
,
'<a href=3D"%randomLink%">Amazing link on Wikipedia</a>'
,
'I was drunk last night and found this amazing article. I favorited it immediately and sent it to all my friends on escoNet
<br><br><a href=3D"%randomLink%">Check it out!</a>'
,
'<span style=3D"font-family:Times New Roman">This article looks pretty important to me. %name%, I think you shoud
take a look at it. <a href=3D"%randomLink%">link</a><br><br>Don\'t worry, it\'s not a virus :)<br><br>--%botname%</span>'
,
'Well this article should be nominated for Nobel prize! <a href=3D"%randomLink%">wikipedia</a>'
,
'It\'s actually quite amazing. I once had to do a paper on it for homework. VERY EYE-OPENING<br><br>
<a href=3D"%randomLink%">wikipedia</a>'
,
'%name%, I know you hate me (after everything that happened) but I found a really cool article on Wikipedia that\'ll make you smile!<br><br><a href=3D"%randomLink%">%randomLink%</a>'
,
'<a href=3D"%randomLink%">This article I found on wikipedia just cracks me up!</a>'
,
'This is a VERY IMPORTANT article. <a href=3D"%randomLink%">%randomLink%</a>'
,
'Well this article really isn\'t THAT important but it left me feeling satified and smarter.<br><a href=3D"%randomLink%">%randomLink%</a>'
,
'<a href=3D"%randomLink%">%randomLink%</a>.<br><br>Ok, it\'s not really important to know but it\'s cool trivia.'
,
'Hey %name%, this <a href=3D"%randomLink%">Wikipedia article</a> makes me think of you. I don\'t know why, lol. <br><br>--%botname%'
,
'%name% you should impress your friends and family with your knowledge about <a href=3D"%randomLink%">this article</a>'
,
'Well, trivia can be important too! <a href=3D"%randomLink%">Click for link</a>'
,
'Honestly this article changed my life. <a href=3D"%randomLink%">Click for link</a>. <br>It could change yours as well!'
,
'Care to read this simple article? <a href=3D"%randomLink%">Click for link</a><br><br>
BTW: Sorry about yesterday, I\'ll get around fixing the truck when I can.'
,
'I was SOOO bored yesterday so I spent hours (really hours) clicking on the random page link on English Wikipedia. But then,
I stumbled across the most beautiful article on the site. It should be featured on CNN. <a href=3D"%randomLink%">Here\'s the link</a><br>
<br>Read it and tell me what you think.'
,
'Weren\'t you just talking about <a href=3D"%randomLink%">this</a> yesterday?'
,
'I\'m planning on blogging about this REALLY impressive article I found on <a href=3D"%randomLink%">Wikipedia</a>, but I\'m worried I might sound stupid.
<br>Then I thought, well, since you\'re the expert, why don\'t you help me write it? But FIRST, read the link. It\'s really cool.'
,
'I\'m taking a BIG RISK using the boss\'s personal computer but you simply MUST read <a href=3D"%randomLink%">this</a>.'
,
'Greetings %fullname%,<br><br>&nbsp;&nbsp;I was browsing the online database of English Wikipedia and found the most extraordinary article. I insist you read it at once.<br><br>
<a href=3D"%randomLink%">Wikipedia link</a>
<br><br>Your dearest friend,<br><br>%botfullname%<br><small>CEO, Founder<br>TastyBurger, inc</small>'
);

// Headers
$headers = 'From: '.$botfullname.' <esconet@esco.net>' . "\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-Type: multipart/related;'."\r\n\t".'type="multipart/alternative";'."\r\n\t".'boundary="===NEXT_SECTION==="' . "\r\n";
$headers .= 'This is a multi-part message in MIME format.';
//$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";

//
// BEGIN MESSAGE
//

$messageSrc = '--===NEXT_SECTION===
Content-Type: multipart/alternative; boundary="===NEXT_SECTION1==="


--===NEXT_SECTION1===
Content-Type: text/html;
	charset="utf-8"
Content-Transfer-Encoding: quoted-printable

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<style>
body {font-family:Arial;font-size:10pt;background-color:#FFFFF;}
#mainBody {margin:50px;}
</style>
</head>
<body>

%randomMessage%


</body>
</html>

--===NEXT_SECTION1===--
--===NEXT_SECTION===--
';

//
// END OF MESSAGE
//



$ch = curl_init();

$options = array(
	CURLOPT_URL => 'http://en.wiki.com/random?content=wikipedia_en_all_2015-05',
	CURLOPT_PORT => 80,
	CURLOPT_CONNECTTIMEOUT => 200,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => false,
	CURLOPT_HEADER => true

);

curl_setopt_array($ch, $options);

$rawResponse = curl_exec($ch);


$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


if($httpCode=='302') {
	$randomLink = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
	
	$selectEmails = mysqli_query($link, 'SELECT owner,email FROM `esco_mail_virtual_users`');
	$ii = 0;
	while ($row = mysqli_fetch_row($selectEmails)) {
		$ii++;
		$escoID = $row[0];
		$email = $row[1];
		
		
		$bUserOnline = false;
		
		//echo "\n---Loop $ii---\n\n";
		
		//if($email=='esconet@esco.net' || $email=='aaron@esco.net')
		//{
		
		
		
		$selectUser = mysqli_query($link, 'SELECT name,lastname FROM `esco_users` WHERE id='.$escoID);
		$userInfo = mysqli_fetch_row($selectUser);
		
		$profile =  mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM esco_user_profiles WHERE user='$escoID';"));

		
		  
		$fullName = $userInfo[0].' '.$userInfo[1];
		
		//echo 'Owner: '.$fullName;
		  
		$subject = $randomSubjects[array_rand($randomSubjects)];
		$subject = str_replace('%name%', $userInfo[0], $subject); 
		$subject = str_replace('%fullname%', $fullName, $subject);
		$subject = str_replace('%botname%', $botname, $subject);
		$subject = str_replace('%botfullname%', $botfullname, $subject);
		
		$randomMsgs = new ArrayObject($randomMessages);
		$randomMsgs = $randomMsgs->getArrayCopy();
		
		$randomMessage = $randomMsgs[array_rand($randomMsgs)];
		
		
		$randomMessage = str_replace('%name%', $userInfo[0], $randomMessage);
		$randomMessage = str_replace('%fullname%', $fullName, $randomMessage);
		$randomMessage = str_replace('%randomLink%', $randomLink, $randomMessage);
		$randomMessage = str_replace('%botname%', $botname, $randomMessage);
		$randomMessage = str_replace('%botfullname%', $botfullname, $randomMessage);
		
		$message = $messageSrc;
		$message = str_replace('%randomMessage%', $randomMessage, $message);
		
		
		// Send
		mail("$fullName <$email>", $subject, $message, $headers); //."To: $fullName <$email>"
		sleep(1/2);
		//} // END OF if($email=='aaron@esco.net')
	}	
}

curl_close($ch);

exit("\n");