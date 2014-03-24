<div class='post'>
	<table><tr><td>
		<?php
		
		session_start();
		require("config.php");
        include("pick_from_queue.php");

		if(isset($_SESSION['USERNAME']) == FALSE)
		{
			header("Location: " . $config_basedir);
		}

		$db = mysql_connect($dbhost, $dbuser, $dbpassword);
		mysql_select_db($dbdatabase, $db);

		if($_SESSION['USERNAME'] == "steppsr" or $_SESSION['USERNAME'] == "westcrabtree" or $_SESSION['USERNAME'] == "russdog" or $_SESSION['USERNAME'] == "gofasta")
		{
			if($_POST['submit'])
			{	// if submitted then we should update the tables
				// First - Check that there are the correct amount of results listed in the race results table for the selected race
				$sql = "SELECT dt,race_id,driver_id,points,startpos,finishpos FROM raceresults WHERE race_id='" . $_POST['race'] . "' ORDER BY finishpos ASC";
				$results = mysql_query($sql);
				$number_of_race_results = mysql_num_rows($results);
				echo "NUMBER OF RACE RESULTS = " . $number_of_race_results . "<br><br>";
				$members_sql = "SELECT COUNT(*) FROM members";
				$members_result = mysql_query($members_sql);
				$number_of_members = mysql_result($members_result,0);

/*				if(intval($numbers_of_race_results) != 43)
				{	echo "<h1>Error - Not all drivers in the database are in the race results table.</h1><br><br>";
					echo "Number of race results = " . $number_of_race_results . "<br><br>";
				}
				else
				{
*/
				// Second - Setup and loop through the race results for the selected race
					// -- loop
					// 		Third - Find the current driver & update the driver table with the race results (increment currentpoints by the race results points)
					// 		Fourth - Find pick for this position of race results & update picks table (set points award, set dollars won - $30 highest finish, $20 if pick was 1st place, $10 next high finish)
					// 		Fifth - Find member for current pick & update member record (set week points, increase current points, set prev rank to current rank, clear current rank, increase dollars won)
					// -- end loop
					
					$update_members_picksequence_sql = "UPDATE members SET picksequence=''";
					mysql_query($update_members_picksequence_sql);
					
					$result_count = 0;
					$count = 0;
					while($row = mysql_fetch_assoc($results))
					{	
						echo "Processing race result " . strval($result_count + 1) . "<br>";
					
						$race_date	= $row['dt'];
						$race_id 	= $row['race_id'];
						$driver_id	= $row['driver_id'];
						$points_won	= $row['points'];
						$start_pos	= $row['startpos'];
						$finish_pos	= $row['finishpos'];
						
						$driver_sql 		= "SELECT currentpoints, name FROM drivers WHERE id='" . $driver_id . "' LIMIT 1";
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;POINTS for race result = " . $points_won . "<br>";
						$driver_res 		= mysql_query($driver_sql);
						$driver_row 		= mysql_fetch_assoc($driver_res);
						$driver_points 		= $driver_row['currentpoints'];
						$driver_name		= $driver_row['name'];
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $driver_name . "'s points BEFORE = " . $driver_points . "<br>";
						$driver_points 		= $driver_points + $points_won;
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $driver_name . "'s points AFTER = " . $driver_points . "<br>";
						$driver_update_sql	= "UPDATE drivers SET currentpoints='" . $driver_points . "' WHERE id='" . $driver_id . "'";
						mysql_query($driver_update_sql);
						
						$picks_sql 				= "SELECT p.id AS pick_id,dt AS pick_date,p.picksequence AS pick_picksequence,p.pointsaward,m.nickname," .
													"p.dollarswon,m.currentpoints,m.weekpoints,m.currentrank,m.prevrank,m.dollarswon,m.picksequence " .
													"AS member_picksequence,m.balance,m.id AS member_id FROM picks p INNER JOIN members m ON p.member_id = m.id " .
													"WHERE p.race_id='" . $race_id . "' AND p.driver_id='" . $driver_id . "' LIMIT 1";
						$picks_res 				= mysql_query($picks_sql);
						$pick 					= mysql_fetch_assoc($picks_res);
						if($pick)	// there was a pick made by a member for this driver at this race
						{	$count++;	// count the member picks
							$pick_id 				= $pick['pick_id'];
							$pick_date 				= $pick['pick_date'];
							$pick_member 			= $pick['member_id'];
							$pick_sequence 			= $pick['pick_picksequence'];
							$pick_pointsaward 		= $pick['pointsaward'];
							$pick_dollars 			= $pick['dollarswon'];
							$member_id				= $pick['member_id'];
							$member_nickname		= $pick['nickname'];
							$member_currentpoints	= $pick['currentpoints'];
							$member_weekpoints		= $pick['weekpoints'];
							$member_currentrank		= $pick['currentrank'];
							$member_prevrank		= $pick['prevrank'];
							$member_dollarswon		= $pick['dollarswon'];
							$member_picksequence	= $pick['member_picksequence'];
							$member_balance			= $pick['balance'];
							
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $driver_name . " was picked by " . $member_nickname . "<br>";
							
							$pick_pointsaward		= $points_won;
							if($count == 1 && $finish_pos == 1)
							{	$pick_dollars = 50;
								$member_dollarswon = $member_dollarswon + $pick_dollars;
								$member_balance = $member_balance + $pick_dollars;
								echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $member_nickname . " - Race Winner! $50<br>";
							}
							elseif($count == 1 && $finish_pos > 1)
							{	$pick_dollars = 30;
								$member_dollarswon = $member_dollarswon + $pick_dollars;
								$member_balance = $member_balance + $pick_dollars;
								echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $member_nickname . " - Highest Finish! $30<br>";
							}
							elseif($count == 2)
							{	$pick_dollars = 10;
								$member_dollarswon = $member_dollarswon + $pick_dollars;
								$member_balance = $member_balance + $pick_dollars;
								echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $member_nickname . " - 2nd highest finish. $10<br>";
							}
							$member_weekpoints		= $points_won;
							$member_currentpoints	= $member_currentpoints + $member_weekpoints;
							$member_prevrank		= $member_currentrank;
							$member_currentrank		= "";
							$member_picksequence	= $number_of_members - $count + 1;	// reverse sequence of winners determines the pick sequence
							if($member_picksequence == 1)
							{	$first_pick_id			= $member_id;
								$first_pick_nickname	= $member_nickname;
							}
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $member_nickname . " is pick number " . strval($member_picksequence) . "<br>";

							// UPDATE members table
							$members_update_sql = "UPDATE members SET dollarswon='" . $member_dollarswon . "', balance='" . $member_balance . "', weekpoints='" . $member_weekpoints . "', currentpoints='" . $member_currentpoints . "', prevrank='" . $member_prevrank . "', currentrank='" . $member_currentrank . "', picksequence='" . $member_picksequence . "' WHERE id='" . $member_id . "'";
							mysql_query($members_update_sql);
							
							// UPDATE picks table
							$picks_update_sql = "UPDATE picks SET pointsaward='" . $pick_pointsaward . "', dollarswon='" . $pick_dollars . "' WHERE id='" . $pick_id . "'";
							mysql_query($picks_update_sql);
						}
						$result_count++;
					}

					// Sixth - Query for all members in database and then loop through each member
					// -- loop
					$count = 0;
					$members_sql = "SELECT id,currentrank FROM members ORDER BY currentpoints DESC";
					$members_result = mysql_query($members_sql);
					while($row = mysql_fetch_assoc($members_result))
					{	//	Seventh - Update the current rank
						$count++;
						$member_update_sql = "UPDATE members SET currentrank='" . $count . "' WHERE id='" . $row['id'] . "'";
						mysql_query($member_update_sql);
					}
					// -- end loop
					
					// NOTIFICATION TO FIRST PICK MEMBER -- TODO
					// This section will assess the various methods of communicating to the members and apply the appropiate technique.
					require("next_race.php");
					
					if($first_pick_nickname != "")
					{
                        echo "<br>FIST PICK FOR " . $next_race_name . " IS " . $first_pick_nickname . "<br>";
					}

                    // now close the race on the schedule
                    $sql = "UPDATE schedule SET closed='1' WHERE id='" . $prev_race_id . "'";
                    mysql_query($sql);

                    // check if picking from queue
                    $picked = pickFromQueue($first_pick_id, $next_race_id, 1, $next_race_key);
//				}
			}
			else	// if not submitted, then user just came to the page and we should display the entry form
			{
				$sql = "SELECT r.id,r.name FROM races r INNER JOIN schedule s ON r.id=s.race_id WHERE s.closed='2' ORDER BY r.name ASC";
				$res = mysql_query($sql);
				
				echo "\n<h1>Close Race</h1>\n";
				echo "<form action='" . $SCRIPT_NAME . "' method='post'>\n";
				echo "\t<table>\n";
				echo "\t\t<tr>\n";
				echo "\t\t\t<td>Race</td>\n";
				echo "\t\t\t<td><select name='race'>\n";
				while($row = mysql_fetch_assoc($res))
				{	echo "\t\t\t\t<option value='" . $row['id'] . "'>" . $row['name'] . "</option>\n";				
				}
				echo "\t\t\t</select></td>\n";
				echo "\t\t</tr>\n";
				echo "\t\t<tr>\n";
				echo "\t\t\t<td>&nbsp;</td>\n";
				echo "\t\t\t<td><input type='submit' name='submit' value='Submit'></td>\n";
				echo "\t\t</tr>\n";
				echo "\t</table>\n";
				echo "</form>\n";
								
				?>
				
				<h4>BE SURE THE RACE RESULTS ARE ENTERED THROUGH THE TABLE EDITOR FIRST!</h4>
				What happens when you submit this form?<br><br>
				1. Each driver's current points will increment by the amount of points won in the race results.<br>
				2. Each pick will have the points award set.<br>
				3. Each pick will have the dollars won set.<br>
				4. Each member will have the week points updated.<br>
				5. Each member will have the current points updated.<br>
				6. Each member will have the prev rank set to current rank.<br>
				7. Then each member's current rank will be recalculated based on total points.<br>
				8. Each member will have their dollars won incremented by the amount won.<br>
				9. Each member's balance will be incremented by the amount won.<br>
				10. The member with the first pick of the next race will be notified that it is now their pick.<br>
				<br>
				<?php
			}
		}
		?>
	</td></tr></table>
</div> <!-- right-col -->