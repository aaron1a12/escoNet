<?php

$fh = fopen(dirname(dirname(__FILE__)) . '/restart_apache.now', "w");
fclose($fh);
exit();