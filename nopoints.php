<h2 class="heading">Pick Info</h2>
<div class="content"  style='line-height:20px;color:#B5AFAE'>
<span class="smallCaps">The following drivers will receive no points if you pick them:</span>
    <p></p>

    <?
    $table = "no_points";
    if( mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$table."'")))
    {
        $sql =  "SELECT driver from no_points order by driver";

  		$results = mysql_query($sql) or die(mysql_error());
  		while($row = mysql_fetch_assoc($results))
  		{
            echo $row["driver"]."<br/>";
        }
    }
    else
    {
    ?>
        #21 - Trevor Bayne<br/>
        #33 - Austin Dillon<br/>
        #51 - Regan Smith<br/>
        #87 - Joe Nemechek<br/>
    <?
    }
    ?>

</div>
