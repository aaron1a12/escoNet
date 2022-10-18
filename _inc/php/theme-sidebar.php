<div class="widget nopadding">
    <div style="float:left;border-right: 4px solid #eeeef5; width:200px;">
        <?php
        $sunrise = date('g:i A', date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP ) );
        $sunset = date('g:i A', date_sunset(time(), SUNFUNCS_RET_TIMESTAMP ) );
        ?>
        <table style="font-family:'Clavika'; color:#333388;margin:10px;" cellpadding="0" cellspacing="0">
            <tr>
                <td rowspan="3"><img src="/_inc/img/sun.gif" style="margin-right:10px;"></td>
            </tr>
            <tr>
                <td style="padding-right:10px">Sunrise:</td>
                <td style="text-align:right;"><b><?php echo $sunrise;?></b></td>
            </tr>
            <tr>
                <td>Sunset:</td>
                <td style="text-align:right;"><b><?php echo $sunset;?></b></td>
            </tr>
        </table>
    </div>
    <div style="margin:10px; float:right; text-align:right; width:150px; font-family:'Clavika'; color:#333388;">
            <a href="/time/" style="color:#333388;text-decoration:underline; text-decoration-style:dotted;"><?php echo date('l,') . '<br>' . date('F jS, Y');?></a>
    </div>
    <div class="cf"></div>
</div>



<div class="widget nopadding" style="overflow:hidden;">
    <div style="font-family:'Clavika'; color:#333388; border-bottom:4px solid #eeeef5;padding:8px;">Trending Now</div>
    <?php
    {
        $select = "SELECT * FROM esco_trending ORDER BY `id` DESC";
        $result = mysqli_query($this->link, $select);


        echo '<table class="trendingNow">';
        echo '<tr>';
        $tRow=0;
        while ($row = mysqli_fetch_row($result)) {
            $tRow++;

            if($tRow==5) { echo '</tr><tr>'; $tRow=1; }

            echo '<td>'.$row[1].'</td>';
        }
        echo '</tr>';
        echo '</table>';
    }
    ?>
</div>



<div class="widget nopadding">
    <div style="font-family:'Clavika'; color:#333388; border-bottom:4px solid #eeeef5;padding:8px;">Upcoming Events</div>
    <div class="paddedContent" style="padding:10px;">
    <?php
    {
        $upcoming = array(
			array('New york flight', 'september 26, 2019', false)
        );

        foreach($upcoming as &$event)
        {

            // snappedToday forces the current time to always be 12:00 AM
            $snappedToday = strtotime(date('F j, Y') . ', 12:00 AM');
            $date = strtotime($event[1] . ' 12:00 AM');

            $remainingTime = $date - $snappedToday;

            $daysRemaining = ceil($remainingTime / 86400);

            //More accurate but less familiar
            //$daysRemaining = floor($remainingTime / 86400);

            $tail = ''; if($event[2]) $tail = ', at sundown';

            $s = 's';
            if($daysRemaining==1) $s = '';

            echo '<div style="padding-bottom:15px;font-size:9pt;">' . $event[0] . ', <b>'.$daysRemaining . '</b> day'.$s.' left ('.date('l, F jS', $date). $tail . ')</div>';
        }

    }
    ?>
    </div>
</div>

<div class="widget nopadding">
    <div style="font-family:'Clavika'; color:#333388; border-bottom:4px solid #eeeef5;padding:8px;">Shoutbox!</div>
    <div style="padding:5px;">
      <form id="shout_form" action="" method="POST">
          <div class="commentBox" id="shoutbox">
            <div class="shout-tmp"> <?php if($this->escoID==NULL){echo 'Please sign in to see shouts!';}else{echo 'Loading latest shouts...';};?></div>
          </div>
		 
          <input id="shout_msg" name="shout_msg" style="width:100%;padding:0;font-family:Roboto;resize:none;">
      </form>
      <script>
	  
      esco.shoutbox.active = 1;

      $("#shout_form").submit(function(event){
        var shoutInput = document.getElementById('shout_msg');

        var shoutBox = document.getElementById('shoutbox');

        $( shoutBox ).prepend('<div class="shout-tmp"><b><?php echo addslashes($this->escoName); ?>: </b>' + shoutInput.value + '</div>');
        shoutBox.scrollTop = '0px';

        /*
        var newMessage = document.createElement('DIV');
        newMessage.innerHTML = 'You: ' + shoutInput.value;
        shoutBox.appendChild(newMessage);
        shoutBox.scrollTop = shoutBox.scrollHeight;
        */
		
        $.ajax({
            type: "POST",
            url : "/api/shout.php",
            dataType : 'json',
            data: { message:shoutInput.value }
        }).done(function(data){
        });
        shoutInput.value = '';
        event.preventDefault();
      });

      function addShouts(shouts)
      {
        var shoutBox = document.getElementById('shoutbox');
        shoutLength = shouts.length;

        for (var i = 0; i < shoutLength; i++) {
          $( shoutBox ).prepend('<div><b>'+ shouts[ i ].author +': </b>' + shouts[ i ].message + '</div>');
          shoutBox.scrollTop = '0px';
        }

        $( '.shout-tmp' ).remove();
      }
      </script>
    </div>
