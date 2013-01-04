<?php
session_start();

require("config.php");

$db = mysql_connect($dbhost, $dbuser, $dbpassword);
mysql_select_db($dbdatabase, $db);

$order = mysql_real_escape_string($_POST['order']);
$active_attr = mysql_real_escape_string($_POST['active']);
$active = 2;
if($active_attr && $active_attr == 'true')
{
    $active = 1;
}

error_log($active_attr);
error_log($active);
if($order != null)
{
    $member_id = $_SESSION['USERID'];
    $results = mysql_query("select id from member_queues where member_id = " . $member_id);
    $row = mysql_fetch_assoc($results);

    if($row == null || $row['id'] == null)
    {
        $member_queue_insert = "INSERT INTO member_queues (active, member_id) VALUES(" . $active . ", " . $member_id . ");";
        $mqi_result = mysql_query($member_queue_insert) or die("System Error = could not insert result row [".$member_queue_insert."] ".mysql_error());
        $results = mysql_query("select id from member_queues where member_id = " . $member_id );
        $row = mysql_fetch_assoc($results);
    }
    else
    {
        // save whether to use queue
        $member_queue_update = "update member_queues set active = " . $active . " where member_id = " . $member_id;
        mysql_query($member_queue_update) or die(mysql_error());
    }

    $member_queue_id = $row['id'];

    // clear out existing queue values for the currently logged in user
    $member_queue_delete = "DELETE from queues where member_queue_id = " . $member_queue_id;
    mysql_query($member_queue_delete) or die(mysql_error());

    $tok = strtok($order, ",");

    $i = 0;
    while ($tok !== false)
    {
        $queue_insert = "INSERT INTO queues (driver_id, position, member_queue_id) VALUES('" . $tok . "', " . $i . ", " . $member_queue_id . ")";
        mysql_query($queue_insert) or die(mysql_error());

        $i++;
        $tok = strtok(",");
    }


}
