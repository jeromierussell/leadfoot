<?php
		// we need to first determine what the next race will be
		$today = date('Y-m-d');
		//	$today = date('Y-m-d H-i-s');		// 6-5-2009 - changed $today to not have the time in it.
		
		$next_race_sql = "SELECT s.id AS schedule_id, DATE_FORMAT(s.dt,'%c/%d/%Y %h:%i %p') AS racetime, s.dt, s.racename, s.pointsrace, t.shortname AS trackname, t.id AS trackid FROM schedule s INNER JOIN tracks t ON t.id=s.track_id WHERE s.year=".SEASON_YEAR." and s.week=".SEASON_NEXT_WEEK.";--";
		$next_race_results = mysql_query($next_race_sql);
		$next_race_row = mysql_fetch_assoc($next_race_results);
		$next_race_id = $next_race_row['schedule_id'];
		$next_race_date = $next_race_row['dt'];
		$next_race_name = $next_race_row['racename'];
		$next_race_track_name = $next_race_row['trackname'];
		$next_race_track_id = $next_race_row['trackid'];
		$next_race_time = $next_race_row['racetime'];
		$next_race_key = SEASON_YEAR.str_pad(SEASON_NEXT_WEEK, 2, "0", STR_PAD_LEFT);
		if( !isset( $next_race_id ) )
		{
			$next_race_id = -1;
		}
		
		/* SRS 06/08/2009 - Added code to calculate the previous race also */
		$prev_race_sql = "SELECT s.id AS schedule_id, s.dt, s.racename, s.pointsrace, t.shortname AS trackname, t.id AS trackid FROM schedule s INNER JOIN tracks t ON t.id=s.track_id WHERE s.year=".SEASON_YEAR." and s.week=".SEASON_COMPLETED_WEEK.";--";
		$prev_race_results = mysql_query($prev_race_sql);
		$prev_race_row = mysql_fetch_assoc($prev_race_results);
		$prev_race_id = $prev_race_row['schedule_id'];
		$prev_race_date = $prev_race_row['dt'];
		$prev_race_name = $prev_race_row['racename'];
		$prev_race_track_name = $prev_race_row['trackname'];
		$prev_race_track_id = $prev_race_row['trackid'];
		$prev_race_key = SEASON_YEAR.str_pad(SEASON_COMPLETED_WEEK, 2, "0", STR_PAD_LEFT);
		if( !isset( $prev_race_id ) )
		{
			$prev_race_id = -1;
		}
?>