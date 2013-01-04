<?php

	require "functions.php";
	if(isset($_POST['message']))
	{
		add_post($_POST['message'], $_POST['name']);
	}
	echo get_entries();

?>