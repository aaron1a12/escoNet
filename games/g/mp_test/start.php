<?php
$command = 'cd /home/pi/www/media.esco.net/nodejs/ && ./node multiplayer.js > /dev/null &';

exec($command);
die();