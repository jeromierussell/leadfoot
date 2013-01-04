<div class="post">    
    <div class="story">
        <?php
			$schedule_id = $_GET['race'];			

			// First we need to find out was the last race closed was, then query for the results of that race and display them in a nice pretty table for everyone to see.
			$today = date('Y-m-d H-i-s');
			
			$sql = "SELECT s.week, s.year, s.id as schedule_id, s.dt, s.racename as name, s.pointsrace FROM schedule s WHERE s.id = ".$schedule_id .";--";
			$results = mysql_query($sql);
			$row = mysql_fetch_assoc($results);
			$race_id = $row['schedule_id'];
			$race_date = $row['dt'];
			$race_name = $row['name'];
			$year = $row['year'];
		
            echo "<h1 class=title'>Race Results Week ".$row['week']." ".$row['year']."</h1>";
			echo "<table id='raceresults'>";
			
			$sql = "SELECT d.name AS drivername,r.points,r.bonuspoints,r.totalpoints,r.startingpos,r.finishingpos,r.winnings, m.nickname AS membername, m.username, p.picksequence FROM historicalraceresults r INNER JOIN drivers d ON r.driver_id = d.id LEFT OUTER JOIN picks p ON p.schedule_id=r.schedule_id AND p.driver_id=d.id LEFT OUTER JOIN members m ON m.id=p.member_id WHERE r.schedule_id='" . $schedule_id . "' ORDER BY r.finishingpos ASC;--";
			$results = mysql_query($sql);
			
			echo "<h2>" . $race_name . " on " . date('F j, Y', strtotime($race_date)) . "</h2>"; 
			
			echo "<tr><th>Fin. Pos.</th><th>Driver</th><th>Points</th><th>Bonus</th><th>Total<br>Points</th><th>Start Position</th><th>Winnings</th><th>Picked By</th><th>Pick #</th></tr>\n";
			while($row = mysql_fetch_assoc($results))
			{
				echo "<tr><td align='center'>" . 
				$row['finishingpos'] . "</td><td align='center'>" . 
				"<a href='".$config_basedir."index.php?content_page=driver&display_driver=".$row['drivername']."&display_driver_year=".$year."'>".
				$row['drivername'].
				"</a>" . "</td><td align='center'>" . 
				$row['points'] . "</td><td align='center'>" . 
				$row['bonuspoints'] . "</td><td align='center'>" .
				$row['totalpoints'] . "</td><td align='center'>" .
				$row['startingpos'] . "</td><td align='right'>".
				"$".number_format($row['winnings']) . "</td><td align='center'>".
				"<a href='".$config_basedir."index.php?content_page=mystats&username=".$row['username']."'>".
				$row['membername'].
                "</a></td><td align='center'>".
                $row['picksequence'].
                "</td></tr>\n";
			}
			echo "</table>";
		?>
	</div>
</div> <!-- right-col -->