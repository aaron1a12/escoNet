    <div style="background-color:#ddd;position:relative; height:210px;margin-bottom:0px;">
        <div class="black-overlay" style="position:absolute; bottom:0px; left:0px; width:100%; background:url('/_inc/img/overlay.png');">
            <div style="margin:20px;">
                <div style="z-index:100;float:left;font-size:0px;text-align:right;border:1px solid #fff;background:#fff; box-shadow: 0px 0px 24px rgba(0, 0, 0, 0.75);">
                    <a href="<?php echo $this->profileLink;?>" style="font-size:0;"><img src="http://media.esco.net/img/social/<?php echo $this->user['id'];?>/profile_small.jpg" style="width:50px; height:50px;"></a>
                </div>
                <div style="float:left; margin-top:0px; margin-left:20px;">
                    <h2 style="margin-bottom:20px;color:#fff;"><?php echo $this->user['name'] .' ' . $this->user['lastname'];?></h2>
                    <a href="<?php echo $this->profileLink;?>">Profile</a> | <a href="<?php echo $this->user['photoBase'];?>">All Albums</a> | <a href="<?php echo $this->user['photoBase'];?>/favorites">View Favorites</a>
                </div>
                <div class="cf"></div>
            </div>
        </div>
        <?php
        {
            if($this->profile['banner']!=''){
                echo '<img src="http://media.esco.net/img/social/'.$this->user['id'].'/'.$this->profile['banner'].'" style="width:1000px;height:210px; outline: 8px solid rgba(255, 255, 255, 0.5);
transition: outline-offset 0.2s cubic-bezier(0, 0, 0.5, 1) 0s, outline 0.2s cubic-bezier(0, 0, 0.5, 1) 0s;
outline-offset: -8px;">';
            }

            if($this->user['id'] == $this->escoID)
            {
                echo '<a href="/user/photo-uploader/" class="btn" style="position:absolute; top:215px; right:5px;"><img src="/_inc/img/icons/upload-cloud.png" style="vertical-align:middle;margin: 0px 5px">Upload</a>';
            }
        }
        ?>

    </div> 