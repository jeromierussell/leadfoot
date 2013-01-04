<?php
session_start();

require("header.php");

if($_GET['error'])
{
	echo "Incorrect login, please try again!";
}

if($_GET['validation_error'])
{
    echo "Error entering payment amount";
}
?>

<?php
require("menu.php");

?>

<?php
    require("menu_left.php");
?>
<div id="content">

<div id="posts">

<?php
if(isset($_GET['content_page']) == TRUE) {
	switch($_GET['content_page'])
	{
		case "home":
			include("news_content.php");
			break;
		case "rules":
			require("rules_content.php");
			break;
		case "schedule":
			require("schedule_content.php");
			break;
		case "tracks":
			require("tracks_content.php");
			break;
		case "vod":
			require("vod_content.php");
			break;
		case "addnews":
			require("addnews.php");
			break;
		case "editnews":
			require("editnews.php");
			break;
		case "deletenews":
			require("deletenews.php");
			break;
		case "position":
			require("position_content.php");
			break;
		case "leaderboard":
			require("leaderboard_content.php");
			break;
		case "poles":
			require("poles_content.php");
			break;
		case "yearly":
			require("yearly_content.php");
			break;
		case "career":
			require("career_content.php");
			break;
		case "makepick":
			require("makepick_content.php");
			break;
		case "messageboard":
			require("messageboard_content.php");
			break;
		case "edittracks":
			require("edittracks_content.php");
			break;
		case "enterpicks":
			require("enterpicks_content.php");
			break;
		case "enterraceresults":
			require("enterraceresults_content.php");
			break;
		case "enterpayment":
			require("enterpayment_content.php");
			break;
		case "enterpayout":
			require("enterpayout_content.php");
			break;
		case "closerace":
			require("closerace_content.php");
			break;
		case "raceresults":
			require("raceresults_content.php");
			break;
		case "mystats":
			require("mystats_content.php");
			break;
		case "setemail":
			require("setemail_content.php");
			break;
		case "historicalresults":
			require("historicalresults_content.php");
			break;
		case "adminloadresults":
			require("adminloadresults_content.php");
			break;
		case "adminledger":
			require("adminledger_content.php");
			break;
		case "adminmemberledger":
			require("adminmemberledger_content.php");
			break;
		case "managedrivers":
			require("managedrivers_content.php");
			break;
		case "addeditdriver":
			require("addeditdriver.php");
			break;
        case "changepassword":
            require("change_password.php");
            break;
        case "historicalleagueresults":
            require("historical_league_results.php");
            break;
        case "driver":
            require("driver.php");
            break;
        case "queue":
            require("queue.php");
            break;
	}
}
else
{
	require("news_content.php");
}
?>
</div>
        </div> <!-- content -->
</div> <!-- wrapper -->
<?php

require("footer.php");

?>
