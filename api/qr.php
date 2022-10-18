<?php

include('_inc/php/thirdparty/phpqrcode/qrlib.php');
header('Content-type: image/png');

if(isset($_GET['code']) && $_GET['code']!='')
	$code = $_GET['code'];
else
	$code = '0';



QRcode::png($code, null, QR_ECLEVEL_H);

exit;