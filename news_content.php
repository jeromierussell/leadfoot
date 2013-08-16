<div class="post">
	<div class="story">
		<div id="messageposts">
			<fieldset class="lastPost">
				<legend>Latest Message Board Posts</legend>
				<a href='<?php $config_basedir ?>index.php?content_page=messageboard' style="float:right;margin-top:-10px">View Full Message Board</a>
				<div style="margin-top:-10px">
				<?php
					$query = "SELECT * FROM `guestbook` ORDER BY `id` DESC limit 5";
					$result = mysql_query($query);
					$output = "";
					while($row = mysql_fetch_assoc($result))
					{
						$posttime = strtotime($row['datetimecreated']);
						$timezone_offset = strtotime("+ 9 hours 30 minutes");
						$posttime += $timezone_offset;
                        $output .= "<div style='margin-top:5px;'><span id='postname'>" . $row['name'] . "</span> - <span id='postmessage'>" . $row['comments']."</span></div>";
                        $output .= "<div style='margin-top:4px;margin-bottom:6px'><span id='timestamp'>".date('F j, Y', strtotime($row['datetimecreated']))." at ".date('g:i a', $posttime) . "</span></div>";
					}

					echo $output;
				?>
				</div>
			</fieldset>
		</div>
	</div>
</div>

	<div class="post">
	<h2 class="title">News</h2>
	<div class="story">


<?php
if(isset($_GET['pagenum']))
{	$pagenum = $_GET['pagenum'];
}
if(!(isset($pagenum)))
{	$pagenum = 1;
}

$sql = "SELECT * FROM news ORDER BY dt DESC";
$res = mysql_query($sql);
$num_rows = mysql_num_rows($res);
$page_rows = PAGE_ROWS;
$last = ceil($num_rows / $page_rows);

if($pagenum < 1)
{	$pagenum = 1;
}
elseif($pagenum > $last)
{	$pagenum = $last;
}

$max = "LIMIT " . ($pagenum - 1) * $page_rows . "," . $page_rows;
$sql = "SELECT * FROM news ORDER BY dt DESC " . $max;
$res = mysql_query($sql);

while($row = mysql_fetch_assoc($res))
{
	echo "";
	echo "<p><div id='newstitle'>" . $row['title'];
	echo "<span id='newsdt'>" . date("n-j-Y g:ia", strtotime($row['dt'])) . "</span></div><br>";
	echo "<span id='newsbody'>" . $row['body'] . "</span></p>";
	echo "";
}
	echo "";
	echo "<p><span style='font-size:18px;'>Page $pagenum of $last</span><br>";
	if($pagenum == 1)
	{	echo "<span style='font-size:36px;color:#cccccc;font-weight:900;'>&laquo;</span> &nbsp;&nbsp; ";
		echo "<span style='font-size:36px;color:#cccccc;font-weight:900;'>&lsaquo;</span> &nbsp;&nbsp; ";
	}
	else
	{	echo "<a href='{$_SERVER['PHP_SELF']}?pagenum=1'><span style='font-size:36px;font-weight:900;'>&laquo;</span></a> &nbsp;&nbsp; ";
		$previous = $pagenum - 1;
		echo "<a href='{$_SERVER['PHP_SELF']}?pagenum=$previous'><span style='font-size:36px;font-weight:900;'>&lsaquo;</span></a> &nbsp;&nbsp; ";
	}
	if($pagenum == $last)
	{	echo " <span style='font-size:36px;color:#cccccc;font-weight:900;'>&rsaquo;</span> &nbsp;&nbsp; ";
		echo " <span style='font-size:36px;color:#cccccc;font-weight:900;'>&raquo;</span>";
	}
	else
	{	$next = $pagenum + 1;
		echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$next'><span style='font-size:36px;font-weight:900;'>&rsaquo;</span></a> &nbsp;&nbsp; ";
		echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$last'><span style='font-size:36px;font-weight:900;'>&raquo;</span></a>";
	}
	echo "";
?>
	
	</div> <!-- post -->
</div> <!-- entry-->
