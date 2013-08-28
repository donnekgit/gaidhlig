<?php

// This script creates a copy of the input file with multiwords connected with an underscore.  Run gather_multiwords.php first.

if (!isset($chain))
{
	include("includes/fns.php");
	include("/opt/gaidhlig/config.php");
}

$multiwords="multiwords";

//$files=scandir($dir);
//$infiles=array("smo.txt");
$infiles=array("inputs/taic21-20.txt");

foreach ($infiles as $infile)
{
	$outfile=preg_replace("/\.txt$/", ".out", $infile);
	
	// Copy the file.
	$output=file_get_contents($infile);
	// 		echo $output."\n";
	
	$sql=query("select surface from $multiwords order by id;");
	while ($row=pg_fetch_object($sql))
	{
		// Non-capitalised forms.
		$surface=$row->surface;
		$replace=preg_replace("/ /", "_", $row->surface);
		echo $replace."\n";
		$output=preg_replace("/(\b)$surface(\b)/", "$1$replace$2", $output);
		
		// Capitalised forms.
		$surface=ucfirst($surface);
		$replace=ucfirst($replace);
		//echo $replace."\n";
		$output=preg_replace("/(\b)$surface(\b)/", "$1$replace$2", $output);
	}
	
	file_put_contents($outfile, $output);
}

?>