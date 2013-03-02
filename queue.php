<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<style>
    #sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
    #sortable li span { position: absolute; margin-left: -1.3em; }

    #possible { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    #possible li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
    #possible li span { position: absolute; margin-left: -1.3em; }

    .x {float:right}

</style>
<script>
    $(function()
    {
        $( "#sortable" ).sortable();
        $( "#sortable" ).disableSelection();

        $("#possible .ui-icon-arrowthick-2-n-s").hide();
        $("#possible .x").hide();
    });

    function saveOrder()
    {
        var order = "";
        $("#sortable .order").each(function()
        {
            var add = this.id + ",";
            order += add;
        });

        var active = $("#useQueue").is(":checked");

        $.post("saveorder.php",{ order: order, active: active });
        alert("Queue Saved!");
    }

    $(document).ready(function()
    {
        $("#possible .clickable").live('click', function(event) {
            var $id = $(this).attr('id');
            var $returnVal = !$(this).remove().appendTo('#sortable');
            $("#sortable .ui-icon-arrowthick-2-n-s").show();
            $("#sortable .x").show();
            return $returnVal;
        });

        $("#sortable .x").live('click', function(event) {
            var $id = $(this).parent().attr('id');
            var $returnVal = !$(this).parent().remove().appendTo('#possible');
            $(this).parent().class = $(this).parent().addClass('clickable')
            $("#possible .ui-icon-arrowthick-2-n-s").hide();
            $("#possible .x").hide();
            return $returnVal;
        });
    });
</script>
<?php
    $member_id = $_SESSION['USERID'];
    $results = mysql_query("select id, active from member_queues where member_id = " . $member_id);
    $row = mysql_fetch_assoc($results);

    $active = false;

    // duping some query executions here, inefficient, but oh well
    $in_queue = array();
    if($row != null && $row['id'] != null)
    {
        $queue_sql = "select d.name, d.id, d.currentpoints from queues q, drivers d where q.member_queue_id = " . $row['id'] . " and d.id = q.driver_id order by q.position";
        if($row['active'] == 1)
        {
            $active = true;
        }

        $queue_results = mysql_query($queue_sql) or die(mysql_error());

        while($row = mysql_fetch_assoc($queue_results))
        {
            $in_queue[$row['id']] = 1;
        }

        $queue_results = mysql_query($queue_sql) or die(mysql_error());
    }

    $possible_sql = "SELECT id,currentpoints,name,number,owner,team,make,must_qualify FROM drivers d WHERE d.isactive=1 ORDER BY currentpoints DESC,name ASC";
    $possible_results = mysql_query($possible_sql) or die(mysql_error());

?>
<div id="instructions" style="color:#B5AFAE">
    <p>
The drivers on the left are drivers that you can add to your pick queue.  The drivers on the right are the drivers currently
    in your pick queue.  Click on a driver on the left to add them to the queue on the right.  Once drivers are on the right
    side, you can drag them up and down to order them.  Click "Save" to save your queue.
    </p>
    <p>
        If you have chosen to use the pick queue to make your pick automatically, the driver highest on your queue who hasn't
        been chosen, will be automatically picked for you.  If all the drivers on your queue have been picked when it is
        your turn, you will need to pick manually (like normal).

    </p>
</div>
<a href = "#" onclick="saveOrder();" style="font-size:1.5em;">Save</a>
<br/>
<br/>
<input type="checkbox" id="useQueue" name="useQueue" <?php if($active) echo 'checked' ?>><span style="color:#B5AFAE">Use pick queue to automatically pick when it is your turn</span></>
<br/>
<br/>
<div>
    <div id='possibleDiv' style="vertical-align: top;display: inline-block; width:40%">
        <span style="color: white;font-size: 1.5em;margin-left: 5px;">All Drivers</span>
        <ul id="possible">
            <?php
                while($row = mysql_fetch_assoc($possible_results))
                {
                    if($in_queue[$row['id']] != 1)
                    {
                        echo "<li class='ui-state-default order clickable' id='" . $row['id'] . "'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span>" . $row['name'] . " (" . $row['currentpoints'] .")<div class='x'>X</div></li>";
                    }
                }
            ?>
        </ul>
    </div>
    <div id='sortableDiv' style="vertical-align: top;display: inline-block; width:40%;float:right">
        <span style="color: white;font-size: 1.5em;margin-left: 5px;">Your Queue</span>
        <ul id="sortable">
            <?php
                while($row = mysql_fetch_assoc($queue_results))
                {
                    echo "<li class='ui-state-default order' id='" . $row['id'] . "'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span>" . $row['name'] . " (" . $row['currentpoints'] .")<div class='x'>X</div></li>";
                }
            ?>
        </ul>
    </div>
</div>
