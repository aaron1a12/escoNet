<?php

//require( '_inc/php/phpwhois/example.php' );

include_once('_inc/php/phpwhois/whois.main.php');
include_once('_inc/php/phpwhois/whois.utils.php');

function code_print($code){
    $code = substr(str_replace('<', '&lt;', $code), 1);
    echo $code;
}








class customPage extends page {
    public $title = 'Javascript';
    function content() {
?>
<h1>Registered Domains</h1>

<pre>
<?php
if (isset($argv[1]))
	$domain = $argv[1];
else
	$domain = 'example.com';

$whois = new Whois();
$result = $whois->Lookup($domain);

print_r($result);
?>
</pre>
<?php
    }
}

new customPage();