<?php

class customPage extends page {
    public $title = 'Gadgets';
    function content() {
?>
<div class="widget" style="position:relative;">
<h1>Gadgets for Windows</h1>
<h2>How to Install</h2>
Open the files and click on "Install". You will see the gadget on you desktop then.
<br><br>


<!--
<a class="widget link" href="/games/g/mad2.php">Mutilate-a-Doll 2</a>
<a class="widget link" href="/games/g/mad2.php">Mutilate-a-Doll 2</a>
-->


<!--
Default: array('NOT YET','a very long description', '/games/g/mad2.swf', 'default.png')
--->


		<?php
        {
            $pngFolder = 'http://www.esco.net/gadgets/g/images/';

            $games = array(
				array('Clear Time &amp; Date', 'Modern time and date', '/gadgets/g/ClearTimeAndDate.gadget', 'time.png')
				,
				array('Clear Clock', 'Transparent clock', '/gadgets/g/ClearClock.gadget', 'clock.png')
				,
				array('Vista\'s Sticky Notes', 'Capture ideas, notes, and reminders in a quick and easy way.', '/gadgets/g/Notes.gadget', 'notes.png')
				,
				array('Custom Desktop Label', 'A nice little label for your desktop. Written by Aaron Escobar', '/gadgets/g/CustomDesktopLabel.gadget', 'label.png')
				,
				array('Wild Montage Logo', 'Proudly show off Wild Montage®\'s logo on your desktop.', '/gadgets/g/WMLogo.gadget', 'logo.png')
            );


            $leftCollumn = ceil(count($games)/2);
            $rightCollumn = floor(count($games)/2);

            echo '<div style="float:left; width:49%;">';
			foreach($games as &$game)
			{
                if($leftCollumn==0)
                {
                    echo '</div>';
                    echo '<div style="float:right; width:49%;">';
                }
                elseif($leftCollumn < 1)
                {
                    $rightCollumn--;
                }


               // $meta = '<b>'.$siteName.'</b> <br> <span style="color:#000;">' . $siteDescription . '</span>';

                $meta = '???';

                if($leftCollumn==0)
                {
                    echo '</div>';
                    echo '<div style="float:right; width:49%;">';
                }
                elseif($leftCollumn < 1)
                {
                    $rightCollumn--;
                }

                $icon = $pngFolder . $game[3];

                echo '<a href="'.$game[2].'" class="widget link" style="display:block;">

                            <img src="'.$icon.'" style="float:left;width:48px;height:48px;">
                            <div style="float:right; width:360px;">
                                '.$game[0].'
                                <br>
                                <span style="color:#000;">'.$game[1].'</span>
                            </div>
                            <div class="cf"></div>
                        </a>';


                $leftCollumn--;
			}
            echo '</div><div class="cf"></div>';
        }
		?>

</div>
<?php
    }
}

new customPage();
