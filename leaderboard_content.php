<div class="post widePost">
	<div class="story">
	<?php echo "<h1>Leaderboard through week ".SEASON_COMPLETED_WEEK." of the ".SEASON_YEAR." season</h1>"; ?>
    <div id="sortInstructions">* Click on column headers of numerical columns to sort.  For example, click on "Wins" to sort by wins.  Click "Wins" again to sort in the reverse direction.</div>
	<table id="leaderboard">
	<?php
		
		require("next_race.php");
		
		$sql_10th_place = "SELECT r.points FROM annualmemberresults r WHERE r.year=".SEASON_YEAR." AND r.rank=10;";
		$results_10th_place = mysql_query($sql_10th_place) or die(mysql_error());
		$row_10th_place = mysql_fetch_assoc($results_10th_place);
		$points_10th_place = $row_10th_place['points'];
		
		// then we'll use that to grab all the members, their stats, and pick for the next race.
		$sql =  "SELECT m.nickname,m.username,r.points,r.weekpoints,r.rank,r.previousrank,r.picksequence,m.id,r.dollarswon,r.dollarspaid, r.poles,r.wins,r.top5,r.top10,r.top15,r.top20,r.avgfinish,r.avgpoints,r.avgpick, rr.finishingpos FROM members m INNER JOIN annualmemberresults r ON m.id=r.member_id LEFT OUTER JOIN picks p ON p.member_id=m.id and p.schedule_id=".$prev_race_id." LEFT OUTER JOIN historicalraceresults rr ON rr.schedule_id=p.schedule_id and rr.driver_id=p.driver_id WHERE r.year=".SEASON_YEAR." ORDER BY r.rank ASC, r.picksequence ASC;--";

		$results = mysql_query($sql) or die(mysql_error());
		$count = 0;
		$maxrankup = 0;
		$maxrankupid = -1;
		$maxrankdown = 0;
		$maxrankdownid = -1;
		while($row = mysql_fetch_assoc($results))
		{
			$count++;
			$member_id = $row['id'];
			
			$rank_change = intval($row['previousrank']) - intval($row['rank']);
			if( SEASON_COMPLETED_WEEK > 0 )
			{
				if($rank_change > $maxrankup)
				{
					$maxrankupid = $member_id;
					$maxrankup = $rank_change;
				}
				if($rank_change < $maxrankdown)
				{
					$maxrankdownid = $member_id;
					$maxrankdown = $rank_change;
				}
			}			
		}


		$results = mysql_query($sql);
		echo "<thead>";
		echo "<tr class='mimicTh'><td class='mimicTh' align='center' colspan=7>Current</td><td class='mimicTh' align='center' colspan=2>Next Race</td><td class='mimicTh' align='center' colspan=4>Last Race</td><td class='mimicTh' align='center' colspan=99>YTD</td></tr>";
		echo "<tr><th align='center' class='sortable-numeric'>Current<br>Rank</th><th><br>Member</th><th class='sortable-numeric'>Current<br>Points</th>" .
			 "<th class='sortable-numeric' align='center'>Previous<br>Rank</th><th class='sortable-numeric' align='center'>Rank<br>Change</th><th class='sortable-numeric' align='center'>Behind<br>Leader</th><th class='sortable-numeric'>From 10th</th>" .
			 "<th class='sortable-numeric'>Next<br>Pick<br>Seq.</th><th>Next Race Pick</th><th class='sortable-numeric'>Last<br>Pick<br>Seq.</th><th>Last Race Pick</th><th class='sortable-numeric'>Finish</th><th class='sortable-numeric'>Week<br>Points</th><th class='sortable-numeric'>Poles</th><th class='sortable-numeric'>Wins</th><th class='sortable-numeric'>Top 5</th><th class='sortable-numeric'>top 10</th><th class='sortable-numeric'>top 15</th><th class='sortable-numeric'>top 20</th><th class='sortable-numeric'>Avg Finish</th><th class='sortable-numeric'>Avg Points</th><th class='sortable-numeric'>Avg Pick</th></tr>\n";
		echo "</thead>";
		echo "<tbody>";
		$count = 0;
		while($row = mysql_fetch_assoc($results))
		{	$count++;
			if($count == 1)
			{
				$leader_points = $row['points'];
			}
			$behind = $leader_points - $row['points'];
			$member_id = $row['id'];
			
			// TODO:  Fix this...split into two queries i guess
			$sql = "SELECT d.number, d.name AS drivername, s.racename AS racename, p.picksequence FROM picks p INNER JOIN drivers d ON p.driver_id=d.id INNER JOIN schedule s ON s.id=p.schedule_id WHERE schedule_id = ".$prev_race_id." AND member_id=" . $member_id . " ORDER BY s.week";			
						
			$d_results = mysql_query($sql) or die(mysql_error());			
			$driver_row_prev = mysql_fetch_assoc($d_results);
			
			$sql = "SELECT d.number, d.name AS drivername, s.racename AS racename, p.picksequence FROM picks p INNER JOIN drivers d ON p.driver_id=d.id INNER JOIN schedule s ON s.id=p.schedule_id WHERE schedule_id = ".$next_race_id." AND member_id=" . $member_id . " ORDER BY s.week";
			$d_results = mysql_query($sql) or die(mysql_error());
			$driver_row_next = mysql_fetch_assoc($d_results);
			
			$rank_change = intval($row['previousrank']) - intval($row['rank']);					
			$rank_change_class = "";
			if($rank_change == 0 || SEASON_COMPLETED_WEEK == 0 || SEASON_COMPLETED_WEEK == 1)
			{
				$rank_change = "";
			}
			else if($rank_change > 0)
			{
				$rank_change_class = "green";	// hot				
				$rank_change = "+" . strval($rank_change);
			}
			else
			{			
				$rank_change_class = "red";	//cold
				$rank_change = strval($rank_change);
			}
			
			echo "<tr><td align='center'>" . $row['rank'] . "</td><td nowrap align='center'>";
			if($member_id == $maxrankupid)
			{
				echo "<img src='./images/fireicon.jpg' width='10' height='15'/>&nbsp;";
			}
			else if($member_id == $maxrankdownid)
			{
				echo "<img src='./images/coldicon.jpg' width='10' height='15'/>&nbsp;";
			}
			
			echo "<a href='".$config_basedir."index.php?content_page=mystats&username=".$row['username']."'>".
				$row['nickname'].
				"</a>";
						
			if($member_id == $maxrankupid)
			{
				echo "&nbsp;<img src='./images/fireicon.jpg' width='10' height='15'/>";
			}
			else if($member_id == $maxrankdownid)
			{
				echo "&nbsp;<img src='./images/coldicon.jpg' width='10' height='15'/>";
			}
			
			$previous_rank = $row['previousrank'];
			if( SEASON_COMPLETED_WEEK == 0 )
			{
				$previous_rank = "";
			}

			echo "</td><td align='center'>" .
			$row['points'] . "</td><td align='center'>" .
				 $previous_rank . "</td><td align='center' class='".$rank_change_class."'>" .
				 $rank_change . "</td><td align='center'>" .
				 $behind . "</td><td align='center'>" .
				 ($points_10th_place - $row['points'])."</td><td align='center' class='darker2'>".
				 $row['picksequence'] . "</td><td nowrap align='center' class='darker2'>" .
				 $driver_row_next['number'] . " - " .
				 "<a href='".$config_basedir."index.php?content_page=driver&display_driver=".$driver_row_next['drivername']."'>".
				 $driver_row_next['drivername'].
				 "</a>"."</td><td align='center'>" .
				 $driver_row_prev['picksequence']."</td><td nowrap align='center'>".
				 $driver_row_prev['number'] . " - " .
				 "<a href='".$config_basedir."index.php?content_page=driver&display_driver=".$driver_row_prev['drivername']."'>".
				 $driver_row_prev['drivername'].
				 "</a>"."</td><td align='center'>" .
				 $row['finishingpos'] . "</td><td align='center'>" .
				 $row['weekpoints'] . "</td><td align='center' class='darker2'>" .
				 $row['poles'] . "</td><td align='center' class='darker2'>".
				 $row['wins'] . "</td><td align='center' class='darker2'>".
				 $row['top5'] . "</td><td align='center' class='darker2'>".
				 $row['top10'] . "</td><td align='center' class='darker2'>".
				 $row['top15'] . "</td><td align='center' class='darker2'>".
				 $row['top20'] . "</td><td align='center' class='darker2'>".
				 $row['avgfinish'] . "</td><td align='center' class='darker2'>".
				 $row['avgpoints'] . "</td><td align='center' class='darker2'>".
				 $row['avgpick'] . "</td></tr>\n";
		}
	?>
	</tbody>	
	</table>
	</div>
</div> <!-- right-col -->