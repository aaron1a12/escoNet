<?php

class customPage extends page {
    public $title = 'Time in Casa Escobar';
    
	public $timestamp;
	
    function init()
    {      
		$this->timestamp = time();
    }
	
    function content() {
?>
<div style="float:left; width:60%;">
	
	<div class="widget">
		<h1>Calendar</h1>
		<?php
		
		$time = time();
		//$time = strtotime("10 September 2000");
		
		$monthBegin = strtotime(date('Y-m-', $time).'01');
		
		$begin = new DateTime( date('Y-m-', $time).'01' );
		$end = new DateTime( date('Y-m-d', strtotime('+13 month -1 day', $monthBegin)) );
		$end = $end->modify('+1 day');
		
		$interval = new DateInterval('P1D');
		$daterange = new DatePeriod($begin, $interval, $end);

		$dayOfWeek = 0;
		$month = 0;
		
		$lastDayOfWeek = 0;
		$lastMonth = 0;
		
		$collumnsWritten = 0;
		
		$monthsDone = 0;
		
		foreach($daterange as $date){
			//echo $date->format('F_j,_Y') . '<br>';
			$month = intval($date->format('n'));
			$dayOfWeek = intval($date->format('N'));
			
			if($lastMonth!==$month){
				$lastMonth = $month;
				
				if($monthsDone>0){
						echo '</tr>';
					echo '</tbody>';
					echo '</table><br><br>';
					
					$collumnsWritten = 0;
				}
				
				echo '<h2 style="text-align:center;"><b>'.$date->format('F, Y').'</b></h2>';
				echo '<table class="calendar">';
					echo '<thead>';
						echo '<tr>';
							echo '<td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td>';
						echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
					echo '<tr>';
					
				$monthsDone++;
			}
					
			$collumnsThatShouldveBeenWritten = $dayOfWeek;
			
			if($collumnsWritten != $collumnsThatShouldveBeenWritten){
				for($i=0; $i < $collumnsThatShouldveBeenWritten; $i++){
					echo '<td></td>';
					$collumnsWritten = $collumnsThatShouldveBeenWritten;
				}
			}
			
			if($dayOfWeek==7){
				$collumnsWritten = 0;
				echo '</tr><tr>';
			}
			
			
			if($date->format('Y-m-d') == date('Y-m-d', $time)) 
				echo '<td class="today">'.$date->format('j').'</b></td>';
			else
				echo "<td>".$date->format('j')."</td>";
			
			
			$collumnsWritten++;
		}
			echo '</tr>';
		echo '</tbody>';
		echo '</table>';
		
		?>
		
	</div>
	
</div>


<div style="float:right; width:39%;">

	<div class="widget">
		<h1>Events</h1>
		<p>Please select a day in the calendar.</p>
	</div>
    
</div>
<div class="cf"></div>

<?php
    }
}

new customPage();