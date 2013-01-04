<?php
	require("config.php");

	$db = mysql_connect($dbhost, $dbuser, $dbpassword);
	mysql_select_db($dbdatabase, $db);

			
	set_time_limit(380);
	
	$sql = "select drivername, carnumber, carmake from historicalraceresults where driver_id is null";
	
	$results = mysql_query($sql);
	
	while($row = mysql_fetch_assoc($results))
	{
	
			$driver = $row['drivername'];
		$driver_sql = "select * from drivers where name='".$driver."';";
		$driver_results = mysql_query($driver_sql) or die("system error = count not execute query [".$driver_sql."] ".mysql_error());
		echo "driver sql: ".$driver_sql." rows: ".mysql_num_rows($driver_results)."<br>";
		
		if( mysql_num_rows($driver_results) == 0 )
		{
			$carnumber = $row['carnumber'];
			$carmake = $row['carmake'];
			$insert_sql = "insert into drivers (name, number, owner, team, make, must_qualify, isActive) "
				."values ('$driver', '$carnumber', '?', '?', '$carmake', 2, 2);";
			$insert_result = mysql_query($insert_sql) or die("System Error = could not insert result row [".$insert_sql."] ".mysql_error());	
		}	
		
	}		
?>
