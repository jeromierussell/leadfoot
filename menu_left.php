<div id="sidebar">
<ul>
<?php
	if(isset($_SESSION['MEMBERNAME']) == TRUE)
	{
?>
        <li>
			<h2 class="heading">Navigation</h2>
				<ul>
					<li class="first"><a href='<?php $config_basedir ?>index.php?content_page=leaderboard'>Leaderboard</a></li>
					<li><a href='<?php $config_basedir ?>index.php?content_page=mystats'>My Stats</a></li>
					<li><a href='<?php $config_basedir ?>index.php?content_page=makepick'>Make Pick</a></li>
                    <li><a href='<?php $config_basedir ?>index.php?content_page=queue'>Manage Pick Queue</a></li>
					<li><a href='<?php $config_basedir ?>index.php?content_page=historicalleagueresults'>Historical League Results</a></li>
					<li><a href='<?php $config_basedir ?>index.php?content_page=driver'>Drivers</a></li>
					<li><a href='<?php $config_basedir ?>index.php?content_page=messageboard'>Message Board</a></li>
				</ul>
        </li>
		
<?php
/*
		<a href='<?php $config_basedir ?>index.php?content_page=position'>Position Of Week</a><br>
		<a href='<?php $config_basedir ?>index.php?content_page=poles'>Poles</a><br>
		<a href='<?php $config_basedir ?>index.php?content_page=yearly'>Yearly Stats</a><br>
		<a href='<?php $config_basedir ?>index.php?content_page=career'>Career Stats</a><br>
		<br>
*/
?>
<?php

	}
?>

<?php 

if(isset($_SESSION['USERNAME']) == TRUE)
{
	if($_SESSION['USERNAME'] == "steppsr" or $_SESSION['USERNAME'] == "westcrabtree" or $_SESSION['USERNAME'] == "russdog" or $_SESSION['USERNAME'] == "gofasta")
	{	
	?>
        <li>
			<h2 class="heading">Admin</h2>
				<ul>
					<!--<li class="first"><a href='" . $config_basedir . "index.php?content_page=enterpayment'>Enter Payment-FIXME</a></li>-->
					<!--<li><a href='<?php $config_basedir ?>index.php?content_page=enterpayout'>Enter Payout-FIXME</a></li>-->
					<!--<li><a href='<?php $config_basedir ?>index.php?content_page=closerace'>Close Race</a></li>-->
					<!--li><a href='../pear/edit_tracks.php'>Edit Tracks</a></li>
					<li><a href='../pear/edit_races.php'>Edit Races</a></li>
					<li><a href='../pear/edit_schedule.php'>Edit Schedule</a></li>
					<li><a href='../pear/edit_drivers.php'>Edit Drivers</a></li>
					<li><a href='../pear/edit_members.php'>Edit Members</a></li>
					<li><a href='../pear/edit_picks.php'>Edit Picks</a></li>
					<li><a href='../pear/edit_poles.php'>Edit Poles</a></li>
					<li><a href='../pear/edit_raceresults.php'>Edit Race Results</a></li>
					<li><a href='../pear/edit_ledger.php'>Edit Ledger</a></li>-->
					<!--<li><a href='../pear/edit_drivers.php'>Edit Drivers-FIXME</a></li>-->
					<li class="first"><a href='<?php $config_basedir ?>index.php?content_page=editnews'>Manage News</a></li>
					<!--<li><a href='../pear/edit_website_setup.php?view=1'>Edit Website Setup</a></li>-->
					<li><a href='<?php $config_basedir ?>index.php?content_page=adminledger'>Ledger</a></li>
					<li><a href='<?php $config_basedir ?>index.php?content_page=managedrivers'>Manage Drivers</a></li>
					<li><a href='<?php $config_basedir ?>index.php?content_page=adminloadresults'>Load Race Results</a></li>
				</ul>
        </li>
<?php
	}

require("streaks.php");
require("nopoints.php");

}

?>
</ul>
</div> <!-- sidebar1 -->
