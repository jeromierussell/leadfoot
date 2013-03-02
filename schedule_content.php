	<div class="post">
		<div class="story">
	<?php 
		$display_year = $_REQUEST['display_year'];
		if( !isset( $display_year ) )
		{
			$display_year = SEASON_YEAR;
		}
		echo "<h1>".$display_year." Schedule</h1>";
		echo "<div align='right'>";
		echo "<form action='".$config_basedir."index.php?content_page=schedule' method='post'>\n";
		echo "<select name='display_year'>\n";
	
		$sql_distinct_years = "select distinct(year) as dyear from schedule order by dyear DESC";
		$results_years = mysql_query($sql_distinct_years);
		while( $row_years = mysql_fetch_assoc($results_years) )
		{
			$selected = " ";
			if( $row_years['dyear'] == $display_year )
			{
				$selected = " selected ";
			}
			echo "<option value='".$row_years['dyear']."'".$selected.">".$row_years['dyear']."</option>";
		}
		echo "</select>\n";
		echo "<input type='submit' name='submit' value='Refresh'>\n";
		echo "</form>";
		echo "</div>";
	?>	
	<div class="story">
	
	<table id="schedule">
	<?php											
		$sql = "SELECT s.week, s.id as race_id, DATE_FORMAT(s.dt,'%a %b %D, %Y') AS date, DATE_FORMAT(s.dt,'%h:%i %p') AS time, s.pointsrace AS pointsrace, s.racename AS race,t.name AS track,s.track_id, s.station, s.dt FROM schedule s INNER JOIN tracks t ON s.track_id = t.id WHERE s.year = " . $display_year . " ORDER BY s.dt ASC;--";
		$results = mysql_query($sql);
		echo "<tr><th align='center'>#</th><th>Race</th><th>Track</th><th align='center'>Station</th><th align='center'>Time (ET)</th><th>Date</th><th>Points Race?</th></tr>\n";
		$count=0;
		while($row = mysql_fetch_assoc($results))
		{	$count++;
			if($row['pointsrace'] == 1)
			{
				$pointsrace = "Yes";
			}
			else
			{
				$pointsrace = "No";
			}
			$date = $row['dt'];
			$today = date('Y-m-d H:i:s');
			// Only indicate a chosen/old/closed row when looking at the current season
			if($date < $today)
			{
				$trclass = "";
				if(SEASON_YEAR == $display_year)
				{
					$trclass = "class='old'";
				}
				//strikeout
				echo "<tr ". $trclass . "><td align='center'>".$row['week']."</td><td>" . 
				"<a href='./index.php?content_page=raceresults&race=" . $row['race_id'] . "'>" . $row['race'] . "</a>" . "</td><td><a href='./index.php?content_page=historicalresults&track_id=".$row['track_id']."'>" . $row['track'] . "</a></td><td align='center'>" . 
				$row['station'] . "</td><td align='center'>" . $row['time'] . "</td><td>" . 
				$row['date'] . "</td><td align='center'>" . $pointsrace . "</td></tr>\n";
			}
			else
			{
				echo "<tr><td align='center'>".$row['week']."</td><td>" . $row['race'] . "</td><td><a href='./index.php?content_page=historicalresults&track_id=".$row['track_id']."'>" . $row['track'] . "</a></td><td align='center'>" . $row['station'] . "</td><td align='center'>" . $row['time'] . "</td><td>" . $row['date'] . "</td><td align='center'>" . $pointsrace . "</td></tr>\n";
			}
		}
	?>
	</table>
	</div>
		
</div> <!-- post -->
