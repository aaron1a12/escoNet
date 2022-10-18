<?php
header('Content-type: text/plain');
$exif = exif_read_data('hermanos.jpg', 'IFD0');



var_dump($exif);


ECHO date('Y', 1449683419);