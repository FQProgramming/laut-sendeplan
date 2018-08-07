<?php
define('STATION', '#####');
$days = array( "mon" => "Montag", "tue" => "Dienstag", "wed" => "Mittwoch", "thu" => "Donnerstag", "fri" => "Freitag", "sat" => "Samstag", "sun" => "Sonntag" );

function get_station_data() {
	$json = file_get_contents('http://api.laut.fm/station/' . STATION);
	$obj = json_decode($json);
	
	return $obj;
}

function get_song_data(){
	$json = file_get_contents('http://api.laut.fm/station/' . STATION . '/current_song');
	$obj = json_decode($json);
	
	return $obj;
}

function get_schedule_data(){
	$json = file_get_contents('http://api.laut.fm/station/' . STATION . '/schedule');
	$obj = json_decode($json);
	
	return $obj;
}

function get_schedules() {
	$schedule = get_schedule_data();
	
	$current_day = "";
	$schedules = array();
	foreach ($schedule as $entry) {
		if ($entry->day != $current_day){
			$schedules[$entry->day] = array();
			$current_day = $entry->day;
		}
		$the_schedule = array();
		$the_schedule['start'] = $entry->hour;
		$the_schedule['end'] = $entry->end_time;
		$the_schedule['name'] = $entry->name;
		
		$schedules[$entry->day][] = $the_schedule;
	}
	
	return $schedules;
}

$schedules = get_schedules();
?>
<div class="tabs">
	<?php
	$i = 0;
	foreach($schedules as $day => $the_schedules):
		echo '<input type="radio" id="tab'.$i.'" name="tab"';
		setlocale(LC_TIME, "de_DE.utf8");
		$wochentag = strftime("%A");
		if ($days[$day] == $wochentag) {
		echo 'checked="checked"';
		}
		else {
		echo "";
		};
		echo '>'."\n";
		echo '<label class="tabButton" for="tab'.$i.'">'.$days[$day].'</label>'."\n";
		echo '<div class="tab">'."\n";
		foreach($the_schedules as $schedule) {
			echo "\t".'<p>Von ';
			if(intval($schedule['start']) < 10){
				echo '0';
			}
			echo $schedule['start'].':00';
			echo ' Uhr bis ';	
			if(intval($schedule['end']) < 10){
				echo '0';
			}
			echo $schedule['end'].':00';
			echo ' Uhr: ';
			echo $schedule['name'];
			echo '</p>'."\n";
		}

		echo '</div>'."\n";
		$i++;
	endforeach;  
	?>
</div>
