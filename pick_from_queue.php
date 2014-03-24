<?php
    function doesMemberHaveActiveQueue($pick_id_for_queue)
    {
        $sql_using_queue = "SELECT active from member_queues where member_id = " . $pick_id_for_queue;
        $member_queue_results = mysql_query($sql_using_queue);
        $member_queue_row = mysql_fetch_assoc($member_queue_results);
        $queue_active = $member_queue_row['active'];

        // if member is using pick queue...
        return $queue_active == 1;
    }

    function insertPick($date, $race_id, $member_id, $driver_id, $picksequence, $next_race_key)
    {
        $sql = "INSERT INTO picks (dt,schedule_id,member_id,driver_id,picksequence,year, racekey) VALUES ('" . $date . "','" . $race_id . "','" . $member_id . "','" . $driver_id . "','" . $picksequence . "', '".SEASON_YEAR."', ".$next_race_key.")";
        mysql_query($sql) or die(mysql_error());
    }

    function makeNextPick($picksequence, $next_race_id, $next_race_key)
    {
        // check to see if we've hit the last pick
        $members_sql = "SELECT COUNT(*) FROM members WHERE active=1";
        $members_result = mysql_query($members_sql);
        $number_of_members = mysql_result($members_result,0);

        $next_picksequence = $picksequence + 1;

        // attempt to auto-pick for next member in pick order
        if($next_picksequence <= $number_of_members)
        {
            $sql_next_pick_member = "SELECT m.id FROM members m INNER JOIN annualmemberresults r ON r.member_id=m.id and r.year=".SEASON_YEAR." WHERE picksequence=".$next_picksequence;
            $next_pick_member_results = mysql_query($sql_next_pick_member) or die(mysql_error());
            $next_pick_member_row = mysql_fetch_assoc($next_pick_member_results);

            pickFromQueue($next_pick_member_row['id'], $next_race_id, $next_picksequence, $next_race_key);
        }
    }

    function pickFromQueue($pick_id_for_queue, $next_race_id, $picksequence, $next_race_key)
    {
        $picked = false;

        // if member is using pick queue...
        $queue_active = doesMemberHaveActiveQueue($pick_id_for_queue);
        if($queue_active)
        {
            // get proper id
            $sql_get_member_queue_id = "SELECT id from member_queues where member_id = " . $pick_id_for_queue;
            $member_queue_id_results = mysql_query($sql_get_member_queue_id);
            $member_queue_id_row = mysql_fetch_assoc($member_queue_id_results);
            $member_queue_id = $member_queue_id_row['id'];

            // get already picked drivers (not applicable for first pick)
            $sql_picked_drivers = "SELECT id FROM drivers d WHERE d.id IN (select driver_id from picks p where p.schedule_id=".$next_race_id.")";
            $picked_drivers_results = mysql_query($sql_picked_drivers);
            // create comma separated list of picked drivers to filter out of query
            $picked_driver_ids = "";
            while($picked_row = mysql_fetch_assoc($picked_drivers_results))
            {
                $picked_driver_ids = $picked_driver_ids . $picked_row['id'] . ",";
            }

            // remove last comma if necessary
            if(strlen($picked_driver_ids) > 0)
            {
                $picked_driver_ids = substr($picked_driver_ids, 0, -1);
            }

            // get queued picks
            $sql_queue_value = "SELECT driver_id from queues where member_queue_id = " . $member_queue_id . " AND driver_id not in (" . $picked_driver_ids . ") ORDER BY position ASC limit 1";

            $sql_queue_results = mysql_query($sql_queue_value);

            $queue_row = mysql_fetch_assoc($sql_queue_results);

            // todo jro - how to determine if there are results?  hopefully this works
            if($queue_row != null)
            {
                // make pick
                $date 		= date('Y-m-d H-i-s');
                $race_id	= $next_race_id;
                $member_id	= $pick_id_for_queue;
                $driver_id	= $queue_row['driver_id'];

                insertPick($date, $race_id, $member_id, $driver_id, $picksequence, $next_race_key);

                $picked = true;

                // check to see if notifications are turned on system-wide first
                if(NOTIFICATIONS == YES || NOTIFICATIONS == 3)
                {
                    // get driver name for email
                    $driver_name_sql = "select name from drivers where id = " . $driver_id;
                    $driver_name_sql_results = mysql_query($driver_name_sql);
                    $driver_name_row = mysql_fetch_assoc($driver_name_sql_results);
                    $picked_driver_name = $driver_name_row['name'];

                    // notify current pick that a selection was made
                    emailAutoPickMade($picksequence, $picked_driver_name);
                }

                // make next pick (will determine if next pick should be made based on pick queue settings)
                makeNextPick($picksequence, $next_race_id, $next_race_key);
            }
            else
            {
                // email auto-pick couldn't be made
                emailAutoPickRejected($picksequence);
            }

            // reset flag to use auto picks - always make user turn this back on for every race
            $update_auto_pick_sql = "UPDATE member_queues set active = 2 where member_id = " . $pick_id_for_queue;
            mysql_query($update_auto_pick_sql) or die(mysql_error());
        }
        else
        {
            if(NOTIFICATIONS == YES || NOTIFICATIONS == 3)
            {
                // email member that he picks next
                emailNextPick( $picksequence );
            }
        }

        return $picked;
    }
?>