<div class="post">
	<h2 class="title">Poles</h2>
	<div class="story">
		<table id="poles">
			<tr>
	<?php
		$sql = "SELECT s.racename AS racename, s.id as race_id, m.nickname, m.username, DATE_FORMAT(s.dt,'%a %b %D, %Y') AS date, s.dt, d.name AS drivername FROM schedule s LEFT OUTER JOIN historicalraceresults r ON r.schedule_id = s.id and r.startingpos=1 LEFT OUTER JOIN picks p ON p.schedule_id=r.schedule_id and r.driver_id=p.driver_id LEFT OUTER JOIN members m ON p.member_id = m.id LEFT OUTER JOIN drivers d ON p.driver_id = d.id WHERE s.pointsrace=1 and s.year=".SEASON_YEAR." ORDER BY s.dt ASC;--";
		$results = mysql_query($sql) or die(mysql_error());
		$today = date('Y-m-d H:i:s');

		echo "<tr><th align='center'>Date</th><th>Race</th><th>Driver</th><th>Member</th></tr>\n";
		while($row = mysql_fetch_assoc($results))
		{
     		$date = $row['dt'];
		    echo "<tr><td align='center'>" .  $row['date'] . "</td><td>";
		
    		// Only show historical results for old races
	    	if($date < $today)
		    {
				echo "<a href='./index.php?content_page=raceresults&race=" . $row['race_id'] . "'>".$row['racename']."</a>";
			}
			else
			{
			    echo $row['racename'];	
			}
			
			echo "</td><td>" .
			"<a href='".$config_basedir."index.php?content_page=driver&display_driver=".$row['drivername']."'>".
			$row['drivername'] . "</a></td><td>" .
			"<a href='".$config_basedir."index.php?content_page=mystats&username=".$row['username']."'>".
		    $row['nickname']."</a></td></tr>\n";
		}
	?>
			</tr>
		</table>
	</div>
</div>
