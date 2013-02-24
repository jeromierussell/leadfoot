<div id='right-col' class='post'>
	<div id='main_content'>
	<h1>Manage Drivers</h1>
<?php
	$selecteddrivers = $_POST['selected_drivers'];
	if( isset($selecteddrivers) && !empty($selecteddrivers) )
	{
		$update_setter = null;
		if($_POST['makeactive'])
		{
			$update_setter = " isactive=1 ";
		}
		else if($_POST['makeinactive'])
		{
			$update_setter = " isactive=2 ";
		}
		else if($_POST['mustqualifyyes'])
		{
			$update_setter = " must_qualify=1 ";
		}
		else if($_POST['mustqualifyno'])
		{
			$update_setter = " must_qualify=2 ";
		}
		
		if( isset($update_setter) )
		{
			foreach($selecteddrivers as $driver_id)
			{
				$sql_update = "UPDATE drivers SET ".$update_setter." WHERE id=".$driver_id;
				mysql_query($sql_update) or die(mysql_error());
			}
		}
	}	
		
	echo "<p><a href='".$config_basedir."index.php?content_page=addeditdriver' class='large'>Add New Driver</a><p>";

	$sql = "SELECT d.name, d.id, d.number, d.owner, d.team, d.make, d.isactive, d.must_qualify AS b_must_qualify, t2.name AS must_qualify, t.name AS active FROM drivers d INNER JOIN types_yes_no t ON t.id=d.isactive INNER JOIN types_yes_no t2 ON t2.id=d.must_qualify ORDER BY d.isactive, d.name;";
	$results = mysql_query($sql);
	echo "<form action='".$config_basedir."index.php?content_page=managedrivers' method='post'>\n";
	echo "<input type='submit' name='makeactive' value='Make Active'>\n";
	echo "<input type='submit' name='makeinactive' value='Make Inactive'>\n";
	echo "<input type='submit' name='mustqualifyyes' value='Must Qualify: Yes'>\n";
	echo "<input type='submit' name='mustqualifyno' value='Must Qualify: No'>\n";
	echo "<table id='drivers'>";
	echo "<tr>";
	echo "<th></th>";
	echo "<th class='sortable'>Name</th>";
	echo "<th class='sortable'>No.</th>";
	echo "<th class='sortable'>Owner</th>";
	echo "<th class='sortable'>Team</th>";
	echo "<th class='sortable'>Make</th>";
	echo "<th class='sortable'>Must Qualify</th>";
	echo "<th class='sortable'>Active</th>";
	echo "</tr>";
	
	while($row = mysql_fetch_assoc($results))
	{
		$class = "";
		if( $row['isactive'] == 2 )
		{
			$class = "darker2";
		}
		$must_qualify_class = $class;
		if( $row['b_must_qualify'] == 1 )
		{
			$must_qualify_class = "blue";
		}
		echo "<tr>";
		echo "<td class='".$class."' align='center'><input type='checkbox' name='selected_drivers[]' value='".$row['id']."'/></td>";
		echo "<td class='".$class."' align='center'><a href='".$config_basedir."index.php?content_page=addeditdriver&driver_id=".$row['id']."'>".$row['name']."</a></td>";
		echo "<td class='".$class."' align='center'>".$row['number']."</td>";
		echo "<td class='".$class."' align='center'>".$row['owner']."</td>";
		echo "<td class='".$class."' align='center'>".$row['team']."</td>";
		echo "<td class='".$class."' align='center'>".$row['make']."</td>";
		echo "<td class='".$must_qualify_class."' align='center'>".$row['must_qualify']."</td>";
		echo "<td class='".$class."' align='center'>".$row['active']."</td>";
		echo "</tr>";
	}
	
	echo "</table>";
	echo "</action>";
?>	
	</div>
</div>