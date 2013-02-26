<div id='right-col' class='post'>
	<div id='main_content'>
<?php
	$driver_id = $_REQUEST['driver_id'];	
	$name = $_POST['name'];
	$number = $_POST['number'];
	$owner = $_POST['owner'];
	$team = $_POST['team'];
	$make = $_POST['make'];
	$must_qualify = $_POST['must_qualify'];
	$isactive = $_POST['isactive'];
	$button_name = "Insert";
	if( !isset($must_qualify) )
	{
		$must_qualify=2;
	}
	if( !isset($isactive) )
	{
		$isactive=1;
	}
	
	if( $_POST['submit'] )
	{
		// update
		if( isset($driver_id) )
		{
			$sql = "UPDATE drivers SET name='".$name."', number='".$number."', owner='".$owner."', team='".$team."', make='".$make."', must_qualify=".$must_qualify.", isactive=".$isactive." WHERE id=".$driver_id.";";
			mysql_query($sql) or die(mysql_error());
			echo "<h2>Successfully updated driver</h2>";
		}
		// insert
		else
		{
			$sql = "INSERT INTO drivers (name, number, owner, team, make, must_qualify, isactive) VALUES ('".$name."', '".$number."', '".$owner."', '".$team."', '".$make."', ".$must_qualify.", ".$isactive.");";
			mysql_query($sql) or die(mysql_error());
			$driver_id = mysql_insert_id();
			echo "<h2>Successfully inserted driver</h2>";
		}
		require("managedrivers_content.php");
	}
	
	echo "<form action='".$config_basedir."index.php?content_page=addeditdriver' method='post'>\n";
	if( isset($driver_id) )
	{
		echo "<h1>Edit Driver</h1>";
		echo "<input type='hidden' name='driver_id' value='".$driver_id."'/>";
		$sql = "SELECT d.name, d.number, d.owner, d.team, d.make, d.isactive, d.must_qualify FROM drivers d WHERE d.id=".$driver_id.";";		
		$results = mysql_query($sql) or die(mysql_error());
		
		$row = mysql_fetch_assoc($results);
		$name = $row['name'];
		$number = $row['number'];
		$owner = $row['owner'];
		$team = $row['team'];
		$make = $row['make'];
		$must_qualify = $row['must_qualify'];
		$isactive = $row['isactive'];
		$button_name = "Update";		
	}
	else
	{
		echo "<h1>Add Driver</h1>";
	}
		
	echo "<table>";
	echo "<tr>";
	echo "	<td>Name</td>";
	echo "	<td><input type='text' name='name' size='50' maxlength='50' value='".$name."'/></td>";
	echo "</tr><tr>";
	echo "	<td>Number</td>";
	echo "	<td><input type='text' name='number' size='5' maxlength='3' value='".$number."'/></td>";
	echo "</tr><tr>";
	echo "	<td>Owner</td>";
	echo "	<td><input type='text' name='owner' size='50' maxlength='50' value='".$owner."'/></td>";
	echo "</tr><tr>";
	echo "	<td>Team</td>";
	echo "	<td><input type='text' name='team' size='50' maxlength='50' value='".$team."'/></td>";
	echo "</tr><tr>";
	echo "	<td>Make</td>";
	echo "	<td><input type='text' name='make' size='50' maxlength='50' value='".$make."'/></td>";
	echo "</tr><tr>";
	echo "	<td>Must Qualify</td>";
	echo "	<td>";
	renderYesNoSelect( 'must_qualify', $must_qualify );
	echo "	</td>";
	echo "</tr><tr>";
	echo "	<td>Active</td>";
	echo "	<td>";
	renderYesNoSelect( 'isactive', $isactive );
	echo "	</td>";
	echo "</tr>";
	echo "</table>";
	
	
	echo "<input type='submit' name='submit' value='".$button_name."'>\n";
	echo "</form>";
	
	function renderYesNoSelect( $select_name, $value )
	{
		echo "<select name='".$select_name."'>";
		$sql_y_n = "SELECT t.name, t.id FROM types_yes_no t ORDER BY t.id";
		$results_y_n = mysql_query($sql_y_n) or die(mysql_error());		
		while($row = mysql_fetch_assoc($results_y_n))
		{
			$id = $row['id'];
			$selected = " ";
			if( $id == $value )
			{
				$selected = " selected ";
			}
			echo "<option value='".$id."' ".$selected.">".$row['name']."</option>";
		}
		echo "</select>";
	}
	
?>	
	</div>
</div>