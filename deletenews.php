<?php

if($_SESSION['USERNAME'] == "steppsr" or $_SESSION['USERNAME'] == "westcrabtree" or $_SESSION['USERNAME'] == "russdog" or $_SESSION['USERNAME'] == "gofasta")
{
	if(isset($_GET['id']) == TRUE)
	{
		if(is_numeric($_GET['id']) == FALSE)
		{
			$error = 1;
		}

		if($error != 1)
		{
			$validentry = $_GET['id'];
		}
	}
	else
	{
		$validentry = 0;
	}

	if($_POST['submit'])
	{
		$sql = "DELETE FROM news WHERE id=" . $_GET['id'] . ";";
		mysql_query($sql) or die(mysql_error());
		echo "The post was deleted successfully";
	}
	else
	{
		if($validentry != 0)
		{
			$fillsql = "SELECT * FROM news WHERE id = " . $validentry . ";";
			$fillres = mysql_query($fillsql);
			$fillrow = mysql_fetch_assoc($fillres);

			?>
<div>
			<h1>Delete News</h1>

			<form action="<?php echo "./index.php?content_page=deletenews" . "&id=" . $validentry; ?>" method="post">
				<table>
					<tr>
						<td>Date/Time</td>
						<td><input type="text" name="dt" value="<?php echo $fillrow['dt']; ?>"></td>
					</tr>
					<tr>
						<td>Title</td>
						<td><input type="text" name="title" value="<?php echo $fillrow['title']; ?>"></td>
					</tr>
					<tr>
						<td>Body</td>
						<td><textarea name="body" rows="10" cols="50"><?php echo $fillrow['body']; ?></textarea></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" name="submit" value="Delete News"></td>
					</tr>
				</table>
			</form>
</div>
			<?php
		}
	}
}