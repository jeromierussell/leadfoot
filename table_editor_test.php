<?php

require("config.php");
require_once('table_editor.php');

$db = mysql_connect($dbhost, $dbuser, $dbpassword) or die("Error connecting to " . $dbhost);
$selected = mysql_select_db($dbdatabase, $db) or die("Error selecting to " . $dbdatabase);

$editor = new TableEditor($db, 'members');

$editor->setConfig('perPage',15);

$editor->setDisplayNames(array( 'id' => 'ID', 
								'username' => 'Username', 
								'password' => 'Password', 
								'nickname' => 'Nickname', 
								'bio' => 'Bio', 
								'currentpoints' => 'Current Points',
								'weekpoints' => 'This Weeks Points',
								'currentrank' => 'Current Rank',
								'prevrank' => 'Previous Rank',
								'memberspaid' => 'Members Paid',
								'membersreceived' => 'Members Received',
								'dollarswon' => 'Dollars Won',
								'email' => 'Email',
								'verifystring' => 'Verify String',
								'active' => 'Active',
								'picksequence' => 'Pick Sequence'));

$editor->noEdit('id');

$editor->setInputType('password','password');
$editor->setInputType('email','email');
$editor->setInputType('active', 'select');
$editor->setValuesFromQuery('active', "SELECT id, name FROM types_active");

$editor->display();

?>