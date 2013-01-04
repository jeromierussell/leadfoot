<?php
	function showMemberWeeklyStats( $user_name )
	{
		$sql_member = "SELECT nickname FROM members m WHERE m.username='".$user_name."';";
		$results_member = mysql_query($sql_member);
		$member_row = mysql_fetch_assoc($results_member);
		
		$sql = "SELECT s.week, s.trackname, s.track_id, s.id AS schedule_id, p.picksequence, p.pointsaward, p.ytdpoints, p.dollarswon, p.ytddollarswon, p.previousrank, p.newrank, d.name AS drivername, r.finishingpos, r.startingpos ".
			" FROM schedule s ".			
			" INNER JOIN picks p ON p.schedule_id=s.id ".
			" INNER JOIN drivers d ON d.id = p.driver_id ".
			" INNER JOIN historicalraceresults r ON r.id=p.result_id ".
			" INNER JOIN members m ON m.id=p.member_id ".
			" WHERE s.year=".SEASON_YEAR." AND s.closed=1 AND s.pointsrace=1 AND m.username='".$user_name."' ".
			" ORDER BY s.week;";
		$results = mysql_query($sql) or die(mysql_error());
		
		echo "<div>";
		echo "<h2>Weekly results for ".$member_row['nickname']."</h2>";
		echo "<table id='weeklyresults'>";
		echo "<tr><th class='sortable'>Week</th><th class='sortable'>Track</th><th class='sortable'>Pick Sequence</th><th class='sortable'>Driver</th><th class='sortable'>Start</th><th class='sortable'>Finish</th><th class='sortable'>Week Points</th><th class='sortable'>YTD Points</th><th class='sortable'>Week Dollars</th><th class='sortable'>YTD Dollars</th><th class='sortable'>Rank</th><th class='sortable'>Change</th></tr>";
		while( $row = mysql_fetch_assoc($results) )
		{			
			$rank_change = $row['previousrank']-$row['newrank'];
			$rank_change_class = "";
			if( $rank_change == 0 )
			{
				$rank_change = "";
			}
			else if( $rank_change > 0 )
			{
				$rank_change = "+".$rank_change;
				$rank_change_class = "green";
			}
			else
			{
				$rank_change_class = "red";
			}
			
			echo "<tr><td align='center'>".
				"<a href='".$config_basedir."index.php?content_page=raceresults&race=".$row['schedule_id']."'>".
				$row['week']."</a></td><td align='center'>".
				"<a href='".$config_basedir."index.php?content_page=historicalresults&track_id=".$row['track_id']."'>".
				$row['trackname']."</a></td><td align='center'>".
				$row['picksequence']."</td><td nowrap align='center'>".
				"<a href='".$config_basedir."index.php?content_page=driver&display_driver=".$row['drivername']."'>".
				$row['drivername'].
				"</a>"."</td><td align='center'>".
				$row['startingpos']."</td><td align='center'>".
				$row['finishingpos']."</td><td align='center'>".
				$row['pointsaward']."</td><td align='center'>".
				$row['ytdpoints']."</td><td align='center'>".
				$row['dollarswon']."</td><td align='center'>".
				$row['ytddollarswon']."</td><td align='center'>".
				$row['newrank']."</td><td align='center' class='".$rank_change_class."'>".				
				$rank_change."</td></tr>";
		}		
		echo "</table>";
		echo "</div>";
	}
?>