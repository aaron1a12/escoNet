<?php

class customPage extends page {
    public $title = 'Games';
    function content() {
?>
<div class="widget" style="position:relative;">
<h1>Games</h1>

<!--
<a class="widget link" href="/games/g/mad2.php">Mutilate-a-Doll 2</a>
<a class="widget link" href="/games/g/mad2.php">Mutilate-a-Doll 2</a>
-->


<!--
Default: array('NOT YET','a very long description', '/games/g/mad2.swf', 'default.png')
--->


		<?php
        {
            $pngFolder = 'http://media.esco.net/swf/icons/';

            $games = array(
				array('Minecraft Skin Editor','Edit skins for Minecraft', '/games/g/skineditor.php', 'skineditor.png')
                ,
                array('BATTLEFIELD 3','A game for torturing other people', 'http://www.bf3.com', 'bf3.png')
                ,
                array('Mutilate a Doll 2','Have fun with this ragdoll physics game', '/games/g/mad2.php', 'mad2.png')
				,
				array('Shift','Play the classic game!', '/games/g/shift.php')
				,
				array('Ant City','Control a giant magnifying glass.', '/games/g/antcity.php', 'antcity.png')
				,
				array('SimTaxi','be a taxi driver in this game', '/games/g/simtaxi.php', 'simtaxi.png')
				,
				array('Crush The Castle 2','Crush castles, or make castles in CTC2!', '/games/g/ctc2.php', 'crushthecastle2.png')
				,
				array('Crush The Castle','Crush castles, or make castles in CTC!', '/games/g/ctc1.php', 'crushthecastle1.png')
                ,
                array('Slot Machine','Gamble your escos', '/games/g/slotmachine.php', 'slot.png')
				,
				array('Battle of Britain: 303 Squadron','2D Airplane game', '/games/g/bob303.php')
				,
				array('Pac-man','Play pac-man on your browser', 'http://www.games.net/games/google_pacman-master', 'pacman.png')
                ,
                array('Hobo','Play as a filthy bum', '/games/g/hobo.php', 'hobo.png')
				,
				array('Audibles','Remember these from Yahoo! Messenger?', '/games/g/audibles.php', 'audibles.png')
				,
				array('Websocket Test','Just experimenting', '/games/g/mp_test/')
				,
				array('escoChess BETA','Just experimenting', '/games/g/chess/')
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

				if(isset($game[3])) $gameIcon = $game[3]; else $gameIcon = 'default.png';
				
                $icon = $pngFolder . $gameIcon;

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
