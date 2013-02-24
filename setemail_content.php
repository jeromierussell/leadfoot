<?php
	if($_POST['submit'])
	{
		$user = "";
		if(isset($_SESSION['USERNAME']) == TRUE)
		{	$user = $_SESSION['USERNAME'];
			$email = $_POST['email'];
			// validate the email before we use it.
			// TODO - add code here for validation
			$sql = "UPDATE members SET email='" . $email . "' WHERE username='" . $user . "'";
			$results = mysql_query($sql);
			header("Location: " . $config_basedir);
		}
		else
		{	echo "<h2>Sorry, you are not logged into the system.</h2>";
		}
	}
	else
	{
	?>
		<div id='right-col' class='post'>
		<div id='main_content'>
		<h1>Set Email Address</h1> 
	<?php
		echo "<form action='" . $SCRIPT_NAME . "' method='post'>\n";
		echo "\t<table>\n";
		echo "\t\t<tr>\n";
		echo "\t\t\t<td>Email Address</td>\n";
		echo "\t\t\t<td><input type='text' name='email'></td>\n";
		echo "\t\t</tr>\n";
		echo "\t\t<tr>\n";
		echo "\t\t\t<td>&nbsp;</td>\n";
		echo "\t\t\t<td><input type='submit' name='submit' value='Submit'></td>\n";
		echo "\t\t</tr>\n";
		echo "\t</table>\n";
		echo "</form>\n";
		echo "<br/><br/>\n";
		echo "Tip: If you have text messaging on your cellphone you can use your cellphone's email address and receive notifications " . 
			 "on your phone when its your pick!<br><br>Example: 2701234567@cingularme.com";
	}
		
	?>
	</table>
	</div>
</div> <!-- right-col -->