<?php

require("config.php");
require("functions.php");

$db = mysql_connect($dbhost, $dbuser, $dbpassword);
mysql_select_db($dbdatabase, $db);

$sql = "SELECT * FROM website_setup WHERE id=1;--";
$results = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_assoc($results);
define("ANNUAL_YEAR", $row['year']);
define("WEBSITE_TITLE", $row['title']);
define("SEASON_YEAR", $row['season']);
define("SEASON_COMPLETED_WEEK", $row['lastcompletedweek']);
define("SEASON_NEXT_WEEK", ($row['lastcompletedweek'])+1);
define("PAGE_ROWS", $row['pagerows']);
define("NOTIFICATIONS", $row['notifications']);
define("YES",1);
define("NO",2);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title><?=WEBSITE_TITLE?></title>
	<link href='style.css' rel='stylesheet'>
	<script type="text/javascript" src="tablesort.js"></script>
    <link href="http://fonts.googleapis.com/css?family=Abel" rel="stylesheet" type="text/css" />
	<!--<link href='layout.css' rel='stylesheet'>	-->
</head>
<body>
<div id="wrapper">

<div id="header">

<div id="logo">





<?php

if(isset($_SESSION['USERNAME']) == TRUE) {
	echo "<div id='logout'><a href='logout.php'>Log Out</a></td></tr></table></div>";
    $grav_url = get_gravatar($_SESSION['EMAIL']);
    echo "<div id='grav'><img src='". $grav_url."' alt='' /></div>";
}
else {
?>

	<form action=login.php method='post'>
	
		<table class="loginTable">
        <tr>
            <td>
            </td>
        </tr>
		<tr>
			<td>Username</td>
			<td><input type='text' name='username' size=16 maxlength=15></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type='password' name='password' size=16 maxlength=15></td>
		</tr>
		<tr>
			<td></td>
			<td><input type='submit' name='submit' value='Login'></td>
		</tr>
		</table>
	</form>
<?php
}
?>


<?php
//   	if(isset($_SESSION['MEMBERNAME']) == TRUE)
    if(true)
   	{
           require("next_race.php");
           if( $next_race_time != null )
   		{
   ?>


   <span class='countdown-main'>
       <span class='countdown-current'>Next race at <?php echo($next_race_time) ?> ET<br>in</span>

       <script language="JavaScript">
       TargetDate = '<?php echo($next_race_time) ?>';
       BackColor = "none";
       ForeColor = "white";
       CountActive = true;
       CountStepper = -1;
       LeadingZero = true;
       DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
       FinishMessage = "progress";
       </script>
       <script language="JavaScript" src="countdown.js"></script>
   </span>


   <?php
           }
           else
           {
               echo "<div id='blankspace'></div>";

           }
       }
   ?>



<?php

//if(isset($_SESSION['USERNAME']) == TRUE) {
if(true) {

}

?>

</div>

<div id="simpleBanner">
    <div style="display:inline-block;margin-top:4px;margin-left:5px;">Leadfoot Racing League</div>
    <div style="display:inline-block;margin-top:4px;margin-right:5px;float:right">Season <?php echo ANNUAL_YEAR ?></div>
</div>
</div> <!-- header -->

<div id="page">
