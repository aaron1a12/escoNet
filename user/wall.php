<?php

class customPage extends page {
    public $title = 'User Account';
    public $private = false;

    public $user;
    public $profile;

    public $bUserExists = false;

    public $badge;

    public $bUserOnline = false;
	public $userLastOnlineTime;


    function init()
    {
        //phpinfo();
        $userid = intval($_GET['usr-id']);

        $query = 'SELECT * FROM esco_users WHERE id="'.$userid.'"';
        $result = mysqli_query($this->link, $query);
        $num_rows = mysqli_num_rows($result);
        $this->user = mysqli_fetch_assoc($result);

        $this->profile =  mysqli_fetch_assoc(mysqli_query($this->link, "SELECT * FROM esco_user_profiles WHERE user='$userid';"));

        //
        // Is user online?
        //
		$this->userLastOnlineTime = strtotime($this->profile['heartbeat']);
        $timeDifference = time()-$this->userLastOnlineTime;

        if($timeDifference < 10) // If the last heartbeat was 10 seconds ago.
          $this->bUserOnline = true;

        //
        // Some other stuff
        //

        if($num_rows!=0)
            $this->bUserExists = true;

        $this->title = $this->user['name'].' '.$this->user['lastname'];


        if($this->user['id'] == $this->escoID)
        {
            $query = "UPDATE esco_user_profiles SET updated='0' WHERE user='".$this->escoID."';";
            mysqli_query($this->link, $query);
        }

        // Check if the user has an account

        $result = mysqli_query($this->link, "SELECT funds FROM esco_bank_accounts WHERE owner=" . $this->user['id']);
        $rows = mysqli_num_rows($result);

        if($rows==0) $this->user['bHasAccount'] = false; else $this->user['bHasAccount'] = true;

        if($this->user['bHasAccount']){
            $this->user['accountFunds'] = mysqli_fetch_row($result)[0];
        }

		// Check if the user has an EMAIL account
		
		$this->user['bHasEmail'] = false;
		$this->user['email'] = '';

        $result = mysqli_query($this->link, "SELECT email FROM esco_mail_virtual_users WHERE owner=" . $this->user['id']);
        $rows = mysqli_num_rows($result);

        if($rows>0) $this->user['bHasEmail'] = true;

        if($this->user['bHasEmail']){
            $this->user['email'] = mysqli_fetch_row($result)[0];
        }
		
		
        $this->badge = '';

        if( $this->profile['birth'] !=null)
        {
            $format = 'Y-m-d';
            $date = DateTime::createFromFormat($format, $this->profile['birth']);

            $this->profile['birth'] = $date->format('F jS, Y');
            $this->profile['age'] = date('Y')-$date->format('Y');

			$iBirthLeapYear = intval($date->format('L'));

			if($iBirthLeapYear!=1){
				if( date('z') < $date->format('z'))
					$this->profile['age']--;
			}



            if( date('dm') == $date->format('dm'))
                $this->badge .= '<img title="'.$this->user['name'].' turned '.$this->profile['age'].' today!" src="/_inc/img/icons/rubber-balloons.png">';
        }

        $topUser = intval(mysqli_fetch_row(mysqli_query($this->link, "SELECT user FROM esco_user_activity_top"))[0]);
        if($topUser==$this->user['id']){
            $this->badge .= '<img title="Most active user!" src="/_inc/img/icons/crown.png">';
        }

        $topRichUser = intval(mysqli_fetch_row(mysqli_query($this->link, "SELECT user FROM esco_user_activity_top_rich"))[0]);
        if($topRichUser==$this->user['id']){
            $this->badge .= '<img title="This user is the richest!" src="/_inc/img/icons/money-bag.png">';
        }

		if( $this->profile['wm_linked'] =="1")
        {
			$this->badge .= '<img title="This user owns a Wild Montage account" src="/_inc/img/icons/wild-montage.png">';
			
			$topWorker = intval(mysqli_fetch_row(mysqli_query($this->link, "SELECT user FROM `wildmontage`.`studio_top_worker` WHERE `id`=1"))[0]);
			if($topWorker==$this->user['id']){
				$this->badge .= '<img title="This user is Employee of the Week at Wild Montage!" src="/_inc/img/icons/award.png">';
			}			
		}

        if($_POST)
        {
            if(isset($_POST['comment']) && $this->loggedIn == true)
            {
                $wallID = $this->user['id'];
                $comment = filter_var( htmlentities($_POST['comment']), FILTER_SANITIZE_MAGIC_QUOTES );
                $author = $this->escoID;
                $time = time();

                if($comment!='')
                {
                    $query = "INSERT INTO esco_wall_comments (wall, author, time, comment) VALUES ('$wallID', '$author', '$time', '$comment')";

                    mysqli_query($this->link, $query);


                    // Register Activity
                    logAction( $this, ACTION_COMMENT );


                    //
                    // Update that user's profile as "updated" so he sees a notification
                    //

                    if($this->user['id'] != $this->escoID)
                    {
                        $query = "UPDATE esco_user_profiles SET updated='1' WHERE user='".$this->user['id']."';";
                        mysqli_query($this->link, $query);
                    }


                    header('Location: ' . $_SERVER["REDIRECT_URL"]);
                    die();
                }

            }
        }

    }

