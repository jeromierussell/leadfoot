<div id='right-col' class='post'>
	<div id='main_content'>

<?php
	include('fetchresults.php');
	include('emailfunctions.php');
		
	$espn_race_id = $_POST['espnkey'];
	
	if( !$_POST['fetch'] && !$_POST['load'] )
	{
		echo "<form action='".$config_basedir."index.php?content_page=adminloadresults' method='post'>\n";		
		echo "<h2>Fetch results for race ".SEASON_NEXT_WEEK." of ".SEASON_YEAR."?</h2>\n";
		echo "<h3>Enter espn.com raceid e.g. 201302240001</h3><input type='text' name='espnkey' size='50'/>";
		echo "<input type='submit' name='fetch' value='Fetch'/>\n";
		echo "</form>";
	}
	// attempt to fetch the race results and dispaly them for confirmation before loading
	else if( $_POST['fetch'] )
	{		
		$raceresults = getCurrentRaceResults( $espn_race_id );
		$allow_load = false;			
		
		$unknown_drivers=false;
		
		$table_string = "";
		$table_string .= "<table id='results'>";
		$table_string .= "<tr><th>Finish Pos</th><th>Starting Pos</th><th>Car No</th><th>Driver</th><th>Make</th><th>Points</th><th>Bonus</th><th>Total<br>Points</th></tr>";
		for( $i = 0; $i < count($raceresults); $i++ )
		{
			$rowdata = $raceresults[$i];
			if( is_numeric($rowdata['finishpos']) && is_numeric($rowdata['startpos']) )
			{
				$allow_load = true;
				$sql_driver = "SELECT d.id FROM drivers d WHERE d.name='".$rowdata['driver']."' AND isactive=1";
				$results_driver = mysql_query($sql_driver) or die(mysql_error());
				$rows = mysql_num_rows($results_driver);
				if( $rows < 1 )
				{
					$unknown_drivers=true;
					echo "<h2>ERROR: unknown driver [".$rowdata['driver']."] - You must add this driver before results can be loaded.</h2><p>";
				}
				else if( $rows > 1 )
				{
					$unknown_drivers=true;
					echo "<h2>ERROR: found more than 1 active driver by the name [".$rowdata['driver']."] - You must ensure only one driver by this name is active before results can be loaded.</h2><p>";
				}
				$table_string .= "<tr><td>".
					$rowdata['finishpos']."</td><td>".
					$rowdata['startpos']."</td><td>".
					$rowdata['carnum']."</td><td>".
					$rowdata['driver']."</td><td>".
					$rowdata['carmake']."</td><td>".					
					$rowdata['points']."</td><td>".
					$rowdata['bonus']."</td><td>".
					$rowdata['totalpoints']."</td>".
					"</tr>";
			}			
		}
		$table_string .= "</table>";
		echo $table_string;
		if( $unknown_drivers )
		{
			echo "<p><p><h1>You must correct driver errors before results can be loaded.</h1>";
		}
		else if( $allow_load )
		{
			echo "<h2>Click 'Load' to import these results</h2>";
			echo "<form action='".$config_basedir."index.php?content_page=adminloadresults' method='post'>";
			echo "<input type='hidden' name='espnkey' value='".$espn_race_id."'>";
			echo "<input type='submit' name='load' value='Load'>\n";
			echo "</form>";
		}		
	}
	// attempt to import the results
	else if( $_POST['load'] )
	{
		// fetch the current schedule entry for the race we are processing
		$schedule_id = getScheduleId();
		
		echo "Inserting race results...<p>";
		// insert rows into the historicalraceresults table
		$raceresults = getCurrentRaceResults( $espn_race_id );
		$race_key = SEASON_YEAR.str_pad(SEASON_NEXT_WEEK, 2, "0", STR_PAD_LEFT);
		
		for( $i = 0; $i < count($raceresults); $i++ )
		{
			$rowdata = $raceresults[$i];
			$driver_id = getDriverId( $rowdata['driver'] );
			if( is_numeric($rowdata['startpos']) && is_numeric($rowdata['finishpos']) && is_numeric($rowdata['points']) )
			{
				$sql = "INSERT INTO historicalraceresults (year, week, drivername, carnumber, startingpos, finishingpos, points, bonuspoints, totalpoints, carmake, schedule_id, driver_id, racekey)".
				" VALUES (".SEASON_YEAR.", ".SEASON_NEXT_WEEK.", '".$rowdata['driver']."', '".$rowdata['carnum']."', ".$rowdata['startpos'].", ".$rowdata['finishpos'].", ".$rowdata['points'].", ".$rowdata['bonus'].
				", ".$rowdata['totalpoints'].", '".$rowdata['carmake']."', ".$schedule_id.", ".$driver_id.", '".$race_key."');";
				echo "&nbsp;&nbsp; Driver [".$rowdata['driver']."] finish [".$rowdata['finishpos']."] start [".$rowdata['startpos']."] points [".$rowdata['points']."]<p>";				
				mysql_query($sql) or die(mysql_error());						
			}
			
		}
		
		echo "\nUpdating picks to associate with results...<p>";
		// associate pick rows to the results rows, update pick rows to show points awarded and money won
		$sql_update_picks = 
			"UPDATE picks p SET ".
			" p.result_id=(SELECT id FROM historicalraceresults r WHERE r.schedule_id=p.schedule_id AND r.driver_id=p.driver_id), ".
			" p.pointsaward=(SELECT totalpoints FROM historicalraceresults r WHERE r.schedule_id=p.schedule_id AND r.driver_id=p.driver_id) ".
			" WHERE p.schedule_id=".$schedule_id.";";
		mysql_query($sql_update_picks) or die(mysql_error());		
		
		echo "\nUpdating member stats...<p>";
		// update the individual member points awarded and money won (annualmemberresults)
		$sql_select_top_finishers = 
			"SELECT r.finishingpos, p.id ".
			" FROM historicalraceresults r ".
			" INNER JOIN picks p ON p.result_id=r.id ".
			" WHERE r.schedule_id=".$schedule_id.
			" ORDER BY r.finishingpos ".
			" LIMIT 2;";
		$results_top_finishers = mysql_query($sql_select_top_finishers) or die(mysql_error());
		$finish_count = 1;
		while( $row = mysql_fetch_assoc($results_top_finishers) )
		{
			$dollars = null;
			$finish_pos = $row['finishingpos'];
			if( $finish_count == 1 )
			{
				if( $finish_pos == 1 )
				{
					$dollars = 50;
				}
				else
				{
					$dollars = 30;
				}
			}
			else if( $finish_count == 2 )
			{
				$dollars = 10;
			}
			$finish_count++;
			if( isset( $dollars ) )
			{			
				$sql_update_dollars = "UPDATE picks p SET p.dollarswon=".$dollars." WHERE p.id=".$row['id'].";";
				mysql_query($sql_update_dollars) or die(mysql_error());
			}
		}
		
		// set new pick sequence for members
		$sql_select_active_members = "SELECT r.id AS annual_id, r.member_id, r.rank, r.points, r.poles, r.wins, r.top5, r.top10, r.top15, r.top20, r.dollarswon, r.balance, ".
			" p.dollarswon AS weekdollars, p.pointsaward AS weekpoints, p.id AS pick_id, hrr.finishingpos, hrr.startingpos ".
			" FROM annualmemberresults r ".
			" INNER JOIN picks p ON p.member_id=r.member_id AND p.schedule_id=".$schedule_id.
			" INNER JOIN historicalraceresults hrr ON hrr.id=p.result_id ".
			" WHERE r.year=".SEASON_YEAR.
			" ORDER BY hrr.finishingpos DESC;";
		$results_active_members = mysql_query($sql_select_active_members) or die(mysql_error());
		$picksequence = 1;
		while($row = mysql_fetch_assoc($results_active_members))
		{
			$annual_id = $row['annual_id'];
			$previousrank = $row['rank'];
			$points = $row['points'];
			$weekpoints = $row['weekpoints'];
			$weekdollars = $row['weekdollars'];
			$poles = $row['poles'];
			$wins = $row['wins'];
			$top5 = $row['top5'];
			$top10 = $row['top10'];
			$top15 = $row['top15'];
			$top20 = $row['top20'];
			$dollarswon = $row['dollarswon'];
			$balance = $row['balance'];
			
			// add week points to total points
			$points += $weekpoints;
			$dollarswon += $weekdollars;
			$balance = $balance - $weekdollars;
			
			$finish = $row['finishingpos'];
			if( $row['startingpos'] == 1 )
			{
				$poles++;
			}
			if( $finish == 1 )
			{
				$wins++;
			}
			if( $finish <= 5 )
			{
				$top5++;
			}
			if( $finish <= 10 )
			{
				$top10++;
			}
			if( $finish <= 15 )
			{
				$top15++;
			}
			if( $finish <= 20 )
			{
				$top20++;
			}
			
			$sql_avg = 
				"SELECT AVG(p.picksequence) AS avg_pick, AVG(r.finishingpos) AS avg_finish, AVG(r.totalpoints) AS avg_points ".
				" FROM picks p ".
				" INNER JOIN historicalraceresults r ON r.id=p.result_id ".
				" WHERE p.member_id=".$row['member_id']." AND p.year=".SEASON_YEAR.";";
			$results_avg = mysql_query($sql_avg) or die(mysql_error());
			$row_avg = mysql_fetch_assoc($results_avg);
			$avg_pick = $row_avg['avg_pick'];
			$avg_finish = $row_avg['avg_finish'];
			$avg_points = $row_avg['avg_points'];
			
			// update the annual row with current stats
			$sql_update = "UPDATE annualmemberresults r SET ".
				" r.points=".$points.
				", r.previousrank=".$previousrank.
				", r.weekpoints=".$weekpoints.
				", r.picksequence=".$picksequence.
				", r.poles=".$poles.
				", r.wins=".$wins.
				", r.top5=".$top5.
				", r.top10=".$top10.
				", r.top15=".$top15.
				", r.top20=".$top20.
				", r.avgfinish=".$avg_finish.
				", r.avgpoints=".$avg_points.
				", r.avgpick=".$avg_pick.
				", r.dollarswon=".$dollarswon.
				", r.balance=".$balance.
				" WHERE r.id=".$annual_id.";";
			mysql_query($sql_update) or die(mysql_error());
			
			// update the picks row so we have a week by week history of points, rank, etc.
			$sql_update = "UPDATE picks p SET p.ytddollarswon=".$dollarswon.", p.previousrank=".$previousrank.", p.ytdpoints=".$points." WHERE p.id=".$row['pick_id'].";";
			mysql_query($sql_update) or die(mysql_error());
			
			$picksequence++;
		}
		
		echo "\nUpdating member rank...<p>";
		$sql_select_rank = "SELECT r.id AS annual_id, p.id AS pick_id FROM annualmemberresults r ".
			" INNER JOIN picks p ON p.member_id=r.member_id AND p.schedule_id=".$schedule_id.
			" WHERE r.year=".SEASON_YEAR." ORDER BY r.points DESC;";
		$results_rank = mysql_query($sql_select_rank) or die(mysql_error());
		$rank = 1;
		while( $row = mysql_fetch_assoc($results_rank) )
		{
			$sql_update = "UPDATE annualmemberresults r SET r.rank=".$rank." WHERE r.id=".$row['annual_id'].";";
			mysql_query($sql_update) or die(mysql_error());
			$sql_update = "UPDATE picks p SET p.newrank=".$rank." WHERE p.id=".$row['pick_id'].";";
			mysql_query($sql_update) or die(mysql_error());
			$rank++;
		}
		
		echo "\nUpdating race week...<p>";
		// update completed week on website_setup
		$sql_increment_week = "UPDATE website_setup SET lastcompletedweek=(lastcompletedweek + 1);";
		mysql_query($sql_increment_week) or die(mysql_error());					
		
		// close current race??
		$sql_close_race = "UPDATE schedule s SET s.closed=1 WHERE s.id=".$schedule_id.";";
		mysql_query($sql_close_race) or die(mysql_error());
		
		echo "Race results are now updated!";		
		
		// email first pick to notify that picks are ready to be made for next race -- note, should only do this if there are more races on the schedule
		emailNextPick( 1 );
	}
	
	// build an intelligent array of race results with keywords and performing necessary transformation on data	
	function getCurrentRaceResults( $key )
	{
		$namedresults[] = array();
		$rawresults = getResults( $key );
		for( $i = 0; $i < count($rawresults); $i++ )
		{
			$rawrow = $rawresults[$i];
						
			$smartrow[] = array();
			$smartrow['finishpos'] = $rawrow[0];
			$smartrow['driver'] = $rawrow[1];						
			$smartrow['startpos'] = $rawrow[6];
			$smartrow['carnum'] = $rawrow[2];
			//$smartrow['driver'] = trim(str_replace(" *", "", $rawrow[3]));
			$smartrow['carmake'] = $rawrow[3];
			//$smartrow['sponsor'] = str_replace("'", "", $rawrow[5]);		
			$smartrow['totalpoints'] = $rawrow[8];
			$smartrow['bonus'] = $rawrow[9];
			$smartrow['points'] = $smartrow['totalpoints']-$smartrow['bonus'];
			//$smartrow['winnings'] = str_replace(",", "", $rawrow[9]);
			$namedresults[$i] = $smartrow;
		}
		return $namedresults;
	}
	
	function getDriverId( $driver_name )
	{
		$sql = "select id from drivers d where d.name='".$driver_name."' and d.isactive=1";
		$results = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_assoc($results);
		return $row['id'];
	}
	
	function getScheduleId()
	{
		$sql = "select id from schedule s where s.week=".SEASON_NEXT_WEEK." and s.year=".SEASON_YEAR;
		$results = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_assoc($results);
		return $row['id'];
	}
?>
	</div>
</div>
