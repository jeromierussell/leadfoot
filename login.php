<?php

session_start();

require("config.php");

$db = mysql_connect($dbhost, $dbuser, $dbpassword) or die("Error connecting to " . $dbhost);
$selected = mysql_select_db($dbdatabase, $db) or die("Error selecting to " . $dbdatabase);

if($_POST['submit'])
{
	$sql = "SELECT * FROM members WHERE username = '" . $_POST['username'] . "' AND password = '" . $_POST['password'] . "';";
	$result = mysql_query($sql);
	$numrows = mysql_num_rows($result);

	if($numrows >= 1)
	{
		$row = mysql_fetch_assoc($result);
		
		if($row['active'] == 1)
		{
			session_register("USERNAME");
			session_register("USERID");

			$_SESSION['USERNAME'] = $row['username'];
			$_SESSION['USERID'] = $row['id'];
			$_SESSION['MEMBERNAME'] = $row['nickname'];
            $_SESSION['EMAIL'] = $row['email'];

			switch($_GET['ref'])
			{
				case "newpost":
					if(isset($_GET['id']) == FALSE)
					{
						header("Location: " . $config_basedir . "newtopic.php");
					}
					else
					{
						header("Location: " . $config_basedir . "newtopic.php?id=" . $_GET['id']);
					}
					break;

				case "reply":
					if(isset($_GET['id']) == FALSE)
					{
						header("Location: " . $config_basedir . "newtopic.php");
					}
					else
					{
						header("Location: " . $config_basedir . "newtopic.php?id=" . $_GET['id']);
					}
					break;

				default:
					header("Location: " . $config_basedir);
					break;
			}
		}
		else
		{
			require("header.php");
			echo "This account is not verified yet. Please check with the administrator to ensure your account has been setup.";
		}
	}
	else
	{
		//echo "Number of rows = " . $$numrows . "<br><br>";
		header("Location: " . $config_basedir . "index.php?error=1");
	}
}
else
{
	require("header.php");

	if($_GET['error'])
	{
		echo "Incorrect login, please try again!";
	}

	?>

	<form action='<?php echo $SCRIPT_NAME ?>' method='post'>

	<table>
	<tr>
		<td>Username</td>
		<td><input type='text' name='username'></td>
	</tr>
	<tr>
		<td>Password</td>
		<td><input type='password' name='password'></td>
	</tr>
	<tr>
		<td></td>
		<td><input type='submit' name='submit' value='Login!'></td>
	</tr>
	</table>
	</form>

<?php
}
require("footer.php");
?>