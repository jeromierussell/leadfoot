<?php

include("config.php");

if(isset($_GET['id']) == TRUE)
{
	if(is_numeric($_GET['id']) == FALSE)
	{
		$error = 1;
	}

	if($error == 1)
	{
		header("Location: " . $config_basedir);
	}
	else
	{
		$validtopic = $_GET['id'];
	}
}
else
{
	header("Location: " . $config_basedir);
}

require("header.php");
require("menu.php");

$topicsql = "SELECT topics.subject, topics.forum_id, forums.name FROM topics, forums WHERE topics.forum_id = forums.id AND topics.id = " . $validtopic . ";";
$topicresult = mysql_query($topicsql);

$topicrow = mysql_fetch_assoc($topicresult);

echo "<h2>" . $topicrow['subject'] . "</h2>";

echo "<a href='index.php'>Home</a> -> <a href='index.php?content_page=forum'>Forum</a> -> <a href='viewforum.php?id=" . $topicrow['forum_id'] . "'>" . $topicrow['name'] . "</a><br /><br />";

$threadsql = "SELECT messages.*, members.nickname FROM messages, members WHERE messages.user_id = members.id AND messages.topic_id = " . $validtopic . " ORDER BY messages.date;";
$threadresult = mysql_query($threadsql);

echo "<table>";

while($threadrow = mysql_fetch_assoc($threadresult))
{
	echo "<tr><td style='border-style: dotted;border-width:1px;'><b>" . $threadrow['subject'] . "</b><br>";
	echo $threadrow['body'] . "<br>";
	echo "Posted by <span id='nickname'>" . $threadrow['nickname'] . "</span> on " . date("n-j-Y g:ia", strtotime($threadrow['date'])) . "</td></tr>";
}

echo "<tr><td><a href='reply.php?id=" . $validtopic . "'>Add Reply</a></td></tr>";
echo "</table>";

require("footer.php");

?>