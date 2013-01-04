<div class="post">    
    <div class="story">
<?php
	$track_id = $_REQUEST['track_id'];	
	$show_all = $_REQUEST['show_all'];
	$stat_type = $_REQUEST['stat_type'];
	
	$show_inactive = $_REQUEST['show_inactive'];
	
	$earliest_year = $_REQUEST['earliest_year'];
	if( !isset( $earliest_year ) )
	{
		$earliest_year = 1999;
	}	
	// stat types supported: 'finishingpos', 'startingpos', 'totalpoints'
	$supported_stat_types = array( 'finishingpos'=>'Finish Position', 'startingpos'=>'Start Position', 'totalpoints'=>'Points' );
	if( !isset( $stat_type ) )
	{
		$stat_type = 'finishingpos';
	}
	$rank_order = "ASC";
	if( $stat_type == 'totalpoints' )
	{
		$rank_order = "DESC";
	}
	
	$sql_track = "select type, t.name, tt.name as type_name from tracks t inner join types_tracktype tt on tt.id=t.type where t.id=".$track_id.";";
	$results_track = mysql_query($sql_track) or die(mysql_error());
	$track_row = mysql_fetch_assoc($results_track);
	$track_name = $track_row['name'];
	$track_type = $track_row['type'];
	$track_type_name = $track_row['type_name'];
	
	// store all track ids in an array
	$like_tracks = array();
	$like_tracks[0] = $track_id;
		
	// if the requested view is to show results for all like tracks, then we need to identify the other tracks of the same type
	if( $show_all && ( $track_type > 2 ) )
	{
		$sql_tracks_like = "select name, id from tracks where type=".$track_type." and id <> ".$track_id.";";
		$results_tracks_like = mysql_query($sql_tracks_like) or die(mysql_error());
	
		$track_count = 1;
		while( $tracks_like_row = mysql_fetch_assoc($results_tracks_like) )
		{
			$like_tracks[$track_count]=$tracks_like_row['id'];
			$track_count++;
		}		
		echo "<h1 class='title'>Showing combined results for all ".$track_type_name." tracks since ".$earliest_year."</h1>";
	}
	else
	{
		$show_all=false;
		echo "<h1 class='title'>Showing ".$supported_stat_types[$stat_type]." results for ".$track_name." since ".$earliest_year."</h1>";
	}
	echo "<p>";
	$tracks_for_search = implode(",", $like_tracks);
	echo "<form action='".$config_basedir."index.php?content_page=historicalresults' method='post'>\n";
	echo "Track: ";
	echo "<select name='track_id'>\n";
		
	$sql_all_tracks = "select name, id from tracks order by name";
	$results_all_tracks = mysql_query($sql_all_tracks);
	while($row_tracks = mysql_fetch_assoc($results_all_tracks))
	{
		$selected = " ";
		if( $track_id == $row_tracks['id'] )
		{
			$selected = " selected ";
		}
		echo "<option value='".$row_tracks['id']."'".$selected.">".$row_tracks['name']."</option>\n";
	}
	echo "</select>\n";
	echo " Since: ";
	echo "<select name='earliest_year'>\n";
	
	$sql_distinct_years = "select distinct(year) as dyear from schedule order by dyear DESC";
	echo "foo".$sql_distinct_years;
	$results_years = mysql_query($sql_distinct_years);
	while( $row_years = mysql_fetch_assoc($results_years) )
	{
		$selected = " ";
		if( $row_years['dyear'] == $earliest_year )
		{
			$selected = " selected ";
		}
		echo "<option value='".$row_years['dyear']."'".$selected.">".$row_years['dyear']."</option>";
	}
	echo "</select>\n";
	
	echo " Stat: ";
	echo "<select name='stat_type'>\n";	
	foreach( $supported_stat_types as $type=>$desc )
	{
		$selected = " ";
		if( $stat_type == $type )
		{
			$selected = " selected ";
		}
		echo "<option value='".$type."'".$selected.">".$desc."</option>";
	}
	echo "</select>";
	//echo " Show Combined Results: ";	
		
	echo "<input type='submit' name='submit' value='Refresh'>\n";
	echo "</form>";