    function head()
    {
	?>
	<script src="/_inc/js/social.js"></script>
	<script>
	function toggleAccountSettingsWidget() {
		var widget = document.getElementById('accountSettingsWidget');

		if(widget.style.display=='none')
			widget.style.display = 'block';
		else
			widget.style.display = 'none';
	}
	</script>
	<?php
    }




    function content() {
        if($this->bUserExists){
?>
<?php if($this->user['id'] == $this->escoID) include($this->siteDirectory . '/_inc/php/user-header.php');?>
<div class="widget nopadding">
    <div style="background-color:#ddd;position:relative; height:210px;margin-bottom:115px;">
        <?php
        {
            if($this->profile['banner']!=''){
                echo '<img src="http://media.esco.net/img/social/'.$this->user['id'].'/'.$this->profile['banner'].'" style="width:1000px;height:210px; outline: 8px solid rgba(255, 255, 255, 0.5);
transition: outline-offset 0.2s cubic-bezier(0, 0, 0.5, 1) 0s, outline 0.2s cubic-bezier(0, 0, 0.5, 1) 0s;
outline-offset: -8px;">';
            }

            if($this->user['id'] == $this->escoID)
            {
                //echo '<a href="/user/account.php" class="btn" style="position:absolute; top:215px; right:5px;"><img src="/_inc/img/icons/settings_16.png" style="vertical-align:middle;margin: 0px 5px">Manage</a>';
				echo '<a href="#" onclick="toggleAccountSettingsWidget();" class="btn" style="position:absolute; top:215px; right:5px;"><img src="/_inc/img/icons/settings_16.png" style="vertical-align:middle;margin: 0px 5px">Manage</a>';
            }
        }
        ?>
        <div style="position:absolute; top:80px; left:35px;">
            <div style="z-index:100;float:left;font-size:0px;text-align:right;border:4px solid #fff;background:#fff; border-bottom-width:10px; box-shadow: 0px 0px 24px rgba(0, 0, 0, 0.5); transform: rotate(-4deg);-ms-transform: rotate(-4deg);-webkit-transform: rotate(-4deg); margin-top:-10px;">
                <img src="http://media.esco.net/img/social/<?php echo $this->user['id'];?>/profile_large.jpg" style="width:230px; height:230px;">
                <br>
                <span style="font-size:10px;font-family:cursive;">
                <?php
                {
                    if($this->user['id'] == $this->escoID)
                    {
                        echo '<a href="/user/profile.php"><i>Change Picture</i></a>';
                    }
                    else
                    {
                        echo '&nbsp;';
                    }
                }
                ?>
                </span>
            </div>
            <div style="float:left; margin-top:140px; margin-left:20px;">
                <h1 style="margin-bottom:20px;"><?php echo $this->user['name'] .' ' . $this->user['lastname'] .'&nbsp;'. $this->badge;?></h1>
                <span style="color:#888;">&nbsp;&nbsp;<?php echo $this->profile['tagline'];?></span>
            </div>
            <div class="cf"></div>
        </div>
    </div>

    <div style="padding:20px;padding-top:0;">
        <div class="profile-info" style="float:left;width:270px; ">
            <?php
                if($this->profile['birth']!=null)
                {
                    echo '<div class="widget">';
                    echo '<img src="/_inc/img/icons/rubber-balloons.png">';
                    echo 'Born ';


                     echo $this->profile['birth'].' <div style="text-align:left;margin-left:26px;">('.$this->profile['age'] .' years old)</div>';

                    //echo '-'.strtotime($date->format('F jS, Y'));


                    // Working
                    //echo date('F jS, Y', strtotime( $this->profile['birth']) );



                    echo '</div>';
                }
            ?>
            <?php
                if($this->profile['height']!=null)
                {
                    echo '<div class="widget">';
                    echo '<img src="/_inc/img/icons/ruler.png">';
                    echo 'Height: ';
					
					$inches = $this->profile['height']/2.54;
					$feet = intval($inches/12);
					$feetInches = $feet * 12;
					$inches = round($inches - $feetInches, 2);					

                    echo $feet.'ft '.$inches.'"';
                    echo '</div>';
                }
            ?>		
			<?php
                if($this->profile['weight']!=null)
                {
                    echo '<div class="widget">';
                    echo '<img src="/_inc/img/icons/scale.png">';
                    echo 'Weight: ';			

                    echo $this->profile['weight'] . ' lbs';
                    echo '</div>';
                }
            ?>		
            <div class="widget">
                <img src="/_inc/img/icons/accept.png">Registered in <?php echo date('F, Y', $this->user['time']);?>
            </div>
            <div class="widget">
                <?php if($this->bUserOnline){ ?>
                  <img src="/_inc/img/icons/user-online.png">This user is ONLINE
                <?php }else{ ?>
                  <img src="/_inc/img/icons/user-offline.png">This user is OFFLINE <br><div style="margin-left:27px;color:rgba(255,255,255, 0.5);"><small>Last online: <?php echo escoDate($this->userLastOnlineTime);?></small></div>
                <?php } ?>
            </div>
            <a class="widget link" href="/user/<?php echo $this->user['id'].'/'.urlify($this->user['name'].' '.$this->user['lastname']); ?>/photos/">
               <img src="/_inc/img/icons/picture.png">View Photos
            </a>
            <a class="widget link" href="/blog/author/<?php echo $this->user['id'].'/'.urlify($this->user['name'].' '.$this->user['lastname']); ?>">
               <img src="/_inc/img/icons/newspaper.png">View All Blog Posts
            </a>

		
			<?php if($this->user['bHasEmail']){ ?>
			<a class="widget link" href="mailto:<?php echo $this->user['email'];?>">
               <img src="/_inc/img/icons/mail.png"><?php echo $this->user['email'];?>
            </a>
			<?php } ?>


            <?php {
                if($this->user['bHasAccount']){
                    echo '<div style="text-align:center;font-size:12px; background-color:#f2f2f7;padding:10px;">';

					if($this->user['id']!=$this->escoID){
						echo '<b>Account balance:</b> &euro;' . money($this->user['accountFunds']);
                        echo '<br><br><button onclick="location.href=\'/bank/send-money.php?to='.urlencode(base64_encode($this->user['id'])).'&return='.urlencode(base64_encode($_SERVER['REDIRECT_URL'])).'\';">Send Money</button>';
                    }else {
						echo '<b>Your money:</b> <br><a href="http://www.esco.net/bank/" style="color:green; font-size:16pt;font-weight:bold;">&euro;' . money($this->user['accountFunds']) . '</a>';
					}

                    echo '</div>';
                }
            } ?>


        </div>
        <!-- END OF LEFT SIDEBAR -->

        <div style="float:right;width:670px; ">


            <!-- COMMENTS BEGIN -->

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
                $COMMENTS_TABLE = 'esco_wall_comments';
                $COMMENTS_FOR_ROW = 'wall';
                $COMMENTS_FOR_VALUE = $this->user['id'];

                // How many items to show per page
                $RESULTS_PER_PAGE = 15;
                // How pages to show until the ellipsis (...)
                // Must be an odd number (not evenly divisable by 2. E.g., 1, 3, 5, 7, 9, 11, 13, etc)
                $MAX_PAGE_GROUP = 9;

                include($this->siteDirectory . '/_inc/php/comments.php');
            }
            ?>

            <!-- COMMENTS END -->



        </div>
        <div class="cf"></div>
    </div>
</div>
<?php
        }
        else
        {
            ?>
<?php
{
    //echo mysqli_insert_id($this->link);
}
?>
<div class="widget">
    <h1>User account non-existent</h1>
    The requested user account does not exist.
</div>
            <?php
        }
    }
}

new customPage();
