<?php

session_start();

require("config.php");
require("functions.php");

$db = mysql_connect($dbhost, $dbuser, $dbpassword);
mysql_select_db($dbdatabase, $db);

if(isset($_SESSION['ADMIN']) == FALSE)
{
	header("Location: " . $config_basedir . "admin.php?ref=add");
}

if($_POST['submit'])
{
	$topicsql = "INSERT INTO forums(cat_id, name, description) VALUES(" . $_POST['cat'] . ", '" . $_POST['name'] . "', '" . $_POST['description'] . "');";
	mysql_query($topicsql);

	header("Location: " . $config_basedir);
}
else
{
	require("header.php");

	?>

	<h2>Add a new forum</h2>

	<form action="<?php echo pf_script_with_get($SCRIPT_NAME); ?>" method="post">
	<table>
	<?php

	if($validforum == 0)
	{
		$forumsql = "SELECT * FROM categories ORDER BY name;";
		$forumresult = mysql_query($forumsql);
	?>

	<tr>
		<td>Forum</td>
		<td>
		<select name="cat">
		<?php
		while($forumsrow = mysql_fetch_assoc($forumresult))
		{
			echo "<option value='" . $forumsrow['id'] . "'>" . $forumsrow['name'] . "</option>";
		}
		?>
		</select>
		</td>
	</tr>
	<?php
	}
	?>

	<tr>
		<td>Name</td>
		<td><input type="text" name="name"></td>
	</tr>
	<tr>
		<td>Description</td>
		<td><textarea name="description" rows="10" cols="50"></textarea></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="submit" value="Add Forum!"></td>
	</tr>
	</table>
	</form>

	<?php
}

require("footer.php");

?>