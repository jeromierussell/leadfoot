<?php
	function displayLedgerTable( $member_id )
	{			
		$sql = "SELECT m.nickname, m.id AS member_id, r.dollarsowed, r.dollarspaid, r.dollarswon, r.dollarspaidout, r.balance FROM annualmemberresults r INNER JOIN members m ON m.id=r.member_id WHERE r.year=".SEASON_YEAR;
		
		if( isset( $member_id ) )
		{
			$sql .= " AND m.id=".$member_id;
		}
		
		$results = mysql_query($sql);
		echo "<table id='ledger'>";
		echo "<tr>";
		if( !isset( $member_id ) )
		{
			echo "<th class='sortable'>Member</th>";
		}
		echo "<th class='sortable'>Season Dues</th>";
		echo "<th class='sortable'>Dollars Received</th>";
		echo "<th class='sortable'>Dollars Won</th>";
		echo "<th class='sortable'>Dollars Paid Out</th>";
		echo "<th class='sortable'>Balance</th>";
		echo "</tr>";
		while($row = mysql_fetch_assoc($results))
		{
			echo "<tr>";
			if( !isset( $member_id ) )
			{
				echo "<td align='center'><a href='".$config_basedir."index.php?content_page=adminmemberledger&member_id=".$row['member_id']."'>".$row['nickname']."</a></td>";
			}
			echo "<td align='right'>".$row['dollarsowed']."</td>";
			echo "<td align='right'>".$row['dollarspaid']."</td>";
			echo "<td align='right'>".$row['dollarswon']."</td>";
			echo "<td align='right'>".$row['dollarspaidout']."</td>";
			echo "<td align='right'>".$row['balance']."</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>