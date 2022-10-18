<?php

class customPage extends page {
    public $title = 'Morse Code Generator';
	
	public $morseCode;
	public $morseDecoded;
	
	public $morseTable;
	public $conversionTable;	
	
	function init() {
		if(isset($_GET['download'])){
			$file = '/home/pi/www/media.esco.net/_httpdocs/morse/out/'. $_GET['download'];
			if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				readfile($file);
			}
			exit();
		}
		
		$this->conversionTable = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');		
		$this->morseTable = array('._','_...','_._.','_..','.','.._.','__.','....','..','.___','_._','._..','__','_.','___','.__.','__._','._.','...','_','.._','..._','.__','_.._','_.__','__..','_____','.____','..___','...__','...._','.....','_....','__...','___..','____.');
		
		
		
		if(!(isset($_POST['morse']) && $_POST['morse']!='')) {
			$_POST['morse'] = '';
		}else{
			$_POST['morse'] = strip_tags($_POST['morse']);
			
			$_POST['morse'] = preg_replace('/[^._ ]/', '', $_POST['morse']);
			
			$morse = explode(' ', $_POST['morse']);
			
			
			foreach($morse as &$morseChar) {
				$key = array_search($morseChar, $this->morseTable);
				if($key!==false){
					$morseChar = $this->conversionTable[$key];
				}
			}
			
			foreach($morse as &$morseChar) {
				if($morseChar=='')
					$morseChar = ' ';
			}
			
			//echo '<pre>';
			//var_dump($morse);
			//echo '</pre>';
			
			$this->morseDecoded = implode('', $morse);
			//$this->morseDecoded = preg_match_all('/([.-])+/', $_POST['morse'], $morseMatches);
			
			//$this->morseCode = str_replace($this->morseTable, $this->conversionTable, $_POST['morse']);
/*
			$nummatches=count($morseMatches[0]);
			
			for( $i=0; $i<$nummatches; $i++ )
			{
				echo 'Match:'.$morseMatches[0][$i].'<br>';
			}
			
			/*
			
			
			$regEx = '/[.-]+/g';
			
			$morse = $_POST['morse'];
			
			//$startNumber
			$limiterNumber = 1;
			
			$bFoundMatch = false;
			$lastMatch = '';
			$bContinue = true;
			
			$loop = 0;
			
			
			
			while($bContinue) {
				$charTest = substr($morse, $limiterNumber-1, $limiterNumber);
				
				echo 'Testing ->'.$charTest.'<br><br>';
				
				$key = array_search($charTest, $this->morseTable);
				if($key!==false){
					$bFoundMatch = true;
					$lastMatch = $this->conversionTable[$key];
					
					echo '<br>Found mapping for '.$lastMatch.'...<br>';
					
					$limiterNumber++;
					// Chop beginning
					$charTest = substr($charTest, 1, 2);
					echo 's'.$charTest;
				}else{
					echo '<br>No match found...<br>';
					// We didn't find a match this time so revert to the last match found
					
					if($bFoundMatch){
						$bContinue = false;
						$this->morseDecoded = $lastMatch;
						
					}else{
						// We never found a match?
					}
				}
				
				
				
				if($loop>10)
					$bContinue = false;
				else
					$loop++;
			}
			

			
			/*
			
			
			
			// Check if the $charTest can map to a letter

			$key = array_search($charTest, $this->morseTable);
			
			echo 'Search result: '.var_dump($key).'<br>';

			if($key!==false){
				echo $this->conversionTable[$key];
			}
			
			echo '<br>';//
			
			die('DECODED: '.$this->morseDecoded);
			*/
			
			// Final
			//$this->morseDecoded = $_POST['morse'];
		}
		
	
	
