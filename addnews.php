<script type="text/javascript" src="./tinymce/jscripts/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript">
tinyMCE.init({
	theme : "advanced",
	mode : "textareas",
	plugins : "fullpage",
	theme_advanced_buttons3_add : "fullpage"
});
</script>

<?php

if($_SESSION['USERNAME'] == "steppsr" or $_SESSION['USERNAME'] == "westcrabtree" or $_SESSION['USERNAME'] == "russdog" or $_SESSION['USERNAME'] == "gofasta")
{
	if($_POST['submit'])
	{
		$postTitle   = mysql_real_escape_string($_POST['title']);
		$postContent = mysql_real_escape_string($_POST['content']);

		if(get_magic_quotes_gpc())
		{
            $postTitle   = stripslashes(str_replace('\r\n', '', $postTitle));
            $postContent = stripslashes(str_replace('\r\n', '', $postContent));
		}

        $sql = "INSERT into news(dt,title,body) VALUES(NOW(), '" . $postTitle . "', '" . $postContent . "');";
		mysql_query($sql) or die(mysql_error());
	}
	else
	{
?>
<form method="post" action="./index.php?content_page=addnews">
	Title:<input type="text" name="title" size="50">
	<p>	
		<textarea name="content" cols="50" rows="15"></textarea>
		<input type="submit" name='submit' value="Add News" />
	</p>
</form>

<?php
	}
}
	?>
