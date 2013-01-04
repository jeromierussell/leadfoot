<?php
	require("config.php");

	$db = mysql_connect($dbhost, $dbuser, $dbpassword);
	mysql_select_db($dbdatabase, $db);

	@include 'fetchresults.php';		
	set_time_limit(380);
	for( $year = 2010; $year <= 2010; $year++ )
	{
		$schedules = getSchedules( $year );
			
		echo "Importing schedule for ".$year."<br>";
		
		for( $i = 0; $i < count( $schedules ); $i++ )
		{
			$rowData = $schedules[$i];
						
			// Get the other data we need
			$finishpos  = $i+1;
			$startpos   = $rowData[1];
							
			$week = $rowData[1];
			
			$race_pad = str_pad($week, 2, "0", STR_PAD_LEFT);
			//echo "race_pad: ".$race_pad;
			$race_id = $year.$race_pad;
			//echo "race_id: ".$race_id;

			// Get the driver id, this will add any missing drivers
			//$driverName = findDriver($rowData[3], $rowData[2], $rowData[5], $rowData[4]);

			//$laps       = $rowData[7];
			//$status     = $rowData[8];
			//$pointArray = explode("/", $rowData[6]);

			// Split the points into total and bonus
			//$points = $pointArray[0] - $pointArray[1];
			//$bonus  = $pointArray[1];
			//echo "adding row row: $raceId, $driverName, $finishpos, $points, $bonus, $startpos, $laps, $status\n";
/*
			for( $j = 0; $j < count($rowData); $j++ )
			{
				echo "results [".$i."] [".$j."] = ".$rowData[$j]."<br>";
			}
*/			
			//$sponsor = str_replace("'", "", $rowData[5]);
			//$winnings = str_replace(",", "", $rowData[9]);
			//$driver = str_replace(" *", "", $rowData[3]);
			
			$date_str = $rowData[2]." ".$year;
			$date = strtotime( $date_str );
			$date_fmt = date("Y-m-d H:i:s", $date);
			//echo "date_str ".$date_str." date: ".$date_fmt."<br>";
			
			if( is_numeric( $rowData[1] ) )
			{				
				$sql = "insert into schedule (dt, closed, year, racename, pointsrace, week, raceid, trackname) "
					."values( '$date_fmt', 1, $year, '$rowData[3]', 1, $rowData[1], $race_id, '$rowData[3]' );";
				$result = mysql_query($sql) or die("System Error = could not insert result row [".$sql."] ".mysql_error());
			}
			
			//$sql = "insert into RaceResult (raceId, driverName, position, points, bonus, startpos, laps, status) "
			//	 . "values( $raceId, '$driverName', $finishpos, $points, $bonus, $startpos, $laps, '$status' )";
			//$result = mysql_query($sql) or die ("System Error - Could not insert result row: ".$i." -- ".mysql_error());
		}
	}				
	
?>
