<?php
function money ($n)
{
    $n = money_format('%i', $n);
    $n = explode('.', $n);
    return substr($n[0], 3) . "." . $n[1];
    //$n[0] = number_format(substr($n[0], 3));
    return implode('.',$n);
}

define('ACTION_COMMENT', 0);
define('ACTION_BLOG_POST', 1);
define('ACTION_NEW_POLL', 2);
define('ACTION_PHOTO_UPLOAD', 3);
define('ACTION_PHOTO_FAV', 4);
define('ACTION_PHOTO_NOTE', 5);
define('ACTION_MONEY_TRANSFER', 6);

function logAction($pageClass, $action, $subject='Unknown', $url=null)
{
    $user = $pageClass->escoID;
    $time = time();
    
    if($subject=='Unknown' && $pageClass->title!='')
        $subject = $pageClass->title;    
    
    
    
    if($url===null)
        $url = str_ireplace('http://www.esco.net/', '/', $_SERVER['REQUEST_URI']);
    
    
    $urlGZ = gzcompress($url, 9);
    
    $urlGZ = strtoupper(unpack('H*', $urlGZ )[1]);
    
    $checkResult = mysqli_query($pageClass->link, "SELECT user,subject FROM esco_user_activity ORDER BY id DESC LIMIT 1");
    $row = mysqli_fetch_row($checkResult);
    
    // Insert only if the latest activity is
    
    $bContinue = true;
    
    if($row[0]==$user)
    {
        if($row[1]==$subject)
            $bContinue = false;
    }
    
    if($bContinue)
    {
        if($url=='') $query = "INSERT INTO esco_user_activity (user, time, action, subject) VALUES ('$user', '$time', '$action', '$subject')";
        else $query = "INSERT INTO esco_user_activity (user, time, action, subject, url) VALUES ('$user', '$time', '$action', '$subject', 0x$urlGZ)";
        mysqli_query($pageClass->link, $query);
    }
}


function transfer($session, $from, $to, $amount){

    $from = intval($from);
    $to = intval($to);
    $amount = floatval($amount);

    $fromResult = mysqli_query($session->link, "SELECT funds FROM esco_bank_accounts WHERE owner=" . $from);
    $fromRows = mysqli_num_rows($fromResult);

    if($fromRows==0)
        return false;

    $toResult = mysqli_query($session->link, "SELECT funds FROM esco_bank_accounts WHERE owner=" . $to);
    $toRows = mysqli_num_rows($toResult);

    if($toRows==0)
        return false;

    $fromFunds = floatval(mysqli_fetch_row($fromResult)[0]);
    $toFunds = floatval(mysqli_fetch_row($toResult)[0]);

    $subtract = $fromFunds-$amount;

    if($subtract!=0){
        if($subtract < 0)
            return false;
    }

    $addition = $toFunds+$amount;

    mysqli_query($session->link, "UPDATE esco_bank_accounts SET `funds`=$subtract WHERE owner=$from");
    mysqli_query($session->link, "UPDATE esco_bank_accounts SET `funds`=$addition WHERE owner=$to");

    return $fromFunds-$amount;
}

function trimText($str, $max){
    if(strlen($str)>$max)
        return substr($str, 0, $max) . '...';
    else
        return $str;
}

function urlify( $str )
{
    $str = preg_replace("/[^A-Za-z0-9_\(\)]/", "_", $str);
    return rtrim($str, '_');
}

function addLinks($string, $bEmbed=false)
{
    $pattern = '!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i';
    if($bEmbed){
		preg_match_all($pattern, $string,$matches);
		$nummatches = count($matches[0]);
		for( $i=0; $i<$nummatches; $i++ )
		{
            $url = $matches[1][$i];
            
            $replacement = $url;
            
            $urlExt = explode('.', $url);
            $urlExt = strtolower($urlExt[count($urlExt)-1]);
            
            if($urlExt=='mp4'){
                $replacement = <<<HTML
                
<video width="100%" controls >
<source src="{$url}" type="video/mp4">
Your browser does not support the video tag.
</video>

HTML;
            }
            elseif($urlExt=='swf'){
				$randGUID = create_guid();
                $replacement = <<<HTML
                
<div style="line-height:0; border:4px solid transparent;" onmouseover="document.getElementById('{$randGUID}').Play();">
	<embed id="{$randGUID}" src="{$url}" type="application/x-shockwave-flash" style="margin-bottom:0;" height="100" width="100">
</div>
HTML;
            }elseif($urlExt=='gifv'){
				$randGUID = create_guid();
                $replacement = <<<HTML
                
<div style="line-height:0; border:4px solid transparent;position:relative;width:75%;" onmouseover="document.getElementById('{$randGUID}').muted = false; document.getElementById('{$randGUID}').currentTime=0; document.getElementById('{$randGUID}_icon').style.visibility='hidden';" onmouseout="document.getElementById('{$randGUID}').muted = true; document.getElementById('{$randGUID}_icon').style.visibility='visible';">
	<img id="{$randGUID}_icon" src="/_inc/img/video-overlay-mute.png" style="position:absolute; top:10px;left:10px;">
	<video id="{$randGUID}" preload="auto" name="media" muted="" loop="" autoplay="" style="margin-bottom:0; outline: medium none;  object-fit:cover;  width:100%;border:4px solid white;box-shadow: 0px 1px 5px 0px #656565;border-radius:4px;">
		<source type="video/mp4" src="{$url}" media="screen">
	</video>
</div>
HTML;
            }
            elseif($urlExt=='jpg' || $urlExt=='jpeg' || $urlExt=='png' || $urlExt=='gif')
            {
                $replacement = '<img src="'.$url.'" style="max-width:60%;">';
            }
            else
            {
                $replacement = '<a href="'.$url.'" rel="nofollow">'.$url.'</a>';
            }
            
            
            
            $search = quotemeta($matches[0][$i]);
			$search = str_replace('/',"\\".'/',$search);
			$string = preg_replace("/$search/",$replacement,$string,1);
        }
        return $string;
    }else{
        return preg_replace($pattern, '<a href="$1" rel="nofollow">$1</a>', $string);    
    }
}


