<!-- FIXME JLR - do we want to provide ability to see a user's stats for any given year?  Should we have a table/drop down?-->
<div class='post'>
	<div class='story'>	
	<?php
		include("memberweeklystats.php");
		include("careerstats.php");
		$user_name_passed = $_GET['username'];
		if( !isset($user_name_passed) )
		{
			$user_name_passed = $_SESSION['USERNAME'];
		}
		
		$count = 0;
		$user = "";
		if( isset($user_name_passed) )
		{	
			$user = $_SESSION['USERNAME'];
			
			$show_all = ($user == $user_name_passed);											
			
			if( $show_all )
			{	
				echo "<h1>My stats through week ".SEASON_COMPLETED_WEEK." of the ".SEASON_YEAR." season</h1>";
				$sql = "SELECT m.nickname, r.points, r.weekpoints, r.rank, r.previousrank, r.dollarspaid, r.dollarswon, m.email, r.picksequence, r.balance, " .
					   "r.poles,r.wins,r.top5,r.top10,r.top15,r.top20,r.avgfinish,r.avgpoints,r.avgpick " .
					   "FROM members m INNER JOIN annualmemberresults r ON r.member_id=m.id WHERE m.username='" . $user . "' and r.year=".SEASON_YEAR.";";		
				$results = mysql_query($sql) or die(mysql_error());
				
				/*	TABLE STRUCTURE - members
						id					username			password			nickname			bio					currentpoints
						weekpoints			currentrank			prevrank			memberspaid			dollarswon			membersreceived
						email				verifystring		active				picksequence		balance				current_poles
						current_wins		current_top5		current_top10		current_top15		current_top20		current_avg_finish
						current_avg_pick	career_points		career_dollarswon	career_poles		career_wins			career_top5
						career_top10		career_top15		career_top20		career_avg_finish	career_avg_pick		career_rank
				*/
				
				$row = mysql_fetch_assoc($results);
				$nickname = $row['nickname'];
				$currentpoints = $row['points'];
				$weekpoints = $row['weekpoints'];
				$currentrank = $row['rank'];
				$prevrank = $row['previousrank'];
				$memberspaid = $row['dollarspaid'];
				$dollarswon = $row['dollarswon'];
				$email = $row['email'];
				$picksequence = $row['picksequence'];
				$balance = $row['balance'];
				$current_poles = $row['poles'];
				$current_wins = $row['wins'];
				$current_top5 = $row['top5'];
				$current_top10 = $row['top10'];
				$current_top15 = $row['top15'];
				$current_top20 = $row['top20'];
				$current_avg_finish = $row['avgfinish'];
				$current_avg_points = $row['avgpoints'];
				$current_avg_pick = $row['avgpick'];
				
				if($email == "")
				{	$email = "missing... <a href='". $config_basedir . "index.php?content_page=setemail'>Enter it</a>";
				}
				else
				{	$email = $email . " <a href='". $config_basedir . "index.php?content_page=setemail'>Change</a>";
				}
				
				echo "<table id='mystats'>";
				echo "<tr><td>Nickname</td><td>" . $nickname . "</td></tr>";
				echo "<tr><td>Current Points</td><td>" . $currentpoints . "</td></tr>";
				echo "<tr><td>Week Points</td><td>" . $weekpoints . "</td></tr>";
				echo "<tr><td>Current Rank</td><td>" . $currentrank . "</td></tr>";
				echo "<tr><td>Previous Rank</td><td>" . $prevrank . "</td></tr>";
				echo "<tr><td>Paid In</td><td>" . $memberspaid . "</td></tr>";
				echo "<tr><td>Dollars Won</td><td>" . $dollarswon . "</td></tr>";
				echo "<tr><td>Current Balance</td><td>" . $balance . "</td></tr>";
				echo "<tr><td>Email Address</td><td>" . $email . "</td></tr>";
				echo "<tr><td>Pick Sequence</td><td>" . $picksequence . "</td></tr>";		
				echo "<tr><td>Current Poles</td><td>" . $current_poles . "</td></tr>";
				echo "<tr><td>Current Wins</td><td>" . $current_wins . "</td></tr>";
				echo "<tr><td>Current Top 5</td><td>" . $current_top5 . "</td></tr>";
				echo "<tr><td>Current Top 10</td><td>" . $current_top10 . "</td></tr>";
				echo "<tr><td>Current Top 15</td><td>" . $current_top15 . "</td></tr>";
				echo "<tr><td>Current Top 20</td><td>" . $current_top20 . "</td></tr>";
				echo "<tr><td>Current Average Finish</td><td>" . $current_avg_finish . "</td></tr>";
				echo "<tr><td>Current Average Points</td><td>" . $current_avg_points . "</td></tr>";
				echo "<tr><td>Current Average Pick</td><td>" . $current_avg_pick . "</td></tr>";
				echo "</table>";
				
				echo "<a href='".$config_basedir."index.php?content_page=changepassword'>Change Password</a>";
				
			}
			
			showMemberWeeklyStats( $user_name_passed );
			showCareerStats( $user_name_passed );
		}
		else
		{	
			echo "<tr><td>Please login to see your stats.</td></tr>";
		}
	?>	
	</div>
</div> <!-- right-col -->