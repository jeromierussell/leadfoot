<div class='post'>
<?php
    $userid = $_SESSION['USERID'];
    $sql = "SELECT * FROM members WHERE id = ".$userid.";";
    if(isset($_SESSION['USERID']) == FALSE)
    {
        echo "<table><tr><td>Sorry, you must be logged in</td></tr></table>";
    }
    if($_POST['submit'])
    {
        $newpassword = $_POST['newpassword'];
        $currentpassword = $_POST['currentpassword'];
        $confirmpassword = $_POST['confirmpassword'];
        

        $result = mysql_query($sql);
        $numrows = mysql_num_rows($result);
    
        if($numrows == 1)
        {
            $row = mysql_fetch_assoc($result);
            $password = $row['password'];
            
            if($password != $currentpassword)
            {
                echo "Sorry, the current password does not match our records.";
            }
            else if($newpassword != $confirmpassword)
            {
                echo "Sorry, the new password and confirmation password do not match.";
            }
            else if($newpassword == $currentpassword)
            {
                echo "Sorry, the current and new passwords must be different";
            }
            else
            {
        		$sql_update = "UPDATE members SET password='".$newpassword."' WHERE id=".$userid.";";
        		mysql_query($sql_update) or die(mysql_error());
                echo "Password changed successfully";
            }
            require("mystats_content.php");
        }        
    }
    else
    {
        $result = mysql_query($sql);
        $numrows = mysql_num_rows($result);
    
        if($numrows == 1)
        {
            $row = mysql_fetch_assoc($result);
            $password = $row['password'];
        	echo "<form action='" . $config_basedir."index.php?content_page=changepassword' method='post'>";
            
            echo "<table>".
            "<tr>".
              "<td>Current Password</td>".
               "<td><input type='password' name='currentpassword'></td>".
            "</tr>".
            "<tr>".
              "<td>New Password</td>".
               "<td><input type='password' name='newpassword'></td>".
            "</tr>".
            "<tr>".
              "<td>Confirm New Password</td>".
               "<td><input type='password' name='confirmpassword'></td>".
            "</tr>".
            "	<tr>
		<td></td>
		<td><input type='submit' name='submit' value='Submit'></td>
	</tr>
    
</table></form>";
            
        }
    }
?>
</div>