<script type="text/javascript" src="./tinymce/jscripts/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript">
tinyMCE.init({
	theme : "advanced",
	mode : "textareas",
	plugins : "fullpage",
	theme_advanced_buttons3_add : "fullpage"
});
</script>
<div class='post'>
<?php

if($_SESSION['USERNAME'] == "steppsr" or $_SESSION['USERNAME'] == "westcrabtree" or $_SESSION['USERNAME'] == "russdog" or $_SESSION['USERNAME'] == "gofasta")
{
	if(isset($_GET['id']) == TRUE)
	{
		if(is_numeric($_GET['id']) == FALSE)
		{
			$error = 1;
		}

		if($error != 1)
		{
			$validentry = $_GET['id'];
		}
	}
	else
	{
		$validentry = 0;
	}

	if($_POST['submit'])
	{
		$postTitle = $_POST['title'];
		$postContent = $_POST['body'];

		if(!get_magic_quotes_gpc())
		{
			$postTitle   = mysql_real_escape_string($postTitle);
			$postContent = mysql_real_escape_string($postContent);

            $postTitle   = stripslashes(str_replace('\r\n', '', $postTitle));
            $postContent = stripslashes(str_replace('\r\n', '', $postContent));
		}

		$sql = "UPDATE news SET title='" . $postTitle . "', dt=NOW(), body = '" . $postContent . "' WHERE id=" . $validentry . ";";
		mysql_query($sql) or die(mysql_error());
		echo "The post was edited successfully";
	}
	else
	{
		
		if($validentry != 0)
		{
			$fillsql = "SELECT * FROM news WHERE id = " . $validentry . ";";
			$fillres = mysql_query($fillsql);
			$fillrow = mysql_fetch_assoc($fillres);
			
			$fillTitle = $fillrow['title'];
			$fillBody  = $fillrow['body'];

			?>
			<div id='post'>
				<div id='story'>
					<h1>Update News</h1>
					<form action="<?php echo "./index.php?content_page=editnews&id=" . $validentry; ?>" method="post">
					Title:<input type="text" name="title" size="50" value="<?php echo $fillTitle; ?>">
					<p>	
						<textarea name="body" cols="50" rows="15"><?php echo $fillBody; ?></textarea>
						<input type="submit" name="submit" value="Update News"></td>
					</p>
					</form>
				</div>
			</div> <!-- right-col -->
			<?php
		}
		else
		{
			$sql = "SELECT * FROM news order by dt desc";
			$req = mysql_query($sql);

			echo "<div id='post'>";
			echo "<div id='story'>";
			echo "<br><a href='./index.php?content_page=addnews'>Add News</a><br><br>";
			echo "<table id='edititem'><tr><th width='25'>&nbsp;</th><th id='edititem'>Date/Time</th><th id='edititem'>Title</th><th width='25'>&nbsp;</th></tr>";
			while($row = mysql_fetch_assoc($req))
			{
				echo "<tr><td id='edititem'><a href='./index.php?content_page=editnews&id=" . $row['id'] . "'>Edit</a></td><td id='edititem'>" . date("n-j-Y g:ia", strtotime($row['dt'])) . "</td><td id='edititem'>" . $row['title'] . "</td><td id='edititem'><a href='./index.php?content_page=deletenews&id=" . $row['id'] . "'>Delete</a></td></tr>";
			}			
			echo "</table>";
			echo "</div>";
			echo "</div> <!-- right-col -->";
		}
	}
	
}

?>
</div>