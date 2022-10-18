<?php

class customPage extends page {
    public $title = 'Social';	
    public $private = false;
    
    public $user;
    
    public $users;

    function init()
    {
        $select = "SELECT * FROM esco_users ORDER BY `name` ASC";
        $result = mysqli_query($this->link, $select);
        
        $this->users = array();
        $this->users[0] = array();
        $this->users[1] = array();     

        
        while ($user = mysqli_fetch_assoc($result)) {
            if(strtolower($user['lastname'])=='escobar' || strtolower($user['lastname'])=='tubella')
                array_push($this->users[0], $user);
            else
                array_push($this->users[1], $user);
            //echo '<div><a href="/user/'. $user['id'] . '/' . urlify($user['name'] . '_' . $user['lastname']). ' ">'.$user['name'].' '. $user['lastname'] . '</a></div>';
        }  
        
    }
    

    
	
    function content() {
?>
<div class="widget">
    <?php if($this->loggedIn) echo '<a class="btn" style="position:absolute; top:0; right:0;" href="'.$this->escoProfileURL.'">My Profile</a>'."\n"; ?>
    
    <h1><img src="/_inc/img/royal.png"> Escobar Users</h1>
    
    
    <?php
    {
        $leftCollumn = ceil(count($this->users[0])/2);
        $rightCollumn = floor(count($this->users[0])/2);
        
        
        echo '<div>';
        echo '<div style="float:left; width:49%;">';
        
        foreach($this->users[0] as &$user)
        {
            if($leftCollumn>0)
            {
                $leftCollumn--;
            }
            elseif($leftCollumn==0)
            {
                echo '</div>';
                echo '<div style="float:right; width:49%;">';
            }
            elseif($leftCollumn < 1)
            {
                $rightCollumn--;
            }
            echo '<a href="/user/'.$user['id'].'/'.urlify($user['name'].' '.$user['lastname']).'" class="widget link profile" style="display:block;">
                                
                                <img src="http://media.esco.net/img/social/'.$user['id'].'/profile_small.jpg" style="float:left;width:48px;height:48px;border-radius:30px;">
                                <div style="float:right; width:360px;">
                                    <span class="name">'.$user['name'].' '.$user['lastname'].'</span><br>
                                    <span style="color:green;"></span>
                                    
                                </div>
                                <div class="cf"></div>
                            </a>';
            
        }
        
        echo '</div>';
        echo '<div class="cf"></div>';
        echo '</div>';
    }
    ?>
    
    <br><br>
    
    <h1><img src="/_inc/img/user.png"> Other Users</h1>
    
    
    <?php
    {
        $leftCollumn = ceil(count($this->users[1])/2);
        $rightCollumn = floor(count($this->users[1])/2);
        
        
        echo '<div>';
        echo '<div style="float:left; width:49%;">';
        
        foreach($this->users[1] as &$user)
        {
            if($user['id']!=0)
            {
                if($leftCollumn>0)
                {
                    $leftCollumn--;
                }
                elseif($leftCollumn==0)
                {
                    echo '</div>';
                    echo '<div style="float:right; width:49%;">';
                }
                elseif($leftCollumn < 1)
                {
                    $rightCollumn--;
                }
                echo '<a href="/user/'.$user['id'].'/'.urlify($user['name'].' '.$user['lastname']).'" class="widget link profile" style="display:block;">

                                    <img src="http://media.esco.net/img/social/'.$user['id'].'/profile_small.jpg" style="float:left;width:48px;height:48px;border-radius:30px;">
                                    <div style="float:right; width:360px;">
                                        <span class="name">'.$user['name'].' '.$user['lastname'].'</span><br>
                                        <span style="color:green;"></span>

                                    </div>
                                    <div class="cf"></div>
                                </a>';
            }
            
        }
        
        echo '</div>';
        echo '<div class="cf"></div>';
        echo '</div>';
    }
    ?>
</div>
<?php
    }

}

new customPage();