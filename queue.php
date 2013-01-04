<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<style>
    #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
    #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
    #sortable li span { position: absolute; margin-left: -1.3em; }
</style>
<script>
    $(function()
    {
        $( "#sortable" ).sortable();
        $( "#sortable" ).disableSelection();
    });

    function saveOrder()
    {
        var order = "";
        $(".order").each(function()
        {
            var add = this.id + ",";
            order += add;
        });

        var active = $("#useQueue").is(":checked");

        $.post("saveorder.php",{ order: order, active: active });
    }
</script>
<?php
    $member_id = $_SESSION['USERID'];
    $results = mysql_query("select id, active from member_queues where member_id = " . $member_id);
    $row = mysql_fetch_assoc($results);

    $active = false;

    if($row != null && $row['id'] != null)
    {
        $queue_sql = "select d.name, d.id from queues q, drivers d where q.member_queue_id = " . $row['id'] . " and d.id = q.driver_id order by q.position";
        if($row['active'] == 1)
        {
            $active = true;
        }
    }
    else
    {
        $queue_sql = "SELECT id,name,number,owner,team,make,must_qualify FROM drivers d WHERE d.isactive=1 ORDER BY name ASC";
    }

    $queue_results = mysql_query($queue_sql) or die(mysql_error());

?>
<a href = "#" onclick="saveOrder();">Save</a>
<br/>
<br/>
<input type="checkbox" id="useQueue" name="useQueue" <?php if($active) echo 'checked' ?>>Use pick queue to automatically pick when it is your turn</>
<br/>
<br/>
<ul id="sortable">
<?php
    while($row = mysql_fetch_assoc($queue_results))
    {
        echo "<li class='ui-state-default order' id='" . $row['id'] . "'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span>" . $row['name'] . "</li>";
    }
?>
</ul>
