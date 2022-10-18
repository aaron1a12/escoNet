<?php

function HumanSize($Bytes)
{
  $Type=array("", "K", "M", "G", "T", "P", "E", "Z", "Y");
  $Index=0;
  while($Bytes>=1024)
  {
    $Bytes/=1024;
    $Index++;
  }
  $Bytes = explode('.', $Bytes);
  if(count($Bytes)>1 && substr($Bytes[1], 0, 2)!='00')
	$Bytes = $Bytes[0].'.'.substr($Bytes[1], 0, 2);
  else
	$Bytes = $Bytes[0];
  
  return("".$Bytes." ".$Type[$Index]."B");
}

/*
function apache_site_exists()
{
    return true;
}
*/
/*

echo '<pre>';


*/
class customPage extends page {
    public $title = 'Register a new Domain';
    
    public $errors = array();
    
    public $tldList = array(
                        'com','dev','escobar','es','fr','gov','home','net','news','org'
                      );
    
    public $apacheConf = '/etc/apache2/sites-available/default';
    
    
    function init()
    {
        //
        if($_POST){
            if( !isset($_POST['domain']) || !isset($_POST['tld']) || !isset($_POST['logging']) || !isset($_POST['documentroot']) ){
                array_push($this->errors, 'Bad request');
            }elseif($_POST['domain']==''){
                array_push($this->errors, '...and you want us to register WHAT domain?');
            }else{
                
                $_POST['domain'] = strtolower($_POST['domain']);
                $_POST['tld'] = strtolower($_POST['tld']);
                
                $firstCharacter = substr($_POST['domain'], 0,1 );
                $lastCharacter = substr($_POST['domain'], strlen($_POST['domain'])-1 );
                
                if(!preg_match('/^[a-zA-Z0-9-_\/]+$/', $firstCharacter) || !preg_match('/^[a-zA-Z0-9-_\/]+$/', $lastCharacter))
                {
                    array_push($this->errors, 'Bad domain name. (first and/or last chars)');
                }
                
                
                if (!in_array(substr($_POST['tld'], 1), $this->tldList)) {
                    array_push($this->errors, 'Invalid tld selected!');
                }
                
                $domainParts = explode('.', $_POST['domain']);
                if (in_array($domainParts[ count($domainParts)-1 ], $this->tldList)) {
                    array_push($this->errors, 'You can\'t include the tld (e.g., .com) in the domain name!');
                }
                
                if($domainParts[0]=='www') {
                    array_push($this->errors, 'Please don\'t include the "WWW" prefix as this is added automatically by us.');
                }
                
            
                if(!preg_match('/^[a-zA-Z0-9-_.\/]+$/', $_POST['domain'] ))
                {
                    array_push($this->errors, 'Your domain name (' .htmlentities($_POST['domain']). ') contains invalid characters!');
                }
                
                $domain = $_POST['domain'] . $_POST['tld'];
                
                $documentRoot = intval($_POST['documentroot']);
                if($documentRoot==0)
                    $documentRoot = '/home/pi/www/';
                elseif($documentRoot==1)
                    $documentRoot = '/home/pi/www/media.esco.net/_sites/';
                
                // Validate domain and make sure it doesn't include www or tld
                
               
                
                //if (in_array('12.4', $a, true)) {
                 //   echo "'12.4' found with strict check\n";
                //}
                
                
                // Check if domain exists
                
                if(count($this->errors)==0){
                    
                    $configFile = file_get_contents($this->apacheConf);

                    $begin = '#<- BEGIN VHOSTS ->#';
                    $end = '#<- END VHOSTS ->#';

                    //$begin = '#<-php->#';

                    $pattern = '!#<- BEGIN VHOSTS ->#(.*?)#<- END VHOSTS ->#!is';
                    $pattern2 = '!#<- BEGIN SITE->#(.*?)#<- END SITE->#!is';
                    $pattern3 = '!<VirtualHost \*:80>(.*?)</VirtualHost>!is';


                    //
                    // Find Custom VHOSTS Section
                    //

                    preg_match($pattern, $configFile, $matches);
                    $entries = $matches[1];
                    
                    // Add a space if there's no site entry at all
                    if(strlen($entries)==1) $entries .= "\n";
                    
                    
                    // Find if ServerName exists
                    $newServerName = 'ServerName '.$domain;
                    $newServerName2 = $newServerName."\n";
                
                    $serverNamePos = stripos($entries, $newServerName);
                    $serverNamePos2 = stripos($entries, $newServerName2);
                    
                    if($serverNamePos !== false || $serverNamePos2 !== false)
                    {
                        array_push($this->errors, 'The domain name is already in use.');
                    }
                    else
                    {
                        //
                        // Site Configuration Entry Building
                        //
                        
                        $domainFull = $domain;
                        
                        $newEntry = "#<- BEGIN SITE->#\n";
                        
                        if( strpos($_POST['domain'], '.') === false)
                        {
                            // The domain doesn't include a period (".") which means we should force the WWW redirection
                            $newEntry .= "<VirtualHost *:80>\n";
                            $newEntry .= "ServerName $domain\n";
                            $newEntry .= "Redirect permanent / http://www.$domain/\n";
                            $newEntry .= "</VirtualHost>\n";
                            $domainFull = "www.$domain";
                        }
                        
                        
                        $newEntry .= "<VirtualHost *:80>\n";
                        $newEntry .= "ServerName $domainFull\n";
                        $newEntry .= "DocumentRoot " . $documentRoot . $domain .  "\n";   
                        
                        if(intval($_POST['logging'])==1){
                            $newEntry .= "CustomLog /home/pi/www/_logs/$domain.log combined\n";
                            $newEntry .= "ErrorLog /home/pi/www/_logs/$domain.log\n";
                        }
                        
                        $newEntry .= "</VirtualHost>\n";
                        $newEntry .= "#<- END SITE->#\n";
                        
                        // Create the document root directory
                        if(!file_exists($documentRoot . $domain))
                        {
                            mkdir( $documentRoot . $domain );
                        
                            // Create the index.html
                            $defaultIndexData = '<!doctype html><html><head><title>Under Construction</title></head><body><h1>Under Construction</h1><p>You may replace this file.</p></body></html>';
                            $fhIndex = fopen($documentRoot . $domain . '/index.html', "w");
                            fwrite($fhIndex, $defaultIndexData);
                            fclose($fhIndex);
                        }
                        
                        $entries .= $newEntry . "\n";
                    
                        
                        //
                        // Find Web Site VHOSTS (2)
                        //

                        //echo '<pre>';
                        //echo htmlentities($entries . $newEntry . "\n");
                        //echo '</pre>';
                        
                        //die();
                        /*
                        
                        preg_match_all($pattern2,$entries,$sitematches);
                        $nummatches = count($sitematches[0]);

                        $vhostConfArray = array();

                        for( $i=0; $i<$nummatches; $i++ )
                        {
                            //
                            // Per site
                            //

                            $siteVhosts = $sitematches[1][$i];    
                            preg_match_all($pattern3, $siteVhosts, $vhostMatches);

                            $vhostCount = count($vhostMatches[1]);

                            echo 'How Many Vhosts For This Site? :'. $vhostCount . "\n";    


                            // Find how many vhosts

                            $lines = explode("\n", $siteVhosts);

                            foreach($lines as &$line){
                                $lineSections = explode(' ',$line);
                                echo $lineSections[0]."\n\n";
                            }


                            // Find ServerName
                            //$serverNamePos = stripos($siteVhosts,'ServerName');

                           // print( var_dump($lines) ."\n");

                           // print('||'.htmlentities( $sitematches[1][$i]).'||');
                        }

                        echo "\n\n";

                        //var_dump($vhostConfArray);
                        //var_var_dump($vhostConfArray);

                        ///print(htmlentities($entries));
                        echo '</pre>';
                        */

                        // Rewrite the file
                        $configFile = preg_replace($pattern, "#<- BEGIN VHOSTS ->#".$entries."#<- END VHOSTS ->#", $configFile);
                        
                        $fh = fopen($this->apacheConf, "w") or die("Unable to open file!");
                        fwrite($fh, $configFile);
                        fclose($fh);
                        
                        
                        
                        header('Location: /links/register.php?restart=1&domain=' . base64_encode($domain) );
                        die();

                                            
                    }


                }
                
                
                
                
            }

            //die('Register');

        }
    }
    
    
    function content() {
?>
<?php
if(!isset($_GET['restart'])){
?>

<div class="widget" style="position:relative;">
    <h1>Register a new Domain</h1>
    <form action="" method="POST">
        <table cellspacing="4">
            <tr>
                <td>Domain:</td>
                <td>&nbsp;</td>
                <td><input name="domain" value="<?php if(isset($_POST['domain'])) echo htmlentities($_POST['domain']);?>" style="text-align:center;font-family:monospace;width:100%;"></td>
                <td>
                    <select name="tld" size="4" style="margin:0;padding:0;">
                        <?php
                        {
                            foreach($this->tldList as &$tldEntry)
                            {
                                $selected='';
                                if(isset($_POST['tld'])){
                                    if($_POST['tld']=='.'.$tldEntry)
                                        $selected=' selected="selected"';                                        
                                }elseif($tldEntry=='com'){
                                    $selected=' selected="selected"';
                                }
                                
                                echo "<option$selected>.$tldEntry</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Enable Logging:</td>
                <td></td>
                <td colspan="2">
                    <select name="logging">
                        <option value="0"<?php if(isset($_POST['logging'])&&intval($_POST['logging'])==0) echo ' selected="selected"';?>>false</option>
                        <option value="1"<?php if(isset($_POST['logging'])&&intval($_POST['logging'])==1) echo ' selected="selected"';?>>true</option>
                    </select>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>Site Location:</td>
                <td></td>
                <td colspan="2">
                    <?php
                        $sdfreeSpace = HumanSize(disk_free_space('/'));
                        $usbfreeSpace = HumanSize(disk_free_space('/home/pi/www/media.esco.net/'));
                    ?>
                    <select name="documentroot">
                        <option value="0" <?php if(isset($_POST['documentroot'])&&intval($_POST['documentroot'])==0) echo ' selected="selected"';?>>/home/pi/www/ (<?php echo $sdfreeSpace;?> Free)</option>
                        <option value="1" <?php if(isset($_POST['documentroot'])&&intval($_POST['documentroot'])==1) echo ' selected="selected"';?>>/home/pi/www/media.esco.net/_sites/ (<?php echo $usbfreeSpace;?> Free)</option>
                    </select>
                </td>
            </tr>
        </table>


        <br><br>
        <button>Check And Register</button>
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
<?php }else{ ?>
<div class="widget" style="position:relative;">
    <h1>Domain registration</h1>
    <div id="console" style="height:200px; overflow:hidden;background-color: #000;font-family:monospace; color:#ccc; border:10px solid #000;">
    </div>
    <script>
        function stout(str){
            var console = document.getElementById('console');
            var newLine = document.createElement('DIV');
            newLine.innerHTML = '&gt; '+str;
            console.appendChild(newLine);
            
            console.scrollTop = console.scrollHeight;
        }
       
        stout('Welcome to escoRegister v0.1');
        stout('The domain "<?php echo base64_decode($_GET['domain']);?>" has been registered. ');
        stout('Please wait while we restart the web server. All escoNET sites will go offline...');
        stout('This will take up to one (1) minute.');
        
        $.ajax({
            type: "GET",
            url : "/api/server_restart.php",
        });

        stout('Restart request sent to CRON');
        
        i=0;
        var serverCheck = setInterval(function() {
    
            $.ajax({
                type: "GET",
                url : "/api/server_status.php",
            }).fail(function(data){
                stout('Server has stopped and is not responding. This is normal...');
            }).done(function(data){                
                
                if(data=='0')
                {
                    stout('Waiting for restart... '+i);
                }
                else
                {
                    stout('Restart complete. All systems online.');
                    stout('The domain "<?php echo base64_decode($_GET['domain']);?>" is now accessible.');
                    stout('Thank you for registering with escoNet!');
                    clearInterval(serverCheck);
                }
                
                if(i>=60)
                {
                    stout('<span style="color:red;">RESTART FAILED - CONTACT AARON ASAP</span>');
                    clearInterval(serverCheck);
                }
                
                i++;
                
            });

        },1000);
        
    </script>
    <?php
            ?>
</div>
<?php } ?>
<?php
    }
}

new customPage();
