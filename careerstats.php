<?php
	function showCareerStats( $user_name )
    {
        $sql = "SELECT r.year, r.points, r.rank," .
               "r.poles,r.wins,r.top5,r.top10,r.top15,r.top20,r.avgfinish,r.avgpoints,r.avgpick,r.poles " .
               "FROM members m INNER JOIN annualmemberresults r ON r.member_id=m.id WHERE m.username='" . $user_name . "' and r.year<".SEASON_YEAR." order by year desc;";
			   
        $results = mysql_query($sql) or die(mysql_error());

		echo "<div>";
		echo "<h2>Career results</h2>";
		echo "<table id='careerresults'>";
		echo "<tr><th>Year</th><th>Rank</th><th>Points</th><th>Avg. Points per Week</th><th>Avg. Finish</th><th>Avg. Pick</th><th>Wins</th><th>Top 5s</th><th>Top 10s</th><th>Top 15s</th><th>Top 20s</th><th>Poles</th></tr>";
        
        while($row = mysql_fetch_assoc($results))
		{
            $year = $row['year'];
            $points = $row['points'];
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
            echo "<tr>";
            echo "<td>".$year."</td>";
            echo "<td>".$rank."</td>";
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
        
    }

?>