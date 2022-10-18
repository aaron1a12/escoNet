<?php
class customPage extends page {
    public $title = 'Send Money';

    public $private = true;
    
    public $bHasAccount = false;
    public $errors = array();
    
    public $funds;
    public $transfer;
    public $recipient;
    
    
    function init() {
        if(!isset($_GET['to'])) die();
        
        $result = mysqli_query($this->link, "SELECT funds FROM esco_bank_accounts WHERE owner=" . $this->escoID);
        $rows = mysqli_num_rows($result);
        
        $this->recipient = new stdClass;

        if($rows!=0) {
            $this->bHasAccount = true; 
            
            $this->recipient->id = intval(base64_decode($_GET['to']));
            $this->recipient->name = mysqli_fetch_row( mysqli_query($this->link, "SELECT name,lastname FROM esco_users WHERE `id`='".$this->recipient->id."' ") );
        
            $this->funds = mysqli_fetch_row($result)[0];
            
            if($_POST){
                
                $this->transfer = floatval($_POST['amount']);
                
                if(isset($_POST['amount']) && isset($_POST['send'])){
                    $amount = $this->transfer;
                    $to = $this->recipient->id;
                    $from = $this->escoID;
                    
                    $availableFunds = floatval(mysqli_fetch_row(mysqli_query($this->link, "SELECT funds FROM esco_bank_accounts WHERE owner=$from"))[0]);
                    
                    if($availableFunds<$amount) die('Not enough funds');
                    
                    $availableFunds = $availableFunds-$amount;
                    mysqli_query($this->link, "UPDATE esco_bank_accounts SET `funds`=$availableFunds WHERE owner=$from");
                    
                    $toFunds = floatval(mysqli_fetch_row(mysqli_query($this->link, "SELECT funds FROM esco_bank_accounts WHERE owner=$to"))[0]);
                    
                    $toFunds = $toFunds+$amount;
                    mysqli_query($this->link, "UPDATE esco_bank_accounts SET `funds`=$toFunds WHERE owner=$to");
                    
                    logAction($this, ACTION_MONEY_TRANSFER, $this->recipient->name[0].' '.$this->recipient->name[1], '');
                    
                    
                    if(isset($_GET['return']))
                    {
                        $back = base64_decode($_GET['return']);
                        header("Location: $back");
                    }
                    
                    die('Sent.');
                }
                
            }
            
        }
        
        
        
    }
    
    function content(){
    ?>
    <?php if($this->bHasAccount==true && !isset($_POST['validate']) && !isset($_POST['send']) ) { ?>
    <div class="widget">
        
    <h1>Send Money</h1>
    <form action="" method="POST">
        <table cellspacing="4" class="table">
            <tr>
                <td>To:</td>
                <td>&nbsp;</td>
                <td><?php echo $this->recipient->name[0].' '.$this->recipient->name[1]; ?></td>
            </tr>
            <tr>
                <td>Amount:</td>
                <td></td>
                <td><input name="amount"></td>
            </tr>
            <tr>
                <td>Available funds:</td>
                <td></td>
                <td><?php echo '&euro;'.money($this->funds);?></td>
            </tr>
        </table>


        <br><br>
        <button name="validate">Validate</button>
    </form>
    
    <?php
        if(count($this->errors)>0){
            echo '<br><br><div class="error"><ul>';
            foreach($this->errors as &$error){
                echo '<li>'.$error.'</li>';
            }
            echo '</ul></div>';
        }
    ?>
        
    </div>
    <?php }elseif($this->bHasAccount==true && isset($_POST['validate'])){ ?>
    <div class="widget">
        <h1>Confirm Transfer</h1>
        You are about to send <b><?php echo '&euro;'.money($this->transfer);?></b> to <?php echo $this->recipient->name[0].' '.$this->recipient->name[1]; ?></b>

        <form action="" method="POST">
            <input name="amount" type="hidden" value="<?php echo $this->transfer;?>">
            
            <br><br>
            <button name="send">Send Now</button>
        </form>
    </div>
    <?php }else{ ?>
    <div class="widget">
        <h1>No account</h1>
        You don't have an account to send money from! Open one <a href="/bank/new-account.php">here</a>.
    </div>
    <?php } ?>
    <?php
    }    
}

new customPage();