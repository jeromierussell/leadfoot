<div class="post">    
    <div class="story">
<?php

		$display_driver = $_REQUEST['display_driver'];
		$display_driver_year = $_REQUEST['display_driver_year'];
		if( !isset( $display_driver ) )
		{
			$display_driver = "Jeff Gordon";
		}
		if( !isset( $display_driver_year))
		{
			$display_driver_year = SEASON_YEAR;
		}
		echo "<div style='margin-top:15px'>";
		echo "<div style='float:left;font-weight:bold;color:black' align='left'>Driver Info</div>";
		echo "</div>";

        echo "<div style='float:left;clear:left' align='left'>";

        $sql = "SELECT name, number, owner, currentpoints, team, make from drivers where name = '".$display_driver."' order by isactive ASC limit 1";
			   
        $results = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_assoc($results))
		{
            $number = $row['number'];
            $owner = $row['owner'];
            $team = $row['team'];
            echo "<div style='font-size:72px;font-weight:bold;float:left;margin-right:18px;font-style:italic'>".$number."</div>";
            echo "<div style='float:left;margin-top:16px'>";
            echo "<div>".$display_driver."</div>";
            echo "<div>".$team."</div>";
            echo "<div>".$owner."</div>";
            echo "<div><a target='_blank' href='http://jayski.com/teams/".$number.".htm'>Jayski team page</a></div>";
            echo "</div>";
        }
        
        echo "</div>";
		
		
		echo "<div style='float:right;clear:both' align='right'>";
		echo "<form action='".$config_basedir."index.php?content_page=driver' method='post'>\n";
		echo "<select name='display_driver'>\n";
	
		$sql_distinct_drivers = "select distinct(name) as dname from drivers where isactive = 1 order by dname ASC";
		$results_drivers = mysql_query($sql_distinct_drivers);
		while( $row_drivers = mysql_fetch_assoc($results_drivers) )
		{
			$selected = " ";
			if( $row_drivers['dname'] == $display_driver )
			{
				$selected = " selected ";
			}
			echo "<option value='".$row_drivers['dname']."'".$selected.">".$row_drivers['dname']."</option>";
		}
		echo "</select>\n";
		echo "<select name='display_driver_year'>\n";
	
		$sql_distinct_years = "select distinct(year) as dyear from schedule order by dyear DESC";
		$results_years = mysql_query($sql_distinct_years);
		while( $row_years = mysql_fetch_assoc($results_years) )
		{
			$selected = " ";
			if( $row_years['dyear'] == $display_driver_year )
			{
				$selected = " selected ";
			}
			echo "<option value='".$row_years['dyear']."'".$selected.">".$row_years['dyear']."</option>";
		}
		echo "</select>\n";
		echo "<input type='submit' name='submit' value='Refresh'>\n";
		echo "</form>";
		echo "</div>";


        echo "<div style='clear:both'>";
        echo "<table id='driverresults'>";
//        $sql = "SELECT r.*, m.nickname as membername, m.username from historicalraceresults r, picks p, members m where m.id = p.member_id and p.racekey = r.racekey and p.driver_id = r.driver_id and r.drivername = '".$display_driver."' and r.year = '".SEASON_YEAR."' order by r.week";


        $sql = "SELECT r.*, m.nickname as membername, m.username, s.id as schedid, s.track_id, s.trackname, p.picksequence from historicalraceresults r LEFT OUTER JOIN picks p on p.racekey = r.racekey and p.driver_id = r.driver_id LEFT OUTER JOIN schedule s on s.id = r.schedule_id LEFT OUTER JOIN members m on m.id = p.member_id where r.drivername = '".$display_driver."' and r.year = '".$display_driver_year."' order by r.week";
        
        $results = mysql_query($sql);
        
        echo "<tr><th>Week</th><th>Track</th><th>Fin. Pos.</th><th>Points</th><th>Bonus</th><th>Total<br>Points</th><th>Start Position</th><th>Winnings</th><th>Picked By</th><th>Pick #</th></tr>\n";
        while($row = mysql_fetch_assoc($results))
        {
            echo "<tr><td align='center'>" .
			"<a href='".$config_basedir."index.php?content_page=raceresults&race=".$row['schedid']."'>".
            $row['week'] . "</a></td><td align='center'>".
			"<a href='".$config_basedir."index.php?content_page=historicalresults&track_id=".$row['track_id']."'>".
            $row['trackname'] . "</a></td><td align='center'>".
            $row['finishingpos'] . "</td><td align='center'>" . 
            $row['points'] . "</td><td align='center'>" . 
            $row['bonuspoints'] . "</td><td align='center'>" .
            $row['totalpoints'] . "</td><td align='center'>" .
            $row['startingpos'] . "</td><td align='right'>".
            "$".number_format($row['winnings']) . "</td><td align='center'>".
            "<a href='".$config_basedir."index.php?content_page=mystats&username=".$row['username']."'>".
            $row['membername'].
            "</a></td><td align='center'>".
            $row['picksequence'].
            "</td></tr>\n";
        }
        echo "</table>";
        echo "</div>";
        

?>
    </div>
</div>
