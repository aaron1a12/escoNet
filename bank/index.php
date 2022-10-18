<?php
class customPage extends page {
    public $title = 'Home';
    //public $pageIsFullscreen = true;
    
    public $bank;

    public $account;
    
    function init() {
        
        $this->account = new stdClass;
        
        $this->bank = new stdClass;
        $this->bank->funds = mysqli_fetch_row(mysqli_query($this->link, "SELECT funds FROM esco_bank_accounts WHERE owner=0"))[0];
		
		
    }
    
    function head(){
?>
<style>
    @import url("http://media.esco.net/fonts/agency/");
</style>
<?php
    }
	

	


	function content() {
	?>
	<div class="widget">
		<h1 style="text-align:center; font-family:Agency; text-transform:uppercase; border-bottom:1px solid #338;">The Escobar Central Bank</h1>    
		<b>Current Bank Funds:</b>
		<?php echo '&euro;'.money($this->bank->funds);?>
	 </div>  

	<div class="widget">
		<?php
			
		if($this->escoID==NULL)
		{
			echo ":(";
		}
		else{
			
			$result = mysqli_query($this->link, "SELECT funds FROM esco_bank_accounts WHERE owner=" . $this->escoID);
			$rows = mysqli_num_rows($result);
			
			if($rows==0){
		?>
		You currently don't have an account with us.
		<a href="new-account.php" class="btn" style="float:right;">Open new account</a>
		<?php
			}
			else
			{
		?>
		<?php
				$this->account->funds = mysqli_fetch_row(mysqli_query($this->link, 'SELECT funds FROM esco_bank_accounts WHERE owner='.$this->escoID))[0];
				
				//
				//$fmt = numfmt_create( 'de_DE', NumberFormatter::CURRENCY );
				//$this->account->funds = numfmt_format_currency($fmt, $this->account->funds, "EUR");
		?>
		<b style="color:#666; font-size:14pt;">My Money</b>
		<div style="border-bottom:1px solid #ccc;margin-top:15px;margin-bottom:25px;"></div>
		<b style="color:#666; font-size:12pt;">Available</b>
		<br>
		<b style="color:#666; font-size:14pt; color:green; font-size:22pt;font-weight:bold;"><?php echo '&euro;'.money($this->account->funds);?> ESC</b>
		<?php
			}
		}
	
		?>
	</div>
	<?php
	}

	
	
	
}

new customPage();