?>

	<table id="results">
	<?php
					
		$sql_races = "select s.year as year, s.id as schedule_id, t.abbreviation as track_name, s.dt from schedule s inner join tracks t on t.id=s.track_id where s.year >= ".$earliest_year." and s.track_id in (".$tracks_for_search.") and s.pointsrace=1 and s.closed=1 order by s.dt DESC;";
		$results_races = mysql_query($sql_races) or die(mysql_error());
		
		$races[] = array();						
		
		echo "<tr><th class='sortable-numeric'>Rank</th><th class='sortable-text'>Driver</th>"
			."<th class='sortable-numeric favour-reverse'>Wins</th><th class='sortable-numeric favour-reverse'>Poles</th><th class='sortable-numeric'>Avg</th>";			
		$count = 0;
		while( $race_row = mysql_fetch_assoc($results_races) )
		{
			$year = substr( $race_row['year'], -2 );
			// add track name abbreviation if we are showing combined results
			if( $show_all )
			{
				$year = $year." ".$race_row['track_name'];
			}
			// append the month to the header
			$year = $year." ".date("M",strtotime($race_row['dt']));
			echo "<th class='sortable-numeric'>".$year."</th>";
			$races[$count] = $race_row['schedule_id'];
			$count++;
		}
		echo "</tr>\n";
		
		$active_drivers = " d.isactive=1 and ";
		if( isset( $show_inactive ) )
		{
			$active_drivers = " ";
		}
				
		$sql_next_race = "select s.id AS schedule_id from schedule s where s.year=".SEASON_YEAR." and s.week=".SEASON_NEXT_WEEK.";";
		$results_next_race = mysql_query($sql_next_race);
		$next_race_row = mysql_fetch_assoc($results_next_race);		
		$next_race_schedule_id = null;
		if( $next_race_row != null )
		{
			$next_race_schedule_id = $next_race_row['schedule_id'];			
		}					

		// For now, searching by driver name, so when drivers change cars, you see their past results
		$sql_average = "select d.name as driver, h.driver_id, h.drivername, d.isactive, round(AVG(h.".$stat_type."), 2) as average ".
			" from historicalraceresults h ".
			" inner join drivers d on d.name=h.drivername ".
			" inner join schedule s on s.id=h.schedule_id ".
			" where ".$active_drivers." s.track_id in (".$tracks_for_search.") and s.pointsrace=1 and s.year >= ".$earliest_year.
			" group by driver, h.drivername, d.isactive ".
			" order by average ".$rank_order.";";
		$results_avg = mysql_query($sql_average) or die(mysql_error());
		
		$row_count = 1;
		while( $avg_row = mysql_fetch_assoc($results_avg) )
		{
			$driver_id = $avg_row['driver_id'];
			$drivername = $avg_row['drivername'];
			$isactive = $avg_row['isactive'];
			$style = "";
			if( isset( $next_race_schedule_id ) )
			{
				// Should be ok to do driver id search here, I think
				$sql_check_picks = "select p.id from picks p, drivers d where d.name = '".$drivername."' and p.schedule_id=".$next_race_schedule_id." and p.driver_id=d.id;";
				$results_check_picks = mysql_query($sql_check_picks);
				if( mysql_num_rows( $results_check_picks ) > 0 )
				{
					// driver already picked
					$style = " class='old' ";
				}
			}
			$wins = getResultCount( 'finishingpos', $tracks_for_search, $drivername, $earliest_year );
			$poles = getResultCount( 'startingpos', $tracks_for_search, $drivername, $earliest_year );
			echo "<tr".$style.">";
			echo "<td align='center'>".$row_count++."</td>";
			echo "<td nowrap align='center'>".
			"<a href='".$config_basedir."index.php?content_page=driver&display_driver=".$avg_row['driver']."'>".
			$avg_row['driver']."</a></td>";
			$win_class = $wins > 0 ? "highlight-blue" : "";			
			echo "<td align='center' class='".$win_class."'>".$wins."</td>";
			$pole_class = $poles > 0 ? "highlight-blue" : "";
			echo "<td align='center' class='".$pole_class."'>".$poles."</td>";
			echo "<td align='center' class='highlight'>".$avg_row['average']."</td>";
			
			$sql_driver = "select h.".$stat_type." AS stat, s.id from historicalraceresults h inner join schedule s on s.id=h.schedule_id where s.track_id in (".$tracks_for_search.") and h.drivername='".$drivername."' and s.pointsrace=1 and s.year >= ".$earliest_year." order by s.dt DESC";
			$results_driver = mysql_query($sql_driver);										

			$races_index = 0;
			while( $driver_row = mysql_fetch_assoc($results_driver) )
			{				
				$stat = $driver_row['stat'];				
				$schedule_id = $driver_row['id'];
				$match = false;
				for( ; $races_index < count($races) && !$match; $races_index++ )
				{
					$cell_class = "";
					if( $races[$races_index] == $schedule_id )
					{
						if( $stat == 1 )
						{
							$cell_class = "blue";
						}
						echo "<td align='center' class='".$cell_class."'>";					
						echo $stat."</td>";
						$match = true;
					}					
					else
					{
						echo "<td class='".$cell_class."'></td>";
					}
				}
			}
			for( ; $races_index < count($races); $races_index++ )
			{
				echo "<td></td>";
			}
			
			echo "</tr>\n";
		}		
	?>
		
	</table>
	<?php
		function getResultCount( $attribute, $tracks_for_search, $drivername, $earliest_year )
		{
			$sql_driver_wins = "SELECT COUNT(*) AS stat_count ".
				" FROM historicalraceresults h ".
				" INNER JOIN schedule s ON s.id=h.schedule_id ".
				" WHERE s.track_id IN (".$tracks_for_search.") AND h.drivername='".$drivername."' AND s.pointsrace=1 AND s.year >= ".$earliest_year." AND h.".$attribute."=1;";
			$results = mysql_query($sql_driver_wins) or die(mysql_error());
			$row = mysql_fetch_assoc($results);
			return $row['stat_count'];
		}
	?>
	
</div> <!-- right-col -->
	</div>