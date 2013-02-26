<?php
include("htmlparser.php");

function getSchedules( $year )
{
	echo "Getting schedule for year ".$year."\n";

	// State flags for begin and end of parsing
	$foundTableStart = FALSE;
	$foundTableEnd   = FALSE;

	// Index in the results array
	$row = 0;

	$results = null;

	// Fetch the results for this race number    
    $parser = HtmlParser_ForURL_cURL( "http://www.nascar.com/races/cup/".$year."/data/schedule.html" );
    while( $parser->parse() )
	{
		// Find the start of the table
		if( $parser->iNodeType == NODE_TYPE_TEXT && $parser->iNodeValue == "Links" || $parser->iNodeValue == "LINKS" )
		{
			$foundTableStart = TRUE;
		}

		// End of the table data, return the results we have
		if( $foundTableStart &&
			$parser->iNodeType == NODE_TYPE_ENDELEMENT && 
			$parser->iNodeName == "table" )
		{
			break;
		}

		// Start looking at table rows
		if( $foundTableStart == TRUE )
		{
			// Start of a TR tag
			if( $parser->iNodeType == NODE_TYPE_ELEMENT && 
				$parser->iNodeName == "tr" )
			{
				// Parse this row
				$rowData = parseRow($parser, 9);

				// If we have row data, add to our row array
				if( isset($rowData) )
				{
					$results[$row++] = $rowData;
				}
			}
		}
	}

	echo "Returning ".count($results)." rows of schedules\n";

	return $results;

}

function getResults( $espn_race_id )
{
	echo "Getting results from espn.com with raceid ".$espn_race_id."\n";

	// State flags for begin and end of parsing
	$foundTableStart = FALSE;
	$foundTableEnd   = FALSE;

	// Index in the results array
	$row = 0;

	$results = null;

	// Fetch the results for this race number
	$url = "http://espn.go.com/racing/raceresults/_/series/sprint/raceId/".$espn_race_id;
	echo "fetching from url: ".$url."\n";
    $parser = HtmlParser_ForURL_cURL( $url );
	if( !isset($parser) || $parser == null )
	{
		echo "No results found for race id ".$espn_race_id.".";
		return;
	}
    while( $parser->parse() )
	{
		// Find the start of the table		
		if( $parser->iNodeType == NODE_TYPE_TEXT && $parser->iNodeValue == "POS" || $parser->iNodeValue == "pos" )
		{
			$foundTableStart = TRUE;			
		}

		// End of the table data, return the results we have
		if( $foundTableStart &&
			$parser->iNodeType == NODE_TYPE_ENDELEMENT && 
			$parser->iNodeName == "table" )
		{
			break;
		}

		// Start looking at table rows
		if( $foundTableStart == TRUE )
		{			
			// Start of a TR tag
			if( $parser->iNodeType == NODE_TYPE_ELEMENT && 
				$parser->iNodeName == "tr" )
			{
				// Parse this row
				$rowData = parseRow($parser, 11);

				// If we have row data, add to our row array
				if( isset($rowData) )
				{
					$results[$row++] = $rowData;
				}
			}
		}
	}

	echo "Returning ".count($results)." rows of results\n";

	return $results;
}

function parseRow( $parser, $target_col_count )
{
	$col = 0;
	$rowData = null;
	
	// We should be on a TR tag when called, we'll
	// iterate and return all the data columns in this row
    while( $parser->parse() )
	{			
		// End tag
		if( $parser->iNodeName == "tr" &&
			$parser->iNodeType == NODE_TYPE_ENDELEMENT )
		{
			// Advance the point and return the array
			//$parser->parse();

			// 8 columns, perfect this is what we want
			//if( $col == $target_col_count )
			//{
				return $rowData;
			//}
			// Crap, weird row, ignore it
			//else
			//{
			//	return null;
			//}
		}
		// Table data 
		else if( $parser->iNodeName == "td" && 
		       $parser->iNodeType != NODE_TYPE_ENDELEMENT )		 
		{
			// Get the actual text data
			$parser->parse();
		        $hadLink = FALSE;

			// If it's a link, skip it
			if( $parser->iNodeName == "a" )
			{
				$parser->parse();
				$hadLink = TRUE;
			}
			
			$rowData[$col++] = $parser->iNodeValue;

			// Find the end tag of the <a> tag
			if( $hadLink )
			{
				while( $parser->parse() && !($parser->iNodeType == NODE_TYPE_ENDELEMENT && $parser->iNodeName == "a") );
			}
		}
	}
}
?>
