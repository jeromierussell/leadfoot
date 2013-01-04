<?php
	require("config.php");
//require("functions.php");

$db = mysql_connect($dbhost, $dbuser, $dbpassword);
mysql_select_db($dbdatabase, $db);

	@include 'fetchresults.php';		
	set_time_limit(600);
	for( $year = 2009; $year < 2010; $year++ )
	{
		$num_races = 36;
		if( $year < 2001 )
		{
			$num_races = 34;
		}
		
		for( $race = 1; $race <= $num_races; $race++ )
		{
			echo "Importing results for ".$year." race #".$race."<br>";
			
			$raceresult = getResults($year, $race);
	
			for( $i = 0; $i < count($raceresult); $i++ )
			{
				$rowData = $raceresult[$i];

				// Get the other data we need
				$finishpos  = $i+1;
				$startpos   = $rowData[1];
								
				$week = $race;
				
				$race_pad = str_pad($race, 2, "0", STR_PAD_LEFT);
				//echo "race_pad: ".$race_pad;
				$race_id = $year.$race_pad;
				//echo "race_id: ".$race_id;

				// Get the driver id, this will add any missing drivers
				//$driverName = findDriver($rowData[3], $rowData[2], $rowData[5], $rowData[4]);

				//$laps       = $rowData[7];
				//$status     = $rowData[8];
				$pointArray = explode("/", $rowData[6]);

				// Split the points into total and bonus
				$points = $pointArray[0] - $pointArray[1];
				$bonus  = $pointArray[1];
				$totalpoints = $points+$bonus;
				//echo "adding row row: $raceId, $driverName, $finishpos, $points, $bonus, $startpos, $laps, $status\n";
/*
				for( $j = 0; $j < count($rowData); $j++ )
				{
					echo "results [".$i."] [".$j."] = ".$rowData[$j]."<br>";
				}

*/
				if( is_numeric($points) && is_numeric($bonus) && is_numeric($totalpoints) )
				{
					$sponsor = str_replace("'", "", $rowData[5]);
					$winnings = str_replace(",", "", $rowData[9]);
					$driver = str_replace(" *", "", $rowData[3]);
					if( is_numeric( $rowData[1] ) )
					{				
						$sql = "update historicalraceresults set bonuspoints=".$bonus.", totalpoints=".$totalpoints." where race_id='".$race_id."' and drivername='".$driver."';";
						//$sql = "insert into historicalraceresults (race_id, year, week, drivername, carnumber, startingpos, finishingpos, points, carmake, sponsor, winnings) "
						//	."values( '$race_id', $year, $week, '$driver', '$rowData[2]', $rowData[1], $rowData[0], $points, '$rowData[4]', '$sponsor', $winnings );";
						$result = mysql_query($sql) or die(mysql_error());
					}
				}
				

				//$sql = "insert into RaceResult (raceId, driverName, position, points, bonus, startpos, laps, status) "
				//	 . "values( $raceId, '$driverName', $finishpos, $points, $bonus, $startpos, $laps, '$status' )";
				//$result = mysql_query($sql) or die ("System Error - Could not insert result row: ".$i." -- ".mysql_error());
			}
		}
		
		
	}
	
	
?>
