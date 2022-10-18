<?php

/*
$configFile = file_get_contents('/etc/apache2/sites-available/default');

$begin = '#<- BEGIN VHOSTS ->#';
$end = '#<- END VHOSTS ->#';

//$begin = '#<-php->#';

$test = 'asdfdsfdsfdsfsdfdsf'.$begin.'foobar'. $end. 'asdasdasdw';

$pattern = '!#<- BEGIN VHOSTS ->#(.*?)#<- END VHOSTS ->#!is';

preg_match($pattern, $configFile, $matches);
$entries = $matches[1];

$entries = 'Lalala';

// Rewrite the file
$configFile = preg_replace($pattern, "#<- BEGIN VHOSTS ->#\n".$entries."\n#<- END VHOSTS ->#", $configFile);

echo '<pre>';
print(htmlentities($configFile));
echo '</pre>';
*/


class customPage extends page {
    public $title = 'Links';
    function content() {
?>


		<div class="widget" style="position:relative;">
            <a class="btn" style="position:absolute; top:0; right:0;" href="/links/register.php">Register a new Domain</a>
            <h1>The escoNet</h1>
		<?php
        {
			$websiteFolders = 'C:/xampp/sites/';
			$xmlFileName = 'site.xml';
			$folders = scandir($websiteFolders);

			$topFolders = array();

			$count = count($folders);

            foreach($folders as $key=>$folder)
			{

                if( $folder=='..' || $folder=='.' || $folder=='_default' || $folder=='_logs' || $folder=='_ssl' ){
                    //$folder = '';
                    unset($folders[$key]);
                }

                if($folder=='esco.net' || $folder=='media.esco.net'  || $folder=='en.wikipedia.org' || $folder=='en.wiktionary.org' || $folder=='fr.wikipedia.org' || $folder=='boogle.com' || $folder=='yacy.com')
                {
                    unset($folders[$key]);
                    array_push($topFolders, $folder);
                }
            }
            $folders = array_merge($topFolders, $folders);
            /*
			for($i=0; $i<$count; $i++)
			{
                echo "<br>CURRENTOFFSET:$i. VALUEATOFFSET:".$folders[0]."<br>";
				if($folders[$i]=='esco.net' || $folders[$i]=='media.esco.net' || $folders[$i]=='router.home' || $folders[$i]=='mainframe.home' || $folders[$i]=='boogle.com')
				{
					//array_unshift( $folders, $folders[$i] );
					array_push($topFolders, $folders[$i]);
					unset($folders[$i]);
					//--$i;
					//$i = $count-1;
				}

                if( $folders[$i]=='..' || $folders[$i]=='.' || $folders[$i]=='_default' || $folders[$i]=='_logs' ){
                    echo 'WTF?' . $folders[$i].'?? ' . $i;
                    unset($folders[$i]);
                    --$i;
					//$i = $count-1;
                }
			}

			$folders = array_merge($topFolders, $folders);

			//array_unshift( $folders, $folders[$i] );

			/*
			foreach($folders as &$folder)
			{
				echo '<p>'.$folder.'</p>';
				if($folder=='escobar.home')
				{
					next($folders);
					//array_unshift( $folders, 'escobar.home' );
					//array_pop($folders);
				}
			}
			*/

            /*
            echo '<pre>';
            var_dump($folders);
            echo '</pre>';
            die();
            */


            $leftCollumn = ceil(count($folders)/2);
            $rightCollumn = floor(count($folders)/2);

            echo '<div style="float:left; width:49%;">';

			foreach($folders as &$folder)
			{

				$partCount = count(explode('.', $folder));
				if($partCount>1 && $folder!='.' && $folder!='..')
				{
					$xmlFile = $websiteFolders . $folder . '/' . $xmlFileName;
					$bHasMeta = false;

					if(file_exists($xmlFile))
					{
						$bHasMeta = true;
						$siteName = '';
						$siteDescription = '';

						$fh = fopen($xmlFile, 'r');
						$xml = fread($fh, filesize($xmlFile));
						fclose($fh);

						$p = xml_parser_create();
						xml_parse_into_struct($p, $xml, $values, $tags);
						xml_parser_free($p);

						foreach($values as $tag)
						{
							if($tag['tag']=='NAME')
							{
								$siteName = $tag['value'];
							}
							if($tag['tag']=='DESCRIPTION')
							{
								$siteDescription = $tag['value'];
							}
						}

					}


					if($partCount>2)
						$link = $folder;
					else
						$link = 'www.'.$folder;

					//$meta = ucwords($folder);
					$meta = $folder;

					if($bHasMeta)
					{
						$meta = '<b>'.$siteName.'</b> <br> <span style="color:#000;">' . $siteDescription . '</span>';
					}



                    if($leftCollumn==0)
                    {
                        echo '</div>';
                        echo '<div style="float:right; width:49%;">';
                    }
                    elseif($leftCollumn < 1)
                    {
                        $rightCollumn--;
                    }

                    $icon = '/_inc/img/site.png';
                    if(file_exists(dirname($this->siteDirectory) . '/' . $folder . '/site.png'))
                    {
                        $icon = '/webicons/'.$folder . '/icon.png';
                    }

					echo '<a href="http://'.$link.'" class="widget link" style="display:block;">

                                <img src="'.$icon.'" style="float:left;width:48px;height:48px;">
                                <div style="float:right; width:360px;">
                                    '.$meta.'<br>
                                    <span style="color:green;">'.$link.'</span>

                                </div>
                                <div class="cf"></div>
                            </a>';

                    $leftCollumn--;
				}
			}
            echo '</div><div class="cf"></div>';
        }
		?>
    </div>

<?php
    }
}

new customPage();
