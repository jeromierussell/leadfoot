<?php 

session_start();

require("config.php");

if(isset($_SESSION['USERNAME']) == TRUE)
{
	if($_SESSION['USERNAME'] == "steppsr" or $_SESSION['USERNAME'] == "westcrabtree" or $_SESSION['USERNAME'] == "russdog" or $_SESSION['USERNAME'] == "gofasta")
	{	require("header.php");
		require("menu.php");
		echo "<div id='right-col'><div id='main_content'><h1>ADMIN PAGE.... more to come!</h1></div></div>";
		require("footer.php");
	}
	else
	{	header("Location: " . $config_basedir);
	}
}


?>