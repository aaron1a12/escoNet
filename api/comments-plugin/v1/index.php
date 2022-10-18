<?php
//error_reporting(0);

class customPage extends page {
	
	public $commentsUrl;
	public $commentsUrlGz;
	
    public $title = 'Comments';

    public $post;

    public $bPostExists;

    public $cond;
	
	function errorMsg()
	{
		header('HTTP/1.1 404 Not Found');
		echo '<h1>400 Bad Request</h1>';
		exit();
	}
	
	function outputHTML()
	{
		?>
		<!doctype html>
		<html>
		<head>
			<meta charset="utf-8">
			<title>esco.net Comments</title>
			<link href="/_inc/css/global.css" rel="stylesheet" type="text/css">
			<link href="/_inc/css/layout.css" rel="stylesheet" type="text/css">
			<style>
			body {background:transparent;}
			body, html {
				min-width: auto;
			}
			</style>
			<script>
			function postIt() {
				var body = document.body,
				html = document.documentElement;
				
				var height = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight );
					   
				parent.postMessage(height, "<?php echo $this->commentsUrl;?>");
			}
			</script>
		</head>
		
		<body onload="postIt();">
			<!-- COMMENTS BEGIN -->
			<h2 style="margin-top:30px;"><?php echo $this->title;?></h2>

			<!-- POST BOX -->
			<?php
			if($this->loggedIn)
			{
				include($this->siteDirectory . '/_inc/php/comment-box.php');
			}
			?>
			<!-- POST BOX -->

			<?php
			{
				// Table
				$COMMENTS_TABLE = 'esco_api_comments';
				$COMMENTS_FOR_ROW = 'url';
				$COMMENTS_FOR_VALUE = '0x'.$this->commentsUrlGz;
				$COMMENTS_FOR_BINARY = 1;

				// How many items to show per page
				$RESULTS_PER_PAGE = 10;
				// How pages to show until the ellipsis (...)
				// Must be an odd number (not evenly divisable by 2. E.g., 1, 3, 5, 7, 9, 11, 13, etc)
				$MAX_PAGE_GROUP = 9;

				include($this->siteDirectory . '/_inc/php/comments.php');
			}
			?>
			<!-- COMMENTS END -->	
		</body>
		</html>
		<?php
	}
	

    function init()
    {
		if(!isset($_GET['url'])) $this->errorMsg();
		
		$this->commentsUrl = filter_var( htmlentities($_GET['url']), FILTER_SANITIZE_MAGIC_QUOTES );	

		if($this->commentsUrl=='') $this->errorMsg();

		
		$urlGZ = gzcompress($this->commentsUrl, 9);    
		$urlGZ = strtoupper(unpack('H*', $urlGZ )[1]);
		
		$this->commentsUrlGz = $urlGZ;
		
		
		
		
        if($_POST)
        {
            if(isset($_POST['comment']) && $this->loggedIn == true)
            {
                $comment = filter_var( htmlentities($_POST['comment']), FILTER_SANITIZE_MAGIC_QUOTES );
                $author = $this->escoID;
                $time = time();

                if($comment!='')
                {
                    $query = "INSERT INTO `esco_api_comments` (url, author, time, comment) VALUES (0x$urlGZ, '$author', '$time', '$comment')";

                    mysqli_query($this->link, $query);

                    // Register Activity
                    logAction( $this, ACTION_COMMENT );

                    //header('Location: ' . $_SERVER["REDIRECT_URL"]);
                    die('Comment Posted!');
                }

            }
        }		
		
		
		if(isset($_GET['title'])) $this->title = htmlentities($_GET['title']);	
		
		/*
		$countQuery = "SELECT COUNT(id) FROM `esco_api_comments` WHERE `url` = 0x$urlGZ;";
		$resultsNum = mysqli_fetch_array(mysqli_query($this->link, $countQuery))[0];
		
		die($resultsNum);
		
		*/
		
		
		$this->outputHTML();
		
		
		die();

    }


}

new customPage();
