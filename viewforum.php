<?php

include("config.php");

if(isset($_GET['id']) == TRUE)
{
	if(is_numeric($_GET['id']) == FALSE)
	{
		header("Location: " . $config_basedir);
	}
	$validforum = $_GET['id'];
}
else
{
	header("Location: " . $config_basedir);
}

require("header.php");
require("menu.php");

$forumsql = "SELECT * FROM forums WHERE id = " . $validforum . ";";
$forumresult = mysql_query($forumsql);
$forumrow = mysql_fetch_assoc($forumresult);

echo "<h2>" . $forumrow['name'] . "</h2>";
echo "<a href='index.php'>Home</a> -> <a href='index.php?content_page=forum'>Forum</a><br /><br />";
echo "<a href='newtopic.php?id=" . $validforum . "'>New Topic</a>";
echo "<br /><br />";

$topicsql = "SELECT MAX( messages.date ) AS maxdate, topics.id AS topicid, topics.*, members.* FROM messages, topics, members
	WHERE messages.topic_id = topics.id AND topics.user_id = members.id AND topics.forum_id = " . $validforum . " GROUP BY
	messages.topic_id ORDER BY maxdate DESC;";
$topicresult = mysql_query($topicsql);
$topicnumrows = mysql_num_rows($topicresult);

if($topicnumrows == 0)
{
	echo "<table class='forum' width='300px'><tr><td>No topics!</td></tr></table>";
}
else
{
	echo "<table class='forum' id='edititem'>";
	echo "<tr>";
	echo "<th id='edititem'>Topic</th>";
	echo "<th id='edititem'>Replies</th>";
	echo "<th id='edititem'>Author</th>";
	echo "<th id='edititem'>Date Posted</th>";
	echo "</tr>";

	while($topicrow = mysql_fetch_assoc($topicresult))
	{
		$msgsql = "SELECT id FROM messages WHERE topic_id = " . $topicrow['topicid'];
		$msgresult = mysql_query($msgsql);
		$msgnumrows = mysql_num_rows($msgresult);
		echo "<tr>";
		echo "<td>";
		if($_SESSION['ADMIN'])
		{	echo "[<a href='delete.php?func=thread&id=" . $topicrow['topicid'] . "&forum=" . $validforum . "'>X</a>] - ";
		}
		echo "<a href='viewmessages.php?id=" . $topicrow['topicid'] . "'>" . $topicrow['subject'] . "</a></td>";
		echo "<td>" . $msgnumrows . "</td>";
		echo "<td><span id='nickname'>" . $topicrow['nickname'] . "</span></td>";
		echo "<td>" . date("n-j-Y g:ia", strtotime($topicrow['date'])) . "</td>";
		echo "</tr>";
	}

	echo "</table>";
}

require("footer.php");

?>