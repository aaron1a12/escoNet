<?php
class customPage extends page {
    function init(){
        if($this->loggedIn){

            //$_POST['shoutbox_state'] = 1;
            //$_POST['shoutbox_lastid'] = 343;

            header('Content-type: application/json');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');

            $data = array(
                'updated' => 0,
                'notifications' => array(),
                'shouts' => array(),
                'last_shout' => 0
            );

            /*
              Shoutbox
            */

            if(!isset($_POST['shoutbox_state'])) $_POST['shoutbox_state'] = 0;
            if(!isset($_POST['shoutbox_lastid'])) $_POST['shoutbox_lastid'] = 0;
            
            $shoutboxState = intval($_POST['shoutbox_state']);
            $shoutboxLastId = intval($_POST['shoutbox_lastid']);



            if($shoutboxState==1)
            {
              $shoutResult = mysqli_query($this->link, "SELECT * FROM `esco_shouts` WHERE `id` > $shoutboxLastId ORDER BY `id` DESC LIMIT 200");

              $whileLap = 0;

              while ($shout = mysqli_fetch_row($shoutResult)) {
                $shoutAuthorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$shout[1]."' ") );

                array_push($data['shouts'], array(
                    'author'  => $shoutAuthorInfo[3],
                    'message'  => $shout[3]
                ));

                if($whileLap==0)
                  $shoutboxLastId = $shout[0];
                $whileLap++;
              }

              $data['last_shout'] = $shoutboxLastId;
            }

            /*
              Profile Notification
            */
            mysqli_query($this->link, " UPDATE `esco_user_profiles` SET `heartbeat`= now() WHERE user='".$this->escoID."'; ");

            $data['updated'] = mysqli_fetch_row(mysqli_query($this->link, "SELECT updated FROM esco_user_profiles WHERE user='".$this->escoID."'; "))[0];

            // Notification Example
            array_push($data['notifications'], array(
                'type'  => 0,
                'text'  => "Aaron posted on your wall",
                'user'  => 1
            ));

            array_push($data['notifications'], array(
                'type'  => 1,
                'text'  => "Aaron commented on Funny Pic",
                'user'  => 1
            ));

            array_push($data['notifications'], array(
                'type'  => 2,
                'text'  => "Aaron added a new Funny Pic",
                'user'  => 1
            ));

            print( json_encode( $data ) );
            die();
        }
    }
}

new customPage();
