<?php
	function emailNextPick( $picksequence )
	{
		require("next_race.php");

		$bcc_email = "catsfan@gmail.com, westcrabtree@gmail.com, jeromierussell@gmail.com";

		$email_to = null;
		$next_pick_name = null;
		$test_indicator = "";
		if(NOTIFICATIONS == YES)
		{
			$sql = "SELECT email,nickname FROM members m INNER JOIN annualmemberresults r ON r.member_id=m.id and r.year=".SEASON_YEAR." WHERE picksequence=".$picksequence;
			$results = mysql_query($sql) or die(mysql_error());
			$row = mysql_fetch_assoc($results);
			$email_to = $row['email'];
			$next_pick_name = $row['nickname'];			
		}
		else if(NOTIFICATIONS == 3)
		{
			echo "Using email test mode<p>";
			$email_to = $bcc_email;
			$next_pick_name = "Admins";
			$test_indicator = "<TEST> ";
		}
		
		if( isset( $email_to ) )
		{			
			$title = $test_indicator."LeadFoot Racing League - Notification";
			$body = "Hey " . $next_pick_name . "! It is now your pick for race #".SEASON_NEXT_WEEK." of ".SEASON_YEAR." - ". $next_race_name . ".";
			// from address
			$header = "From: westcrabtree.com <west@westcrabtree.com>\r\n"; //optional headerfields
			// bcc address
			$header .= "BCC: ".$bcc_email."\r\n";
		
			if(strlen($email_to) > 2)
			{
				mail($email_to, $title, $body, $header);
			}
		}	
	}

    function emailAutoPickMade( $picksequence, $picked_driver_name )
    {
        require("next_race.php");

        $bcc_email = "catsfan@gmail.com, westcrabtree@gmail.com, jeromierussell@gmail.com";

        $email_to = null;
        $next_pick_name = null;
        $test_indicator = "";
        if(NOTIFICATIONS == YES)
        {
            $sql = "SELECT email,nickname FROM members m INNER JOIN annualmemberresults r ON r.member_id=m.id and r.year=".SEASON_YEAR." WHERE picksequence=".$picksequence;
            $results = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_assoc($results);
            $email_to = $row['email'];
            $next_pick_name = $row['nickname'];
        }
        else if(NOTIFICATIONS == 3)
        {
            echo "Using email test mode<p>";
            $email_to = $bcc_email;
            $next_pick_name = "Admins";
            $test_indicator = "<TEST> ";
        }

        if( isset( $email_to ) )
        {
            $title = $test_indicator."LeadFoot Racing League - Notification";
            $body = "Hey " . $next_pick_name . "! " . $picked_driver_name . " was auto-picked for race #".SEASON_NEXT_WEEK." of ".SEASON_YEAR." - ". $next_race_name . ".";
            // from address
            $header = "From: westcrabtree.com <west@westcrabtree.com>\r\n"; //optional headerfields
            // bcc address
            $header .= "BCC: ".$bcc_email."\r\n";

            if(strlen($email_to) > 2)
            {
                mail($email_to, $title, $body, $header);
            }
        }
    }

    function emailAutoPickRejected( $picksequence )
    {
        require("next_race.php");

        $bcc_email = "catsfan@gmail.com, westcrabtree@gmail.com, jeromierussell@gmail.com";

        $email_to = null;
        $next_pick_name = null;
        $test_indicator = "";
        if(NOTIFICATIONS == YES)
        {
            $sql = "SELECT email,nickname FROM members m INNER JOIN annualmemberresults r ON r.member_id=m.id and r.year=".SEASON_YEAR." WHERE picksequence=".$picksequence;
            $results = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_assoc($results);
            $email_to = $row['email'];
            $next_pick_name = $row['nickname'];
        }
        else if(NOTIFICATIONS == 3)
        {
            echo "Using email test mode<p>";
            $email_to = $bcc_email;
            $next_pick_name = "Admins";
            $test_indicator = "<TEST> ";
        }

        if( isset( $email_to ) )
        {
            $title = $test_indicator."LeadFoot Racing League - Notification";
            $body = "Hey " . $next_pick_name . "! No one from your pick queue was available.  Please visit the site and pick manually.  Race #".SEASON_NEXT_WEEK." of ".SEASON_YEAR." - ". $next_race_name . ".";
            // from address
            $header = "From: westcrabtree.com <west@westcrabtree.com>\r\n"; //optional headerfields
            // bcc address
            $header .= "BCC: ".$bcc_email."\r\n";

            if(strlen($email_to) > 2)
            {
                mail($email_to, $title, $body, $header);
            }
        }
    }
?>