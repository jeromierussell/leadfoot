<?php

require("config.php");

function pf_script_with_get($script)
{
	$page = $script;
	$page = $page . "?";

	foreach($_GET as $key => $val)
	{
		$page = $page . $key . "=" . $val . "&";
	}

	return substr($page, 0, strlen($page) - 1);
}

function db_connect()
{
	require("config.php");
	$connection = mysql_connect($dbhost, $dbuser, $dbpassword);
	mysql_select_db($dbdatabase, $connection);
	return $connection;
}

function db_close($conn)
{
	mysql_close($conn);
}

function get_entries()
{
	$connection = db_connect();
	$query = "SELECT * FROM `guestbook` ORDER BY `id` DESC";
	$result = mysql_query($query, $connection);
	db_close($connection);
	return create_output($result);
}

function create_output($result)
{
	$output = "";
	while($row = mysql_fetch_assoc($result))
	{
		$posttime = strtotime($row['datetimecreated']);
		$timezone_offset = strtotime("+ 9 hours 30 minutes");
		$posttime += $timezone_offset;
        $output .= "<div style='margin-top:5px;'><span id='postname'>" . $row['name'] . "</span> - <span id='postmessage'>" . $row['comments']."</span></div>";
        $output .= "<div style='margin-top:4px;margin-bottom:6px'><span id='timestamp'>".date('F j, Y', strtotime($row['datetimecreated']))." at ".date('g:i a', $posttime) . "</span></div>";
	}
	return $output;
}

function add_post($message, $name)
{
	$connection = db_connect();
	$name = mysql_real_escape_string($name,$connection);
	$message = mysql_real_escape_string($message,$connection);
    $message = stripslashes(str_replace('\r\n', '', $message));

	$query = "INSERT INTO `guestbook` SET `name` = '{$name}', `comments` = '{$message}'";
	mysql_query($query);
	db_close($connection);
}

/**
    * Get either a Gravatar URL or complete image tag for a specified email address.
    *
    * @param string $email The email address
    * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
    * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
    * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
    * @param boole $img True to return a complete IMG tag False for just the URL
    * @param array $atts Optional, additional key/value attributes to include in the IMG tag
    * @return String containing either just a URL or a complete image tag
    * @source http://gravatar.com/site/implement/images/php/
    */
   function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
   	$url = 'http://www.gravatar.com/avatar/';
   	$url .= md5( strtolower( trim( $email ) ) );
   	$url .= "?s=$s&d=$d&r=$r";
   	if ( $img ) {
   		$url = '<img src="' . $url . '"';
   		foreach ( $atts as $key => $val )
   			$url .= ' ' . $key . '="' . $val . '"';
   		$url .= ' />';
   	}
   	return $url;
   }


?>