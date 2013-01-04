<div class="post">
	<div class="story">	
		<h1 class="title">Tracks</h1>
		<table id="tracks">
	<?php
		$count = 0;
		$sql = "SELECT t.name, tt.name AS type, ts.name AS shape, t.length, t.banking, t.frontstretch, t.backstretch, t.seating, t.id FROM tracks t INNER JOIN types_tracktype tt ON t.type = tt.id INNER JOIN types_trackshape ts ON t.shape = ts.id ORDER BY t.name ASC;--";
		$results = mysql_query($sql);
		
		echo "<tr><th align='center'>#</th><th>Track Name</th><th>Type</th><th>Shape</th><th>Length</th><th>Banking</th><th>Front Stretch</th><th>Back Stretch</th><th>Seating</th><th>Driver Results & Averages</th></tr>\n";
		while($row = mysql_fetch_assoc($results))
		{	$count++;
			echo "<tr><td align='center'>$count</td><td>" . $row['name'] . "</td><td>" . $row['type'] . "</td><td>" . $row['shape'] . "</td><td align='right'>" . $row['length'] . "</td><td>" . $row['banking'] . "</td><td align='right'>" . $row['frontstretch'] . "</td><td align='right'>" . $row['backstretch'] . "</td><td align='right'>" . $row['seating'] . "</td><td align='center'><a href='".$config_basedir."index.php?content_page=historicalresults&track_id=".$row['id']."'>View</a></td></tr>\n";
		}
	?>
		</table>
	</div>
</div> <!-- right-col -->