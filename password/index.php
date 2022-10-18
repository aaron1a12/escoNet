<?php

class customPage extends page {
    public $title = 'Links';
    
    public $errors = array();
    
    function init() {
        

        if($_POST){
            if( !isset($_POST['password']) ){
                array_push($this->errors, 'Bad request');
            }else{
                
                $_POST['password'] = strip_tags(filter_var( $_POST['password'], FILTER_SANITIZE_MAGIC_QUOTES));
            
                if($_POST['password'] == '')
                    array_push($this->errors, 'A password is necessary to secure your account');
                

            
                
                if(count($this->errors)==0){
                    
                    //
                    // Register the user
                    //
                                     
                    $password = sha1( encrypt( sha1( md5($_POST['password']) ) ) );
					
					array_push($this->errors, $password);

                }
                
            }
        }
    }
    
    function content() {
?>
<div class="widget">
    <h1>Generate a password hash</h1>
    <form action="" method="post">
        <table border="0" cellpadding="0" cellspacing="10" class="form" align="center">
            <tr>
                <td>Your password:</td>
                <td><input name="password" type="password" style="width:225px;"></td>
            </tr>
            <tr>
                <td colspan="2" align="right"><button type="submit">Generate</button></td>
            </tr>
        </table>
    </form>
    <?php
        if(count($this->errors)>0){
            echo '<div class="error"><ul>';
            foreach($this->errors as &$error){
                echo '<li>'.$error.'</li>';
            }
            echo '</ul></div>';
        }
    ?>
</div>
<?php
    }
}

new customPage();