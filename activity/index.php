<?php

class customPage extends page {
    public $title = 'Recent Activity';
    
    
    function init()
    {
    }
    
    function content() {
?>
<div class="widget">
    <h1>Recent Global Activity on EscoNet</h1>
    

    
    
    <?php
        
        $result = mysqli_query($this->link, 'SELECT * FROM esco_user_activity ORDER BY id DESC LIMIT 20');

        /*
        $string = '/blog/2015/36/Amazing_places_to_visit_and_why_we_should_travel_soon';
        
        $urlGZ = gzcompress($string, 9);
        
        echo 'Unpacked: '.unpack('H*', $urlGZ )[1].'<br>';
        
        
        $urlGZ = gzuncompress( pack('H*', unpack('H*', $urlGZ )[1] ) );
        
        
        
        
        echo $urlGZ;
        //
        //pack('H*', '78DAD32F2D4E2DD23736D277294FACCC4B8DF74DCD29CBCC030055AC07E4')
        //
        echo '<hr><BR><BR>';
        //show(unpack('H*', $binary));
        */
        while ($row = mysqli_fetch_assoc($result)) {
            $row['action'] = intval($row['action']);

            
            if($row['url']!=null)
                $url = gzuncompress( pack('H*', unpack('H*', $row['url'])[1] ) );
            else
                $url = 'javascript:void(0);';
            
            $authorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$row['user']."';") );
            //unpack('H*', $row['action'])

            echo '<a class="widget blog link" href="'.$url.'">';
            
            
            switch($row['action']){
                case ACTION_COMMENT:
                    $description = '<img src="/_inc/img/icons/pencil.png" style="vertical-align:middle; margin-right:10px;">';
                    $description .= '[%USER%] commented on "[%SUBJECT%]"';
                break;
                    
                case ACTION_BLOG_POST:
                    $description = '<img src="/_inc/img/icons/document.png" style="vertical-align:middle; margin-right:10px;">';
                    $description .= 'New Blog Post "[%SUBJECT%]" by [%USER%]';
                break;    
                    
                case ACTION_NEW_POLL:
                    $description = '<img src="/_inc/img/icons/poll.png" style="vertical-align:middle; margin-right:10px;">';
                    $description .= 'New Voting Poll "[%SUBJECT%]" by [%USER%]';
                break;      
                    
                case ACTION_PHOTO_UPLOAD:
                    $description = '<img src="/_inc/img/icons/picture.png" style="vertical-align:middle; margin-right:10px;">';
                    $description .= '[%USER%] just uploaded a new photo';
                break;      
                    
                case ACTION_PHOTO_FAV:
                    $description = '<img src="/_inc/img/icons/fav.png" style="vertical-align:middle; margin-right:10px;">';
                    $description .= '[%USER%] favorited "[%SUBJECT%]"';
                break;   
                    
                case ACTION_PHOTO_NOTE:
                    $description = '<img src="/_inc/img/icons/note.png" style="vertical-align:middle; margin-right:10px;">';
                    $description .= '[%USER%] added a note to "[%SUBJECT%]"';
                break;  
                
                case ACTION_MONEY_TRANSFER:
                    $description = '<img src="/_inc/img/icons/money-bag.png" style="vertical-align:middle; margin-right:10px;">';
                    $description .= '[%USER%] sent money to [%SUBJECT%]';
                break;  
                    
                default:
                    $description = '[%USER%] did something on "[%SUBJECT%]"';
            }
                    
            
            
            $description = str_replace('[%USER%]', $authorInfo[3].' '.$authorInfo[4], $description);
            $description = str_replace('[%SUBJECT%]', $row['subject'], $description);
                
            echo $description;
            echo '<small>&nbsp;| '.escoDate($row['time']).'</small>';
            echo '</a>';
        }
    ?>
    
    <br><br><br>
    
    <h3>Top Ten Most Active Users</h3>
    <?php {
        $query = 'SELECT user, COUNT(*) c FROM esco_user_activity WHERE `user`!=0 GROUP BY user ORDER BY c DESC LIMIT 10';
        
        $result = mysqli_query($this->link, $query);
        
        $biggest = 0;
        
        while($user=mysqli_fetch_row($result)){
    ?>
    <?php if($biggest==0) $biggest = $user[1]; ?>
    <?php $userData = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$user[0]."' ") ); ?>
    <div class="widget">
        <b><?php echo $userData[3];?></b>
        <?php
            $percentage = round( ($user[1]/$biggest)*100, 2 );

            echo '<div style="background-color:#ccc; width:100%; height:4px;">';
            echo '<div style="width:'.$percentage.'%; background-color:#7722bb; height:4px;"></div>';
            echo '</div>';
        ?>
    </div>
    
    <?php
        }
    } ?>    
</div>
<?php
    }
}

new customPage();