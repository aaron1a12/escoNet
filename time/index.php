<?php

class customPage extends page {
    public $title = 'Time in Casa Escobar';
    
	public $timestamp;
	
    function init()
    {      
		$this->timestamp = time();
    }
    
	
	function head()
	{
	?>
<script>
var canvas, ctx, radius, realhour, realminute, realsecond;

$( document ).ready(function() {
    canvas = document.getElementById("canvas");
	ctx = canvas.getContext("2d");
	radius = canvas.height / 2;
	ctx.translate(radius, radius);

	
	$.ajax({
		type: 'GET',
		url: "/api/time.php?no-cache="+Math.random(),
		success: function(now) {
			now=now.split(':');
			realhour = Number(now[0]);
			realminute = Number(now[1]);
			realsecond = Number(now[2]);
			
			drawClock();
			setInterval(drawClock, 1000);
		} 
	});		
	
	
	
});

function drawClock() {
  drawFace(ctx, radius);
  drawNumbers(ctx, radius);
  drawTime(ctx, radius);
}

function drawFace(ctx, radius) {
  var grad;
  ctx.beginPath();
  ctx.arc(0, 0, radius-5, 0, 2*Math.PI);
  ctx.fillStyle = 'white';
  ctx.fill();
  grad = ctx.createRadialGradient(0,0,radius*0.95, 0,0,radius*1.05);
  grad.addColorStop(0, '#338');
  grad.addColorStop(0.002, 'white');
  grad.addColorStop(0.3, 'white');
  ctx.strokeStyle = grad;
  ctx.lineWidth = radius*0.002;
  ctx.stroke();
  ctx.beginPath();
  ctx.arc(0, 0, radius*0.02, 0, 2*Math.PI);
  ctx.fillStyle = '#338';
  ctx.fill();
}

function drawNumbers(ctx, radius) {
  var ang;
  var num;
  ctx.font = radius*0.15 + "px Clavika";
  ctx.textBaseline="middle";
  ctx.textAlign="center";
  for(num = 1; num < 13; num++){
    ang = num * Math.PI / 6;
    ctx.rotate(ang);
    ctx.translate(0, -radius*0.85);
    ctx.rotate(-ang);
    ctx.fillText(num.toString(), 0, 0);
    ctx.rotate(ang);
    ctx.translate(0, radius*0.85);
    ctx.rotate(-ang);
  }
}

function drawTime(ctx, radius){	
	//
	// Update Time (Go forward by 1 second)
	//
	
	realsecond++;
	if(realsecond==60){
		realsecond = 0;
		realminute++;
		
		if(realminute==60){
			realminute = 0;
			realhour++;
			
			if(realhour==23){
				realhour=0;
			}
		}
	}
	
    var hour = realhour;
    var minute = realminute;
    var second = realsecond;	
	
    //hour
    hour=hour%12;
    hour=(hour*Math.PI/6)+
    (minute*Math.PI/(6*60))+
    (second*Math.PI/(360*60));
    drawHand(ctx, hour, radius*0.5, radius*0.02);
    //minute
    minute=(minute*Math.PI/30)+(second*Math.PI/(30*60));
    drawHand(ctx, minute, radius*0.8, radius*0.02);
    // second
    second=(second*Math.PI/30);
    drawHand(ctx, second, radius*0.9, radius*0.005);
}

function drawHand(ctx, pos, length, width) {
    ctx.beginPath();
    ctx.lineWidth = width;
    ctx.lineCap = "round";
    ctx.moveTo(0,0);
    ctx.rotate(pos);
    ctx.lineTo(0, -length);
    ctx.stroke();
    ctx.rotate(-pos);
}
</script>
	<?php
	}
	
