<div class='post'>
	<h2 class='title'>Message Board</h2>
	<!--<div class='story'>-->
	<!--<table><tr><td>-->
		<script type="text/javascript" src="messageboard.js"></script>
		<form name="messageboard_form" id="messageboard_form" action="" method="post">
			<input type="hidden" name="messageboard_name" id="messageboard_name" value="<?php echo $_SESSION['MEMBERNAME']; ?>"/><br>
			<label>Message:</label><input type="text" name="messageboard_message" id="messageboard_message" tabindex="1" size="100" />
			<input type="submit" class="submit" value="Post" id="messageboard_submit" tabindex="3" />
			<script type="text/javascript">
				messageboard_form.messageboard_message.focus();
				messageboard_form.messageboard_message.select();
			</script>
		</form>
		
		<hr>
		
		<div id="messageposts">
			<?php echo get_entries(); ?>
		</div>
		
	<!--</td></tr></table>-->
	</div>
</div> <!-- right-col -->