<div id='right-col'>
	<div id='main_content'>
	<table><tr><td>
		<?php
		
		session_start();
		require("config.php");

		if(isset($_SESSION['USERNAME']) == FALSE)
		{
			header("Location: " . $config_basedir);
		}

		$db = mysql_connect($dbhost, $dbuser, $dbpassword);
		mysql_select_db($dbdatabase, $db);

		if($_SESSION['USERNAME'] == "steppsr" or $_SESSION['USERNAME'] == "westcrabtree")
		{
			if($_POST['submit'])
			{	// if submitted then we should update the tables
				// 1. query the members table for the selected member. We'll need to know the values of memberspaid so we can increment it. We'll also need the balance so we can increment it too.
				$sql = "SELECT balance,membersreceived FROM members WHERE id='" . $_POST['member'] . "' LIMIT 1;--";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				
				if(!is_numeric($_POST['amount']))
				{	header("Location: " . $config_basedir . "index.php?validation_error=1");
				}
				else
				{	$membersbalance = $row['balance'] - $_POST['amount'];
					$membersreceived = $row['membersreceived'] + $_POST['amount'];
					$membersid = $_POST['member'];
					
					$sql = "UPDATE members SET membersreceived='" . $membersreceived . "', balance='" . $membersbalance . "' WHERE id='" . $membersid . "';--";
					mysql_query($sql);

					$sql = "INSERT INTO ledger (amount,member_id,dt,type) VALUES ('" . $_POST['amount'] . "', '" . $membersid . "', NOW(), '3');--";
					mysql_query($sql);
					
					header("Location: " . $config_basedir . "../pear/edit_members.php?view=" . $membersid);
				}
				
			}
			else	// if not submitted, then user just came to the page and we should display the entry form
			{
				$sql = "SELECT id,nickname FROM members ORDER BY nickname ASC;--";
				$res = mysql_query($sql);
				
				echo "\n<h1>Enter Payout</h1>\n";
				echo "<form action='" . $SCRIPT_NAME . "' method='post'>\n";
				echo "\t<table>\n";
				echo "\t\t<tr>\n";
				echo "\t\t\t<td>Member</td>\n";
				echo "\t\t\t<td><select name='member'>\n";
				while($row = mysql_fetch_assoc($res))
				{	echo "\t\t\t\t<option value='" . $row['id'] . "'>" . $row['nickname'] . "</option>\n";				
				}
				echo "\t\t\t</select></td>\n";
				echo "\t\t</tr>\n";
				echo "\t\t<tr>\n";
				echo "\t\t\t<td>Amount</td>\n";
				echo "\t\t\t<td><input type='text' name='amount'></td>\n";
				echo "\t\t</tr>\n";
				echo "\t\t<tr>\n";
				echo "\t\t\t<td>&nbsp;</td>\n";
				echo "\t\t\t<td><input type='submit' name='submit' value='Submit'></td>\n";
				echo "\t\t</tr>\n";
				echo "\t</table>\n";
				echo "</form>\n";
				
				?>
				<br><br>What happens when you submit this form?<br><br>
				1. The member's balance is decreased by the amount entered.<br>
				2. The member's received amount is increased by the amount entered.<br>
				3. An entry is created in the ledger for the member with the amount, current date & time, and the type recorded. (The type is a payout)<br>
				<br>
				Any manual adjustments that need to happen should change all these fields to ensure all the calculations work properly.<br>
				<?php
			}
		}
		?>
	</td></tr></table>
	</div>
</div> <!-- right-col -->