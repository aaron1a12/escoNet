<?php

class customPage extends page {
    public $title = 'User Account';	
    public $private = true;
    
    public $profile;
    
    function init()
    {
        if($_POST){
            
            $yyyy = intval($_POST['yyyy']);
            $mm = intval($_POST['mm']);
            $dd = intval($_POST['dd']);
            
            $birth = $yyyy . '-' . $mm . '-' . $dd;
            
            $tagline = strip_tags(filter_var( $_POST['tagline'], FILTER_SANITIZE_MAGIC_QUOTES));
			
			$feet = intval($_POST['height_feet']);
			$inches = floatval($_POST['height_inches']);
			
			// Convert to CM
			$height = ($feet * 30.48) + ($inches * 2.54);
			
			
			
			$weight = floatval($_POST['weight']);
            
            //echo $weight;
            
            $maxChar = 35;

            if(strlen($tagline)>$maxChar)
                $tagline = substr($tagline,0, $maxChar) . '...';         
            
            $query = "UPDATE esco_user_profiles SET birth='$birth',tagline='$tagline',height='$height',weight='$weight' WHERE user='".$this->escoID."';";
            mysqli_query($this->link, $query);
			
			
			$query = "insert into esco_fat(user, date, weight) values('".$this->escoID."', '".date('Y-m-d')."', '$weight') ON DUPLICATE KEY UPDATE weight='$weight';";
			mysqli_query($this->link, $query);
        }
        
        
        $this->profile = mysqli_fetch_assoc(mysqli_query($this->link,
            "SELECT * FROM esco_user_profiles WHERE user='".$this->escoID."';"
        ));
        
        if($this->profile['birth']==NULL)
            $this->profile['birth'] = '1800-00-00';
        
        $birth = explode('-', $this->profile['birth']);
        
        $this->profile['yyyy'] = $birth[0];
        $this->profile['mm'] = $birth[1];
        $this->profile['dd'] = $birth[2];
		
		$this->height = floatval($this->profile['height']);
		$this->weight = floatval($this->profile['weight']);
    }
    
	
    function content() {
?>

<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>
<div class="widget" style="position:relative;">    
    <h1>Profile Information</h1>

    <form action="" method="POST">
        <table class="table">
            <tr>
                <td>Tagline</td>
                <td><input name="tagline" value="<?php echo $this->profile['tagline'];?>" size="35" maxlength="35"></td>
            </tr>		
            <tr>
                <td style="width:200px;">Birth Date (YYYY-MM-DD)</td>
                <td><input name="yyyy" value="<?php echo $this->profile['yyyy'];?>" size="4" max maxlength="4" style="text-align:center;"><input name="mm" value="<?php echo $this->profile['mm'];?>" size="2" maxlength="2" style="text-align:center;"><input name="dd" value="<?php echo $this->profile['dd'];?>" size="4" maxlength="2" style="text-align:center;"></td>
            </tr>
            <tr>
                <td style="width:200px;">Height</td>
                <td>
					<?php
					
					$inches = $this->height/2.54;
					
					$feet = intval($inches/12);
					
					$feetInches = $feet * 12;
					$inches = round($inches - $feetInches, 2);
					
					//$inches = $inches - ();
					
					//$inches = $inches%12;
					
					?>
					<select name="height_feet">
						<option value="4" <?php if($feet==4)echo 'selected="selected"';?>>4 ft</option>
						<option value="5" <?php if($feet==5)echo 'selected="selected"';?>>5 ft</option>
						<option value="6" <?php if($feet==6)echo 'selected="selected"';?>>6 ft</option>
                    </select>
					<input name="height_inches" value="<?php echo $inches?>" style="width: 20px;">
					<?php /*
					<select name="height_inches">
						<option value="0" <?php if($inches==0)echo 'selected="selected"';?>>0</option>
						<option value="0.5" <?php if($inches==0.5)echo 'selected="selected"';?>>0.5</option>
						<option value="1" <?php if($inches==1)echo 'selected="selected"';?>>1</option>
						<option value="1.5" <?php if($inches==1.5)echo 'selected="selected"';?>>1.5</option>
						<option value="2" <?php if($inches==2)echo 'selected="selected"';?>>2</option>
						<option value="2.5" <?php if($inches==2.5)echo 'selected="selected"';?>>2.5</option>
						<option value="3" <?php if($inches==3)echo 'selected="selected"';?>>3</option>
						<option value="3.5" <?php if($inches==3.5)echo 'selected="selected"';?>>3.5</option>
						<option value="4" <?php if($inches==4)echo 'selected="selected"';?>>4</option>
						<option value="4.5" <?php if($inches==4.5)echo 'selected="selected"';?>>4.5</option>
						<option value="5" <?php if($inches==5)echo 'selected="selected"';?>>5</option>
						<option value="5.5" <?php if($inches==5.5)echo 'selected="selected"';?>>5.5</option>
						<option value="6" <?php if($inches==6)echo 'selected="selected"';?>>6</option>
						<option value="6.5" <?php if($inches==6.5)echo 'selected="selected"';?>>6.5</option>
						<option value="7" <?php if($inches==7)echo 'selected="selected"';?>>7</option>
						<option value="7.5" <?php if($inches==7.5)echo 'selected="selected"';?>>7.5</option>
						<option value="8" <?php if($inches==8)echo 'selected="selected"';?>>8</option>
						<option value="8.5" <?php if($inches==8.5)echo 'selected="selected"';?>>8.5</option>
						<option value="9" <?php if($inches==9)echo 'selected="selected"';?>>9</option>
						<option value="9.5">9.5</option>
						<option value="10">10</option>
						<option value="10.5">10.5</option>
						<option value="11">11</option>
						<option value="11.5">11.5</option>
					</select>
					*/
					?>
					in
				</td>
			</tr>
			<tr>
                <td style="width:200px;">Weight</td>
                <td>
					<input name="weight" value="<?php echo $this->weight;?>" style="width: 50px;"> lbs
				</td>
            </tr>			

        </table>
        
        <button>Save</button>
    </form>    
</div>
<?php
    }
}

new customPage();