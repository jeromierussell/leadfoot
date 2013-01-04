<div id='right-col'>
	<div id='main_content'>
<?php
	include("displayledgertable.php");
	$member_id = $_REQUEST['member_id'];	
	$member_sql = "SELECT r.id AS result_id, m.nickname, m.id AS member_id, r.dollarsowed, r.dollarspaid, r.dollarswon, r.dollarspaidout, r.balance FROM annualmemberresults r INNER JOIN members m ON m.id=r.member_id WHERE r.year=".SEASON_YEAR." AND m.id=".$member_id.";";
	$member_results = mysql_query($member_sql);
	$member_row = mysql_fetch_assoc($member_results);	
	
	echo "<h1>".SEASON_YEAR." Ledger For ".$member_row['nickname']."</h1>";	
	
	echo "<p>";
	
	if($_POST['submit'])
	{
		$amount = $_POST['insert_amount'];
		$type = $_POST['insert_type'];
		$notes = $_POST['insert_notes'];
		
		// insert the new row
		$sql_insert = "INSERT INTO ledger (amount, member_id, dt, type, year, notes) VALUES (".$amount.",".$member_id.",'".date('Y-m-d H-i-s')."',".$type.",".SEASON_YEAR.",'".$notes."');";		
		mysql_query($sql_insert) or die(mysql_error());
		
		// update the user's balance
		$dollarsowed = $member_row['dollarsowed'];
		$dollarspaid = $member_row['dollarspaid'];
		$dollarspaidout = $member_row['dollarspaidout'];
		$dollarswon = $member_row['dollarswon'];
		$balance = $member_row['balance'];
		
		switch($type)
		{
			case 1:
				$dollarswon += $amount;
				break;
			case 2:
				$dollarspaid += $amount;
				break;
			case 3:
				$dollarspaidout += $amount;
				break;
		}		
								
		$balance = $dollarsowed - $dollarspaid + $dollarspaidout - $dollarswon;		
		$sql_update = "UPDATE annualmemberresults SET dollarspaid=".$dollarspaid.", dollarswon=".$dollarswon.", dollarspaidout=".$dollarspaidout.", balance=".$balance." WHERE id=".$member_row['result_id'].";";
		mysql_query($sql_update) or die(mysql_error());
	}
	
	echo "<form action='" . $config_basedir."index.php?content_page=adminmemberledger' method='post'>\n";
	echo "<h2>Add New Entry</h2><p>";
	echo "Type: ";
	$sql_type = "SELECT t.id, t.name FROM types_amounttype t ORDER BY t.name;";
	$results_type = mysql_query($sql_type);
	echo "<select name='insert_type'>";
	while($row = mysql_fetch_assoc($results_type))
	{
		echo "<option value='".$row['id']."'>".$row['name']."</option>";
	}
	echo "</select>";
	echo " Amount: ";
	echo "<input type='text' name='insert_amount' size=5>";
	echo " Notes: ";
	echo "<input type='text' name='insert_notes' size=20>";
	echo "<input type='hidden' name='member_id' value='" . $member_id . "' />";
	echo "<input type='submit' name='submit' value='Submit'>\n";
	echo "</form>";
	
	echo "<p>";
	echo "<h2>Season Summary</h2>";
	displayLedgerTable($member_id);
	
	echo "<p><h2>Ledger Transactions</h2>";
	
	echo "<table id='ledgerentries'>";
	echo "<tr>";
	echo "<th class='sortable'>Date</th>";
	echo "<th class='sortable'>Type</th>";
	echo "<th class='sortable'>Amount</th>";
	echo "<th class='sortable'>Notes</th>";
	echo "</tr>";
	
	$sql_trans = "SELECT l.amount, l.dt, tat.name AS type, l.notes FROM ledger l INNER JOIN types_amounttype tat ON tat.id=l.type WHERE l.member_id=".$member_id." AND l.year=".SEASON_YEAR." ORDER BY l.dt;";
	$results_trans = mysql_query($sql_trans);
	while($row = mysql_fetch_assoc($results_trans))
	{
		echo "<tr>";
		echo "<td align='center'>".$row['dt']."</td>";
		echo "<td align='center'>".$row['type']."</td>";
		echo "<td align='center'>".$row['amount']."</td>";
		echo "<td align='center'>".$row['notes']."</td>";
		echo "</tr>";
	}
	echo "</table>";	
?>	
	</div>
</div>