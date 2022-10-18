<?php

class customPage extends page {
    public $title = 'Edit Home Sections';	
    public $private = true;
    
    public $errors = array();
    
    public $poll;

    function init()
    {
        $this->poll = new stdClass;
        $this->poll->title = '';
        
        if($_POST)
        {
            if(!isset($_POST['title']) || !isset($_POST['choices'])){
                array_push($this->errors, 'Bad request');
            }
            else
            {
                $choices = explode( '%|%', strip_tags($_POST['choices']) );
                array_pop($choices);
                
                $title = strip_tags(filter_var( $_POST['title'], FILTER_SANITIZE_MAGIC_QUOTES));

                if(count($choices)<2)
                    array_push($this->errors, 'You need at least two choices for voting polls');

                if($title=='')
                    array_push($this->errors, 'Your poll needs a title');

                
                if(count($this->errors)==0)
                {
                    $choicesJSON = filter_var(json_encode($choices), FILTER_SANITIZE_MAGIC_QUOTES);
                    $author = $this->escoID;
                    $time = time();
                    
                    $insertQuery = "INSERT INTO esco_polls (author, time, title, choices) VALUES ('$author','$time','$title','$choicesJSON') "; ;
                    mysqli_query($this->link, $insertQuery);
                    
                    $pollId = mysqli_insert_id($this->link);
                    
                    logAction( $this, ACTION_NEW_POLL, $title, '/polls/'.$pollId.'/'.urlify($title) );
                    
                    header("Location: /user/polls/");
                    die();
                }

            }
        }
    }
    
    function head(){
?>
<script>

    function makeid()
    {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for( var i=0; i < 20; i++ )
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }
    
    function refreshChoicesString()
    {
        var choicesString = '';
        var children = document.getElementById('choices').children;
        var childrenLength = document.getElementById('choices').children.length;
        for(var i=0; i<childrenLength; i++)
        {
            choicesString += children[i].getAttribute('data-value') + "%|%";
        }
        
        document.getElementById('choicesHiddenString').value = choicesString;        
    }
    
    function removeChoice( id )
    {
        e = document.getElementById( id );
        e.parentNode.removeChild(e);
        
        refreshChoicesString();
    }
    
    function addChoice()
    {
        var choice = document.getElementById('choice_input').value;
        var list = document.getElementById('choices');
        
        var li = document.createElement('LI');
        li.id = makeid();
        li.innerHTML = choice + ' - <a href="#" onclick="removeChoice(\''+li.id+'\');">Remove</a>';
        li.setAttribute('data-value', choice);
        
        list.appendChild( li );
        
        document.getElementById('choice_input').value = '';
        refreshChoicesString();
    }
</script>
<?php
    }

    function content() {
?>
<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>
<div class="widget" style="position:relative;">
    <h1>New Poll</h1>
    <form action="" method="POST">
        <table class="table">
            <tr>
                <td style="width:200px;">Poll Question</td>
                <td><input name="title" value="<?php echo $this->poll->title;?>" size="60" max maxlength="255"></td>
            </tr>
            <tr>
                <td style="vertical-align:top;">Available Choices</td>
                <td>
                    <ul id="choices">
                    </ul>
                    <input id="choice_input" value="" size="35" maxlength="35"><button type="button" onclick="addChoice();">Add Choice</button>
                </td>
            </tr>
        </table>
        <input type="hidden" name="choices" value="" id="choicesHiddenString">
        
        <button>Save</button>
    </form> 
    
    
           <?php
            if(count($this->errors)>0){
                echo '<div class="error"><ul>';
                foreach($this->errors as &$error){
                    echo '<li>'.$error.'</li>';
                }
                echo '</ul></div><br><br>';
            }
        ?>
    
</div>
<?php
    }
}

new customPage();