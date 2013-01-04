<div class="post">    
    <div class="story">
<?php
		echo "<h1>Historical League Results</h1>";

// League Winners
        $sql = "SELECT r.year, m.nickname " .
               "FROM members m INNER JOIN annualmemberresults r ON r.member_id=m.id WHERE r.year<".SEASON_YEAR." and r.rank = 1 order by year desc;";
			   
        $results = mysql_query($sql) or die(mysql_error());

		echo "<h2>League Champions</h2>";
		echo "<table id='champs'>";
		echo "<tr><th>Year</th><th>Name</th></tr>";
        
        while($row = mysql_fetch_assoc($results))
		{
            $year = $row['year'];
            $name = $row['nickname'];
            echo "<tr>";
            echo "<td>".$year."</td>";
            echo "<td>".$name."</td>";
            echo "</tr>";
        }
        echo "</table>";
?>
    </div>
    
	<div class="story">
	<?php 
// Detailed Stats
		$display_year = $_REQUEST['display_year'];
		if( !isset( $display_year ) )
		{
			$display_year = SEASON_YEAR;
		}
		echo "<div style='margin-top:15px'><div style='float:left;font-weight:bold;color:black' align='left'>".$display_year." League Results</div>";
		echo "<div align='right'>";
		echo "<form action='".$config_basedir."index.php?content_page=historicalleagueresults' method='post'>\n";
		echo "<select name='display_year'>\n";
	
		$sql_distinct_years = "select distinct(year) as dyear from annualmemberresults order by dyear DESC";
		$results_years = mysql_query($sql_distinct_years);
		while( $row_years = mysql_fetch_assoc($results_years) )
		{
			$selected = " ";
			if( $row_years['dyear'] == $display_year )
			{
				$selected = " selected ";
			}
			echo "<option value='".$row_years['dyear']."'".$selected.">".$row_years['dyear']."</option>";
		}
		echo "</select>\n";
		echo "<input type='submit' name='submit' value='Refresh'>\n";
		echo "</form>";
		echo "</div></div>";



        $sql = "SELECT r.year, m.nickname, m.username, r.points, r.rank," .
               "r.poles,r.wins,r.top5,r.top10,r.top15,r.top20,r.avgfinish,r.avgpoints,r.avgpick,r.poles " .
               "FROM members m INNER JOIN annualmemberresults r ON r.member_id=m.id WHERE r.year = " . $display_year . " order by points desc;";
			   
        $results = mysql_query($sql) or die(mysql_error());

		echo "<div>";
		echo "<table id='careerresults'>";
		echo "<tr><th>Year</th><th>Rank</th><th>Name</th><th>Points</th><th>Avg. Points per Week</th><th>Avg. Finish</th><th>Avg. Pick</th><th>Wins</th><th>Top 5s</th><th>Top 10s</th><th>Top 15s</th><th>Top 20s</th><th>Poles</th></tr>";
        
        while($row = mysql_fetch_assoc($results))
		{
            $year = $row['year'];
            $points = $row['points'];
            $name = $row['nickname'];
            $rank = $row['rank'];
            $poles = $row['poles'];
            $wins = $row['wins'];
            $top5 = $row['top5'];
            $top10 = $row['top10'];
            $top15 = $row['top15'];
            $top20 = $row['top20'];
            $avg_finish = $row['avgfinish'];
            $avg_points = $row['avgpoints'];
            $avg_pick = $row['avgpick'];
            $poles = $row['poles'];
            echo "<tr>";
            echo "<td>".$year."</td>";
            echo "<td>".$rank."</td>";
            echo "<td nowrap><a href='".$config_basedir."index.php?content_page=mystats&username=".$row['username']."'>".$name."</a></td>";
            echo "<td>".$points."</td>";
            echo "<td>".$avg_points."</td>";
            echo "<td>".$avg_finish."</td>";
            echo "<td>".$avg_pick."</td>";
            echo "<td>".$wins."</td>";
            echo "<td>".$top5."</td>";
            echo "<td>".$top10."</td>";
            echo "<td>".$top15."</td>";
            echo "<td>".$top20."</td>";
            echo "<td>".$poles."</td>";
            echo "</tr>";
        }
        echo "</table>";
?>
	
</div> <!-- right-col -->
	</div>