</div>

<!--
<div class="widget nopadding">
    <div style="font-family:'Clavika'; color:#333388; border-bottom:4px solid #eeeef5;padding:8px;">Funny Pic of the Day</div>
    <div class="paddedContent" style="font-size:0px;padding-bottom:30px;">
        <a href="/funny/"><img src="http://www.esco.net/funny/pic_of_the_day_thumb.jpg?rd=<?php echo date('z');?>"></a>
    </div>
</div>
-->

<div class="widget nopadding">
    <div style="font-family:'Clavika'; color:#333388; border-bottom:4px solid #eeeef5;padding:8px;">Funny Pic of the Day</div>
    <div class="paddedContent" style="font-size:12px;padding-bottom:30px;">
        <?php
        {
            $today = date('z');
            $currentFunnyPic = mysqli_fetch_row(mysqli_query($this->link, 'SELECT photo,day FROM `esco_fpotd_current` WHERE `id`=0'));
            $currentFunnyPicID = $currentFunnyPic[0];


            function fetchRandom( $link )
            {
                return mysqli_fetch_row( mysqli_query($link, "
                    SELECT `photo` FROM esco_photo_album_assoc WHERE `album`=1 ORDER BY rand() LIMIT 1
                ") );
            }

            if($currentFunnyPic[1]!=$today){

                //
                // Fetch new funny pic
                //

                // Make a list of the recently shown

                $recentlyShown = array();
                $recentPhotoR = mysqli_query($this->link, 'SELECT photo FROM `esco_fpotd_past`');
                while($recentPhoto=mysqli_fetch_row($recentPhotoR)[0]){
                    array_push($recentlyShown, $recentPhoto);
                }

                // Keep fetching a random pic as long as they are not
                // in the recently-shown list

                $newPic = $recentlyShown[0];
                while( in_array( $newPic, $recentlyShown ) )
                {
                    $newPic = fetchRandom( $this->link )[0];
                }

                $currentFunnyPicID = $newPic;

                // Add it to the recently shown db table and remove the oldest

                $oldestInList = mysqli_fetch_row(mysqli_query($this->link, 'SELECT `photo` FROM `esco_fpotd_past` ORDER BY `lastshown` ASC LIMIT 1'))[0];
                mysqli_query($this->link, 'DELETE FROM `esco_fpotd_past` WHERE `photo`='.$oldestInList);
                mysqli_query($this->link, "INSERT INTO `esco_fpotd_past` (`photo`) VALUES ($currentFunnyPicID)");

                // Update the current funny pic

                mysqli_query($this->link, "UPDATE `esco_fpotd_current` SET `photo`=$currentFunnyPicID, `day`=$today WHERE `id`=0");
            }


            $fpotd_img = mysqli_fetch_row(mysqli_query($this->link, 'SELECT author,name,format,title FROM `esco_photos` WHERE id='.$currentFunnyPicID));

            $format = '.jpg';
            switch($fpotd_img[2]){
                case 0:
                    $format = '.jpg';
                    break;
                case 1:
                    $format = '.png';
                    break;
                case 2:
                    $format = '.gif';
                    break;
            }

            $imgSrc = 'http://media.esco.net/img/social/photos/'.$fpotd_img[0].'/'.$fpotd_img[1].'_l'.$format;

            $imgAuthor = mysqli_fetch_row( mysqli_query($this->link, "SELECT name,lastname FROM esco_users WHERE id='".$fpotd_img[0]."' ") );

            $imgLink = '/user/'.$fpotd_img[0].'/'.urlify($imgAuthor[0].' '.$imgAuthor[1]).'/photos/'.$currentFunnyPicID.'/'.urlify($fpotd_img[3]);
            echo '<a href="'.$imgLink.'"><img src="'.$imgSrc.'" style="width:100%;"></a>';
        }
        ?>
<!--        <a href="/funny/"><img src="http://www.esco.net/funny/pic_of_the_day_thumb.jpg?rd=<?php echo date('z');?>"></a>-->
    </div>
</div>

<div class="widget nopadding">
    <div style="font-family:'Clavika'; color:#333388; border-bottom:4px solid #eeeef5;padding:8px;">Whose month is it?</div>
    <div class="paddedContent" style="font-size:0px;padding-bottom:30px; text-align:center;">
        <?php
        {
            $id = 1;
            switch(date('n'))
            {
                case 1:
                    $id = 4;
                    break;
                case 2:
                    $id = 2;
                    break;
                case 3:
                    $id = 1;
                    break;
                case 4:
                    $id = 3;
                    break;
                case 5:
                    $id = 4;
                    break;
                case 6:
                    $id = 2;
                    break;
                case 7:
                    $id = 1;
                    break;
                case 8:
                    $id = 3;
                    break;
                case 9:
                    $id = 4;
                    break;
                case 10:
                    $id = 2;
                    break;
                case 11:
                    $id = 1;
                    break;
                case 12:
                    $id = 3;
                    break;
            }


            $monthlyUserInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='$id';") );
            $link = '/user/'.$id.'/'.urlify($monthlyUserInfo[3] . ' ' . $monthlyUserInfo[4]);

            echo '<a href="'.$link.'"><img src="http://media.esco.net/img/social/'.$id.'/profile_large.jpg"></a>';
        }
        ?>
    </div>
</div>



<div class="widget nopadding">
        <div style="font-family:'Clavika'; color:#333388; border-bottom:4px solid #eeeef5;padding:8px;">Latest Poll</div>
        <div class="paddedContent" style="padding:20px;">
        <?php
        {
            $selectQuery = "SELECT * FROM esco_polls ORDER BY id DESC LIMIT 1";
            $result = mysqli_query($this->link, $selectQuery);
            $numrows = mysqli_num_rows($result);

            if($numrows!=0)
            {

                        $poll = mysqli_fetch_assoc($result);

                        $pollChoices = json_decode($poll['choices']);
                        $pollChoicesCount = count($pollChoices);

                        $pollURL = '/polls/'. $poll['id'] . '/' . urlify($poll['title']);

                        //$commentAuthorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$poll['author']."' ") );
                        echo '<div style="float:left;margin-right:20px;"><img src="http://media.esco.net/img/social/'.$poll['author'].'/profile_small.jpg"></div><form action="/polls/'.$poll['id'].'/'.urlify($poll['title']).'" method="POST" style="float:left; width:270px;"><h3 style="font-weight:bold; margin-bottom:10px;cursor:pointer;" onclick="location.href=\''.$pollURL.'\';">'.$poll['title'].'</h3>';


                        $pollTotalResults = mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_poll_results WHERE poll='".$poll['id']."';"))[0];

                        if(mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_poll_results WHERE poll='".$poll['id']."' AND author='".$this->escoID."';"))[0]>0 || $this->loggedIn==false)
                        {
                            //
                            // Show Results
                            //

                            for($i=0; $i<$pollChoicesCount; $i++)
                            {
                                echo '<b>'.$pollChoices[$i].'</b>';
                                $number = mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_poll_results WHERE poll='".$poll['id']."' AND answer='$i';"))[0];
                                $percentage = round( ($number/$pollTotalResults)*100, 2 );

                                if($number==1) $votes = 'vote'; else $votes = 'votes';

                                echo '<div style="background-color:#ccc; width:100%; height:4px;">';
                                echo '<div style="width:'.$percentage.'%; background-color:#7722bb; height:4px;"></div>';
                                echo '</div>';


                                echo "<span style=\"font-size:8pt;\"> ($percentage%, $number $votes)</span>";

                                echo '<br>';


                            }
                            echo '<br><br>';

                            echo '<a href="/polls/" class="btn" style="position:absolute; right:0; bottom:0;">More Polls</a>';
                        }
                        else
                        {
                            //
                            // Show Form
                            //

                            for($i=0; $i<$pollChoicesCount; $i++)
                            {
                                echo '<label style="font-size:10pt; cursor:pointer;"><input type="radio" name="choice" value="'.$i.'" style="height:auto;vertical-align:middle;cursor:pointer;"> &nbsp;'.$pollChoices[$i].'</label><br>';
                            }
                            echo '<div style="position:absolute; right:0; bottom:0;">';
                            echo '<button>Vote</button>&nbsp;';
                            echo '<a href="/polls/" class="btn" style="padding:10px;">More Polls</a>';
                            echo '</div><br><br>';
                        }

                        echo '</form><div class="cf"></div>';
            }
        }
        ?>
        </div>
</div>

<div class="widget nopadding">
    <div style="font-family:'Clavika'; color:#333388; border-bottom:4px solid #eeeef5;padding:8px;">Wild Montage Studio</div>
    <div class="paddedContent" style="padding-bottom:30px;">
        <div style="text-align:center;">
            Stay in touch with your fellow co-workers
            <br><br><br>
            <a class="btn" href="http://studio.wildmontage.com/download/">Download Studio</a>
            <br><br>
        </div>
    </div>
</div>
