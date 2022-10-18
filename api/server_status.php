<?php
if(file_exists(dirname(dirname(__FILE__)) . '/restart_apache.now'))
{
    echo '0';
}
else
{
    echo '1';
}

die();