		if(!(isset($_POST['message']) && $_POST['message']!='')) {
			$_POST['message'] = '';
		}else{
			$_POST['message'] = strtoupper(strip_tags($_POST['message']));
			$_POST['message'] = preg_replace('/[^A-Za-z0-9 ]/', '', $_POST['message']);
			//$_POST['message'] = substr($_POST['message'], 0, 100); //LIMIT TO 100 CHARS
			
			
			
			$this->morseCode = $_POST['message'];
			$this->morseCode = str_replace(' ','/',$this->morseCode);
			
			$this->morseCode = str_split($this->morseCode);
			$this->morseCode = implode(' ', $this->morseCode);
			
			$this->morseCode = str_replace($this->conversionTable, $this->morseTable, $this->morseCode);
			
			//$this->morseCode = str_replace('/','&nbsp;',$this->morseCode);
			
			//foreach($message_text as $letter) {
			//	$this->morseCode .= $letter .'.';
			//}
		}
			
			
	}

	
	function head(){?>
	<style>
	@import url("http://media.esco.net/fonts/binary-morse/");
	
	#morseText {font-family:'Binary-Morse';}
	
	#morseLight {height:200px;width:200px;float:right;background:url('http://media.esco.net/morse/light.jpg') no-repeat; }
	</style>
	<script>
	function checkLength(obj) {
		var maxLenReached = document.getElementById('maxLenReached');
		if(obj.value.length>100)
			maxLenReached.innerHTML = 'Messages larger than 100 chars will not be audio-encoded.';
		else
			maxLenReached.innerHTML = '';
	}
	</script>
	<?php }

    function content() {
?>
<div class="widget">
	<h1>Morse Encoder</h1>
	<form action="" method="POST" autocomplete="off">
		<input name="message" value="<?php echo $_POST['message'];?>" onchange="checkLength(this);" onkeypress="checkLength(this);" onkeyup="checkLength(this);">
		<button type="submit">Get Morse</button>
		<span id="maxLenReached"></span>
	</form>
	
</div>

<?php if($this->morseCode!='') {?>
<div class="widget">
	<h2>Encoded Message</h2>
	<?php
	$message_text = str_split($this->morseCode);
	
	$concatList = '';
	
	echo '<div id="morseText" style="word-break: break-all;word-wrap: break-word;border:1px solid #ccc;padding:20px;">';
	
	$previewCode = implode('',$message_text);
	$previewCode = str_replace('/','',$previewCode);
	$previewCode = str_replace('  ','&nbsp; &nbsp;',$previewCode);
	echo $previewCode;
	
	
	foreach($message_text as $letter) {
		if($letter=='_'){
			$concatList .= "\n".'file \'morse-dash.wav\'';
			//echo '1';
		}elseif($letter=='.'){
			$concatList .= "\n".'file \'morse-dot.wav\'';
			//echo '0';
		}
		else{
			$concatList .= "\n".'file \'morse-space.wav\'';
			//echo '&nbsp;';
		}
	}
	echo '</div>';
	
	$concatList .= "\n".'file \'morse-space.wav\'';
	
	$hConcatFile = fopen('/home/pi/www/media.esco.net/_httpdocs/morse/list.txt', 'w');
	fwrite($hConcatFile, $concatList);
	fclose($hConcatFile);
	
	
	$outFile = urlify($_POST['message']);
	
	$command = 'cd /home/pi/www/media.esco.net/_httpdocs/morse/ && /home/pi/www/media.esco.net/ffmpeg/bin/ffmpeg -f concat -i list.txt -c copy out/'.$outFile.'.WAV';

	// Only encode audio if less than 100 chars
	if(strlen($_POST['message'])<=100)
		exec($command);
	?>

	<?php
		echo '<audio id="morsePlayer">
	  <source src="http://media.esco.net/morse/out/'.$outFile.'.WAV" type="audio/wav">
	  Your browser does not support the audio element.
	</audio>';?>

	<br><br>
	
	<button onclick="playMorse();">Play Morse</button>
	<?php if(strlen($_POST['message'])<=100) { ?>
	<button onclick="location.href='morse.php?download=<?php echo $outFile;?>.WAV';">Download</button>	
	&nbsp;&nbsp;<small>(Sound file will be available for around an hour.)</small>
	<?php } ?>
	
	<div id="morseLight"></div>
	
	<div class="cf"></div>
	
	
	<script>
	function playMorse() {
	
		var morsePlayer = document.getElementById('morsePlayer');
		morsePlayer.load();
		
		morsePlayer.play();

		setTimeout(function(){
			morsePlayer.play();
		},100);
		

		<?php
		foreach($message_text as $letter) {
			if($letter=='_'){
				echo 'sendMorse(1);'."\n";
			}elseif($letter=='.'){
				echo 'sendMorse(0);'."\n";
			}
			else{
				echo 'sendMorse(2);'."\n";
			}
		}
		?>
		window.morseWaitingTime = 0;
	}
	
	window.morseWaitingTime = 0;
	
	// Don't change
	window.morseDotDuration = 80;
	window.morseDashDuration = 210;
	
	var morseLight = document.getElementById('morseLight');
	
	function sendMorse(type){		
		setTimeout(function() {		
			var duration = 0;

			if(type==0){ // Dot
				duration = window.morseDotDuration;
			}else if(type==1){ // Dash
				duration = window.morseDashDuration;
			}
			
			if(duration!=2){
				morseLight.style.backgroundPosition = '0px -200px';
			}
				
			setTimeout(function() {
				morseLight.style.backgroundPosition = '0px 0px';
			},duration);
			
		},window.morseWaitingTime);
		
		
		// WAV Bit Durations (in milliseconds)
		if(type==0){ // Dot
			window.morseWaitingTime = window.morseWaitingTime+150;
		}else if(type==1){ // Dash
			window.morseWaitingTime = window.morseWaitingTime+270;
		}else{ // Space
			window.morseWaitingTime = window.morseWaitingTime+200;
		}
		
		console.log(window.morseWaitingTime);
	}
	</script>
</div>
<?php } ?>

<div class="widget">
	<h1>Morse Decoder</h1>
	<form action="" method="POST" autocomplete="off">
		<input name="morse" value="<?php echo $_POST['morse'];?>">
		<button type="submit">Decode</button>
	</form>
</div>

<?php if($this->morseDecoded!='') {?>
<div class="widget">
	<h2>Decoded Message</h2>
	<code><?php echo $this->morseDecoded;?></code>
</div>
<?php } ?>
<?php
    }
}

new customPage();
