<script>
function confirmSubmit()
{
	var index = document.selectDriver.driver.selectedIndex;
	var pick  = document.selectDriver.driver[index].text;
	var agree=confirm("Are you sure you want to pick " + pick + "?");
	if (agree)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function confirmDelete()
{
	var agree=confirm("Are you sure you want to delete the last pick?");
	if (agree)
	{
		return true;
	}
	else
	{
		return false;
	}
}

</script>
<div class="post">
	<div class="story">
		
	<table id="makepick">
	<?php		
		/*	1. Check if member should have next pick, if not then redirect
				Determine the next race
				Determine the last pick for the next race
				Using the pick sequence for the last pick, query the members table for the next pick
				If logged in user doesn't match member, the redirect			
		*/
		require("next_race.php");
		include("emailfunctions.php");
        include("pick_from_queue.php");

		$sql_race_exists = "SELECT * FROM schedule s WHERE s.year=".SEASON_YEAR." AND s.week=".SEASON_NEXT_WEEK.";";
		$results_race_exists = mysql_query($sql_race_exists) or die(mysql_error());
		if( mysql_num_rows($results_race_exists) == 0 )
		{
			echo "<h2 class='title'>That's all for ".SEASON_YEAR."!</h2>";
		}
		else
		{
			echo "<h2 class='title'>Picks for week ".SEASON_NEXT_WEEK." of ".SEASON_YEAR." -- ".$next_race_track_name."</h2>";
			
			$sql = "SELECT picksequence FROM picks WHERE schedule_id='" . $next_race_id . "' ORDER BY picksequence DESC LIMIT 1;--";
			$results = mysql_query($sql);
			$row = mysql_fetch_assoc($results);
			if($row == false)
			{	// couldn't find any picks for this race, we need to verify this member is the first pick - also check that the previous race is closed before letting user make pick
				$prev_race_closed = false;
				if( SEASON_NEXT_WEEK == 1 )
				{
					$prev_race_closed = true;
				}
				else
				{
					$sql = "SELECT closed,id FROM schedule WHERE id='" . $prev_race_id . "' LIMIT 1";
					$res = mysql_query($sql);
					$row = mysql_fetch_assoc($res);
					$prev_race_closed = $row['closed'];
				}
				
				
				$sql = "SELECT r.picksequence,m.id FROM members m INNER JOIN annualmemberresults r ON r.member_id=m.id WHERE r.year=".SEASON_YEAR." and m.username='" . $_SESSION['USERNAME'] . "' LIMIT 1";
				$m_res = mysql_query($sql);
				$m_row = mysql_fetch_assoc($m_res);
				$picksequence = $m_row['picksequence'];
				$member_id = $m_row['id'];
				if($picksequence == 1 && $prev_race_closed == 1)
				{	$username = $_SESSION['USERNAME'];
				}
				else
				{	$username = "";
				}
			}		
			else
			{	$picksequence = $row['picksequence'];	// latest entry in the picks table. Need to check if this user is next pick after the latest entry
				$picksequence = $picksequence + 1;		// so we increment $picksequence by 1
				
				$sql = "SELECT m.username,m.id FROM members m INNER JOIN annualmemberresults r ON r.member_id=m.id WHERE r.year=".SEASON_YEAR." and r.picksequence='" . $picksequence . "' LIMIT 1";
				$results = mysql_query($sql);
				$row = mysql_fetch_assoc($results);
				$username = $row['username'];
				$member_id = $row['id'];
			}
					
			$flag = false;
			if(isset($_SESSION['USERNAME']) == true)
			{	if($username == $_SESSION['USERNAME'])
				{	$flag = true;
				}		
			}

			$displayTable = true;
			if($_POST['delete'])
			{
				$sql = "delete from picks where schedule_id = ".$next_race_id." and picksequence = ".($picksequence-1)." and result_id = 0";
				mysql_query($sql) or die(mysql_error());
				echo "<h2>The pick was deleted successfully</h2>";
				$displayTable = false;
			}
			else if($flag == false)
			{	echo "<h2 class='title'>Sorry, it's not your pick.</h2><br><br>";
				/*
				echo "<b>DEBUG INFO</b><br>";
				echo "Username = " . $username . "<br>";
				echo "SESSION USER = " . $_SESSION['USERNAME'] . "<br>";
				echo "Pick Sequence = " . $picksequence . "<br>";
				echo "Next Race = " . $next_race_name . " - " . $next_race_id . " - " . $next_race_date . "<br>";
				echo "Prev Race = " . $prev_race_name . " - " . $prev_race_id . " - " . $prev_race_date;
				if($prev_race_closed == 1)
				{	echo " - Closed<br>";
				}
				else
				{	echo " - Open<br>";
				}
				*/
			}
			else
			{
				if(!$_POST['submit']) // if not submitted, then user just came to the page and we should display the entry form
				{	// 2. Determine if page has been submitted, if not show form.	
					// 3. 		Filter choices and allow member to make pick.
					// 4.		FORM SUBMISSIOM
					/*	Make a list of drivers from current picks
						Query drivers excluding list so we can populate a drop-down box with available drivers to pick
						
					*/
					
					$sql = "SELECT id,name,number,owner,team,make,must_qualify FROM drivers d WHERE d.isactive=1 and d.id NOT IN (select driver_id from picks p where p.schedule_id=".$next_race_id.") ORDER BY name ASC";				
					$res = mysql_query($sql) or die(mysql_error());
					
					echo "\n<h2>Make Pick</h2>\n";
					
					echo "<form name='selectDriver' onsubmit='return confirmSubmit();' method='POST' action='" . $config_basedir."index.php?content_page=makepick'>";
					echo "\t<table>\n";
					echo "\t\t<tr>\n";
					echo "\t\t\t<th>Race</th>\n";
					echo "\t\t\t<th>" . $next_race_name . "</th>\n";
					echo "\t\t</tr>\n";
					echo "\t\t\t<td>Driver</td>\n";
					echo "\t\t\t<td><select name='driver'>\n";
					while($row = mysql_fetch_assoc($res))
					{	echo "\t\t\t\t<option value='" . $row['id'] . "'>" . $row['number'] . " - " . $row['name'] . " - " . $row['team'] . " - " . $row['make'] . " - " . $row['owner'];
						if( $row['must_qualify'] == 1 )
						{
							echo " ***Must Qualify***";
						}
						echo "</option>\n";
					}
					echo "\t\t\t</select></td>\n";
					echo "\t\t<tr>\n";
					echo "\t\t\t<td>&nbsp;</td>\n";
					echo "\t\t\t<td><input type='submit' name='submit' value='Submit'></td>\n";
					echo "\t\t</tr>\n";
					echo "\t</table>\n";
					echo "<input type='hidden' name='date' value='" . date('Y-m-d H-i-s') . "' />";
					echo "<input type='hidden' name='race_id' value='" . $next_race_id . "' />";
					echo "<input type='hidden' name='member_id' value='" . $member_id . "' />";
					echo "<input type='hidden' name='picksequence' value='" . $picksequence . "' />";
					echo "</form>\n";				
				}
				else	
				{
					// 5. If submitted then record pick for member.
					// 6. 		Determine next member to make pick.
					// 7. 		Notify next member to pick.
					$date 		= $_POST['date'];
					$race_id	= $_POST['race_id'];
					$member_id	= $_POST['member_id'];
					$driver_id	= $_POST['driver'];
					$picksequence	= $_POST['picksequence'];					

                    insertPick($date, $race_id, $member_id, $driver_id, $picksequence, $next_race_key);

                    // make next pick (will determine if next pick should be made based on pick queue settings)
                    makeNextPick($picksequence, $next_race_id, $next_race_key);

					echo "\n<h1>Your pick has been saved!</h1>\n";
				}					
			}		
			
			if($displayTable)
			{
				echo "<a href='".$config_basedir."index.php?content_page=historicalresults&track_id=".$next_race_track_id."' class='large'>View past driver results and averages from ".$next_race_track_name."</a>";
				
				$sql_all_picks = "select m.nickname, m.username, r.rank, r.points, d.name, r.picksequence from members m INNER JOIN annualmemberresults r ON r.member_id=m.id and r.year=".SEASON_YEAR." LEFT OUTER JOIN picks p ON p.member_id=m.id and p.schedule_id=".$next_race_id." LEFT OUTER JOIN drivers d ON d.id=p.driver_id ORDER BY r.picksequence";		
				$results_all_picks = mysql_query($sql_all_picks) or die(mysql_error());			
				
				echo "<h2>Current Picks</h2>\n";
				echo "<table id='picks'>\n";
				echo "<tr><th>Pick<br>Sequence</th><th>Member</th><th>Rank</th><th>Points</th><th>Pick</th></tr>";
				while( $row = mysql_fetch_assoc($results_all_picks) )
				{
					$class="";
					if($row['name'] != "")
					{
						$class="class='old'";
					}
					echo "<tr ".$class."><td align='center'>".$row['picksequence']."</td><td align='center'>".
					"<a href='".$config_basedir."index.php?content_page=mystats&username=".$row['username']."'>".
					$row['nickname'].
					"</a></td><td align='center'>".$row['rank']."</td><td align='center'>".$row['points']."</td><td align='center'>".
					"<a href='".$config_basedir."index.php?content_page=driver&display_driver=".$row['name']."'>".
					$row['name'].
					"</a>"."</td></tr>\n";
				}
				echo "</table>\n";
			}
		}
	?>
	</table>
	<div id='admindeletepick'>
<?php
		if($displayTable && ($_SESSION['USERNAME'] == "steppsr" or $_SESSION['USERNAME'] == "westcrabtree" or $_SESSION['USERNAME'] == "russdog" or $_SESSION['USERNAME'] == "gofasta"))
		{
			echo "<form name='deletePick' onsubmit='return confirmDelete();' method='POST' action='" . $config_basedir."index.php?content_page=makepick&action=delete'>";
			echo "<input type='submit' name='delete' value='Delete Last Pick'>";
			echo "</form>";
		}
?>		
	</div>
	</div>
</div> <!-- right-col -->