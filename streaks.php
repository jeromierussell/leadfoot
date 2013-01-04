<h2 class="heading">Streaks</h2>
<div class="content">
<?php
	if( SEASON_COMPLETED_WEEK > 0 )
	{
		$week = SEASON_COMPLETED_WEEK;

		if($week > 5)
		{
			$week = 5;
		}

		$memberSql = "select id, nickname from members m where active = 1";
		$memberResults = mysql_query($memberSql);

		$pointsHash = array();
		while($memberRow = mysql_fetch_assoc($memberResults))
		{
			$memberId = $memberRow['id'];
			$memberName = $memberRow['nickname'];

			$sql = "select sum(points) as pointsSum from (select p.pointsaward as points from picks p, members m where p.member_id = m.id and m.id = ".$memberId." and p.year = '".SEASON_YEAR."' and p.result_id > 0 order by p.dt desc limit ".$week.") as sub";
			$results = mysql_query($sql);

			while($row = mysql_fetch_assoc($results))
			{
				$pointsHash[$memberName] = $row['pointsSum'];
			}
		}

		asort($pointsHash);
		$bottom5points = array();

		$i = 0;
		foreach($pointsHash as $key => $value)
		{
			if($i < 5)
			{
				$bottom5points[$key] = $value;
			}
			$i++;
		}

		$top5points = array();

		arsort($pointsHash);
		$i = 0;
		foreach($pointsHash as $key => $value)
		{
			if($i < 5)
			{
				$top5points[$key] = $value;
			}
			$i++;
		}

		echo "<table style='line-height:20px;'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th class='smallCaps'>";
		if($week > 1)
		{
			echo "Most points in last " . $week . " races";
		}
		else
		{
			echo "Most points in last race";
		}
		echo "</th>";
		echo "</tr>";
		echo "</thead>";

		foreach($top5points as $key => $value)
		{
			echo "<tr><td>";
			echo "{$key} - $value";
			echo "</td></tr>";
		}

		echo "</table>";

		echo "<table style='line-height:20px;'>";
		echo "<tr>";
		echo "<th class='smallCaps'>";
		if($week > 1)
		{
			echo "Least points in last " . $week . " races";
		}
		else
		{
			echo "Least points in last race";
		}
		echo "</th>";
		echo "</tr>";
		echo "</thead>";
		foreach($bottom5points as $key => $value)
		{
			echo "<tr><td>";
			echo "{$key} - $value\n";
			echo "</td></tr>";
		}

		echo "</table>";
	}
	else
	{
		echo "Check back after the first race!";
	}
?>
</div>