function show( $var )
{
    echo '<pre>';
    var_dump( $var );
    echo '</pre>';
}

function escoDate( $timestamp )
{
    $currentTime = time();
    
    $thisYDay = date('z',$currentTime);
    $thatYDay = date('z',$timestamp);
    
	$thisYear = date('Y', $currentTime);
	$thatYear = date('Y', $timestamp);
	
    $currentHour = date('G',$currentTime);
    
    $date = date('F jS, Y', $timestamp);
    $time = date('g:i A', $timestamp);
    $hour = date('G', $timestamp);
    
    if( $thisYDay == $thatYDay && $thisYear==$thatYear ){
        $date = 'Today';
        
        if($hour==$currentHour){
            $secondsAgo = $currentTime-$timestamp;
            $s = '';
            
            if($secondsAgo > 1)
                $s = 's';
            
            if($secondsAgo < 60){
                $time = $secondsAgo . " second$s ago";
            }
            else
            {
                $minutesAgo = floor($secondsAgo / 60);
                
                if($minutesAgo > 1)
                    $s = 's';
                
                $time = $minutesAgo . " minute$s ago";
            }
            
        }
    }
    elseif ($thisYDay-$thatYDay==1 && $thisYear==$thatYear)
    {
        $date = 'Yesterday';
    }
    
    return $date.', '.$time;
}


function create_guid()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    # fallback to mt_rand if php < 5 or no com_create_guid available
    return sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

    //return substr(hash('sha512',uniqid(rand(), true)), 0, 15);
}


function getAllCategoryChildren($link, $categoryId, &$catArray)
{
    $r=mysqli_query($link, "SELECT * FROM esco_blog_categories WHERE parent=$categoryId;");

    if(mysqli_num_rows($r)!=0){
        while($row=mysqli_fetch_row($r)){
            array_push($catArray, intval($row[0]));
            getAllCategoryChildren($link, $row[0], $catArray);
        }
    }
}    


function getAllCategoryParents($link, $categoryId, &$catArray)
{
    $sql1 = mysqli_fetch_row(mysqli_query($link, "SELECT parent,name FROM esco_blog_categories WHERE id=$categoryId;"));
    $parent = intval($sql1[0]);
    
    array_push($catArray, array($categoryId, $sql1[1]) );
    
    if($parent!=0)
        getAllCategoryParents($link, $parent, $catArray);
} 



/*
That it is an implementation of the function money_format for the
platforms that do not it bear. 

The function accepts to same string of format accepts for the
original function of the PHP. 

(Sorry. my writing in English is very bad) 

The function is tested using PHP 5.1.4 in Windows XP
and Apache WebServer.
*/
function money_format($format, $number)
{
    $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'.
              '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
    if (setlocale(LC_MONETARY, 0) == 'C') {
        setlocale(LC_MONETARY, '');
    }
    $locale = localeconv();
    preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
    foreach ($matches as $fmatch) {
        $value = floatval($number);
        $flags = array(
            'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
                           $match[1] : ' ',
            'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                           $match[0] : '+',
            'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
            'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
        );
        $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
        $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
        $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
        $conversion = $fmatch[5];

        $positive = true;
        if ($value < 0) {
            $positive = false;
            $value  *= -1;
        }
        $letter = $positive ? 'p' : 'n';

        $prefix = $suffix = $cprefix = $csuffix = $signal = '';

        $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
        switch (true) {
            case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                $prefix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                $suffix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                $cprefix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                $csuffix = $signal;
                break;
            case $flags['usesignal'] == '(':
            case $locale["{$letter}_sign_posn"] == 0:
                $prefix = '(';
                $suffix = ')';
                break;
        }
        if (!$flags['nosimbol']) {
            $currency = $cprefix .
                        ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                        $csuffix;
        } else {
            $currency = '';
        }
        $space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';

        $value = number_format($value, $right, $locale['mon_decimal_point'],
                 $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
        $value = @explode($locale['mon_decimal_point'], $value);

        $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
        if ($left > 0 && $left > $n) {
            $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
        }
        $value = implode($locale['mon_decimal_point'], $value);
        if ($locale["{$letter}_cs_precedes"]) {
            $value = $prefix . $currency . $space . $value . $suffix;
        } else {
            $value = $prefix . $value . $space . $currency . $suffix;
        }
        if ($width > 0) {
            $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                     STR_PAD_RIGHT : STR_PAD_LEFT);
        }

        $format = str_replace($fmatch[0], $value, $format);
    }
    return $format;
}