    function content() {
?>
<div style="float:left; width:60%;">
    <div class="widget">
		<div style="text-align:center;">
			<h3>Time in Casa Escobar</h3>
			<canvas id="canvas" width="400" height="400"></canvas>
		</div>
	</div>
	
	
	<div class="widget">
		<div style="text-align:center;">
			<h3>Year's Progress Bar</h3>
			<div style="float:left;">
				<b><?php echo date('Y');?></b>
			</div>
			<div style="float:right;">
				<b><?php echo date('Y')+1;?></b>
			</div>
			<div class="cf"></div>
			<div style="background-color:#ddd; width:100%; height:8px;">
				<?php
					$daysInYear = 365;
					$today = intval(date('z'));
					
					if(date('L')=='1')
						$daysInYear++;
						
					$yearPercentage = round( ($today/$daysInYear)*100, 2 );
					
					echo '<div style="width:'.$yearPercentage.'%; background-color:#7722bb; height:8px;"></div>';
				?>
			</div>			
		</div>
	</div>
    
<!--
    <div class="widget">
		<div style="text-align:center;">
			<h2>Calendar</h2>
			<table class="calendar">
                <thead>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>
            </table>
		</div>
	</div>
-->
	
	<div class="widget">
	<h2>Current Hebrew Date</h1>
	<h1 style="text-align:center;"><?php 
	
	//$this->timestamp = strtotime('01/01/2015');

	//
	// Change the date used for the hebrew calander if the sun has already set
	//
	
	$dateYear = intval(date('Y', $this->timestamp));
	$dateMonth = intval(date('m', $this->timestamp));
	$dateDay = intval(date('j', $this->timestamp));

	$tomorrow = new DateTime('tomorrow');
	
	$sunsetTimestamp = date_sunset($this->timestamp, SUNFUNCS_RET_TIMESTAMP );
	
	
	$currentHour = idate('H', $this->timestamp );
	$currentMinute = idate('i', $this->timestamp );
	
	$sunsetHour = idate('H', $sunsetTimestamp );
	$sunsetMinute = idate('i', $sunsetTimestamp );
	
	$bTomorrow = false;
	
	if($currentHour == $sunsetHour && $currentMinute >= $sunsetMinute)
		$bTomorrow = true;
	elseif($currentHour > $sunsetHour)
		$bTomorrow = true;
		
	if($bTomorrow)
	{
		$dateYear = $tomorrow->format('Y');	
		$dateMonth = $tomorrow->format('m');
		$dateDay = $tomorrow->format('j');
	}
	
	$jdate = jdtojewish(gregoriantojd($dateMonth, $dateDay, $dateYear), false, CAL_JEWISH_ADD_GERESHAYIM + CAL_JEWISH_ADD_ALAFIM + CAL_JEWISH_ADD_ALAFIM_GERESH);
	$jdate = explode('/', $jdate);
	
	$jyear = intval($jdate[2]);
	$jmonth = intval($jdate[1]);
	$jday = intval($jdate[0]);

	$hebrewMonths = array(
		'Tishri',
		'Heshvan',
		'Kislev',
		'Tevet',
		'Shevat',
		'Adar',
		'Adar II',
		'Nisan',
		'Iyar',
		'Sivan',
		'Tammuz',
		'Av',
		'Elul'
	);
	$hebrewMonth = $hebrewMonths[ $jdate[0]-1 ];
	
	echo $hebrewMonth . ' ' . $jdate[1] . ', ' . $jdate[2];	
		?>
	</h1>
	<small>Note: This hebrew date will change automatically at sundown.</small>
	
	<br><br>
	
	<h2>Upcoming Holy Days</h2>
	<p>All of the following holidays begin at <b>sundown</b> on that day.</p>
	<table style="width:100%;">
	<?php
	
	//
	// Jewish Fixed Holiday dates. (MONTH, DAY)
	// DO NOT CHANGE THE FOLLOWING
	//
	
	$ROSH_HASHANAH_DATE = array(1, 1);
	$YOM_KIPPUR_DATE = array(1, 10);
	$SUKKOT_DATE = array(1, 15);
	$SHEMINI_ATZERET_DATE = array(1, 22);
	$SHAVUOT_DATE = array(10, 6);
	$PASSOVER_DATE = array(8, 15);
	$PASSOVER_END_DATE = array(8, 21);
	
	
	
	
	$holidayNames = array(
		'Rosh Hashanah',
		'Yom Kippur',
		'Sukkot',
		'Shemini Atzeret', //<small>(8th day of Sukkot)</small>
		'Shavuot',
		'Pesach',
		'End of Pesach',
	);
	
	
	$thisYearsHolidays = array(
		jewishtojd( $ROSH_HASHANAH_DATE[0], $ROSH_HASHANAH_DATE[1], $jyear ),
		jewishtojd( $YOM_KIPPUR_DATE[0], $YOM_KIPPUR_DATE[1], $jyear ),
		jewishtojd( $SUKKOT_DATE[0], $SUKKOT_DATE[1], $jyear ),
		jewishtojd( $SHEMINI_ATZERET_DATE[0], $SHEMINI_ATZERET_DATE[1], $jyear ),
		jewishtojd( $SHAVUOT_DATE[0], $SHAVUOT_DATE[1], $jyear ),
		jewishtojd( $PASSOVER_DATE[0], $PASSOVER_DATE[1], $jyear ),
		jewishtojd( $PASSOVER_END_DATE[0], $PASSOVER_END_DATE[1], $jyear ),
	);
	
	$nextYearsHolidays = array(
		jewishtojd( $ROSH_HASHANAH_DATE[0], $ROSH_HASHANAH_DATE[1], $jyear+1 ),
		jewishtojd( $YOM_KIPPUR_DATE[0], $YOM_KIPPUR_DATE[1], $jyear+1 ),
		jewishtojd( $SUKKOT_DATE[0], $SUKKOT_DATE[1], $jyear+1 ),
		jewishtojd( $SHEMINI_ATZERET_DATE[0], $SHEMINI_ATZERET_DATE[1], $jyear+1 ),
		jewishtojd( $SHAVUOT_DATE[0], $SHAVUOT_DATE[1], $jyear+1 ),
		jewishtojd( $PASSOVER_DATE[0], $PASSOVER_DATE[1], $jyear+1 ),
		jewishtojd( $PASSOVER_END_DATE[0], $PASSOVER_END_DATE[1], $jyear+1 ),
	);
	
	
	$i=0; $b=true;
	foreach($thisYearsHolidays as $holiday){
		$julianToday = GregorianToJD(date('m',$this->timestamp), date('j',$this->timestamp), date('Y',$this->timestamp));
		
		$b = !$b;
		
		if($b) $color = '#ebebf3'; else $color = '#E4E4EF';
		
		// This becomes a negative number if the date
		// has already passed
		$diff = $holiday-$julianToday;
		
		// Don't show past holidays
		if($diff > 0){
			echo '<tr style="background-color:'.$color.';">';
			echo '<td style="width:200px; padding:10px;">'.$holidayNames[$i] .'</td>';
			echo '<td style="text-align:right;font-weight:bold;padding:10px;">'.date('F jS, Y',jdtounix( $holiday )).' ('.date('g:i A', date_sunset(jdtounix($holiday), SUNFUNCS_RET_TIMESTAMP ) ).')</td>';
			echo '</tr>';
		}
		
		$i++;
	}
	
	$i=0; $b=true;
	foreach($nextYearsHolidays as $holiday){
		$julianToday = GregorianToJD(date('m',$this->timestamp), date('j',$this->timestamp), date('Y',$this->timestamp));
		
		$b = !$b;
		
		if($b) $color = '#ebebf3'; else $color = '#E4E4EF';
		
		// This becomes a negative number if the date
		// has already passed
		$diff = $holiday-$julianToday;
		
		// Don't show past holidays
		if($diff > 0){
			echo '<tr style="background-color:'.$color.';">';
			echo '<td style="width:200px; padding:10px;">'.$holidayNames[$i] .'</td>';
			echo '<td style="text-align:right;font-weight:bold;padding:10px;">'.date('F jS, Y',jdtounix( $holiday )).' ('.date('g:i A', date_sunset(jdtounix($holiday), SUNFUNCS_RET_TIMESTAMP ) ).')</td>';
			echo '</tr>';
		}
		
		$i++;
	}
	
	
	
	?>
	</table>
	
	<?php
	/*
	Yom Kippur 2015 &mdash; <?php echo '<b>'.date('F jS',jdtounix(jewishtojd(1, 10, 5776)) ).'</b>';?>
	<br>
	Yom Kippur 2016 &mdash; <?php echo '<b>'.date('F jS',jdtounix(jewishtojd(1, 10, 5777)) ).'</b>';?>
	<br>
	Yom Kippur 2017 &mdash; <?php echo '<b>'.date('F jS',jdtounix(jewishtojd(1, 10, 5778)) ).'</b>';?>
	<HR>
	
	<br>
	Passover 2016 &mdash; <?php echo '<b>'.date('F jS Y',jdtounix(jewishtojd(8, 15, 5776)) ).'</b>';?>
	<br>
	Passover 2017 &mdash; <?php echo '<b>'.date('F jS Y',jdtounix(jewishtojd(8, 15, 5777)) ).'</b>';?>
	<br>
	Passover 2018 &mdash; <?php echo '<b>'.date('F jS Y',jdtounix(jewishtojd(8, 15, 5778)) ).'</b>';?>
	*/
	?>
	</div>
</div>



<div id="homePage-rightCollumn" style="float:right; width:39%;">
    <?php include($this->siteDirectory.'/_inc/php/theme-sidebar.php'); ?>
</div>
<div class="cf"></div>

<?php
    }
}

new customPage();