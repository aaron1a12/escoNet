<?php
class customPage extends page {
    public $title = 'Home';

    public $private = true;
    
    function init() {
        $result = mysqli_query($this->link, "SELECT funds FROM esco_bank_accounts WHERE owner=" . $this->escoID);
        $rows = mysqli_num_rows($result);
        
        if($rows==0){
            
            /*
            $amount = 100;
            
            $availableFunds = floatval(mysqli_fetch_row(mysqli_query($this->link, "SELECT funds FROM esco_bank_accounts WHERE owner=0"))[0]);
            
            $availableFunds = $availableFunds-$amount;
            mysqli_query($this->link, "UPDATE esco_bank_accounts SET `funds`=$availableFunds WHERE owner=0");
            */
            
            $amount = 0;
            
            mysqli_query($this->link, 'INSERT INTO esco_bank_accounts (`owner`, `funds`) VALUES(\''.$this->escoID.'\', '.$amount.')');
            header('Location: /bank/');
            die();
        }
        
    }
}

new customPage();