<?php
	if(isset($_SESSION['MEMBERNAME']) == TRUE)
	{
?>
<div id="menu">
    <ul><li><a href='<?php $config_basedir ?>index.php?content_page=home'>Home</a></li></ul>
    <ul><li><a href='<?php $config_basedir ?>index.php?content_page=poles'>Poles</a></li></ul>
    <ul><li><a href='<?php $config_basedir ?>index.php?content_page=tracks'>Tracks</a></li></ul>
    <ul><li><a href='<?php $config_basedir ?>index.php?content_page=schedule'>Schedule / Results</a></li></ul>
    <ul><li><a href='<?php $config_basedir ?>index.php?content_page=rules'>Rules</a></li></ul>
</div>
<?php
    }
    else
    {
?>
<div id="menu">
    <ul><li><a href='#' onclick="alert('Please login')">Home</a></li></ul>
    <ul><li><a href='#' onclick="alert('Please login')">Poles</a></li></ul>
    <ul><li><a href='#' onclick="alert('Please login')">Tracks</a></li></ul>
    <ul><li><a href='#' onclick="alert('Please login')">Schedule / Results</a></li></ul>
    <ul><li><a href='#' onclick="alert('Please login')">Rules</a></li></ul>
</div>
    <?php

    }

?>
