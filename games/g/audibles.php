<?php

class customPage extends page {
    public $title = 'Fun Stuff';

	function head() {
	?>
	<style>
	#explorerLinks {cursor:pointer;border:1px solid #EAEAF3;overflow:hidden;}
	#explorerLinks div {width:200%;padding:10px;line-height:15px;}
	#explorerLinks div {}
	#explorerLinks div:hover {background-color:#72B;color:#fff;}
	#explorerLinks img {vertical-align:middle;}
	
	#fileLinks {cursor:pointer;border:1px solid #EAEAF3;overflow:hidden;}
	#fileLinks div {display:inline-block;border:2px solid #EAEAF3; margin:0px;text-align:center;}
	#fileLinks div:hover {background:#EAEAF3;}
	#fileLinks div input {font-family:monospace;font-size:11px;padding:2px;height:15px;}
	#fileLinks p {padding:30px;}
	</style>
	<script>
	function goTo(path) {
		document.getElementById('input_path').value = path;
		document.getElementById('browserForm').submit();
	}
	</script>
	<?php
	}
    function content() {
?>
<div class="widget">
	<h1>Yahoo! Audibles</h1>
	<?php	
	
	function folderUp($path) {
		$path = explode('/', $path);
		array_pop($path);
		return implode('/', $path);
	}

	$rootFolder = '/home/pi/www/media.esco.net/_httpdocs/swf/audibles';
	
	if(!isset($_GET['path'])) $_GET['path'] = ''; else $_GET['path'] = ltrim(rtrim($_GET['path'],'/'), '/');
	if($_GET['path']=='..') $_GET['path'] = '';

	//$_GET['path'] = urlencode($_GET['path']);
	//$_GET['path'] = str_replace(array('..%2F','..%5C'), '', $_GET['path']);
	//$_GET['path'] = urldecode($_GET['path']);
	$_GET['path'] = str_replace(array('../','..\\'), '', $_GET['path']);
	
	$folder = rtrim($rootFolder.'/'.$_GET['path'],'/');
	
	if(!file_exists($folder)) {
		$folder = $rootFolder;
	}	
	
	$dh = opendir($folder);
	
	$audibles = array();
	$audibleFolders = array();
	?>
	<div style="float:left;width:20%;">
		<h2>Browse</h2>
		<form id="browserForm" action="" method="GET"><input type="hidden" id="input_path" name="path" value=""></form>
		<div id="explorerLinks">
			<div onclick="goTo('<?php echo addSlashes(ltrim(folderUp($_GET['path'],'/'))); ?>');"><img src="/_inc/img/icons/back.png"> Back</div>
		<?php
		while(($file = readdir($dh)) !== false) {
			if($file != '.' && $file != '..'){
				if(is_dir($folder.'/'.$file)){
					//echo '<div onclick="goTo(\''.addSlashes(ltrim($_GET['path'].'/'.$file,'/')).'\');"><img src="/_inc/img/icons/folder.png"> '.ucwords(ucwords($file,'-'),' ').'</div>';
					array_push($audibleFolders, $file);
				}else{
					$extension = explode('.', $file);
					if(count($extension)>1) {
						$extension = $extension[ count($extension)-1 ];
						if($extension=='swf'||$extension=='gifv') {
							array_push($audibles, $file);
						}
					}
				}
			}
		}
		
		asort($audibleFolders);
		asort($audibles);
		
		foreach($audibleFolders as $audibleFolder)
		{
			echo '<div onclick="goTo(\''.addSlashes(ltrim($_GET['path'].'/'.$audibleFolder,'/')).'\');"><img src="/_inc/img/icons/folder.png"> '.ucwords(ucwords($audibleFolder,'-'),' ').'</div>';
		}		
		
		closedir($dh);
		?>
		</div>
	</div>
	<div style="float:right;width:76%;">
		<h2>Files</h2>
		<div id="fileLinks">
			<?php
			
			if(count($audibles)<1) {
				echo '<p>No audibles in this directory</p>';
			}
			
			foreach($audibles as $audible) { ?>
			<div>
				<?php
				$randGUID = create_guid();
				$audibleHTTPRoot = 'http://media.esco.net/swf/audibles/';
				
				$audibleExtension = explode('.', $audible);
				if(count($audibleExtension)>1)	$audibleExtension = strtolower($audibleExtension[ count($audibleExtension)-1 ]); else $audibleExtension = '';
				
				if($_GET['path']=='') $audibleURL = $audibleHTTPRoot.$_GET['path'].$audible; else $audibleURL = $audibleHTTPRoot.$_GET['path'].'/'.$audible;
				
				if($audibleExtension=='swf') {
				?>
				<div style="line-height:0; border:4px solid transparent;" onmouseover="document.getElementById('<?php echo $randGUID;?>').Play(); document.getElementById('<?php echo $randGUID;?>_').select();" onmouseout="document.getElementById('<?php echo $randGUID;?>').GotoFrame(0);">
					<embed id="<?php echo $randGUID;?>" src="<?php echo $audibleURL;?>" type="application/x-shockwave-flash" style="margin-bottom:0;" height="100" width="100">
					<br>
					<input id="<?php echo $randGUID;?>_" value="<?php echo $audibleURL;?>" readonly="readonly" onclick="this.select();">
				</div>
			</div>
			<?php }else if($audibleExtension=='gifv'){ ?>
				<div style="line-height:0; border:4px solid transparent;" onmouseover="document.getElementById('<?php echo $randGUID;?>').play(); document.getElementById('<?php echo $randGUID;?>_').select();" onmouseout="document.getElementById('<?php echo $randGUID;?>').pause(); document.getElementById('<?php echo $randGUID;?>').currentTime=0;">
					<video id="<?php echo $randGUID;?>" preload="auto" name="media" loop="" style="margin-bottom:0; outline: medium none;  object-fit:cover;  width:100px;height:100px;">
						<source type="video/mp4" src="<?php echo $audibleURL;?>" media="screen">
					</video>	
					<br>
					<input id="<?php echo $randGUID;?>_" value="<?php echo $audibleURL;?>" readonly="readonly" onclick="this.select();">
				</div>
			</div>
			<?php }
			}
			?>
		</div>
	</div>
	<div class="cf"></div>
</div>
<!--
<div class="widget">
  <button onclick="document.getElementById('audible1').Play();">Play</button>
  <button onclick="document.getElementById('audible1').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible1').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible1" width="200" height="200" src="http://media.esco.net/swf/audibles/emoticats/base.us.emoticats.vacation2.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible2').Play();">Play</button>
  <button onclick="document.getElementById('audible2').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible2').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible2" width="200" height="200" src="http://media.esco.net/swf/audibles/international/philippines/base.aa.philippines2.ph_yourock.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible3').Play();">Play</button>
  <button onclick="document.getElementById('audible3').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible3').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible3" width="200" height="200" src="http://media.esco.net/swf/audibles/international/philippines/base.aa.philippines2.ph_hello.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible4').Play();">Play</button>
  <button onclick="document.getElementById('audible4').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible4').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible4" width="200" height="200" src="http://media.esco.net/swf/audibles/insults/base.us.insults.sock_09.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible5').Play();">Play</button>
  <button onclick="document.getElementById('audible5').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible5').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible5" width="200" height="200" src="http://media.esco.net/swf/audibles/international/taiwan/base.tw.smiley.smiley82.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible6').Play();">Play</button>
  <button onclick="document.getElementById('audible6').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible6').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible6" width="200" height="200" src="http://media.esco.net/swf/audibles/international/china/base.cn.emotion.1ganbade.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible7').Play();">Play</button>
  <button onclick="document.getElementById('audible7').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible7').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible7" width="200" height="200" src="http://media.esco.net/swf/audibles/international/china/base.cn.emotion.heartmelt.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible8').Play();">Play</button>
  <button onclick="document.getElementById('audible8').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible8').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible8" width="200" height="200" src="http://media.esco.net/swf/audibles/flirts/base.us.flirts.heartmelt1.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible9').Play();">Play</button>
  <button onclick="document.getElementById('audible9').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible9').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible9" width="200" height="200" src="http://media.esco.net/swf/audibles/international/german/base.de.siedler5.pilgrim3.swf" type="application/x-shockwave-flash"></embed>
</div>
-->
<?php
    }
}

new customPage();
