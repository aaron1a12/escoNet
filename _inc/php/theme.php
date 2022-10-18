<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php $this->output('title'); ?> - esco.net</title>
<!--<base href="http://www.esco.net/">-->
<meta name="keywords" content="escobar, casa, esconet">
<meta name="description" content="">
<meta name="author" content="Casa Escobar">
<link rel="shortcut icon" href="/favicon.ico">
<link href="/_inc/css/global.css" rel="stylesheet" type="text/css">
<link href="/_inc/css/layout.css" rel="stylesheet" type="text/css">
<link href="/_inc/css/prism.css" rel="stylesheet">
<script src="http://media.esco.net/global/jquery.js"></script>
<script src="http://media.esco.net/global/jquery.easing.js"></script>

<link href="http://media.esco.net/global/jquery-ui.css" rel="stylesheet">
<script src="http://media.esco.net/global/jquery-ui.js"></script>

<script type="text/javascript" src="/_inc/js/syntaxhighlighter/scripts/shCore.js"></script>
<script type="text/javascript" src="/_inc/js/syntaxhighlighter/scripts/shBrushJScript.js"></script>
<script type="text/javascript" src="/_inc/js/syntaxhighlighter/scripts/shBrushXml.js"></script>
<link type="text/css" rel="stylesheet" href="/_inc/js/syntaxhighlighter/styles/shCoreDefault.css"/>
<script type="text/javascript">SyntaxHighlighter.all();</script>
<script type="text/javascript">
	var esco = new Object();
	esco.loggedIn = <?php echo $this->loggedIn ? 'true' : 'false'; ?>;
    esco.escoProfileURL = "<?php echo $this->escoProfileURL; ?>";
    esco.escoID = <?php echo intval($this->escoID); ?>;
    esco.updated = 0;
    esco.updatedBlinkerHandle = 0;
    esco.notifications = new Object();
		esco.shoutbox = new Object();
		esco.shoutbox.active = 0;
		esco.shoutbox.lastid = 0;	
</script>
<script src="/_inc/js/main.js"></script>

<?php $this->output('head'); ?>
</head>

<body>
    <div class="hidden">
        <audio id="message_sound"><source src="/_inc/message.wav"></audio>
    </div>
    <div id="notifications-menu">
        <div id="profile-notifications"></div>
        <hr>
        <div onclick="location.href='/activity/';"><img src="/_inc/img/icons/clock.png" style="vertical-align:middle;"> &nbsp; Recent Activity</div>
        <hr>
        <div onclick="location.href='<?php echo $this->escoProfileURL;?>/photos/';"><img src="/_inc/img/icons/picture.png" style="vertical-align:middle;"> &nbsp; Your Photos</div>
    </div>
    <div id="block-layer" onclick="hidePopups();" style="display:none; opacity:0;position:fixed; top:0; left:0; width:100%; height:100%; background-color:#000; z-index:999;"></div>

<!--

	<div style="background-color:#b20625;; color:white; font-size:10pt;">
		<div class="siteWidth center" style="text-align:center; padding:5px;">
		<img src="/_inc/img/icons/exclamation.png" style="vertical-align:middle;"> &nbsp;
		<a style="color:#cddaff;" target="http://www.weather.com">Yom Kippur</a> starts TONIGHT!</div>
    </div>
	
-->




    <div id="header">
        <div class="siteWidth center">
            <a id="logo" href="/" title="Nice logo, no?">
                esco<span>.net</span>
            </a>

            <a href="" id="coatOfArms">
            </a>

            <form id="searchBar" action="http://www.boogle.com/search" method="get">
                <input name="q" value="" onblur="searchToggle(this)" autocomplete="off"><button>Search</button>
            </form>



            <div class="cf"></div>
        </div>
    </div>

    <div id="link-btns" class="center siteWidth">
        <div class="box-container">
            <a class="box" href="http://www.esco.net/blog/"><img src="/_inc/img/icons/newspaper.png">Blog</a>
            <a class="box" href="/user/list/"><img src="/_inc/img/icons/users.png">Social</a>
            <a class="box" href="/photos/"><img src="/_inc/img/icons/picture.png">Photos</a>
            <a class="box" href="/calendar/"><img src="/_inc/img/icons/calendar-day.png">Events</a>
            <a class="box" href="http://www.esco.net/wiki"><img src="/_inc/img/icons/esco-wiki.png">EscoWiki</a>
            <a class="box" href="https://www.neoranker.com/"><img src="/_inc/img/icons/gamecritic.png" style="width:16px;">NEORANKER</a>
<!--            <a class="box" href="http://en.wiktionary.org/"><img src="/_inc/img/icons/book-open-text.png" style="margin-right:0;"></a>-->
            <a class="box" href="/games/"><img src="/_inc/img/icons/board-game-go.png">Games</a>
            <a class="box" href="/links/"><img src="/_inc/img/icons/globe-green.png">Links</a>
            <span class="stretch"></span>
        </div>

        <?php
        {
            if($this->loggedIn)
            {
        ?>
        <a class="account-btn notify" id="notifyBtn" style="position:relative;" href="#" onclick="showNotifications();">
            <div id="notifyBtnLayer" style="background-color:#ff006c; opacity: 0;filter: alpha(opacity=0); font-size:0; width:35px; height:41px; position:absolute; top:0; right:0;"></div>
            <img id="notifyBtnIcon" src="/_inc/img/icons/at-sign-balloon.png">
        </a>
        <a class="account-btn" href="<?php echo $this->escoProfileURL;?>">
            <?php
                $maxChar = 10;

                if(strlen($this->escoName)<9)
                    echo '<img src="/_inc/img/icons/user-green.png">';

                if(strlen($this->escoName)>$maxChar)
                    echo substr($this->escoName,0, $maxChar) . '...';
                else
                    echo $this->escoName;
            ?>
        </a>
        <?php
            }else{
        ?>
        <a class="account-btn" href="/user/"><img src="/_inc/img/icons/user-green.png">SIGN IN</a>
        <?php
            }
        }
        ?>

    </div>
    <div class="cf"></div>

    <!-- Beging Floating Header -->
    <div id="floating-header">
        <div style="background-color:#eee; padding:5px;">
            <div id="rantra-logo-small" onclick="location.href='/'">
                <span>Rantra<small>.com</small></span>
            </div>

            <nav>
                <a href="/">Home</a>
                <a href="/html/">HTML</a>
                <a href="/css/">CSS</a>
                <a href="/javascript/">Javascript</a>
                <a href="/javascript/console.php">Console</a>
                <a href="/javascript/webgl.php">WebGL Experiments</a>
            </nav>
            <div class="cf"></div>
        </div>
        <div style="clear:both;background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0) 5px); height:12px;">
        </div>
    </div>
    <!-- End Floating Header -->

    <div id="main">
        <div <?php if(!$this->pageIsFullscreen){ echo 'class="siteWidth center paddedContent"';} ?>>
        <?php $this->output('content'); ?>
        </div>
    </div>

		<iframe src="http://media.esco.net/public_messages/iframe/" style="width:720px;height:120px;border:0;overflow:hidden;margin-left:auto;margin-right:auto;display:block;padding:10px;" seamless></iframe>

    <div id="footer">
        <div class="center siteWidth">
            <nav>
                <ul>
                    <li>escoNet</li>
                    <li><a href="/blog/">Blog</a></li>
                    <li><a href="/user/list/">Social</a></li>
                    <li><a href="/photos/">Photos</a></li>
                    <li><a href="/events">Events</a></li>
                    <li><a href="/links/">Links</a></li>
                </ul>

                <ul>
                    <li>User Account</li>
                    <li><a href="/user/profile.php">Edit Profile</a></li>
                    <li><a href="/user/profile-pictures.php">Change Profile Pictures</a></li>
                    <li><a href="/edit/">Edit Home Sections</a></li>
                    <li><a href="/user/polls/">Polls</a></li>
                </ul>

                <ul>
                    <li>Blog Management</li>
                    <li><a href="/user/blog/">Manage my Blog Posts</a></li>
                    <li><a href="/user/blog/post.php">Add New Blog Post</a></li>
                </ul>

                <ul>
                    <li>Photos</li>
                    <?php if($this->loggedIn) $photostreamURL=$this->escoProfileURL.'/photos'; else $photostreamURL = '/user/'; ?>
                    <li><a href="<?php echo $photostreamURL;?>">View My Photostream</a></li>

                    <li><a href="/user/photo-uploader/">Upload new photo</a></li>
                </ul>

                <ul>
                    <li>Misc</li>
                    <li><a href="/time/">Current Time and Date</a></li>
                    <li><a href="/bank/">The Escobar Central Bank</a></li>
					<li><a href="/misc/qr.php">QR Code</a></li>
					<li><a href="/fun/morse.php">Morse Code Generator</a></li>
                </ul>

            </nav>
            <hr>
            <small>&copy; Copyright <?php echo date('Y');?> The Escobar Family. All rights reserved. UNAUTHORIZED ACCESS IS STRICTLY FORBIDDEN.</small>
        </div>
    </div>


    <!--
    <script type="text/javascript">
    window.onscroll = scroll;
    var headerHeight = 60;
    var bIsHeaderFloating = false;

    $( document ).ready(function() {
        $("#floating-header").css({ top: -200 });
        document.getElementById('floating-header').style.display = 'block';
    });

    function scroll () {
        if(window.pageYOffset > headerHeight) {
            if(!bIsHeaderFloating){
                $( "#floating-header" ).animate({ top: 0}, 400, function() {
                });
                bIsHeaderFloating = true;
            }
        }
        else{
            if(bIsHeaderFloating) {
                $( "#floating-header" ).animate({ top: -200}, 400, function() {
                });
                bIsHeaderFloating = false;
            }
        }
    }
    </script>
    -->
    <script src="/_inc/js/prism.js"></script>
</body>
</html>
